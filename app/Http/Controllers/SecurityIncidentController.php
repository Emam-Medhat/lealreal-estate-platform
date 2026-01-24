<?php

namespace App\Http\Controllers;

use App\Models\SecurityIncident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SecurityIncidentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $incidentStats = [
            'total_incidents' => SecurityIncident::where('user_id', $user->id)->count(),
            'active_incidents' => SecurityIncident::where('user_id', $user->id)
                ->where('status', 'active')
                ->count(),
            'critical_incidents' => SecurityIncident::where('user_id', $user->id)
                ->where('severity', 'critical')
                ->count(),
            'resolved_today' => SecurityIncident::where('user_id', $user->id)
                ->where('status', 'resolved')
                ->whereDate('resolved_at', today())
                ->count(),
        ];

        $recentIncidents = SecurityIncident::where('user_id', $user->id)
            ->with(['property', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('security.incidents.index', compact('incidentStats', 'recentIncidents'));
    }

    public function create()
    {
        return view('security.incidents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'incident_type' => 'required|in:unauthorized_access,data_breach,malware_detected,suspicious_activity,phishing_attempt,system_compromise,physical_breach,identity_theft,fraud_attempt,other',
            'severity' => 'required|in:low,medium,high,critical',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'affected_systems' => 'nullable|array',
            'affected_systems.*' => 'string|max:100',
            'impact_assessment' => 'required|array',
            'impact_assessment.data_confidentiality' => 'required|in:none,low,medium,high,critical',
            'impact_assessment.data_integrity' => 'required|in:none,low,medium,high,critical',
            'impact_assessment.data_availability' => 'required|in:none,low,medium,high,critical',
            'impact_assessment.financial_impact' => 'required|in:none,low,medium,high,critical',
            'impact_assessment.reputational_impact' => 'required|in:none,low,medium,high,critical',
            'impact_assessment.operational_impact' => 'required|in:none,low,medium,high,critical',
            'detection_method' => 'required|in:automated,manual,user_report,external_notification,security_monitoring',
            'detection_source' => 'required|string|max:255',
            'incident_timeline' => 'required|array',
            'incident_timeline.*.timestamp' => 'required|date',
            'incident_timeline.*.event' => 'required|string|max:500',
            'incident_timeline.*.description' => 'nullable|string|max:1000',
            'root_cause' => 'nullable|string|max:1000',
            'immediate_actions' => 'nullable|array',
            'immediate_actions.*.action' => 'required|string|max:500',
            'immediate_actions.*.taken_by' => 'required|string|max:255',
            'immediate_actions.*.timestamp' => 'required|date',
            'evidence' => 'nullable|array',
            'evidence.*.type' => 'required|in:log_file,screenshot,network_capture,system_report,user_testimony,other',
            'evidence.*.description' => 'required|string|max:500',
            'evidence.*.file_path' => 'nullable|string|max:255',
            'affected_users' => 'nullable|array',
            'affected_users.*.user_id' => 'required|exists:users,id',
            'affected_users.*.impact_level' => 'required|in:low,medium,high,critical',
            'affected_users.*.description' => 'nullable|string|max:500',
            'notifications_sent' => 'nullable|array',
            'notifications_sent.*.recipient' => 'required|string|max:255',
            'notifications_sent.*.method' => 'required|in:email,sms,phone,in_person',
            'notifications_sent.*.timestamp' => 'required|date',
            'external_reporting' => 'boolean',
            'regulatory_reporting' => 'boolean',
            'legal_requirements' => 'nullable|array',
            'compliance_violations' => 'nullable|array',
            'compliance_violations.*.regulation' => 'required|string|max:255',
            'compliance_violations.*.description' => 'required|string|max:1000',
            'remediation_plan' => 'nullable|array',
            'remediation_plan.*.step' => 'required|string|max:500',
            'remediation_plan.*.responsible_party' => 'required|string|max:255',
            'remediation_plan.*.due_date' => 'required|date',
            'remediation_plan.*.status' => 'required|in:pending,in_progress,completed',
            'lessons_learned' => 'nullable|string|max:2000',
            'preventive_measures' => 'nullable|array',
            'preventive_measures.*.measure' => 'required|string|max:500',
            'preventive_measures.*.priority' => 'required|in:low,medium,high,critical',
            'preventive_measures.*.implementation_date' => 'nullable|date',
            'cost_analysis' => 'nullable|array',
            'cost_analysis.direct_costs' => 'nullable|numeric|min:0',
            'cost_analysis.indirect_costs' => 'nullable|numeric|min:0',
            'cost_analysis.recovery_costs' => 'nullable|numeric|min:0',
            'cost_analysis.prevention_costs' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $incident = SecurityIncident::create([
            'user_id' => Auth::id(),
            'property_id' => $validated['property_id'],
            'incident_type' => $validated['incident_type'],
            'severity' => $validated['severity'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'affected_systems' => json_encode($validated['affected_systems'] ?? []),
            'impact_assessment' => json_encode($validated['impact_assessment']),
            'detection_method' => $validated['detection_method'],
            'detection_source' => $validated['detection_source'],
            'incident_timeline' => json_encode($validated['incident_timeline']),
            'root_cause' => $validated['root_cause'],
            'immediate_actions' => json_encode($validated['immediate_actions'] ?? []),
            'evidence' => json_encode($validated['evidence'] ?? []),
            'affected_users' => json_encode($validated['affected_users'] ?? []),
            'notifications_sent' => json_encode($validated['notifications_sent'] ?? []),
            'external_reporting' => $validated['external_reporting'] ?? false,
            'regulatory_reporting' => $validated['regulatory_reporting'] ?? false,
            'legal_requirements' => json_encode($validated['legal_requirements'] ?? []),
            'compliance_violations' => json_encode($validated['compliance_violations'] ?? []),
            'remediation_plan' => json_encode($validated['remediation_plan'] ?? []),
            'lessons_learned' => $validated['lessons_learned'],
            'preventive_measures' => json_encode($validated['preventive_measures'] ?? []),
            'cost_analysis' => json_encode($validated['cost_analysis'] ?? []),
            'notes' => $validated['notes'],
            'status' => 'active',
            'incident_id' => $this->generateIncidentId(),
            'risk_score' => $this->calculateRiskScore($validated),
            'priority_level' => $this->determinePriorityLevel($validated['severity'], $validated['incident_type']),
        ]);

        // Trigger automated responses for critical incidents
        if ($validated['severity'] === 'critical') {
            $this->triggerAutomatedResponse($incident);
        }

        // Log incident creation
        Log::emergency('Security incident reported', [
            'user_id' => Auth::id(),
            'incident_id' => $incident->id,
            'incident_type' => $validated['incident_type'],
            'severity' => $validated['severity'],
        ]);

        return redirect()->route('security.incidents.show', $incident)
            ->with('success', 'تم الإبلاغ عن الحادث الأمني بنجاح');
    }

    public function show(SecurityIncident $incident)
    {
        $this->authorize('view', $incident);
        
        $incident->load(['property', 'user', 'affectedUsers', 'remediationPlan']);
        
        return view('security.incidents.show', compact('incident'));
    }

    public function edit(SecurityIncident $incident)
    {
        $this->authorize('update', $incident);
        
        return view('security.incidents.edit', compact('incident'));
    }

    public function update(Request $request, SecurityIncident $incident)
    {
        $this->authorize('update', $incident);

        $validated = $request->validate([
            'severity' => 'required|in:low,medium,high,critical',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'root_cause' => 'nullable|string|max:1000',
            'immediate_actions' => 'nullable|array',
            'remediation_plan' => 'nullable|array',
            'lessons_learned' => 'nullable|string|max:2000',
            'preventive_measures' => 'nullable|array',
            'cost_analysis' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
        ]);

        $incident->update([
            'severity' => $validated['severity'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'root_cause' => $validated['root_cause'],
            'immediate_actions' => json_encode($validated['immediate_actions'] ?? []),
            'remediation_plan' => json_encode($validated['remediation_plan'] ?? []),
            'lessons_learned' => $validated['lessons_learned'],
            'preventive_measures' => json_encode($validated['preventive_measures'] ?? []),
            'cost_analysis' => json_encode($validated['cost_analysis'] ?? []),
            'notes' => $validated['notes'],
            'risk_score' => $this->calculateRiskScore($validated + [
                'incident_type' => $incident->incident_type
            ]),
            'priority_level' => $this->determinePriorityLevel($validated['severity'], $incident->incident_type),
        ]);

        return redirect()->route('security.incidents.show', $incident)
            ->with('success', 'تم تحديث الحادث الأمني بنجاح');
    }

    public function resolve(Request $request, SecurityIncident $incident)
    {
        $this->authorize('resolve', $incident);

        $validated = $request->validate([
            'resolution_summary' => 'required|string|max:2000',
            'resolution_details' => 'required|string|max:5000',
            'final_impact_assessment' => 'required|array',
            'final_cost_analysis' => 'required|array',
            'lessons_learned' => 'required|string|max:2000',
            'preventive_actions' => 'required|array',
            'follow_up_required' => 'boolean',
            'follow_up_date' => 'nullable|date|after:today',
            'external_reporting_completed' => 'boolean',
            'regulatory_reporting_completed' => 'boolean',
        ]);

        $incident->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolved_by' => Auth::id(),
            'resolution_summary' => $validated['resolution_summary'],
            'resolution_details' => $validated['resolution_details'],
            'final_impact_assessment' => json_encode($validated['final_impact_assessment']),
            'final_cost_analysis' => json_encode($validated['final_cost_analysis']),
            'lessons_learned' => $validated['lessons_learned'],
            'preventive_actions' => json_encode($validated['preventive_actions']),
            'follow_up_required' => $validated['follow_up_required'],
            'follow_up_date' => $validated['follow_up_date'],
            'external_reporting_completed' => $validated['external_reporting_completed'],
            'regulatory_reporting_completed' => $validated['regulatory_reporting_completed'],
        ]);

        return redirect()->route('security.incidents.show', $incident)
            ->with('success', 'تم حل الحادث الأمني بنجاح');
    }

    public function escalate(Request $request, SecurityIncident $incident)
    {
        $this->authorize('escalate', $incident);

        $validated = $request->validate([
            'escalation_reason' => 'required|string|max:1000',
            'escalation_level' => 'required|in:level_1,level_2,level_3,executive',
            'escalated_to' => 'required|exists:users,id',
            'urgency_level' => 'required|in:normal,high,critical',
            'additional_context' => 'nullable|string|max:2000',
        ]);

        $incident->update([
            'status' => 'escalated',
            'escalated_at' => now(),
            'escalated_by' => Auth::id(),
            'escalation_reason' => $validated['escalation_reason'],
            'escalation_level' => $validated['escalation_level'],
            'escalated_to' => $validated['escalated_to'],
            'urgency_level' => $validated['urgency_level'],
            'additional_context' => $validated['additional_context'],
        ]);

        // Notify escalated user
        $this->notifyEscalatedUser($incident, $validated);

        return redirect()->route('security.incidents.show', $incident)
            ->with('success', 'تم تصعيد الحادث الأمني بنجاح');
    }

    public function investigate(Request $request, SecurityIncident $incident)
    {
        $this->authorize('investigate', $incident);

        $validated = $request->validate([
            'investigation_method' => 'required|in:technical,forensic,comprehensive',
            'investigation_scope' => 'required|string|max:1000',
            'investigation_team' => 'required|array',
            'investigation_team.*.user_id' => 'required|exists:users,id',
            'investigation_team.*.role' => 'required|string|max:100',
            'investigation_plan' => 'required|array',
            'investigation_plan.*.step' => 'required|string|max:500',
            'investigation_plan.*.assigned_to' => 'required|exists:users,id',
            'investigation_plan.*.due_date' => 'required|date',
            'evidence_collection_plan' => 'nullable|array',
            'timeline_requirements' => 'nullable|array',
        ]);

        $investigationData = [
            'investigation_method' => $validated['investigation_method'],
            'investigation_scope' => $validated['investigation_scope'],
            'investigation_team' => $validated['investigation_team'],
            'investigation_plan' => $validated['investigation_plan'],
            'evidence_collection_plan' => $validated['evidence_collection_plan'] ?? [],
            'timeline_requirements' => $validated['timeline_requirements'] ?? [],
            'started_at' => now(),
            'started_by' => Auth::id(),
        ];

        $incident->update([
            'status' => 'under_investigation',
            'investigation_data' => json_encode($investigationData),
        ]);

        return redirect()->route('security.incidents.show', $incident)
            ->with('success', 'تم بدء التحقيق في الحادث الأمني');
    }

    public function report(Request $request, SecurityIncident $incident)
    {
        $this->authorize('report', $incident);

        $validated = $request->validate([
            'reporting_authority' => 'required|string|max:255',
            'reporting_method' => 'required|in:online,phone,email,in_person,mail',
            'report_reference' => 'nullable|string|max:255',
            'reporting_date' => 'required|date',
            'regulatory_requirements' => 'required|array',
            'compliance_obligations' => 'required|array',
            'report_content' => 'required|string|max:5000',
            'supporting_documents' => 'nullable|array',
        ]);

        $reportingData = [
            'reporting_authority' => $validated['reporting_authority'],
            'reporting_method' => $validated['reporting_method'],
            'report_reference' => $validated['report_reference'],
            'reporting_date' => $validated['reporting_date'],
            'regulatory_requirements' => $validated['regulatory_requirements'],
            'compliance_obligations' => $validated['compliance_obligations'],
            'report_content' => $validated['report_content'],
            'supporting_documents' => $validated['supporting_documents'] ?? [],
            'reported_by' => Auth::id(),
            'reported_at' => now(),
        ];

        $incident->update([
            'external_reporting' => true,
            'reporting_data' => json_encode($reportingData),
        ]);

        return redirect()->route('security.incidents.show', $incident)
            ->with('success', 'تم الإبلاغ الخارجي عن الحادث بنجاح');
    }

    public function analytics()
    {
        $user = Auth::user();
        
        $analytics = [
            'incident_trends' => $this->getIncidentTrends($user->id),
            'severity_distribution' => $this->getSeverityDistribution($user->id),
            'type_distribution' => $this->getTypeDistribution($user->id),
            'resolution_times' => $this->getResolutionTimes($user->id),
            'cost_analysis' => $this->getCostAnalysis($user->id),
            'risk_assessment' => $this->getRiskAssessment($user->id),
        ];

        return view('security.incidents.analytics', compact('analytics'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,pdf',
            'date_range' => 'required|in:last_week,last_month,last_quarter,last_year,custom',
            'start_date' => 'nullable|date|required_if:date_range,custom',
            'end_date' => 'nullable|date|required_if:date_range,custom|after_or_equal:start_date',
            'status' => 'nullable|in:active,under_investigation,escalated,resolved,closed',
            'severity' => 'nullable|in:low,medium,high,critical',
            'incident_type' => 'nullable|array',
        ]);

        $incidents = $this->getFilteredIncidents($validated);

        switch ($validated['format']) {
            case 'csv':
                return $this->exportCSV($incidents);
            case 'xlsx':
                return $this->exportExcel($incidents);
            case 'pdf':
                return $this->exportPDF($incidents);
        }
    }

    private function generateIncidentId()
    {
        return 'INC-' . Str::upper(Str::random(8)) . '-' . date('Ymd');
    }

    private function calculateRiskScore($data)
    {
        $score = 0;

        // Base score by severity
        switch ($data['severity']) {
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

        // Additional score by incident type
        switch ($data['incident_type']) {
            case 'data_breach':
                $score += 25;
                break;
            case 'system_compromise':
                $score += 20;
                break;
            case 'unauthorized_access':
                $score += 15;
                break;
            case 'identity_theft':
                $score += 20;
                break;
            case 'fraud_attempt':
                $score += 15;
                break;
            default:
                $score += 10;
        }

        // Impact assessment score
        if (isset($data['impact_assessment'])) {
            $impactScore = 0;
            foreach ($data['impact_assessment'] as $impact) {
                switch ($impact) {
                    case 'critical':
                        $impactScore += 5;
                        break;
                    case 'high':
                        $impactScore += 3;
                        break;
                    case 'medium':
                        $impactScore += 2;
                        break;
                    case 'low':
                        $impactScore += 1;
                        break;
                }
            }
            $score += min($impactScore, 20);
        }

        return min(100, $score);
    }

    private function determinePriorityLevel($severity, $incidentType)
    {
        if ($severity === 'critical') {
            return 'critical';
        } elseif ($severity === 'high') {
            return 'high';
        } elseif ($incidentType === 'data_breach' || $incidentType === 'system_compromise') {
            return 'high';
        } elseif ($severity === 'medium') {
            return 'medium';
        } else {
            return 'low';
        }
    }

    private function triggerAutomatedResponse(SecurityIncident $incident)
    {
        // Implement automated response for critical incidents
        // This could include:
        // - Automatic system lockdown
        // - Alert security team
        // - Backup critical data
        // - Enable additional monitoring
        
        Log::critical('Automated security response triggered', [
            'incident_id' => $incident->id,
            'severity' => $incident->severity,
            'incident_type' => $incident->incident_type,
        ]);
    }

    private function notifyEscalatedUser(SecurityIncident $incident, $data)
    {
        // Implement notification logic for escalated incidents
        Log::info('Incident escalated notification sent', [
            'incident_id' => $incident->id,
            'escalated_to' => $data['escalated_to'],
            'escalation_level' => $data['escalation_level'],
        ]);
    }

    private function getIncidentTrends($userId)
    {
        return SecurityIncident::where('user_id', $userId)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(90))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getSeverityDistribution($userId)
    {
        return SecurityIncident::where('user_id', $userId)
            ->selectRaw('severity, COUNT(*) as count')
            ->groupBy('severity')
            ->get();
    }

    private function getTypeDistribution($userId)
    {
        return SecurityIncident::where('user_id', $userId)
            ->selectRaw('incident_type, COUNT(*) as count')
            ->groupBy('incident_type')
            ->get();
    }

    private function getResolutionTimes($userId)
    {
        return SecurityIncident::where('user_id', $userId)
            ->whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_resolution_hours')
            ->first();
    }

    private function getCostAnalysis($userId)
    {
        return SecurityIncident::where('user_id', $userId)
            ->selectRaw('SUM(CAST(cost_analysis->>"$.direct_costs" AS DECIMAL)) as total_direct_costs, SUM(CAST(cost_analysis->>"$.indirect_costs" AS DECIMAL)) as total_indirect_costs')
            ->first();
    }

    private function getRiskAssessment($userId)
    {
        return [
            'total_risk_score' => SecurityIncident::where('user_id', $userId)
                ->avg('risk_score'),
            'high_risk_incidents' => SecurityIncident::where('user_id', $userId)
                ->where('risk_score', '>', 70)
                ->count(),
            'risk_trends' => $this->getRiskTrends($userId),
        ];
    }

    private function getRiskTrends($userId)
    {
        return SecurityIncident::where('user_id', $userId)
            ->selectRaw('DATE(created_at) as date, AVG(risk_score) as avg_risk')
            ->where('created_at', '>=', now()->subDays(90))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getFilteredIncidents($validated)
    {
        $query = SecurityIncident::where('user_id', Auth::id());

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

        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (isset($validated['severity'])) {
            $query->where('severity', $validated['severity']);
        }

        if (isset($validated['incident_type'])) {
            $query->whereIn('incident_type', $validated['incident_type']);
        }

        return $query->get();
    }

    private function exportCSV($incidents)
    {
        $filename = 'security_incidents_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($incidents) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'ID', 'Incident ID', 'Title', 'Type', 'Severity', 'Status',
                'Created At', 'Resolved At', 'Risk Score'
            ]);

            // Data
            foreach ($incidents as $incident) {
                fputcsv($file, [
                    $incident->id,
                    $incident->incident_id,
                    $incident->title,
                    $incident->incident_type,
                    $incident->severity,
                    $incident->status,
                    $incident->created_at,
                    $incident->resolved_at,
                    $incident->risk_score,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportExcel($incidents)
    {
        // Implementation for Excel export
        return response()->download('security_incidents.xlsx');
    }

    private function exportPDF($incidents)
    {
        // Implementation for PDF export
        return response()->download('security_incidents.pdf');
    }
}
