<?php

namespace App\Http\Controllers;

use App\Models\FraudAlert;
use App\Models\SecurityIncident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\FraudAlertNotification;

class FraudPreventionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $fraudStats = [
            'total_alerts' => FraudAlert::where('user_id', $user->id)->count(),
            'active_alerts' => FraudAlert::where('user_id', $user->id)
                ->where('status', 'active')
                ->count(),
            'high_risk_alerts' => FraudAlert::where('user_id', $user->id)
                ->where('risk_level', 'high')
                ->count(),
            'resolved_today' => FraudAlert::where('user_id', $user->id)
                ->where('status', 'resolved')
                ->whereDate('resolved_at', today())
                ->count(),
        ];

        $recentAlerts = FraudAlert::where('user_id', $user->id)
            ->with(['property', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('security.fraud-prevention.index', compact('fraudStats', 'recentAlerts'));
    }

    public function create()
    {
        return view('security.fraud-prevention.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'alert_type' => 'required|in:suspicious_activity,identity_theft,financial_fraud,listing_fraud,document_fraud',
            'description' => 'required|string|max:2000',
            'evidence' => 'nullable|array',
            'evidence.*' => 'file|max:5120',
            'risk_level' => 'required|in:low,medium,high,critical',
            'affected_parties' => 'nullable|array',
            'estimated_loss' => 'nullable|numeric|min:0',
            'currency' => 'required|string|in:SAR,USD,EUR',
            'urgency_level' => 'required|in:low,medium,high,critical',
            'contact_information' => 'nullable|string|max:500',
            'additional_notes' => 'nullable|string|max:1000',
        ]);

        // Calculate fraud score
        $fraudScore = $this->calculateFraudScore($validated);

        $fraudAlert = FraudAlert::create([
            'user_id' => Auth::id(),
            'property_id' => $validated['property_id'],
            'alert_type' => $validated['alert_type'],
            'description' => $validated['description'],
            'evidence' => $this->processEvidence($request->file('evidence')),
            'risk_level' => $validated['risk_level'],
            'fraud_score' => $fraudScore,
            'affected_parties' => json_encode($validated['affected_parties'] ?? []),
            'estimated_loss' => $validated['estimated_loss'],
            'currency' => $validated['currency'],
            'urgency_level' => $validated['urgency_level'],
            'contact_information' => $validated['contact_information'],
            'additional_notes' => $validated['additional_notes'],
            'status' => 'active',
            'auto_detected' => false,
        ]);

        // Send notification if high risk
        if ($validated['risk_level'] === 'high' || $validated['risk_level'] === 'critical') {
            $this->sendFraudNotification($fraudAlert);
        }

        // Log the alert
        Log::warning('Fraud alert created', [
            'user_id' => Auth::id(),
            'property_id' => $validated['property_id'],
            'alert_id' => $fraudAlert->id,
            'risk_level' => $validated['risk_level'],
        ]);

        return redirect()->route('security.fraud-prevention.show', $fraudAlert)
            ->with('success', 'تم إنشاء تنبيه الاحتيال بنجاح');
    }

    public function show(FraudAlert $fraudAlert)
    {
        $this->authorize('view', $fraudAlert);
        
        $fraudAlert->load(['property', 'user', 'investigationLogs']);
        
        return view('security.fraud-prevention.show', compact('fraudAlert'));
    }

    public function edit(FraudAlert $fraudAlert)
    {
        $this->authorize('update', $fraudAlert);
        
        return view('security.fraud-prevention.edit', compact('fraudAlert'));
    }

    public function update(Request $request, FraudAlert $fraudAlert)
    {
        $this->authorize('update', $fraudAlert);

        $validated = $request->validate([
            'description' => 'required|string|max:2000',
            'risk_level' => 'required|in:low,medium,high,critical',
            'estimated_loss' => 'nullable|numeric|min:0',
            'urgency_level' => 'required|in:low,medium,high,critical',
            'contact_information' => 'nullable|string|max:500',
            'additional_notes' => 'nullable|string|max:1000',
        ]);

        $fraudAlert->update($validated);

        // Recalculate fraud score
        $fraudScore = $this->calculateFraudScore($validated + [
            'alert_type' => $fraudAlert->alert_type
        ]);
        $fraudAlert->update(['fraud_score' => $fraudScore]);

        return redirect()->route('security.fraud-prevention.show', $fraudAlert)
            ->with('success', 'تم تحديث تنبيه الاحتيال بنجاح');
    }

    public function resolve(Request $request, FraudAlert $fraudAlert)
    {
        $this->authorize('resolve', $fraudAlert);

        $validated = $request->validate([
            'resolution_notes' => 'required|string|max:2000',
            'resolution_type' => 'required|in:true_positive,false_positive,investigating,closed',
            'actual_loss' => 'nullable|numeric|min:0',
            'recovery_amount' => 'nullable|numeric|min:0',
            'preventive_actions' => 'nullable|string|max:1000',
        ]);

        $fraudAlert->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_notes' => $validated['resolution_notes'],
            'resolution_type' => $validated['resolution_type'],
            'actual_loss' => $validated['actual_loss'],
            'recovery_amount' => $validated['recovery_amount'],
            'preventive_actions' => $validated['preventive_actions'],
            'resolved_by' => Auth::id(),
        ]);

        return redirect()->route('security.fraud-prevention.show', $fraudAlert)
            ->with('success', 'تم حل تنبيه الاحتيال بنجاح');
    }

    public function investigate(FraudAlert $fraudAlert)
    {
        $this->authorize('investigate', $fraudAlert);

        $investigationResults = $this->performFraudInvestigation($fraudAlert);

        return view('security.fraud-prevention.investigation', compact('fraudAlert', 'investigationResults'));
    }

    public function scan(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'scan_type' => 'required|in:quick,comprehensive,custom',
            'time_period' => 'required|in:last_24h,last_7d,last_30d,last_90d,custom',
            'custom_start_date' => 'nullable|date|required_if:time_period,custom',
            'custom_end_date' => 'nullable|date|required_if:time_period,custom|after_or_equal:custom_start_date',
        ]);

        $scanResults = $this->performFraudScan($validated);

        return response()->json($scanResults);
    }

    public function analytics()
    {
        $user = Auth::user();
        
        $analytics = [
            'fraud_trends' => $this->getFraudTrends($user->id),
            'risk_distribution' => $this->getRiskDistribution($user->id),
            'alert_types' => $this->getAlertTypeDistribution($user->id),
            'resolution_times' => $this->getResolutionTimes($user->id),
            'financial_impact' => $this->getFinancialImpact($user->id),
        ];

        return view('security.fraud-prevention.analytics', compact('analytics'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,pdf',
            'date_range' => 'required|in:last_week,last_month,last_quarter,last_year,custom',
            'start_date' => 'nullable|date|required_if:date_range,custom',
            'end_date' => 'nullable|date|required_if:date_range,custom|after_or_equal:start_date',
            'status' => 'nullable|in:active,resolved,closed',
            'risk_level' => 'nullable|in:low,medium,high,critical',
        ]);

        $alerts = $this->getFilteredAlerts($validated);

        switch ($validated['format']) {
            case 'csv':
                return $this->exportCSV($alerts);
            case 'xlsx':
                return $this->exportExcel($alerts);
            case 'pdf':
                return $this->exportPDF($alerts);
        }
    }

    private function calculateFraudScore($data)
    {
        $score = 0;

        // Base score by risk level
        switch ($data['risk_level']) {
            case 'critical':
                $score += 40;
                break;
            case 'high':
                $score += 30;
                break;
            case 'medium':
                $score += 20;
                break;
            case 'low':
                $score += 10;
                break;
        }

        // Additional score by alert type
        switch ($data['alert_type']) {
            case 'financial_fraud':
                $score += 25;
                break;
            case 'identity_theft':
                $score += 20;
                break;
            case 'listing_fraud':
                $score += 15;
                break;
            case 'document_fraud':
                $score += 10;
                break;
            case 'suspicious_activity':
                $score += 5;
                break;
        }

        // Score by urgency level
        switch ($data['urgency_level']) {
            case 'critical':
                $score += 15;
                break;
            case 'high':
                $score += 10;
                break;
            case 'medium':
                $score += 5;
                break;
        }

        // Score by estimated loss
        if (isset($data['estimated_loss']) && $data['estimated_loss'] > 0) {
            if ($data['estimated_loss'] > 100000) {
                $score += 20;
            } elseif ($data['estimated_loss'] > 50000) {
                $score += 15;
            } elseif ($data['estimated_loss'] > 10000) {
                $score += 10;
            } elseif ($data['estimated_loss'] > 1000) {
                $score += 5;
            }
        }

        return min(100, $score);
    }

    private function processEvidence($evidenceFiles)
    {
        if (!$evidenceFiles) {
            return null;
        }

        $evidenceData = [];
        foreach ($evidenceFiles as $file) {
            $path = $file->store('fraud_evidence', 'public');
            $evidenceData[] = [
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_at' => now(),
            ];
        }

        return json_encode($evidenceData);
    }

    private function sendFraudNotification(FraudAlert $fraudAlert)
    {
        try {
            Mail::to(Auth::user()->email)->send(new FraudAlertNotification($fraudAlert));
        } catch (\Exception $e) {
            Log::error('Failed to send fraud notification', [
                'alert_id' => $fraudAlert->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function performFraudInvestigation(FraudAlert $fraudAlert)
    {
        $results = [
            'investigation_id' => Str::uuid(),
            'started_at' => now(),
            'findings' => [],
            'recommendations' => [],
            'risk_assessment' => [],
        ];

        // Analyze patterns
        $patterns = $this->analyzeFraudPatterns($fraudAlert);
        $results['findings']['patterns'] = $patterns;

        // Check for similar cases
        $similarCases = $this->findSimilarCases($fraudAlert);
        $results['findings']['similar_cases'] = $similarCases;

        // Risk assessment
        $riskAssessment = $this->assessFraudRisk($fraudAlert);
        $results['risk_assessment'] = $riskAssessment;

        // Generate recommendations
        $recommendations = $this->generateRecommendations($fraudAlert, $riskAssessment);
        $results['recommendations'] = $recommendations;

        return $results;
    }

    private function performFraudScan($validated)
    {
        $scanResults = [
            'scan_id' => Str::uuid(),
            'scan_type' => $validated['scan_type'],
            'scanned_at' => now(),
            'suspicious_activities' => [],
            'risk_indicators' => [],
            'recommendations' => [],
        ];

        // Simulate fraud detection scan
        $indicators = [
            'unusual_login_patterns' => rand(0, 3),
            'suspicious_transactions' => rand(0, 5),
            'document_anomalies' => rand(0, 2),
            'identity_mismatches' => rand(0, 1),
        ];

        $scanResults['risk_indicators'] = $indicators;
        $scanResults['total_risk_score'] = array_sum($indicators);

        // Generate alerts for high-risk indicators
        if ($scanResults['total_risk_score'] > 5) {
            $scanResults['suspicious_activities'][] = [
                'type' => 'high_risk_detected',
                'description' => 'تم اكتشاف أنشطة مشبوهة عالية المخاطر',
                'recommended_action' => 'إجراء تحقيق فوري',
            ];
        }

        return $scanResults;
    }

    private function getFraudTrends($userId)
    {
        return FraudAlert::where('user_id', $userId)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getRiskDistribution($userId)
    {
        return FraudAlert::where('user_id', $userId)
            ->selectRaw('risk_level, COUNT(*) as count')
            ->groupBy('risk_level')
            ->get();
    }

    private function getAlertTypeDistribution($userId)
    {
        return FraudAlert::where('user_id', $userId)
            ->selectRaw('alert_type, COUNT(*) as count')
            ->groupBy('alert_type')
            ->get();
    }

    private function getResolutionTimes($userId)
    {
        return FraudAlert::where('user_id', $userId)
            ->whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_resolution_hours')
            ->first();
    }

    private function getFinancialImpact($userId)
    {
        return FraudAlert::where('user_id', $userId)
            ->selectRaw('SUM(estimated_loss) as total_estimated_loss, SUM(actual_loss) as total_actual_loss, SUM(recovery_amount) as total_recovery')
            ->first();
    }

    private function getFilteredAlerts($validated)
    {
        $query = FraudAlert::where('user_id', Auth::id());

        // Apply date range filter
        switch ($validated['date_range']) {
            case 'last_week':
                $query->where('created_at', '>=', now()->subWeek());
                break;
            case 'last_month':
                $query->where('created_at', '>=', now()->subMonth());
                break;
            case 'last_quarter':
                $query->where('created_at', '>=', now()->subQuarter());
                break;
            case 'last_year':
                $query->where('created_at', '>=', now()->subYear());
                break;
            case 'custom':
                $query->whereBetween('created_at', [$validated['start_date'], $validated['end_date']]);
                break;
        }

        // Apply status filter
        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        // Apply risk level filter
        if (isset($validated['risk_level'])) {
            $query->where('risk_level', $validated['risk_level']);
        }

        return $query->get();
    }

    private function exportCSV($alerts)
    {
        $filename = 'fraud_alerts_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($alerts) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'ID', 'Property ID', 'Alert Type', 'Risk Level', 'Description',
                'Status', 'Created At', 'Resolved At', 'Estimated Loss'
            ]);

            // Data
            foreach ($alerts as $alert) {
                fputcsv($file, [
                    $alert->id,
                    $alert->property_id,
                    $alert->alert_type,
                    $alert->risk_level,
                    $alert->description,
                    $alert->status,
                    $alert->created_at,
                    $alert->resolved_at,
                    $alert->estimated_loss,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportExcel($alerts)
    {
        // Implementation for Excel export
        return response()->download('fraud_alerts.xlsx');
    }

    private function exportPDF($alerts)
    {
        // Implementation for PDF export
        return response()->download('fraud_alerts.pdf');
    }

    private function analyzeFraudPatterns(FraudAlert $fraudAlert)
    {
        // Simulate pattern analysis
        return [
            'temporal_patterns' => 'النشاط يحدث بشكل متكرر خلال ساعات العمل',
            'behavioral_patterns' => 'محاولات وصول غير عادية من عناوين IP متعددة',
            'transaction_patterns' => 'معاملات ذات قيم غير عادية',
        ];
    }

    private function findSimilarCases(FraudAlert $fraudAlert)
    {
        return FraudAlert::where('alert_type', $fraudAlert->alert_type)
            ->where('id', '!=', $fraudAlert->id)
            ->where('created_at', '>=', now()->subDays(90))
            ->take(5)
            ->get();
    }

    private function assessFraudRisk(FraudAlert $fraudAlert)
    {
        return [
            'current_risk_level' => $fraudAlert->risk_level,
            'risk_trend' => 'stable',
            'potential_impact' => 'medium',
            'recommended_actions' => ['monitoring', 'investigation'],
        ];
    }

    private function generateRecommendations(FraudAlert $fraudAlert, $riskAssessment)
    {
        $recommendations = [];

        if ($fraudAlert->risk_level === 'high' || $fraudAlert->risk_level === 'critical') {
            $recommendations[] = 'إجراء تحقيق فوري';
            $recommendations[] = 'تعزيز إجراءات الأمان';
        }

        if ($fraudAlert->estimated_loss > 50000) {
            $recommendations[] = 'إبلاغ السلطات المختصة';
        }

        $recommendations[] = 'مراجعة إجراءات التحقق';
        $recommendations[] = 'تدريب الموظفين على كشف الاحتيال';

        return $recommendations;
    }
}
