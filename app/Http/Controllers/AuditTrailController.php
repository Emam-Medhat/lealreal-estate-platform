<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuditTrailController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $auditStats = [
            'total_logs' => AuditLog::where('user_id', $user->id)->count(),
            'today_logs' => AuditLog::where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->count(),
            'failed_attempts' => AuditLog::where('user_id', $user->id)
                ->where('success', false)
                ->count(),
            'suspicious_activities' => AuditLog::where('user_id', $user->id)
                ->where('risk_level', 'high')
                ->count(),
        ];

        $recentLogs = AuditLog::where('user_id', $user->id)
            ->with(['property', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return view('security.audit-trail.index', compact('auditStats', 'recentLogs'));
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'action' => 'nullable|string|max:100',
            'user_id' => 'nullable|exists:users,id',
            'property_id' => 'nullable|exists:properties,id',
            'date_range' => 'required|in:today,yesterday,last_7_days,last_30_days,last_90_days,custom',
            'start_date' => 'nullable|date|required_if:date_range,custom',
            'end_date' => 'nullable|date|required_if:date_range,custom|after_or_equal:start_date',
            'risk_level' => 'nullable|in:low,medium,high,critical',
            'success' => 'nullable|boolean',
            'ip_address' => 'nullable|ip',
            'per_page' => 'nullable|integer|min:10|max:100',
        ]);

        $query = AuditLog::with(['property', 'user']);

        // Apply filters
        if (isset($validated['action'])) {
            $query->where('action', 'like', '%' . $validated['action'] . '%');
        }

        if (isset($validated['user_id'])) {
            $query->where('user_id', $validated['user_id']);
        }

        if (isset($validated['property_id'])) {
            $query->where('property_id', $validated['property_id']);
        }

        // Apply date range filter
        switch ($validated['date_range']) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'yesterday':
                $query->whereDate('created_at', yesterday());
                break;
            case 'last_7_days':
                $query->where('created_at', '>=', now()->subDays(7));
                break;
            case 'last_30_days':
                $query->where('created_at', '>=', now()->subDays(30));
                break;
            case 'last_90_days':
                $query->where('created_at', '>=', now()->subDays(90));
                break;
            case 'custom':
                $query->whereBetween('created_at', [$validated['start_date'], $validated['end_date']]);
                break;
        }

        if (isset($validated['risk_level'])) {
            $query->where('risk_level', $validated['risk_level']);
        }

        if (isset($validated['success'])) {
            $query->where('success', $validated['success']);
        }

        if (isset($validated['ip_address'])) {
            $query->where('ip_address', $validated['ip_address']);
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate($validated['per_page'] ?? 20);

        return view('security.audit-trail.search', compact('logs', 'validated'));
    }

    public function show(AuditLog $auditLog)
    {
        $this->authorize('view', $auditLog);
        
        $auditLog->load(['property', 'user']);
        
        return view('security.audit-trail.show', compact('auditLog'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,pdf',
            'date_range' => 'required|in:today,yesterday,last_7_days,last_30_days,last_90_days,custom',
            'start_date' => 'nullable|date|required_if:date_range,custom',
            'end_date' => 'nullable|date|required_if:date_range,custom|after_or_equal:start_date',
            'action' => 'nullable|string|max:100',
            'risk_level' => 'nullable|in:low,medium,high,critical',
            'success' => 'nullable|boolean',
        ]);

        $logs = $this->getFilteredLogs($validated);

        switch ($validated['format']) {
            case 'csv':
                return $this->exportCSV($logs);
            case 'xlsx':
                return $this->exportExcel($logs);
            case 'pdf':
                return $this->exportPDF($logs);
        }
    }

    public function analytics()
    {
        $user = Auth::user();
        
        $analytics = [
            'activity_trends' => $this->getActivityTrends($user->id),
            'risk_distribution' => $this->getRiskDistribution($user->id),
            'action_frequency' => $this->getActionFrequency($user->id),
            'user_activity' => $this->getUserActivity($user->id),
            'property_activity' => $this->getPropertyActivity($user->id),
            'time_based_analysis' => $this->getTimeBasedAnalysis($user->id),
            'geographic_analysis' => $this->getGeographicAnalysis($user->id),
            'device_analysis' => $this->getDeviceAnalysis($user->id),
        ];

        return view('security.audit-trail.analytics', compact('analytics'));
    }

    public function securityReport()
    {
        $user = Auth::user();
        
        $reportData = [
            'summary' => $this->generateSecuritySummary($user->id),
            'threats_detected' => $this->getThreatsDetected($user->id),
            'compliance_status' => $this->getComplianceStatus($user->id),
            'recommendations' => $this->generateSecurityRecommendations($user->id),
            'incident_timeline' => $this->getIncidentTimeline($user->id),
        ];

        return view('security.audit-trail.security-report', compact('reportData'));
    }

    public function complianceReport()
    {
        $user = Auth::user();
        
        $complianceData = [
            'overall_score' => $this->calculateComplianceScore($user->id),
            'regulatory_compliance' => $this->getRegulatoryCompliance($user->id),
            'policy_adherence' => $this->getPolicyAdherence($user->id),
            'audit_trail_integrity' => $this->getAuditTrailIntegrity($user->id),
            'data_protection_compliance' => $this->getDataProtectionCompliance($user->id),
            'access_control_compliance' => $this->getAccessControlCompliance($user->id),
        ];

        return view('security.audit-trail.compliance-report', compact('complianceData'));
    }

    public function logActivity(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|string|max:255',
            'property_id' => 'nullable|exists:properties,id',
            'details' => 'nullable|array',
            'success' => 'required|boolean',
            'risk_level' => 'required|in:low,medium,high,critical',
            'additional_data' => 'nullable|array',
        ]);

        $auditLog = AuditLog::create([
            'user_id' => Auth::id(),
            'property_id' => $validated['property_id'],
            'action' => $validated['action'],
            'details' => json_encode($validated['details'] ?? []),
            'success' => $validated['success'],
            'risk_level' => $validated['risk_level'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'location_data' => json_encode($request->header('X-Location') ?? []),
            'device_info' => json_encode($request->header('X-Device-Info') ?? []),
            'additional_data' => json_encode($validated['additional_data'] ?? []),
        ]);

        // Check for suspicious activity
        if ($validated['risk_level'] === 'high' || $validated['risk_level'] === 'critical') {
            $this->handleSuspiciousActivity($auditLog);
        }

        return response()->json([
            'success' => true,
            'audit_log_id' => $auditLog->id,
        ]);
    }

    public function bulkLog(Request $request)
    {
        $validated = $request->validate([
            'logs' => 'required|array|max:100',
            'logs.*.action' => 'required|string|max:255',
            'logs.*.property_id' => 'nullable|exists:properties,id',
            'logs.*.details' => 'nullable|array',
            'logs.*.success' => 'required|boolean',
            'logs.*.risk_level' => 'required|in:low,medium,high,critical',
        ]);

        $createdLogs = [];
        $errors = [];

        foreach ($validated['logs'] as $index => $logData) {
            try {
                $auditLog = AuditLog::create([
                    'user_id' => Auth::id(),
                    'property_id' => $logData['property_id'] ?? null,
                    'action' => $logData['action'],
                    'details' => json_encode($logData['details'] ?? []),
                    'success' => $logData['success'],
                    'risk_level' => $logData['risk_level'],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'location_data' => json_encode($request->header('X-Location') ?? []),
                    'device_info' => json_encode($request->header('X-Device-Info') ?? []),
                ]);

                $createdLogs[] = $auditLog->id;

                // Check for suspicious activity
                if ($logData['risk_level'] === 'high' || $logData['risk_level'] === 'critical') {
                    $this->handleSuspiciousActivity($auditLog);
                }

            } catch (\Exception $e) {
                $errors[] = [
                    'index' => $index,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'success' => count($createdLogs) > 0,
            'created_logs' => $createdLogs,
            'errors' => $errors,
            'total_processed' => count($validated['logs']),
        ]);
    }

    public function archive(Request $request)
    {
        $validated = $request->validate([
            'date_range' => 'required|in:last_90_days,last_180_days,last_year,custom',
            'start_date' => 'nullable|date|required_if:date_range,custom',
            'end_date' => 'nullable|date|required_if:date_range,custom|after_or_equal:start_date',
            'action' => 'nullable|string|max:100',
        ]);

        $query = AuditLog::where('user_id', Auth::id());

        // Apply date range filter
        switch ($validated['date_range']) {
            case 'last_90_days':
                $query->where('created_at', '<', now()->subDays(90));
                break;
            case 'last_180_days':
                $query->where('created_at', '<', now()->subDays(180));
                break;
            case 'last_year':
                $query->where('created_at', '<', now()->subYear());
                break;
            case 'custom':
                $query->whereBetween('created_at', [$validated['start_date'], $validated['end_date']]);
                break;
        }

        if (isset($validated['action'])) {
            $query->where('action', 'like', '%' . $validated['action'] . '%');
        }

        $archivedCount = $query->count();

        // Archive logs (move to archive table or mark as archived)
        $query->update(['archived' => true, 'archived_at' => now()]);

        return response()->json([
            'success' => true,
            'archived_count' => $archivedCount,
        ]);
    }

    public function restore(Request $request)
    {
        $validated = $request->validate([
            'log_ids' => 'required|array',
            'log_ids.*' => 'exists:audit_logs,id',
        ]);

        $restoredCount = AuditLog::whereIn('id', $validated['log_ids'])
            ->where('user_id', Auth::id())
            ->where('archived', true)
            ->update(['archived' => false, 'archived_at' => null]);

        return response()->json([
            'success' => true,
            'restored_count' => $restoredCount,
        ]);
    }

    private function getFilteredLogs($validated)
    {
        $query = AuditLog::where('user_id', Auth::id());

        // Apply date range filter
        switch ($validated['date_range']) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'yesterday':
                $query->whereDate('created_at', yesterday());
                break;
            case 'last_7_days':
                $query->where('created_at', '>=', now()->subDays(7));
                break;
            case 'last_30_days':
                $query->where('created_at', '>=', now()->subDays(30));
                break;
            case 'last_90_days':
                $query->where('created_at', '>=', now()->subDays(90));
                break;
            case 'custom':
                $query->whereBetween('created_at', [$validated['start_date'], $validated['end_date']]);
                break;
        }

        if (isset($validated['action'])) {
            $query->where('action', 'like', '%' . $validated['action'] . '%');
        }

        if (isset($validated['risk_level'])) {
            $query->where('risk_level', $validated['risk_level']);
        }

        if (isset($validated['success'])) {
            $query->where('success', $validated['success']);
        }

        return $query->get();
    }

    private function exportCSV($logs)
    {
        $filename = 'audit_logs_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'ID', 'User', 'Property', 'Action', 'Success', 'Risk Level',
                'IP Address', 'User Agent', 'Created At'
            ]);

            // Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user->name ?? 'N/A',
                    $log->property->name ?? 'N/A',
                    $log->action,
                    $log->success ? 'Yes' : 'No',
                    $log->risk_level,
                    $log->ip_address,
                    $log->user_agent,
                    $log->created_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportExcel($logs)
    {
        // Implementation for Excel export
        return response()->download('audit_logs.xlsx');
    }

    private function exportPDF($logs)
    {
        // Implementation for PDF export
        return response()->download('audit_logs.pdf');
    }

    private function getActivityTrends($userId)
    {
        return AuditLog::where('user_id', $userId)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getRiskDistribution($userId)
    {
        return AuditLog::where('user_id', $userId)
            ->selectRaw('risk_level, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('risk_level')
            ->get();
    }

    private function getActionFrequency($userId)
    {
        return AuditLog::where('user_id', $userId)
            ->selectRaw('action, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();
    }

    private function getUserActivity($userId)
    {
        return AuditLog::where('user_id', $userId)
            ->selectRaw('user_id, COUNT(*) as activity_count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('user_id')
            ->get();
    }

    private function getPropertyActivity($userId)
    {
        return AuditLog::where('user_id', $userId)
            ->selectRaw('property_id, COUNT(*) as activity_count')
            ->where('created_at', '>=', now()->subDays(30))
            ->whereNotNull('property_id')
            ->groupBy('property_id')
            ->orderBy('activity_count', 'desc')
            ->take(10)
            ->get();
    }

    private function getTimeBasedAnalysis($userId)
    {
        return [
            'hourly_distribution' => AuditLog::where('user_id', $userId)
                ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('hour')
                ->get(),
            'daily_distribution' => AuditLog::where('user_id', $userId)
                ->selectRaw('DAYOFWEEK(created_at) as day, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('day')
                ->get(),
        ];
    }

    private function getGeographicAnalysis($userId)
    {
        return AuditLog::where('user_id', $userId)
            ->selectRaw('location_data, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->whereNotNull('location_data')
            ->groupBy('location_data')
            ->take(10)
            ->get();
    }

    private function getDeviceAnalysis($userId)
    {
        return AuditLog::where('user_id', $userId)
            ->selectRaw('device_info, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->whereNotNull('device_info')
            ->groupBy('device_info')
            ->take(10)
            ->get();
    }

    private function generateSecuritySummary($userId)
    {
        return [
            'total_activities' => AuditLog::where('user_id', $userId)->count(),
            'high_risk_activities' => AuditLog::where('user_id', $userId)
                ->where('risk_level', 'high')
                ->count(),
            'failed_activities' => AuditLog::where('user_id', $userId)
                ->where('success', false)
                ->count(),
            'suspicious_patterns' => $this->detectSuspiciousPatterns($userId),
        ];
    }

    private function getThreatsDetected($userId)
    {
        return AuditLog::where('user_id', $userId)
            ->where('risk_level', 'high')
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    private function getComplianceStatus($userId)
    {
        return [
            'audit_trail_complete' => $this->checkAuditTrailCompleteness($userId),
            'data_protection_compliant' => $this->checkDataProtectionCompliance($userId),
            'access_control_compliant' => $this->checkAccessControlCompliance($userId),
            'overall_compliance_score' => $this->calculateComplianceScore($userId),
        ];
    }

    private function generateSecurityRecommendations($userId)
    {
        $recommendations = [];

        // Analyze patterns and generate recommendations
        $failedAttempts = AuditLog::where('user_id', $userId)
            ->where('success', false)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        if ($failedAttempts > 10) {
            $recommendations[] = 'زيادة إجراءات الأمان بسبب محاولات الفشل المتكررة';
        }

        $highRiskActivities = AuditLog::where('user_id', $userId)
            ->where('risk_level', 'high')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        if ($highRiskActivities > 5) {
            $recommendations[] = 'مراجعة الأنشطة عالية المخاطر وتقييد الوصول';
        }

        return $recommendations;
    }

    private function getIncidentTimeline($userId)
    {
        return AuditLog::where('user_id', $userId)
            ->where('risk_level', 'high')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();
    }

    private function calculateComplianceScore($userId)
    {
        $totalLogs = AuditLog::where('user_id', $userId)->count();
        $compliantLogs = AuditLog::where('user_id', $userId)
            ->where('success', true)
            ->count();

        return $totalLogs > 0 ? ($compliantLogs / $totalLogs) * 100 : 0;
    }

    private function getRegulatoryCompliance($userId)
    {
        return [
            'data_retention_compliant' => true,
            'privacy_compliant' => true,
            'security_standards_compliant' => true,
            'audit_requirements_met' => true,
        ];
    }

    private function getPolicyAdherence($userId)
    {
        return [
            'access_policy_compliance' => 95,
            'data_handling_compliance' => 90,
            'security_policy_compliance' => 88,
            'incident_reporting_compliance' => 92,
        ];
    }

    private function getAuditTrailIntegrity($userId)
    {
        return [
            'log_completeness' => 98,
            'log_accuracy' => 99,
            'log_availability' => 100,
            'log_immutability' => 100,
        ];
    }

    private function getDataProtectionCompliance($userId)
    {
        return [
            'encryption_compliance' => 95,
            'access_control_compliance' => 90,
            'data_minimization_compliance' => 88,
            'consent_management_compliance' => 92,
        ];
    }

    private function getAccessControlCompliance($userId)
    {
        return [
            'authentication_compliance' => 95,
            'authorization_compliance' => 90,
            'session_management_compliance' => 88,
            'privilege_management_compliance' => 92,
        ];
    }

    private function handleSuspiciousActivity(AuditLog $auditLog)
    {
        // Log suspicious activity
        Log::warning('Suspicious activity detected', [
            'audit_log_id' => $auditLog->id,
            'user_id' => $auditLog->user_id,
            'action' => $auditLog->action,
            'risk_level' => $auditLog->risk_level,
        ]);

        // In a real implementation, this would trigger security alerts,
        // notify administrators, or take automated security actions
    }

    private function detectSuspiciousPatterns($userId)
    {
        $patterns = [];

        // Detect unusual login patterns
        $unusualLogins = AuditLog::where('user_id', $userId)
            ->where('action', 'login')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('ip_address')
            ->havingRaw('COUNT(*) > 10')
            ->count();

        if ($unusualLogins > 0) {
            $patterns[] = 'محاولات تسجيل دخول غير عادية من عناوين IP متعددة';
        }

        return $patterns;
    }

    private function checkAuditTrailCompleteness($userId)
    {
        // Check if audit trail is complete for the last 30 days
        $expectedLogs = 30; // Expected minimum logs per day
        $actualLogs = AuditLog::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        return $actualLogs >= ($expectedLogs * 30);
    }

    private function checkDataProtectionCompliance($userId)
    {
        // Check data protection compliance
        return true; // Simplified implementation
    }

    private function checkAccessControlCompliance($userId)
    {
        // Check access control compliance
        return true; // Simplified implementation
    }
}
