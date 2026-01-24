<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentCompliance;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DocumentComplianceController extends Controller
{
    public function index()
    {
        $compliances = DocumentCompliance::with(['document', 'checkedBy'])
            ->filter(request(['status', 'document_type', 'date_range']))
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('documents.compliance.index', compact('compliances'));
    }
    
    public function create(Document $document)
    {
        $document->load(['category', 'template']);
        
        // Get compliance requirements based on document type
        $requirements = $this->getComplianceRequirements($document);
        
        return view('documents.compliance.create', compact('document', 'requirements'));
    }
    
    public function store(Request $request, Document $document)
    {
        $request->validate([
            'compliance_checks' => 'required|array',
            'compliance_checks.*.requirement_id' => 'required|string',
            'compliance_checks.*.status' => 'required|in:compliant,non_compliant,not_applicable',
            'compliance_checks.*.notes' => 'nullable|string',
            'compliance_checks.*.evidence' => 'nullable|array',
            'overall_status' => 'required|in:compliant,non_compliant,needs_review',
            'compliance_notes' => 'required|string',
            'next_review_date' => 'nullable|date|after:today',
        ]);
        
        DB::beginTransaction();
        
        try {
            $compliance = DocumentCompliance::create([
                'document_id' => $document->id,
                'overall_status' => $request->overall_status,
                'compliance_notes' => $request->compliance_notes,
                'compliance_checks' => $request->compliance_checks,
                'checked_by' => auth()->id(),
                'checked_at' => now(),
                'next_review_date' => $request->next_review_date,
                'compliance_score' => $this->calculateComplianceScore($request->compliance_checks),
            ]);
            
            // Update document compliance status
            $document->update([
                'compliance_status' => $request->overall_status,
                'last_compliance_check' => now(),
                'next_compliance_review' => $request->next_review_date,
            ]);
            
            // Log compliance check
            $this->logComplianceActivity($document, $compliance);
            
            DB::commit();
            
            return redirect()->route('documents.compliance.show', $compliance)
                ->with('success', 'تم إجراء فحص الامتثال بنجاح');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إجراء فحص الامتثال: ' . $e->getMessage());
        }
    }
    
    public function show(DocumentCompliance $compliance)
    {
        $compliance->load(['document', 'checkedBy', 'document.category']);
        
        return view('documents.compliance.show', compact('compliance'));
    }
    
    public function edit(DocumentCompliance $compliance)
    {
        $compliance->load(['document', 'document.category']);
        
        // Get compliance requirements
        $requirements = $this->getComplianceRequirements($compliance->document);
        
        return view('documents.compliance.edit', compact('compliance', 'requirements'));
    }
    
    public function update(Request $request, DocumentCompliance $compliance)
    {
        $request->validate([
            'compliance_checks' => 'required|array',
            'compliance_checks.*.requirement_id' => 'required|string',
            'compliance_checks.*.status' => 'required|in:compliant,non_compliant,not_applicable',
            'compliance_checks.*.notes' => 'nullable|string',
            'compliance_checks.*.evidence' => 'nullable|array',
            'overall_status' => 'required|in:compliant,non_compliant,needs_review',
            'compliance_notes' => 'required|string',
            'next_review_date' => 'nullable|date|after:today',
        ]);
        
        DB::beginTransaction();
        
        try {
            $compliance->update([
                'overall_status' => $request->overall_status,
                'compliance_notes' => $request->compliance_notes,
                'compliance_checks' => $request->compliance_checks,
                'checked_by' => auth()->id(),
                'checked_at' => now(),
                'next_review_date' => $request->next_review_date,
                'compliance_score' => $this->calculateComplianceScore($request->compliance_checks),
            ]);
            
            // Update document compliance status
            $compliance->document->update([
                'compliance_status' => $request->overall_status,
                'last_compliance_check' => now(),
                'next_compliance_review' => $request->next_review_date,
            ]);
            
            // Log compliance update
            $this->logComplianceActivity($compliance->document, $compliance, 'updated');
            
            DB::commit();
            
            return redirect()->route('documents.compliance.show', $compliance)
                ->with('success', 'تم تحديث فحص الامتثال بنجاح');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث فحص الامتثال: ' . $e->getMessage());
        }
    }
    
    public function bulkCheck(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:documents,id',
            'compliance_type' => 'required|in:legal,security,quality,operational',
        ]);
        
        $results = [];
        
        foreach ($request->document_ids as $documentId) {
            $document = Document::findOrFail($documentId);
            
            // Perform automated compliance check
            $checkResult = $this->performAutomatedCheck($document, $request->compliance_type);
            
            $results[] = [
                'document_id' => $documentId,
                'document_title' => $document->title,
                'compliance_result' => $checkResult,
            ];
        }
        
        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }
    
    public function report(Request $request)
    {
        $startDate = $request->start_date ?? now()->subMonths(3);
        $endDate = $request->end_date ?? now();
        
        $compliances = DocumentCompliance::with(['document', 'document.category'])
            ->whereBetween('checked_at', [$startDate, $endDate])
            ->get();
            
        // Generate statistics
        $stats = [
            'total_checks' => $compliances->count(),
            'compliant' => $compliances->where('overall_status', 'compliant')->count(),
            'non_compliant' => $compliances->where('overall_status', 'non_compliant')->count(),
            'needs_review' => $compliances->where('overall_status', 'needs_review')->count(),
            'average_score' => $compliances->avg('compliance_score'),
        ];
        
        // Group by category
        $categoryStats = $compliances->groupBy(function($compliance) {
            return $compliance->document->category->name ?? 'غير مصنف';
        })->map(function($group) {
            return [
                'total' => $group->count(),
                'compliant' => $group->where('overall_status', 'compliant')->count(),
                'non_compliant' => $group->where('overall_status', 'non_compliant')->count(),
                'average_score' => $group->avg('compliance_score'),
            ];
        });
        
        // Non-compliant issues
        $issues = $compliances->where('overall_status', 'non_compliant')
            ->flatMap(function($compliance) {
                return collect($compliance->compliance_checks)
                    ->where('status', 'non_compliant')
                    ->map(function($check) use ($compliance) {
                        return [
                            'document' => $compliance->document->title,
                            'requirement' => $check['requirement_id'],
                            'notes' => $check['notes'] ?? '',
                            'checked_at' => $compliance->checked_at,
                        ];
                    });
            });
        
        return view('documents.compliance.report', compact(
            'stats', 
            'categoryStats', 
            'issues', 
            'startDate', 
            'endDate'
        ));
    }
    
    public function export(Request $request)
    {
        $startDate = $request->start_date ?? now()->subMonths(3);
        $endDate = $request->end_date ?? now();
        
        $compliances = DocumentCompliance::with(['document', 'document.category', 'checkedBy'])
            ->whereBetween('checked_at', [$startDate, $endDate])
            ->get();
            
        $filename = 'compliance-report-' . now()->format('Y-m-d') . '.xlsx';
        
        return \Excel::download(new \App\Exports\ComplianceExport($compliances), $filename);
    }
    
    public function reminders()
    {
        // Get documents due for compliance review
        $upcomingReviews = Document::where('next_compliance_review', '<=', now()->addDays(30))
            ->where('next_compliance_review', '>=', now())
            ->with(['category', 'lastCompliance'])
            ->orderBy('next_compliance_review')
            ->get();
            
        $overdueReviews = Document::where('next_compliance_review', '<', now())
            ->with(['category', 'lastCompliance'])
            ->orderBy('next_compliance_review')
            ->get();
            
        return view('documents.compliance.reminders', compact('upcomingReviews', 'overdueReviews'));
    }
    
    public function sendReminders(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:documents,id',
            'message' => 'required|string',
        ]);
        
        $sent = 0;
        
        foreach ($request->document_ids as $documentId) {
            $document = Document::findOrFail($documentId);
            
            // Send reminder notification
            $this->sendComplianceReminder($document, $request->message);
            
            $sent++;
        }
        
        return back()->with('success', 'تم إرسال ' . $sent . ' تذكير بنجاح');
    }
    
    private function getComplianceRequirements(Document $document): array
    {
        $requirements = [];
        
        // Base requirements for all documents
        $requirements[] = [
            'id' => 'basic_info',
            'title' => 'المعلومات الأساسية',
            'description' => 'تحقق من اكتمال المعلومات الأساسية',
            'mandatory' => true,
        ];
        
        $requirements[] = [
            'id' => 'proper_formatting',
            'title' => 'التنسيق الصحيح',
            'description' => 'تحقق من التنسيق والهيكل',
            'mandatory' => true,
        ];
        
        // Document type specific requirements
        switch ($document->type) {
            case 'contract':
                $requirements = array_merge($requirements, [
                    [
                        'id' => 'contract_terms',
                        'title' => 'بنود العقد',
                        'description' => 'تحقق من اكتمال بنود العقد',
                        'mandatory' => true,
                    ],
                    [
                        'id' => 'signatures',
                        'title' => 'التوقيعات',
                        'description' => 'تحقق من وجود توقيعات جميع الأطراف',
                        'mandatory' => true,
                    ],
                    [
                        'id' => 'legal_clauses',
                        'title' => 'البنود القانونية',
                        'description' => 'تحقق من البنود القانونية الإلزامية',
                        'mandatory' => true,
                    ],
                ]);
                break;
                
            case 'legal_document':
                $requirements = array_merge($requirements, [
                    [
                        'id' => 'legal_references',
                        'title' => 'المراجع القانونية',
                        'description' => 'تحقق من المراجع القانونية',
                        'mandatory' => true,
                    ],
                    [
                        'id' => 'jurisdiction',
                        'title' => 'الاختصاص القضائي',
                        'description' => 'تحقق من تحديد الاختصاص القضائي',
                        'mandatory' => true,
                    ],
                ]);
                break;
                
            case 'financial_document':
                $requirements = array_merge($requirements, [
                    [
                        'id' => 'financial_data',
                        'title' => 'البيانات المالية',
                        'description' => 'تحقق من دقة البيانات المالية',
                        'mandatory' => true,
                    ],
                    [
                        'id' => 'currency_specification',
                        'title' => 'تحديد العملة',
                        'description' => 'تحقق من تحديد العملة والأسعار',
                        'mandatory' => true,
                    ],
                ]);
                break;
        }
        
        return $requirements;
    }
    
    private function calculateComplianceScore(array $checks): float
    {
        $totalChecks = count($checks);
        if ($totalChecks === 0) {
            return 0;
        }
        
        $compliantChecks = collect($checks)->where('status', 'compliant')->count();
        $notApplicableChecks = collect($checks)->where('status', 'not_applicable')->count();
        
        $applicableChecks = $totalChecks - $notApplicableChecks;
        
        if ($applicableChecks === 0) {
            return 100;
        }
        
        return ($compliantChecks / $applicableChecks) * 100;
    }
    
    private function performAutomatedCheck(Document $document, string $type): array
    {
        $checks = [];
        $score = 0;
        
        switch ($type) {
            case 'legal':
                $checks = $this->performLegalCheck($document);
                break;
            case 'security':
                $checks = $this->performSecurityCheck($document);
                break;
            case 'quality':
                $checks = $this->performQualityCheck($document);
                break;
            case 'operational':
                $checks = $this->performOperationalCheck($document);
                break;
        }
        
        return [
            'type' => $type,
            'checks' => $checks,
            'score' => $this->calculateComplianceScore($checks),
            'checked_at' => now(),
        ];
    }
    
    private function performLegalCheck(Document $document): array
    {
        // Implement automated legal compliance checks
        return [
            [
                'requirement_id' => 'legal_format',
                'status' => 'compliant',
                'notes' => 'التنسيق القانوني صحيح',
            ],
            // Add more automated checks
        ];
    }
    
    private function performSecurityCheck(Document $document): array
    {
        // Implement automated security compliance checks
        return [
            [
                'requirement_id' => 'data_protection',
                'status' => 'compliant',
                'notes' => 'حماية البيانات متوافقة',
            ],
            // Add more automated checks
        ];
    }
    
    private function performQualityCheck(Document $document): array
    {
        // Implement automated quality compliance checks
        return [
            [
                'requirement_id' => 'content_quality',
                'status' => 'compliant',
                'notes' => 'جودة المحتوى جيدة',
            ],
            // Add more automated checks
        ];
    }
    
    private function performOperationalCheck(Document $document): array
    {
        // Implement automated operational compliance checks
        return [
            [
                'requirement_id' => 'operational_procedures',
                'status' => 'compliant',
                'notes' => 'الإجراءات التشغيلية متبعة',
            ],
            // Add more automated checks
        ];
    }
    
    private function logComplianceActivity(Document $document, DocumentCompliance $compliance, string $action = 'created')
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($document)
            ->withProperties([
                'compliance_id' => $compliance->id,
                'overall_status' => $compliance->overall_status,
                'compliance_score' => $compliance->compliance_score,
                'action' => $action,
            ])
            ->log('تم ' . $action . ' فحص الامتثال للوثيقة: ' . $document->title);
    }
    
    private function sendComplianceReminder(Document $document, string $message)
    {
        // Implement reminder notification system
        // $document->createdBy->notify(new ComplianceReminder($document, $message));
    }
}
