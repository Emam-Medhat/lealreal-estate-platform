<?php

namespace App\Http\Controllers;

use App\Models\ReportTemplate;
use App\Http\Requests\StoreReportTemplateRequest;
use App\Http\Requests\UpdateReportTemplateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportTemplateController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed, e.g.:
        // $this->middleware('auth');
        // $this->middleware('permission:report_templates_view')->only(['index', 'show']);
    }

    /**
     * Display a listing of report templates.
     */
    public function index(Request $request)
    {
        $templates = ReportTemplate::with(['creator', 'reports'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->category, function ($query, $category) {
                $query->where('category', $category);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('is_active', $status === 'active');
            })
            ->latest()
            ->paginate(10);

        $categories = ReportTemplate::distinct()->pluck('category');

        return view('reports.templates.index', compact('templates', 'categories'));
    }

    /**
     * Show the form for creating a new report template.
     */
    public function create()
    {
        $categories = ReportTemplate::distinct()->pluck('category');
        return view('reports.templates.create', compact('categories'));
    }

    /**
     * Store a newly created report template in storage.
     */
    public function store(StoreReportTemplateRequest $request)
    {
        $template = ReportTemplate::create([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'template_type' => $request->template_type,
            'configuration' => $request->configuration,
            'parameters' => $request->parameters,
            'layout' => $request->layout,
            'styles' => $request->styles,
            'is_active' => $request->boolean('is_active'),
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('templates.show', $template)
            ->with('success', 'تم إنشاء قالب التقرير بنجاح');
    }

    /**
     * Display the specified report template.
     */
    public function show(ReportTemplate $template)
    {
        $template->load(['creator', 'reports' => function ($query) {
            $query->latest()->limit(5);
        }]);

        return view('reports.templates.show', compact('template'));
    }

    /**
     * Show the form for editing the specified report template.
     */
    public function edit(ReportTemplate $template)
    {
        $categories = ReportTemplate::distinct()->pluck('category');
        return view('reports.templates.edit', compact('template', 'categories'));
    }

    /**
     * Update the specified report template in storage.
     */
    public function update(UpdateReportTemplateRequest $request, ReportTemplate $template)
    {
        $template->update([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'template_type' => $request->template_type,
            'configuration' => $request->configuration,
            'parameters' => $request->parameters,
            'layout' => $request->layout,
            'styles' => $request->styles,
            'is_active' => $request->boolean('is_active'),
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('templates.show', $template)
            ->with('success', 'تم تحديث قالب التقرير بنجاح');
    }

    /**
     * Remove the specified report template from storage.
     */
    public function destroy(ReportTemplate $template)
    {
        if ($template->reports()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف القالب المستخدم في تقارير موجودة');
        }

        $template->delete();

        return redirect()
            ->route('templates.index')
            ->with('success', 'تم حذف قالب التقرير بنجاح');
    }

    /**
     * Duplicate the specified report template.
     */
    public function duplicate(ReportTemplate $template)
    {
        $newTemplate = $template->replicate([
            'created_by',
            'updated_by',
        ]);

        $newTemplate->name = $template->name . ' (نسخة)';
        $newTemplate->created_by = Auth::id();
        $newTemplate->updated_by = null;
        $newTemplate->save();

        return redirect()
            ->route('templates.edit', $newTemplate)
            ->with('success', 'تم نسخ قالب التقرير بنجاح');
    }

    /**
     * Preview the report template.
     */
    public function preview(ReportTemplate $template, Request $request)
    {
        $parameters = $request->except(['_token', '_method']);
        
        // Generate preview data based on template configuration
        $previewData = $template->generatePreviewData($parameters);

        return view('reports.templates.preview', [
            'template' => $template,
            'data' => $previewData,
            'parameters' => $parameters
        ]);
    }

    /**
     * Export the report template.
     */
    public function export(ReportTemplate $template, Request $request)
    {
        $format = $request->format ?? 'json';
        
        switch ($format) {
            case 'json':
                return response()->json($template->toArray());
                break;
            case 'pdf':
                // Generate PDF export
                $pdf = $template->generatePdfExport();
                return $pdf->download($template->name . '.pdf');
                break;
            default:
                return back()->with('error', 'تنسيق التصدير غير مدعوم');
        }
    }

    /**
     * Import a report template.
     */
    public function import(Request $request)
    {
        $request->validate([
            'template_file' => 'required|file|mimes:json'
        ]);

        $file = $request->file('template_file');
        $content = json_decode(file_get_contents($file), true);

        if (!$content) {
            return back()->with('error', 'ملف القالب غير صالح');
        }

        $template = ReportTemplate::create([
            'name' => $content['name'] . ' (مستورد)',
            'description' => $content['description'] ?? '',
            'category' => $content['category'] ?? 'general',
            'template_type' => $content['template_type'] ?? 'standard',
            'configuration' => $content['configuration'] ?? [],
            'parameters' => $content['parameters'] ?? [],
            'layout' => $content['layout'] ?? [],
            'styles' => $content['styles'] ?? [],
            'is_active' => false,
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('templates.edit', $template)
            ->with('success', 'تم استيراد قالب التقرير بنجاح');
    }

    /**
     * Get template parameters for AJAX requests.
     */
    public function getParameters(ReportTemplate $template)
    {
        return response()->json([
            'parameters' => $template->parameters,
            'configuration' => $template->configuration
        ]);
    }

    /**
     * Validate template configuration.
     */
    public function validateConfiguration(Request $request)
    {
        $request->validate([
            'configuration' => 'required|array',
            'template_type' => 'required|string'
        ]);

        $configuration = $request->configuration;
        $templateType = $request->template_type;

        // Validate configuration based on template type
        $validator = ReportTemplate::validateConfiguration($configuration, $templateType);

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'message' => 'التكوين صحيح'
        ]);
    }
}
