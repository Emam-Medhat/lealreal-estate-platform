<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DocumentVersionController extends Controller
{
    public function index(Document $document)
    {
        $versions = $document->versions()
            ->with(['createdBy'])
            ->orderBy('version_number', 'desc')
            ->paginate(20);
            
        return view('documents.versions.index', compact('document', 'versions'));
    }

    public function dashboard()
    {
        $versions = DocumentVersion::with(['document', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_versions' => DocumentVersion::count(),
            'active_versions' => DocumentVersion::where('is_current', true)->count(),
            'archived_versions' => DocumentVersion::where('status', 'archived')->count(),
            'recent_versions' => DocumentVersion::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('documents.versions.dashboard', compact('versions', 'stats'));
    }
    
    public function create(Document $document)
    {
        $document->load(['versions']);
        
        return view('documents.versions.create', compact('document'));
    }
    
    public function store(Request $request, Document $document)
    {
        $request->validate([
            'content' => 'required|string',
            'changes_summary' => 'required|string|max:1000',
            'version_type' => 'required|in:major,minor,patch',
            'create_from_current' => 'boolean',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Get latest version
            $latestVersion = $document->versions()->latest()->first();
            
            // Calculate new version number
            $newVersionNumber = $this->calculateVersionNumber($latestVersion, $request->version_type);
            
            // Create new version
            $version = DocumentVersion::create([
                'document_id' => $document->id,
                'version_number' => $newVersionNumber,
                'content' => $request->content,
                'changes_summary' => $request->changes_summary,
                'version_type' => $request->version_type,
                'created_by' => auth()->id(),
            ]);
            
            // Update document with latest version content
            $document->update([
                'content' => $request->content,
                'current_version_id' => $version->id,
                'updated_at' => now(),
            ]);
            
            // Log version creation
            $this->logVersionActivity($document, $version, 'created');
            
            DB::commit();
            
            return redirect()->route('documents.versions.show', [$document, $version])
                ->with('success', 'تم إنشاء نسخة جديدة من الوثيقة بنجاح');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء النسخة الجديدة: ' . $e->getMessage());
        }
    }
    
    public function show(Document $document, DocumentVersion $version)
    {
        // Verify version belongs to document
        if ($version->document_id !== $document->id) {
            abort(404);
        }
        
        $version->load(['createdBy', 'document']);
        
        // Get previous and next versions for navigation
        $previousVersion = $document->versions()
            ->where('version_number', '<', $version->version_number)
            ->orderBy('version_number', 'desc')
            ->first();
            
        $nextVersion = $document->versions()
            ->where('version_number', '>', $version->version_number)
            ->orderBy('version_number', 'asc')
            ->first();
        
        return view('documents.versions.show', compact('document', 'version', 'previousVersion', 'nextVersion'));
    }
    
    public function edit(Document $document, DocumentVersion $version)
    {
        // Verify version belongs to document
        if ($version->document_id !== $document->id) {
            abort(404);
        }
        
        // Check if version can be edited (only latest version or draft versions)
        $latestVersion = $document->versions()->latest()->first();
        
        if ($version->id !== $latestVersion->id && $version->status !== 'draft') {
            return back()->with('error', 'لا يمكن تعديل هذه النسخة');
        }
        
        return view('documents.versions.edit', compact('document', 'version'));
    }
    
    public function update(Request $request, Document $document, DocumentVersion $version)
    {
        // Verify version belongs to document
        if ($version->document_id !== $document->id) {
            abort(404);
        }
        
        $request->validate([
            'content' => 'required|string',
            'changes_summary' => 'required|string|max:1000',
        ]);
        
        DB::beginTransaction();
        
        try {
            $oldContent = $version->content;
            
            $version->update([
                'content' => $request->content,
                'changes_summary' => $request->changes_summary,
                'updated_at' => now(),
            ]);
            
            // If this is the current version, update document too
            if ($document->current_version_id === $version->id) {
                $document->update([
                    'content' => $request->content,
                    'updated_at' => now(),
                ]);
            }
            
            // Log version update
            $this->logVersionActivity($document, $version, 'updated', [
                'old_content_length' => strlen($oldContent),
                'new_content_length' => strlen($request->content),
            ]);
            
            DB::commit();
            
            return redirect()->route('documents.versions.show', [$document, $version])
                ->with('success', 'تم تحديث النسخة بنجاح');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث النسخة: ' . $e->getMessage());
        }
    }
    
    public function compare(Document $document, DocumentVersion $version1, DocumentVersion $version2 = null)
    {
        // Verify versions belong to document
        if ($version1->document_id !== $document->id) {
            abort(404);
        }
        
        if ($version2 && $version2->document_id !== $document->id) {
            abort(404);
        }
        
        // If version2 is not provided, compare with current document content
        if (!$version2) {
            $version2Content = $document->content;
            $version2Title = 'النسخة الحالية';
        } else {
            $version2Content = $version2->content;
            $version2Title = 'نسخة ' . $version2->version_number;
        }
        
        // Generate diff
        $diff = $this->generateDiff($version1->content, $version2Content);
        
        return view('documents.versions.compare', compact(
            'document', 
            'version1', 
            'version2', 
            'version2Content', 
            'version2Title', 
            'diff'
        ));
    }
    
    public function restore(Document $document, DocumentVersion $version)
    {
        // Verify version belongs to document
        if ($version->document_id !== $document->id) {
            abort(404);
        }
        
        DB::beginTransaction();
        
        try {
            // Create new version from restored content
            $newVersionNumber = $this->calculateVersionNumber($version, 'minor');
            
            $newVersion = DocumentVersion::create([
                'document_id' => $document->id,
                'version_number' => $newVersionNumber,
                'content' => $version->content,
                'changes_summary' => 'استعادة من نسخة ' . $version->version_number,
                'version_type' => 'minor',
                'created_by' => auth()->id(),
                'restored_from' => $version->id,
            ]);
            
            // Update document
            $document->update([
                'content' => $version->content,
                'current_version_id' => $newVersion->id,
                'updated_at' => now(),
            ]);
            
            // Log restoration
            $this->logVersionActivity($document, $newVersion, 'restored', [
                'restored_from_version' => $version->version_number,
            ]);
            
            DB::commit();
            
            return redirect()->route('documents.versions.show', [$document, $newVersion])
                ->with('success', 'تم استعادة النسخة بنجاح');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->with('error', 'حدث خطأ أثناء استعادة النسخة: ' . $e->getMessage());
        }
    }
    
    public function download(Document $document, DocumentVersion $version)
    {
        // Verify version belongs to document
        if ($version->document_id !== $document->id) {
            abort(404);
        }
        
        $filename = Str::slug($document->title) . '-v' . $version->version_number . '.pdf';
        
        $pdf = \PDF::loadView('documents.versions.pdf', compact('document', 'version'));
        
        return $pdf->download($filename);
    }
    
    public function publish(Document $document, DocumentVersion $version)
    {
        // Verify version belongs to document
        if ($version->document_id !== $document->id) {
            abort(404);
        }
        
        DB::beginTransaction();
        
        try {
            $version->update([
                'status' => 'published',
                'published_at' => now(),
                'published_by' => auth()->id(),
            ]);
            
            // Update document status
            $document->update([
                'status' => 'published',
                'published_at' => now(),
                'published_version_id' => $version->id,
            ]);
            
            // Log publication
            $this->logVersionActivity($document, $version, 'published');
            
            DB::commit();
            
            return back()->with('success', 'تم نشر النسخة بنجاح');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->with('error', 'حدث خطأ أثناء نشر النسخة');
        }
    }
    
    public function archive(Document $document, DocumentVersion $version)
    {
        // Verify version belongs to document
        if ($version->document_id !== $document->id) {
            abort(404);
        }
        
        $version->update([
            'status' => 'archived',
            'archived_at' => now(),
            'archived_by' => auth()->id(),
        ]);
        
        return back()->with('success', 'تم أرشفة النسخة بنجاح');
    }
    
    public function destroy(Document $document, DocumentVersion $version)
    {
        // Verify version belongs to document
        if ($version->document_id !== $document->id) {
            abort(404);
        }
        
        // Check if version can be deleted
        if ($document->current_version_id === $version->id) {
            return back()->with('error', 'لا يمكن حذف النسخة الحالية');
        }
        
        if ($document->published_version_id === $version->id) {
            return back()->with('error', 'لا يمكن حذف النسخة المنشورة');
        }
        
        $version->delete();
        
        return redirect()->route('documents.versions.index', $document)
            ->with('success', 'تم حذف النسخة بنجاح');
    }
    
    public function history(Document $document)
    {
        $versions = $document->versions()
            ->with(['createdBy'])
            ->orderBy('version_number', 'desc')
            ->get();
            
        return view('documents.versions.history', compact('document', 'versions'));
    }
    
    private function calculateVersionNumber(?DocumentVersion $latestVersion, string $type): string
    {
        if (!$latestVersion) {
            return '1.0.0';
        }
        
        $parts = explode('.', $latestVersion->version_number);
        $major = (int) $parts[0];
        $minor = (int) ($parts[1] ?? 0);
        $patch = (int) ($parts[2] ?? 0);
        
        switch ($type) {
            case 'major':
                return ($major + 1) . '.0.0';
            case 'minor':
                return $major . '.' . ($minor + 1) . '.0';
            case 'patch':
                return $major . '.' . $minor . '.' . ($patch + 1);
            default:
                return $major . '.' . $minor . '.' . ($patch + 1);
        }
    }
    
    private function generateDiff(string $content1, string $content2): array
    {
        // Simple diff implementation - in production, use a proper diff library
        $lines1 = explode("\n", $content1);
        $lines2 = explode("\n", $content2);
        
        $diff = [];
        $maxLines = max(count($lines1), count($lines2));
        
        for ($i = 0; $i < $maxLines; $i++) {
            $line1 = $lines1[$i] ?? '';
            $line2 = $lines2[$i] ?? '';
            
            if ($line1 !== $line2) {
                $diff[] = [
                    'line_number' => $i + 1,
                    'type' => $this->getDiffType($line1, $line2),
                    'old' => $line1,
                    'new' => $line2,
                ];
            }
        }
        
        return $diff;
    }
    
    private function getDiffType(string $line1, string $line2): string
    {
        if (empty($line1) && !empty($line2)) {
            return 'added';
        }
        
        if (!empty($line1) && empty($line2)) {
            return 'removed';
        }
        
        return 'modified';
    }
    
    private function logVersionActivity(Document $document, DocumentVersion $version, string $action, array $extra = [])
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($document)
            ->withProperties(array_merge([
                'version_id' => $version->id,
                'version_number' => $version->version_number,
                'action' => $action,
            ], $extra))
            ->log('تم ' . $action . ' نسخة ' . $version->version_number . ' من الوثيقة: ' . $document->title);
    }
}
