<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::with(['category', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('documents.index', compact('documents'));
    }
    
    public function create()
    {
        $categories = DocumentCategory::all();
        
        return view('documents.create', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'category_id' => 'required|exists:document_categories,id',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:100',
            'is_confidential' => 'boolean',
            'expires_at' => 'nullable|date|after:today',
        ]);
        
        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('documents', $filename);
        
        $document = Document::create([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'file_type' => $file->getMimeType(),
            'tags' => $request->tags ?? [],
            'is_confidential' => $request->boolean('is_confidential'),
            'expires_at' => $request->expires_at,
            'created_by' => auth()->id(),
        ]);
        
        return redirect()->route('documents.show', $document)
            ->with('success', 'تم إنشاء الوثيقة بنجاح');
    }
    
    public function show(Document $document)
    {
        $document->load(['category', 'createdBy', 'versions', 'signatures']);
        
        return view('documents.show', compact('document'));
    }
    
    public function edit(Document $document)
    {
        $categories = DocumentCategory::all();
        
        return view('documents.edit', compact('document', 'categories'));
    }
    
    public function update(Request $request, Document $document)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'category_id' => 'required|exists:document_categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:100',
            'is_confidential' => 'boolean',
            'expires_at' => 'nullable|date|after:today',
        ]);
        
        $document->update([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'tags' => $request->tags ?? [],
            'is_confidential' => $request->boolean('is_confidential'),
            'expires_at' => $request->expires_at,
        ]);
        
        return redirect()->route('documents.show', $document)
            ->with('success', 'تم تحديث الوثيقة بنجاح');
    }
    
    public function destroy(Document $document)
    {
        // Delete file from storage
        if ($document->filename) {
            \Storage::delete('documents/' . $document->filename);
        }
        
        $document->delete();
        
        return redirect()->route('documents.index')
            ->with('success', 'تم حذف الوثيقة بنجاح');
    }
    
    public function download(Document $document)
    {
        if (!$document->filename) {
            abort(404);
        }
        
        $path = storage_path('app/documents/' . $document->filename);
        
        if (!file_exists($path)) {
            abort(404);
        }
        
        // Log access
        \App\Models\DocumentAccessLog::create([
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'action' => 'download',
            'ip_address' => request()->ip(),
        ]);
        
        return response()->download($path, $document->original_filename);
    }
    
    public function preview(Document $document)
    {
        if (!$document->filename) {
            abort(404);
        }
        
        // Log access
        \App\Models\DocumentAccessLog::create([
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'action' => 'preview',
            'ip_address' => request()->ip(),
        ]);
        
        return view('documents.preview', compact('document'));
    }
    
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $documents = Document::with(['category', 'createdBy'])
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhereJsonContains('tags', $query);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('documents.search', compact('documents', 'query'));
    }
}
