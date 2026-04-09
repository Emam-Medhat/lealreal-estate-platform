<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use App\Repositories\Contracts\DocumentTemplateRepositoryInterface;
use App\Services\DocumentTemplateService;
use Illuminate\Http\Request;

class DocumentTemplateController extends Controller
{
    protected $documentTemplateRepository;
    protected $documentTemplateService;

    public function __construct(
        DocumentTemplateRepositoryInterface $documentTemplateRepository,
        DocumentTemplateService $documentTemplateService
    ) {
        $this->documentTemplateRepository = $documentTemplateRepository;
        $this->documentTemplateService = $documentTemplateService;
    }

    public function index()
    {
        $templates = $this->documentTemplateRepository->getAllPaginated(20);
        return view('document-templates.index', compact('templates'));
    }
    
    public function create()
    {
        return view('document-templates.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'content' => 'required|string',
            'variables' => 'nullable|array',
            'variables.*.name' => 'required|string|max:100',
            'variables.*.description' => 'nullable|string|max:255',
            'variables.*.type' => 'required|in:text,date,number,select,currency,email,phone',
            'variables.*.options' => 'nullable|array',
            'variables.*.options_text' => 'nullable|string',
            'category' => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);

        $variables = $this->documentTemplateService->parseVariables($request->variables);
        
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'content' => $request->content,
            'variables' => $variables,
            'category' => $request->category,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => auth()->id(),
        ];
        
        $template = $this->documentTemplateRepository->create($data);
        
        return redirect()->route('document-templates.show', $template)
            ->with('success', 'تم إنشاء قالب الوثيقة بنجاح');
    }
    
    public function show(DocumentTemplate $template)
    {
        // Using model directly here since route binding provides it, 
        // but we ensure relationships are loaded efficiently.
        $template->load(['createdBy']);
        
        return view('document-templates.show', compact('template'));
    }
    
    public function edit(DocumentTemplate $template)
    {
        return view('document-templates.edit', compact('template'));
    }
    
    public function update(Request $request, DocumentTemplate $template)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'content' => 'required|string',
            'variables' => 'nullable|array',
            'variables.*.name' => 'required|string|max:100',
            'variables.*.description' => 'nullable|string|max:255',
            'variables.*.type' => 'required|in:text,date,number,select,currency,email,phone',
            'variables.*.options' => 'nullable|array',
            'variables.*.options_text' => 'nullable|string',
            'category' => 'required|string|max:100',
            'is_active' => 'boolean',
            'language' => 'nullable|string|max:10',
            'direction' => 'nullable|string|in:rtl,ltr',
            'requires_signature' => 'nullable|boolean',
        ]);

        $variables = $this->documentTemplateService->parseVariables($request->variables);
        
        $updateData = [
            'name' => $request->name,
            'description' => $request->description,
            'content' => $request->content,
            'variables' => $variables,
            'category' => $request->category,
            'is_active' => $request->boolean('is_active', $template->is_active),
        ];
        
        $this->documentTemplateRepository->update($template, $updateData);
        
        return redirect()->route('document-templates.show', $template)
            ->with('success', 'تم تحديث قالب الوثيقة بنجاح');
    }
    
    public function destroy(DocumentTemplate $template)
    {
        $this->documentTemplateRepository->delete($template);
        
        return redirect()->route('document-templates.index')
            ->with('success', 'تم حذف قالب الوثيقة بنجاح');
    }
    
    public function duplicate(DocumentTemplate $template)
    {
        $newTemplate = $this->documentTemplateRepository->duplicate($template);
        
        return redirect()->route('document-templates.show', $newTemplate)
            ->with('success', 'تم نسخ قالب الوثيقة بنجاح');
    }
    
    public function preview(Request $request, DocumentTemplate $template)
    {
        $data = $request->except(['_token', '_method']);
        
        $content = $this->documentTemplateService->preview($template, $data);
        
        return view('document-templates.preview', compact('template', 'content'));
    }

    public function showGenerateForm(DocumentTemplate $template)
    {
        return view('document-templates.generate', compact('template'));
    }
    
    public function generate(Request $request, DocumentTemplate $template)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'data' => 'required|array',
        ]);
        
        // Generate document from template
        $content = $this->documentTemplateService->render($template, $request->data);
        
        // Create temporary PDF or document file
        // Ensure directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $filename = 'generated_' . time() . '.pdf';
        $filepath = storage_path('app/temp/' . $filename);
        
        // This would use a PDF generation library like DomPDF
        // For now, we'll create a simple text file
        file_put_contents($filepath, $content);
        
        return response()->download($filepath, $request->title . '.pdf')
            ->deleteFileAfterSend(true);
    }
}
