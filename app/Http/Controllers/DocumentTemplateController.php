<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use Illuminate\Http\Request;

class DocumentTemplateController extends Controller
{
    public function index()
    {
        $templates = DocumentTemplate::with(['createdBy'])
            ->orderBy('name')
            ->paginate(20);
            
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
            'variables.*.type' => 'required|in:text,date,number,select',
            'variables.*.options' => 'nullable|array',
            'category' => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);
        
        $template = DocumentTemplate::create([
            'name' => $request->name,
            'description' => $request->description,
            'content' => $request->content,
            'variables' => $request->variables ?? [],
            'category' => $request->category,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => auth()->id(),
        ]);
        
        return redirect()->route('document-templates.show', $template)
            ->with('success', 'تم إنشاء قالب الوثيقة بنجاح');
    }
    
    public function show(DocumentTemplate $template)
    {
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
            'variables.*.type' => 'required|in:text,date,number,select',
            'variables.*.options' => 'nullable|array',
            'category' => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);
        
        $template->update([
            'name' => $request->name,
            'description' => $request->description,
            'content' => $request->content,
            'variables' => $request->variables ?? [],
            'category' => $request->category,
            'is_active' => $request->boolean('is_active', $template->is_active),
        ]);
        
        return redirect()->route('document-templates.show', $template)
            ->with('success', 'تم تحديث قالب الوثيقة بنجاح');
    }
    
    public function destroy(DocumentTemplate $template)
    {
        $template->delete();
        
        return redirect()->route('document-templates.index')
            ->with('success', 'تم حذف قالب الوثيقة بنجاح');
    }
    
    public function duplicate(DocumentTemplate $template)
    {
        $newTemplate = $template->replicate();
        $newTemplate->name = $template->name . ' (نسخة)';
        $newTemplate->created_by = auth()->id();
        $newTemplate->save();
        
        return redirect()->route('document-templates.show', $newTemplate)
            ->with('success', 'تم نسخ قالب الوثيقة بنجاح');
    }
    
    public function preview(Request $request, DocumentTemplate $template)
    {
        $data = $request->except(['_token', '_method']);
        
        $content = $template->content;
        
        // Replace variables with actual data
        foreach ($template->variables ?? [] as $variable) {
            $placeholder = '{{' . $variable['name'] . '}}';
            $value = $data[$variable['name']] ?? '[' . $variable['name'] . ']';
            $content = str_replace($placeholder, $value, $content);
        }
        
        return view('document-templates.preview', compact('template', 'content'));
    }
    
    public function generate(Request $request, DocumentTemplate $template)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'data' => 'required|array',
        ]);
        
        // Generate document from template
        $content = $template->content;
        
        foreach ($template->variables ?? [] as $variable) {
            $placeholder = '{{' . $variable['name'] . '}}';
            $value = $request->data[$variable['name']] ?? '';
            $content = str_replace($placeholder, $value, $content);
        }
        
        // Create temporary PDF or document file
        $filename = 'generated_' . time() . '.pdf';
        $filepath = storage_path('app/temp/' . $filename);
        
        // This would use a PDF generation library like DomPDF
        // For now, we'll create a simple text file
        file_put_contents($filepath, $content);
        
        return response()->download($filepath, $request->title . '.pdf')
            ->deleteFileAfterSend(true);
    }
}
