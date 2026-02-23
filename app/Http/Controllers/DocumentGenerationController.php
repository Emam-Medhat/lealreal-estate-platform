<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\DocumentVersion;
use App\Http\Requests\GenerateDocumentRequest;
use App\Services\DocumentTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PDF;
use Dompdf\Dompdf;

class DocumentGenerationController extends Controller
{
    protected $documentTemplateService;

    public function __construct(DocumentTemplateService $documentTemplateService)
    {
        $this->documentTemplateService = $documentTemplateService;
    }

    public function index()
    {
        $templates = DocumentTemplate::active()->get();
        $recentDocuments = Document::with(['template', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        // Calculate statistics
        $todayGenerations = Document::whereDate('created_at', today())->count();
        $thisMonthGenerations = Document::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
                    
        return view('documents.generation.index', compact('templates', 'recentDocuments', 'todayGenerations', 'thisMonthGenerations'));
    }
    
    public function create(DocumentTemplate $template)
    {
        $template->load(['fields', 'variables']);
        
        return view('documents.generation.create', compact('template'));
    }
    
    public function preview(Request $request, DocumentTemplate $template)
    {
        $data = $request->except(['_token', '_method']);
        
        $content = $this->documentTemplateService->preview($template, $data);
        
        return response()->json([
            'content' => $content,
            'success' => true
        ]);
    }
    
    public function generate(GenerateDocumentRequest $request, DocumentTemplate $template)
    {
        DB::beginTransaction();
        
        try {
            $data = $request->validated();
            
            // Process template content
            $content = $this->documentTemplateService->render($template, $data);
            
            // Create document
            $document = Document::create([
                'title' => $data['title'] ?? $template->name,
                'content' => $content,
                'template_id' => $template->id,
                'category_id' => $template->category_id,
                'type' => $template->type,
                'status' => 'draft',
                'created_by' => auth()->id(),
                'metadata' => [
                    'template_data' => $data,
                    'generated_at' => now()->toISOString(),
                ]
            ]);
            
            // Create initial version
            $version = DocumentVersion::create([
                'document_id' => $document->id,
                'version_number' => '1.0',
                'content' => $content,
                'changes_summary' => 'النسخة الأولية',
                'created_by' => auth()->id(),
            ]);
            
            // Generate PDF if requested
            if ($request->has('generate_pdf') && $request->generate_pdf) {
                $this->generatePDF($document, $version);
            }
            
            DB::commit();
            
            return redirect()->route('documents.show', $document)
                ->with('success', 'تم إنشاء الوثيقة بنجاح');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الوثيقة: ' . $e->getMessage());
        }
    }
    
    public function generateFromContract(Request $request)
    {
        $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'template_id' => 'required|exists:document_templates,id',
            'title' => 'required|string|max:255',
        ]);
        
        $contract = \App\Models\Contract::with(['parties', 'terms'])->findOrFail($request->contract_id);
        $template = DocumentTemplate::findOrFail($request->template_id);
        
        // Prepare contract data for template
        $data = [
            'title' => $request->title,
            'contract_number' => $contract->contract_number,
            'contract_date' => $contract->contract_date->format('Y-m-d'),
            'parties' => $contract->parties->map(function($party) {
                return [
                    'name' => $party->name,
                    'type' => $party->type,
                    'role' => $party->role,
                    'address' => $party->address,
                    'phone' => $party->phone,
                    'email' => $party->email,
                ];
            })->toArray(),
            'terms' => $contract->terms->map(function($term) {
                return [
                    'title' => $term->title,
                    'content' => $term->content,
                    'order' => $term->order,
                ];
            })->toArray(),
            'total_amount' => $contract->total_amount,
            'currency' => $contract->currency,
            'start_date' => $contract->start_date->format('Y-m-d'),
            'end_date' => $contract->end_date?->format('Y-m-d'),
        ];
        
        // Process template
        $content = $this->documentTemplateService->render($template, $data);
        
        // Create document
        $document = Document::create([
            'title' => $request->title,
            'content' => $content,
            'template_id' => $template->id,
            'category_id' => $template->category_id,
            'type' => 'contract_document',
            'status' => 'draft',
            'created_by' => auth()->id(),
            'contract_id' => $contract->id,
            'metadata' => [
                'contract_data' => $data,
                'generated_from_contract' => true,
                'generated_at' => now()->toISOString(),
            ]
        ]);
        
        // Create version
        DocumentVersion::create([
            'document_id' => $document->id,
            'version_number' => '1.0',
            'content' => $content,
            'changes_summary' => 'مستند من العقد #' . $contract->contract_number,
            'created_by' => auth()->id(),
        ]);
        
        return redirect()->route('documents.show', $document)
            ->with('success', 'تم إنشاء الوثيقة من العقد بنجاح');
    }
    
    public function bulkGenerate(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:document_templates,id',
            'data' => 'required|array',
            'data.*.title' => 'required|string|max:255',
        ]);
        
        $template = DocumentTemplate::findOrFail($request->template_id);
        $documents = [];
        
        DB::beginTransaction();
        
        try {
            foreach ($request->data as $item) {
                $content = $this->documentTemplateService->render($template, $item);
                
                $document = Document::create([
                    'title' => $item['title'],
                    'content' => $content,
                    'template_id' => $template->id,
                    'category_id' => $template->category_id,
                    'type' => $template->type,
                    'status' => 'draft',
                    'created_by' => auth()->id(),
                    'metadata' => [
                        'template_data' => $item,
                        'generated_at' => now()->toISOString(),
                        'bulk_generated' => true,
                    ]
                ]);
                
                DocumentVersion::create([
                    'document_id' => $document->id,
                    'version_number' => '1.0',
                    'content' => $content,
                    'changes_summary' => 'إنشاء مجمع',
                    'created_by' => auth()->id(),
                ]);
                
                $documents[] = $document;
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء ' . count($documents) . ' وثيقة بنجاح',
                'documents' => $documents
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الإنشاء المجمع: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function generatePDF(Document $document, DocumentVersion $version)
    {
        try {
            $pdf = new Dompdf();
            $pdf->loadHtml($version->content);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();
            
            $filename = 'documents/' . Str::slug($document->title) . '_' . $version->version_number . '.pdf';
            
            Storage::put($filename, $pdf->output());
            
            $document->update([
                'file_path' => $filename,
                'file_type' => 'pdf',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('PDF generation failed: ' . $e->getMessage());
        }
    }
}
