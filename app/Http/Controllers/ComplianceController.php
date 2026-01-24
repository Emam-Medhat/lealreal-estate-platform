<?php

namespace App\Http\Controllers;

use App\Models\ComplianceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ComplianceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $complianceStats = [
            'total_records' => ComplianceRecord::where('user_id', $user->id)->count(),
            'compliant_records' => ComplianceRecord::where('user_id', $user->id)
                ->where('compliance_status', 'compliant')
                ->count(),
            'pending_reviews' => ComplianceRecord::where('user_id', $user->id)
                ->where('compliance_status', 'pending_review')
                ->count(),
            'non_compliant' => ComplianceRecord::where('user_id', $user->id)
                ->where('compliance_status', 'non_compliant')
                ->count(),
        ];

        $recentRecords = ComplianceRecord::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('security.compliance.index', compact('complianceStats', 'recentRecords'));
    }

    public function create()
    {
        return view('security.compliance.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'compliance_type' => 'required|in:data_protection,privacy,security_standards,audit_requirements,regulatory,industry_specific',
            'regulation_reference' => 'required|string|max:255',
            'compliance_category' => 'required|string|max:100',
            'description' => 'required|string|max:2000',
            'requirements' => 'required|array',
            'requirements.*.requirement' => 'required|string|max:500',
            'requirements.*.status' => 'required|in:compliant,non_compliant,partial,not_applicable',
            'requirements.*.evidence' => 'nullable|string|max:1000',
            'requirements.*.notes' => 'nullable|string|max:500',
            'assessment_date' => 'required|date',
            'next_assessment_date' => 'required|date|after:assessment_date',
            'assessor_name' => 'required|string|max:255',
            'assessor_role' => 'required|string|max:100',
            'risk_level' => 'required|in:low,medium,high,critical',
            'compliance_score' => 'required|numeric|min:0|max:100',
            'findings' => 'nullable|array',
            'recommendations' => 'nullable|array',
            'corrective_actions' => 'nullable|array',
            'evidence_documents' => 'nullable|array',
            'evidence_documents.*' => 'file|max:10240',
            'approval_status' => 'required|in:draft,submitted,approved,rejected',
            'approved_by' => 'nullable|exists:users,id',
            'approval_date' => 'nullable|date',
            'approval_notes' => 'nullable|string|max:1000',
        ]);

        // Process evidence documents
        $evidencePaths = $this->processEvidenceDocuments($request->file('evidence_documents'));

        $complianceRecord = ComplianceRecord::create([
            'user_id' => Auth::id(),
            'compliance_type' => $validated['compliance_type'],
            'regulation_reference' => $validated['regulation_reference'],
            'compliance_category' => $validated['compliance_category'],
            'description' => $validated['description'],
            'requirements' => json_encode($validated['requirements']),
            'assessment_date' => $validated['assessment_date'],
            'next_assessment_date' => $validated['next_assessment_date'],
            'assessor_name' => $validated['assessor_name'],
            'assessor_role' => $validated['assessor_role'],
            'risk_level' => $validated['risk_level'],
            'compliance_score' => $validated['compliance_score'],
            'findings' => json_encode($validated['findings'] ?? []),
            'recommendations' => json_encode($validated['recommendations'] ?? []),
            'corrective_actions' => json_encode($validated['corrective_actions'] ?? []),
            'evidence_documents' => json_encode($evidencePaths),
            'approval_status' => $validated['approval_status'],
            'approved_by' => $validated['approved_by'],
            'approval_date' => $validated['approval_date'],
            'approval_notes' => $validated['approval_notes'],
            'compliance_status' => $this->determineComplianceStatus($validated['compliance_score']),
        ]);

        // Log compliance record creation
        Log::info('Compliance record created', [
            'user_id' => Auth::id(),
            'compliance_record_id' => $complianceRecord->id,
            'compliance_type' => $validated['compliance_type'],
            'compliance_score' => $validated['compliance_score'],
        ]);

        return redirect()->route('security.compliance.show', $complianceRecord)
            ->with('success', 'تم إنشاء سجل الامتثال بنجاح');
    }

    public function show(ComplianceRecord $complianceRecord)
    {
        $this->authorize('view', $complianceRecord);
        
        return view('security.compliance.show', compact('complianceRecord'));
    }

    public function edit(ComplianceRecord $complianceRecord)
    {
        $this->authorize('update', $complianceRecord);
        
        return view('security.compliance.edit', compact('complianceRecord'));
    }

    public function update(Request $request, ComplianceRecord $complianceRecord)
    {
        $this->authorize('update', $complianceRecord);

        $validated = $request->validate([
            'compliance_type' => 'required|in:data_protection,privacy,security_standards,audit_requirements,regulatory,industry_specific',
            'regulation_reference' => 'required|string|max:255',
            'compliance_category' => 'required|string|max:100',
            'description' => 'required|string|max:2000',
            'requirements' => 'required|array',
            'assessment_date' => 'required|date',
            'next_assessment_date' => 'required|date|after:assessment_date',
            'assessor_name' => 'required|string|max:255',
            'assessor_role' => 'required|string|max:100',
            'risk_level' => 'required|in:low,medium,high,critical',
            'compliance_score' => 'required|numeric|min:0|max:100',
            'findings' => 'nullable|array',
            'recommendations' => 'nullable|array',
            'corrective_actions' => 'nullable|array',
            'approval_notes' => 'nullable|string|max:1000',
        ]);

        $complianceRecord->update([
            'compliance_type' => $validated['compliance_type'],
            'regulation_reference' => $validated['regulation_reference'],
            'compliance_category' => $validated['compliance_category'],
            'description' => $validated['description'],
            'requirements' => json_encode($validated['requirements']),
            'assessment_date' => $validated['assessment_date'],
            'next_assessment_date' => $validated['next_assessment_date'],
            'assessor_name' => $validated['assessor_name'],
            'assessor_role' => $validated['assessor_role'],
            'risk_level' => $validated['risk_level'],
            'compliance_score' => $validated['compliance_score'],
            'findings' => json_encode($validated['findings'] ?? []),
            'recommendations' => json_encode($validated['recommendations'] ?? []),
            'corrective_actions' => json_encode($validated['corrective_actions'] ?? []),
            'approval_notes' => $validated['approval_notes'],
            'compliance_status' => $this->determineComplianceStatus($validated['compliance_score']),
        ]);

        return redirect()->route('security.compliance.show', $complianceRecord)
            ->with('success', 'تم تحديث سجل الامتثال بنجاح');
    }

    public function approve(Request $request, ComplianceRecord $complianceRecord)
    {
        $this->authorize('approve', $complianceRecord);

        $validated = $request->validate([
            'approval_notes' => 'required|string|max:1000',
            'next_review_date' => 'nullable|date|after:today',
        ]);

        $complianceRecord->update([
            'approval_status' => 'approved',
            'approved_by' => Auth::id(),
            'approval_date' => now(),
            'approval_notes' => $validated['approval_notes'],
            'next_review_date' => $validated['next_review_date'],
        ]);

        return redirect()->route('security.compliance.show', $complianceRecord)
            ->with('success', 'تمت الموافقة على سجل الامتثال بنجاح');
    }

    public function reject(Request $request, ComplianceRecord $complianceRecord)
    {
        $this->authorize('reject', $complianceRecord);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
            'required_changes' => 'nullable|array',
        ]);

        $complianceRecord->update([
            'approval_status' => 'rejected',
            'approved_by' => Auth::id(),
            'approval_date' => now(),
            'approval_notes' => $validated['rejection_reason'],
            'required_changes' => json_encode($validated['required_changes'] ?? []),
        ]);

        return redirect()->route('security.compliance.show', $complianceRecord)
            ->with('success', 'تم رفض سجل الامتثال');
    }

    public function assessment(Request $request, ComplianceRecord $complianceRecord)
    {
        $this->authorize('assess', $complianceRecord);

        $validated = $request->validate([
            'assessment_method' => 'required|in:automated,manual,hybrid',
            'assessment_criteria' => 'required|array',
            'assessment_results' => 'required|array',
            'assessor_comments' => 'nullable|string|max:2000',
            'next_assessment_date' => 'required|date|after:today',
        ]);

        $assessmentData = [
            'assessment_method' => $validated['assessment_method'],
            'assessment_criteria' => $validated['assessment_criteria'],
            'assessment_results' => $validated['assessment_results'],
            'assessor_comments' => $validated['assessor_comments'],
            'assessed_at' => now(),
            'assessed_by' => Auth::id(),
        ];

        $complianceRecord->update([
            'assessment_data' => json_encode($assessmentData),
            'next_assessment_date' => $validated['next_assessment_date'],
            'last_assessment_date' => now(),
        ]);

        return redirect()->route('security.compliance.show', $complianceRecord)
            ->with('success', 'تم إجراء التقييم بنجاح');
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        $dashboardData = [
            'overview' => $this->getComplianceOverview($user->id),
            'trends' => $this->getComplianceTrends($user->id),
            'risk_analysis' => $this->getRiskAnalysis($user->id),
            'upcoming_assessments' => $this->getUpcomingAssessments($user->id),
            'compliance_matrix' => $this->getComplianceMatrix($user->id),
        ];

        return view('security.compliance.dashboard', compact('dashboardData'));
    }

    public function reports()
    {
        $user = Auth::user();
        
        $reportData = [
            'compliance_summary' => $this->generateComplianceSummary($user->id),
            'regulatory_compliance' => $this->getRegulatoryCompliance($user->id),
            'risk_assessment' => $this->getRiskAssessment($user->id),
            'improvement_recommendations' => $this->getImprovementRecommendations($user->id),
        ];

        return view('security.compliance.reports', compact('reportData'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,pdf',
            'report_type' => 'required|in:summary,detailed,risk_assessment,regulatory',
            'date_range' => 'required|in:last_month,last_quarter,last_year,custom',
            'start_date' => 'nullable|date|required_if:date_range,custom',
            'end_date' => 'nullable|date|required_if:date_range,custom|after_or_equal:start_date',
            'compliance_type' => 'nullable|in:data_protection,privacy,security_standards,audit_requirements,regulatory,industry_specific',
        ]);

        $records = $this->getFilteredComplianceRecords($validated);

        switch ($validated['format']) {
            case 'csv':
                return $this->exportCSV($records, $validated['report_type']);
            case 'xlsx':
                return $this->exportExcel($records, $validated['report_type']);
            case 'pdf':
                return $this->exportPDF($records, $validated['report_type']);
        }
    }

    public function automatedCheck(Request $request)
    {
        $validated = $request->validate([
            'compliance_type' => 'required|in:data_protection,privacy,security_standards',
            'property_id' => 'nullable|exists:properties,id',
            'check_level' => 'required|in:basic,comprehensive,advanced',
        ]);

        $checkResults = $this->performAutomatedComplianceCheck($validated);

        return response()->json($checkResults);
    }

    private function processEvidenceDocuments($documents)
    {
        if (!$documents) {
            return [];
        }

        $paths = [];
        foreach ($documents as $document) {
            $filename = Str::uuid() . '.' . $document->getClientOriginalExtension();
            $path = $document->storeAs('compliance_evidence', $filename, 'public');
            $paths[] = [
                'filename' => $document->getClientOriginalName(),
                'path' => $path,
                'size' => $document->getSize(),
                'mime_type' => $document->getMimeType(),
                'uploaded_at' => now(),
            ];
        }

        return $paths;
    }

    private function determineComplianceStatus($score)
    {
        if ($score >= 90) {
            return 'compliant';
        } elseif ($score >= 70) {
            return 'partial_compliance';
        } elseif ($score >= 50) {
            return 'needs_improvement';
        } else {
            return 'non_compliant';
        }
    }

    private function getComplianceOverview($userId)
    {
        return [
            'total_score' => ComplianceRecord::where('user_id', $userId)
                ->avg('compliance_score'),
            'compliance_rate' => ComplianceRecord::where('user_id', $userId)
                ->where('compliance_status', 'compliant')
                ->count() / ComplianceRecord::where('user_id', $userId)->count() * 100,
            'high_risk_items' => ComplianceRecord::where('user_id', $userId)
                ->where('risk_level', 'high')
                ->count(),
            'overdue_assessments' => ComplianceRecord::where('user_id', $userId)
                ->where('next_assessment_date', '<', now())
                ->count(),
        ];
    }

    private function getComplianceTrends($userId)
    {
        return ComplianceRecord::where('user_id', $userId)
            ->selectRaw('DATE(assessment_date) as date, AVG(compliance_score) as score')
            ->where('assessment_date', '>=', now()->subDays(90))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getRiskAnalysis($userId)
    {
        return [
            'risk_distribution' => ComplianceRecord::where('user_id', $userId)
                ->selectRaw('risk_level, COUNT(*) as count')
                ->groupBy('risk_level')
                ->get(),
            'risk_trends' => ComplianceRecord::where('user_id', $userId)
                ->selectRaw('DATE(assessment_date) as date, risk_level, COUNT(*) as count')
                ->where('assessment_date', '>=', now()->subDays(90))
                ->groupBy('date', 'risk_level')
                ->get(),
        ];
    }

    private function getUpcomingAssessments($userId)
    {
        return ComplianceRecord::where('user_id', $userId)
            ->where('next_assessment_date', '>', now())
            ->where('next_assessment_date', '<=', now()->addDays(30))
            ->orderBy('next_assessment_date')
            ->get();
    }

    private function getComplianceMatrix($userId)
    {
        return ComplianceRecord::where('user_id', $userId)
            ->selectRaw('compliance_type, compliance_category, AVG(compliance_score) as avg_score')
            ->groupBy('compliance_type', 'compliance_category')
            ->get();
    }

    private function generateComplianceSummary($userId)
    {
        return [
            'overall_compliance' => $this->calculateOverallCompliance($userId),
            'compliance_by_type' => $this->getComplianceByType($userId),
            'compliance_by_category' => $this->getComplianceByCategory($userId),
            'trend_analysis' => $this->getComplianceTrends($userId),
        ];
    }

    private function getRegulatoryCompliance($userId)
    {
        return ComplianceRecord::where('user_id', $userId)
            ->where('compliance_type', 'regulatory')
            ->get();
    }

    private function getRiskAssessment($userId)
    {
        return [
            'risk_summary' => $this->getRiskAnalysis($userId),
            'risk_mitigation' => $this->getRiskMitigation($userId),
            'risk_monitoring' => $this->getRiskMonitoring($userId),
        ];
    }

    private function getImprovementRecommendations($userId)
    {
        $recommendations = [];
        
        $nonCompliantRecords = ComplianceRecord::where('user_id', $userId)
            ->where('compliance_status', '!=', 'compliant')
            ->get();

        foreach ($nonCompliantRecords as $record) {
            $recommendations[] = [
                'area' => $record->compliance_category,
                'recommendation' => 'تحسين الامتثال في ' . $record->compliance_category,
                'priority' => $record->risk_level,
                'target_score' => 90,
            ];
        }

        return $recommendations;
    }

    private function getFilteredComplianceRecords($validated)
    {
        $query = ComplianceRecord::where('user_id', Auth::id());

        // Apply date range filter
        switch ($validated['date_range']) {
            case 'last_month':
                $query->where('assessment_date', '>=', now()->subMonth());
                break;
            case 'last_quarter':
                $query->where('assessment_date', '>=', now()->subQuarter());
                break;
            case 'last_year':
                $query->where('assessment_date', '>=', now()->subYear());
                break;
            case 'custom':
                $query->whereBetween('assessment_date', [$validated['start_date'], $validated['end_date']]);
                break;
        }

        if (isset($validated['compliance_type'])) {
            $query->where('compliance_type', $validated['compliance_type']);
        }

        return $query->get();
    }

    private function exportCSV($records, $reportType)
    {
        $filename = "compliance_{$reportType}_" . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'ID', 'Type', 'Category', 'Score', 'Status', 'Risk Level',
                'Assessment Date', 'Next Assessment', 'Assessor'
            ]);

            // Data
            foreach ($records as $record) {
                fputcsv($file, [
                    $record->id,
                    $record->compliance_type,
                    $record->compliance_category,
                    $record->compliance_score,
                    $record->compliance_status,
                    $record->risk_level,
                    $record->assessment_date,
                    $record->next_assessment_date,
                    $record->assessor_name,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportExcel($records, $reportType)
    {
        // Implementation for Excel export
        return response()->download("compliance_{$reportType}.xlsx");
    }

    private function exportPDF($records, $reportType)
    {
        // Implementation for PDF export
        return response()->download("compliance_{$reportType}.pdf");
    }

    private function performAutomatedComplianceCheck($validated)
    {
        $results = [
            'check_id' => Str::uuid(),
            'check_type' => $validated['compliance_type'],
            'check_level' => $validated['check_level'],
            'performed_at' => now(),
            'findings' => [],
            'compliance_score' => 0,
            'recommendations' => [],
        ];

        // Simulate automated compliance check
        switch ($validated['compliance_type']) {
            case 'data_protection':
                $results['findings'] = $this->checkDataProtectionCompliance($validated);
                break;
            case 'privacy':
                $results['findings'] = $this->checkPrivacyCompliance($validated);
                break;
            case 'security_standards':
                $results['findings'] = $this->checkSecurityStandardsCompliance($validated);
                break;
        }

        $results['compliance_score'] = $this->calculateAutomatedComplianceScore($results['findings']);
        $results['recommendations'] = $this->generateAutomatedRecommendations($results['findings']);

        return $results;
    }

    private function checkDataProtectionCompliance($validated)
    {
        // Simulate data protection compliance check
        return [
            'encryption_status' => 'compliant',
            'access_control' => 'partial_compliance',
            'data_retention' => 'compliant',
            'privacy_policy' => 'compliant',
        ];
    }

    private function checkPrivacyCompliance($validated)
    {
        // Simulate privacy compliance check
        return [
            'consent_management' => 'compliant',
            'data_minimization' => 'partial_compliance',
            'user_rights' => 'compliant',
            'privacy_notices' => 'compliant',
        ];
    }

    private function checkSecurityStandardsCompliance($validated)
    {
        // Simulate security standards compliance check
        return [
            'authentication' => 'compliant',
            'authorization' => 'compliant',
            'audit_trail' => 'compliant',
            'incident_response' => 'partial_compliance',
        ];
    }

    private function calculateAutomatedComplianceScore($findings)
    {
        $scores = [
            'compliant' => 100,
            'partial_compliance' => 70,
            'non_compliant' => 30,
            'not_applicable' => 100,
        ];

        $totalScore = 0;
        $count = 0;

        foreach ($findings as $finding) {
            $totalScore += $scores[$finding] ?? 0;
            $count++;
        }

        return $count > 0 ? $totalScore / $count : 0;
    }

    private function generateAutomatedRecommendations($findings)
    {
        $recommendations = [];

        foreach ($findings as $area => $status) {
            if ($status === 'partial_compliance') {
                $recommendations[] = "تحسين الامتثال في مجال {$area}";
            } elseif ($status === 'non_compliant') {
                $recommendations[] = "معالجة عدم الامتثال في مجال {$area}";
            }
        }

        return $recommendations;
    }

    private function calculateOverallCompliance($userId)
    {
        return ComplianceRecord::where('user_id', $userId)
            ->avg('compliance_score');
    }

    private function getComplianceByType($userId)
    {
        return ComplianceRecord::where('user_id', $userId)
            ->selectRaw('compliance_type, AVG(compliance_score) as avg_score')
            ->groupBy('compliance_type')
            ->get();
    }

    private function getComplianceByCategory($userId)
    {
        return ComplianceRecord::where('user_id', $userId)
            ->selectRaw('compliance_category, AVG(compliance_score) as avg_score')
            ->groupBy('compliance_category')
            ->get();
    }

    private function getRiskMitigation($userId)
    {
        return ComplianceRecord::where('user_id', $userId)
            ->where('risk_level', 'high')
            ->get();
    }

    private function getRiskMonitoring($userId)
    {
        return [
            'monitored_risks' => ComplianceRecord::where('user_id', $userId)
                ->where('risk_level', 'high')
                ->count(),
            'mitigation_progress' => 75, // Example value
            'next_review' => now()->addMonth(),
        ];
    }
}
