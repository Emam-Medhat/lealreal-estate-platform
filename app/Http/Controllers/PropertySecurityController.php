<?php

namespace App\Http\Controllers;

use App\Models\PropertySecurity;
use App\Models\SecurityIncident;
use App\Models\AuditLog;
use App\Models\FraudAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PropertySecurityController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $securityStats = [
            'total_properties' => PropertySecurity::where('user_id', $user->id)->count(),
            'active_incidents' => SecurityIncident::where('status', 'active')->count(),
            'recent_alerts' => FraudAlert::where('created_at', '>=', now()->subDays(7))->count(),
            'security_score' => $this->calculateSecurityScore($user->id),
        ];

        $recentIncidents = SecurityIncident::with(['property', 'user'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $fraudAlerts = FraudAlert::where('user_id', $user->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('security.dashboard', compact('securityStats', 'recentIncidents', 'fraudAlerts'));
    }

    public function create()
    {
        return view('security.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'security_level' => 'required|in:low,medium,high,critical',
            'access_permissions' => 'required|array',
            'encryption_enabled' => 'boolean',
            'biometric_required' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'audit_frequency' => 'required|in:daily,weekly,monthly',
            'security_notes' => 'nullable|string|max:1000',
        ]);

        $security = PropertySecurity::create([
            'user_id' => Auth::id(),
            'property_id' => $validated['property_id'],
            'security_level' => $validated['security_level'],
            'access_permissions' => json_encode($validated['access_permissions']),
            'encryption_enabled' => $validated['encryption_enabled'] ?? false,
            'biometric_required' => $validated['biometric_required'] ?? false,
            'two_factor_enabled' => $validated['two_factor_enabled'] ?? false,
            'audit_frequency' => $validated['audit_frequency'],
            'security_notes' => $validated['security_notes'],
            'last_audit_date' => now(),
            'next_audit_date' => $this->calculateNextAuditDate($validated['audit_frequency']),
        ]);

        // Log the security setup
        Log::info('Property security setup completed', [
            'user_id' => Auth::id(),
            'property_id' => $validated['property_id'],
            'security_id' => $security->id,
        ]);

        return redirect()->route('security.dashboard')
            ->with('success', 'تم إعداد أمان العقار بنجاح');
    }

    public function show(PropertySecurity $security)
    {
        $this->authorize('view', $security);
        
        $security->load(['property', 'auditLogs' => function($query) {
            $query->orderBy('created_at', 'desc')->take(10);
        }]);

        return view('security.show', compact('security'));
    }

    public function edit(PropertySecurity $security)
    {
        $this->authorize('update', $security);
        return view('security.edit', compact('security'));
    }

    public function update(Request $request, PropertySecurity $security)
    {
        $this->authorize('update', $security);

        $validated = $request->validate([
            'security_level' => 'required|in:low,medium,high,critical',
            'access_permissions' => 'required|array',
            'encryption_enabled' => 'boolean',
            'biometric_required' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'audit_frequency' => 'required|in:daily,weekly,monthly',
            'security_notes' => 'nullable|string|max:1000',
        ]);

        $security->update([
            'security_level' => $validated['security_level'],
            'access_permissions' => json_encode($validated['access_permissions']),
            'encryption_enabled' => $validated['encryption_enabled'] ?? $security->encryption_enabled,
            'biometric_required' => $validated['biometric_required'] ?? $security->biometric_required,
            'two_factor_enabled' => $validated['two_factor_enabled'] ?? $security->two_factor_enabled,
            'audit_frequency' => $validated['audit_frequency'],
            'security_notes' => $validated['security_notes'],
            'next_audit_date' => $this->calculateNextAuditDate($validated['audit_frequency']),
        ]);

        // Log the security update
        AuditLog::create([
            'user_id' => Auth::id(),
            'property_id' => $security->property_id,
            'action' => 'security_updated',
            'details' => json_encode([
                'security_id' => $security->id,
                'changes' => $validated
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('security.show', $security)
            ->with('success', 'تم تحديث إعدادات الأمان بنجاح');
    }

    public function destroy(PropertySecurity $security)
    {
        $this->authorize('delete', $security);

        // Log before deletion
        AuditLog::create([
            'user_id' => Auth::id(),
            'property_id' => $security->property_id,
            'action' => 'security_deleted',
            'details' => json_encode([
                'security_id' => $security->id,
                'property_id' => $security->property_id
            ]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $security->delete();

        return redirect()->route('security.dashboard')
            ->with('success', 'تم حذف إعدادات الأمان بنجاح');
    }

    public function audit(PropertySecurity $security)
    {
        $this->authorize('audit', $security);

        $auditResults = $this->performSecurityAudit($security);

        // Update last audit date
        $security->update([
            'last_audit_date' => now(),
            'next_audit_date' => $this->calculateNextAuditDate($security->audit_frequency),
        ]);

        // Log the audit
        AuditLog::create([
            'user_id' => Auth::id(),
            'property_id' => $security->property_id,
            'action' => 'security_audit_performed',
            'details' => json_encode([
                'security_id' => $security->id,
                'audit_results' => $auditResults
            ]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return view('security.audit-results', compact('security', 'auditResults'));
    }

    public function scan(PropertySecurity $security)
    {
        $this->authorize('scan', $security);

        $scanResults = $this->performSecurityScan($security);

        return view('security.scan-results', compact('security', 'scanResults'));
    }

    public function generateReport(PropertySecurity $security)
    {
        $this->authorize('view', $security);

        $reportData = $this->generateSecurityReport($security);

        return response()->json($reportData);
    }

    private function calculateSecurityScore($userId)
    {
        $properties = PropertySecurity::where('user_id', $userId)->get();
        
        if ($properties->isEmpty()) {
            return 0;
        }

        $totalScore = 0;
        $maxScore = 0;

        foreach ($properties as $security) {
            $score = 0;
            $maxScore += 100;

            // Base score based on security level
            switch ($security->security_level) {
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

            // Additional security features
            if ($security->encryption_enabled) $score += 20;
            if ($security->biometric_required) $score += 15;
            if ($security->two_factor_enabled) $score += 15;
            if ($security->access_permissions) $score += 10;

            $totalScore += $score;
        }

        return $maxScore > 0 ? round(($totalScore / $maxScore) * 100) : 0;
    }

    private function calculateNextAuditDate($frequency)
    {
        switch ($frequency) {
            case 'daily':
                return now()->addDay();
            case 'weekly':
                return now()->addWeek();
            case 'monthly':
                return now()->addMonth();
            default:
                return now()->addMonth();
        }
    }

    private function performSecurityAudit(PropertySecurity $security)
    {
        $results = [
            'overall_score' => 0,
            'checks' => [],
            'recommendations' => [],
            'vulnerabilities' => [],
        ];

        // Check encryption status
        if ($security->encryption_enabled) {
            $results['checks']['encryption'] = ['status' => 'pass', 'score' => 20];
        } else {
            $results['checks']['encryption'] = ['status' => 'fail', 'score' => 0];
            $results['recommendations'][] = 'تفعيل تشفير البيانات';
            $results['vulnerabilities'][] = 'البيانات غير مشفرة';
        }

        // Check biometric authentication
        if ($security->biometric_required) {
            $results['checks']['biometric'] = ['status' => 'pass', 'score' => 15];
        } else {
            $results['checks']['biometric'] = ['status' => 'fail', 'score' => 0];
            $results['recommendations'][] = 'تفعيل المصادقة البيومترية';
        }

        // Check two-factor authentication
        if ($security->two_factor_enabled) {
            $results['checks']['two_factor'] = ['status' => 'pass', 'score' => 15];
        } else {
            $results['checks']['two_factor'] = ['status' => 'fail', 'score' => 0];
            $results['recommendations'][] = 'تفعيل المصادقة الثنائية';
        }

        // Check access permissions
        if ($security->access_permissions) {
            $results['checks']['access_control'] = ['status' => 'pass', 'score' => 10];
        } else {
            $results['checks']['access_control'] = ['status' => 'fail', 'score' => 0];
            $results['recommendations'][] = 'إعداد صلاحيات الوصول';
        }

        // Calculate overall score
        $totalScore = array_sum(array_column($results['checks'], 'score'));
        $results['overall_score'] = $totalScore;

        return $results;
    }

    private function performSecurityScan(PropertySecurity $security)
    {
        $scanResults = [
            'scan_date' => now(),
            'threats_detected' => 0,
            'scan_details' => [],
        ];

        // Simulate security scan
        $threats = [
            'unauthorized_access_attempts' => rand(0, 5),
            'data_breach_attempts' => rand(0, 2),
            'malware_detected' => rand(0, 1),
            'phishing_attempts' => rand(0, 3),
        ];

        $scanResults['scan_details'] = $threats;
        $scanResults['threats_detected'] = array_sum($threats);

        // Log any detected threats
        if ($scanResults['threats_detected'] > 0) {
            SecurityIncident::create([
                'user_id' => Auth::id(),
                'property_id' => $security->property_id,
                'incident_type' => 'security_scan_detected',
                'severity' => $scanResults['threats_detected'] > 5 ? 'high' : 'medium',
                'description' => 'تم اكتشاف تهديدات أمنية أثناء الفحص الأمني',
                'details' => json_encode($threats),
                'status' => 'active',
            ]);
        }

        return $scanResults;
    }

    private function generateSecurityReport(PropertySecurity $security)
    {
        $report = [
            'property_id' => $security->property_id,
            'security_level' => $security->security_level,
            'generated_at' => now(),
            'metrics' => [
                'security_score' => $this->calculateSecurityScore(Auth::id()),
                'last_audit_date' => $security->last_audit_date,
                'next_audit_date' => $security->next_audit_date,
                'total_incidents' => SecurityIncident::where('property_id', $security->property_id)->count(),
                'active_incidents' => SecurityIncident::where('property_id', $security->property_id)
                    ->where('status', 'active')->count(),
            ],
            'security_features' => [
                'encryption_enabled' => $security->encryption_enabled,
                'biometric_required' => $security->biometric_required,
                'two_factor_enabled' => $security->two_factor_enabled,
                'access_permissions' => json_decode($security->access_permissions, true),
            ],
            'recent_activity' => AuditLog::where('property_id', $security->property_id)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get(),
        ];

        return $report;
    }
}
