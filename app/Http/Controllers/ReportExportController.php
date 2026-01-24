<?php

namespace App\Http\Controllers;

use App\Models\ReportExport;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReportExportController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    /**
     * Display a listing of report exports.
     */
    public function index(Request $request)
    {
        $exports = ReportExport::with(['report', 'creator'])
            ->when($request->search, function ($query, $search) {
                $query->where('filename', 'like', "%{$search}%")
                    ->orWhereHas('report', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    });
            })
            ->when($request->format, function ($query, $format) {
                $query->where('format', $format);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        return view('reports.exports.index', compact('exports'));
    }

    /**
     * Export a specific report.
     */
    public function export(Report $report, Request $request)
    {
        $request->validate([
            'format' => 'required|in:pdf,excel,csv,json',
            'options' => 'nullable|array'
        ]);

        // Check if export already exists
        $existingExport = ReportExport::where('report_id', $report->id)
            ->where('format', $request->format)
            ->where('status', 'completed')
            ->first();

        if ($existingExport && !$request->force_new) {
            return redirect()->route('exports.download', $existingExport);
        }

        // Create new export record
        $export = ReportExport::create([
            'report_id' => $report->id,
            'filename' => $this->generateFilename($report, $request->format),
            'format' => $request->format,
            'options' => $request->options ?? [],
            'status' => 'processing',
            'created_by' => Auth::id(),
        ]);

        // Process export (in background or synchronously)
        try {
            $this->processExport($export);
            $export->update([
                'status' => 'completed',
                'completed_at' => now(),
                'file_size' => Storage::size($export->file_path)
            ]);
        } catch (\Exception $e) {
            $export->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
            
            return back()->with('error', 'فشل تصدير التقرير: ' . $e->getMessage());
        }

        return redirect()->route('exports.download', $export)
            ->with('success', 'تم تصدير التقرير بنجاح');
    }

    /**
     * Download an exported report.
     */
    public function download(ReportExport $export)
    {
        if ($export->status !== 'completed') {
            return back()->with('error', 'التصدير غير مكتمل بعد');
        }

        if (!Storage::exists($export->file_path)) {
            return back()->with('error', 'ملف التصدير غير موجود');
        }

        return Storage::download($export->file_path, $export->filename);
    }

    /**
     * Preview an exported report.
     */
    public function preview(ReportExport $export)
    {
        if ($export->status !== 'completed') {
            return response()->json(['error' => 'التصدير غير مكتمل بعد'], 422);
        }

        if (!Storage::exists($export->file_path)) {
            return response()->json(['error' => 'ملف التصدير غير موجود'], 404);
        }

        // For PDF files, return a preview URL
        if ($export->format === 'pdf') {
            return response()->json([
                'preview_url' => Storage::url($export->file_path),
                'filename' => $export->filename
            ]);
        }

        // For other formats, return a sample of the content
        $content = Storage::get($export->file_path);
        $preview = substr($content, 0, 1000); // First 1000 characters

        return response()->json([
            'preview' => $preview,
            'filename' => $export->filename,
            'format' => $export->format
        ]);
    }

    /**
     * Delete an exported report.
     */
    public function destroy(ReportExport $export)
    {
        // Delete the file if it exists
        if (Storage::exists($export->file_path)) {
            Storage::delete($export->file_path);
        }

        $export->delete();

        return back()->with('success', 'تم حذف التصدير بنجاح');
    }

    /**
     * Share an exported report.
     */
    public function share(ReportExport $export, Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'message' => 'nullable|string|max:500'
        ]);

        // Generate share link
        $shareToken = \Str::random(32);
        $export->update([
            'share_token' => $shareToken,
            'share_expires_at' => now()->addDays(7)
        ]);

        $shareUrl = route('exports.shared', $shareToken);

        // Send email (implementation depends on your email system)
        // Mail::to($request->email)->send(new ReportExportShared($export, $shareUrl, $request->message));

        return back()->with('success', 'تم مشاركة التقرير بنجاح');
    }

    /**
     * Access shared export.
     */
    public function shared($token)
    {
        $export = ReportExport::where('share_token', $token)
            ->where('share_expires_at', '>', now())
            ->firstOrFail();

        return $this->download($export);
    }

    /**
     * Process the export based on format.
     */
    private function processExport(ReportExport $export)
    {
        $report = $export->report;
        $data = $report->getData(); // Assuming report has getData method

        switch ($export->format) {
            case 'pdf':
                $this->generatePdfExport($export, $report, $data);
                break;
            case 'excel':
                $this->generateExcelExport($export, $report, $data);
                break;
            case 'csv':
                $this->generateCsvExport($export, $report, $data);
                break;
            case 'json':
                $this->generateJsonExport($export, $report, $data);
                break;
        }
    }

    /**
     * Generate PDF export.
     */
    private function generatePdfExport(ReportExport $export, Report $report, $data)
    {
        // Implementation depends on your PDF library (e.g., DomPDF, SnappyPDF)
        // Placeholder implementation
        $content = view('reports.exports.pdf', [
            'report' => $report,
            'data' => $data,
            'options' => $export->options
        ])->render();

        $filePath = 'exports/' . $export->filename;
        Storage::put($filePath, $content);
        $export->update(['file_path' => $filePath]);
    }

    /**
     * Generate Excel export.
     */
    private function generateExcelExport(ReportExport $export, Report $report, $data)
    {
        // Implementation depends on your Excel library (e.g., Laravel Excel)
        $filePath = 'exports/' . $export->filename;
        
        // Example using Laravel Excel
        // Excel::store(new ReportExport($report, $data), $filePath);
        
        // Placeholder implementation
        Storage::put($filePath, json_encode($data));
        $export->update(['file_path' => $filePath]);
    }

    /**
     * Generate CSV export.
     */
    private function generateCsvExport(ReportExport $export, Report $report, $data)
    {
        $filePath = 'exports/' . $export->filename;
        $csv = $this->convertToCsv($data);
        Storage::put($filePath, $csv);
        $export->update(['file_path' => $filePath]);
    }

    /**
     * Generate JSON export.
     */
    private function generateJsonExport(ReportExport $export, Report $report, $data)
    {
        $filePath = 'exports/' . $export->filename;
        Storage::put($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $export->update(['file_path' => $filePath]);
    }

    /**
     * Convert data to CSV format.
     */
    private function convertToCsv($data)
    {
        if (empty($data)) {
            return '';
        }

        $csv = '';
        $headers = array_keys((array) $data[0]);
        $csv .= implode(',', $headers) . "\n";

        foreach ($data as $row) {
            $values = array_map(function ($value) {
                return is_string($value) ? '"' . str_replace('"', '""', $value) . '"' : $value;
            }, (array) $row);
            $csv .= implode(',', $values) . "\n";
        }

        return $csv;
    }

    /**
     * Generate filename for export.
     */
    private function generateFilename(Report $report, $format)
    {
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $report->name);
        return "{$safeName}_{$timestamp}.{$format}";
    }

    /**
     * Get export statistics.
     */
    public function getStats()
    {
        $stats = [
            'total_exports' => ReportExport::count(),
            'completed_exports' => ReportExport::where('status', 'completed')->count(),
            'failed_exports' => ReportExport::where('status', 'failed')->count(),
            'processing_exports' => ReportExport::where('status', 'processing')->count(),
            'total_file_size' => ReportExport::where('status', 'completed')->sum('file_size'),
            'exports_today' => ReportExport::whereDate('created_at', today())->count(),
            'most_used_format' => ReportExport::selectRaw('format, COUNT(*) as count')
                ->groupBy('format')
                ->orderByDesc('count')
                ->first()
        ];

        return response()->json($stats);
    }

    /**
     * Clean up old exports.
     */
    public function cleanup()
    {
        $oldExports = ReportExport::where('created_at', '<', now()->subDays(30))
            ->where('status', 'completed')
            ->get();

        foreach ($oldExports as $export) {
            if (Storage::exists($export->file_path)) {
                Storage::delete($export->file_path);
            }
            $export->delete();
        }

        return back()->with('success', 'تم تنظيف التصديرات القديمة بنجاح');
    }
}
