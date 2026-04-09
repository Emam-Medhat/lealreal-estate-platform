<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SecurityIncident extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'property_id',
        'incident_type',
        'severity',
        'status',
        'title',
        'description',
        'impact_assessment',
        'affected_systems',
        'affected_data',
        'detection_method',
        'reported_by',
        'reported_at',
        'investigated_by',
        'investigated_at',
        'resolved_by',
        'resolved_at',
        'resolution_details',
        'lessons_learned',
        'preventive_measures',
        'evidence',
        'timeline',
        'notifications_sent',
        'escalation_level',
        'external_parties_involved',
        'legal_implications',
        'financial_impact',
        'reputational_impact',
        'compliance_impact',
        'root_cause_analysis',
        'action_items',
        'follow_up_required',
        'follow_up_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'impact_assessment' => 'array',
        'affected_systems' => 'array',
        'affected_data' => 'array',
        'evidence' => 'array',
        'timeline' => 'array',
        'notifications_sent' => 'array',
        'external_parties_involved' => 'array',
        'legal_implications' => 'array',
        'financial_impact' => 'array',
        'reputational_impact' => 'array',
        'compliance_impact' => 'array',
        'root_cause_analysis' => 'array',
        'action_items' => 'array',
        'follow_up_required' => 'boolean',
        'reported_at' => 'datetime',
        'investigated_at' => 'datetime',
        'resolved_at' => 'datetime',
        'follow_up_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'reported_at' => 'datetime',
        'investigated_at' => 'datetime',
        'resolved_at' => 'datetime',
        'follow_up_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function investigatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'investigated_by');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function fraudAlerts(): MorphMany
    {
        return $this->morphMany(FraudAlert::class, 'auditable');
    }

    public function complianceRecords(): MorphMany
    {
        return $this->morphMany(ComplianceRecord::class, 'auditable');
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('incident_type', $type);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeHigh($query)
    {
        return $query->where('severity', 'high');
    }

    public function scopeMedium($query)
    {
        return $query->where('severity', 'medium');
    }

    public function scopeLow($query)
    {
        return $query->where('severity', 'low');
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['reported', 'investigating']);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeRequiresFollowUp($query)
    {
        return $query->where('follow_up_required', true)
            ->where('follow_up_date', '<=', now());
    }

    // Methods
    public function calculateRiskScore(): int
    {
        $score = 0;
        
        // Base score from severity
        switch ($this->severity) {
            case 'critical':
                $score = 90;
                break;
            case 'high':
                $score = 70;
                break;
            case 'medium':
                $score = 50;
                break;
            case 'low':
                $score = 30;
                break;
        }
        
        // Add points for impact factors
        if (!empty($this->impact_assessment)) {
            foreach ($this->impact_assessment as $impact) {
                $score += ($impact['score'] ?? 0);
            }
        }
        
        // Add points for affected systems
        if (!empty($this->affected_systems)) {
            $score += count($this->affected_systems) * 5;
        }
        
        return min(100, $score);
    }

    public function escalate($level, $reason = null): void
    {
        $this->escalation_level = $level;
        
        $this->timeline[] = [
            'action' => 'escalated',
            'level' => $level,
            'reason' => $reason,
            'timestamp' => now(),
            'user_id' => auth()->id(),
        ];
        
        $this->save();
        
        // Send notifications
        $this->sendEscalationNotification($level, $reason);
    }

    public function startInvestigation($investigatorId): void
    {
        $this->status = 'investigating';
        $this->investigated_by = $investigatorId;
        $this->investigated_at = now();
        
        $this->timeline[] = [
            'action' => 'investigation_started',
            'investigator_id' => $investigatorId,
            'timestamp' => now(),
            'user_id' => auth()->id(),
        ];
        
        $this->save();
    }

    public function addTimelineEvent($action, $details = []): void
    {
        $this->timeline[] = array_merge([
            'action' => $action,
            'timestamp' => now(),
            'user_id' => auth()->id(),
        ], $details);
        
        $this->save();
    }

    public function addEvidence($evidenceType, $evidenceData, $description = null): void
    {
        $evidence = [
            'type' => $evidenceType,
            'data' => $evidenceData,
            'description' => $description,
            'added_at' => now(),
            'added_by' => auth()->id(),
        ];
        
        $this->evidence[] = $evidence;
        
        $this->timeline[] = [
            'action' => 'evidence_added',
            'evidence_type' => $evidenceType,
            'timestamp' => now(),
            'user_id' => auth()->id(),
        ];
        
        $this->save();
    }

    public function resolve($resolutionDetails, $lessonsLearned = [], $preventiveMeasures = []): void
    {
        $this->status = 'resolved';
        $this->resolved_by = auth()->id();
        $this->resolved_at = now();
        $this->resolution_details = $resolutionDetails;
        $this->lessons_learned = $lessonsLearned;
        $this->preventive_measures = $preventiveMeasures;
        
        $this->timeline[] = [
            'action' => 'resolved',
            'resolution_details' => $resolutionDetails,
            'timestamp' => now(),
            'user_id' => auth()->id(),
        ];
        
        $this->save();
        
        // Send resolution notifications
        $this->sendResolutionNotification();
    }

    public function addActionItem($action, $assignedTo, $dueDate, $priority = 'medium'): void
    {
        $actionItem = [
            'action' => $action,
            'assigned_to' => $assignedTo,
            'due_date' => $dueDate,
            'priority' => $priority,
            'status' => 'pending',
            'created_at' => now(),
            'created_by' => auth()->id(),
        ];
        
        $this->action_items[] = $actionItem;
        
        $this->timeline[] = [
            'action' => 'action_item_added',
            'action_item' => $action,
            'assigned_to' => $assignedTo,
            'timestamp' => now(),
            'user_id' => auth()->id(),
        ];
        
        $this->save();
    }

    public function updateActionItem($index, $status, $notes = null): void
    {
        if (isset($this->action_items[$index])) {
            $this->action_items[$index]['status'] = $status;
            $this->action_items[$index]['updated_at'] = now();
            $this->action_items[$index]['updated_by'] = auth()->id();
            
            if ($notes) {
                $this->action_items[$index]['notes'] = $notes;
            }
            
            $this->timeline[] = [
                'action' => 'action_item_updated',
                'item_index' => $index,
                'new_status' => $status,
                'timestamp' => now(),
                'user_id' => auth()->id(),
            ];
            
            $this->save();
        }
    }

    public function performRootCauseAnalysis(): array
    {
        $analysis = [
            'incident_type' => $this->incident_type,
            'severity' => $this->severity,
            'affected_systems' => $this->affected_systems,
            'timeline_events' => $this->timeline,
            'evidence_summary' => $this->summarizeEvidence(),
            'potential_causes' => $this->identifyPotentialCauses(),
            'root_cause' => $this->determineRootCause(),
            'contributing_factors' => $this->identifyContributingFactors(),
            'recommendations' => $this->generateRecommendations(),
            'analysis_date' => now(),
        ];
        
        $this->root_cause_analysis = $analysis;
        $this->save();
        
        return $analysis;
    }

    private function summarizeEvidence(): array
    {
        $summary = [];
        
        if (!empty($this->evidence)) {
            foreach ($this->evidence as $evidence) {
                $type = $evidence['type'];
                $summary[$type] = ($summary[$type] ?? 0) + 1;
            }
        }
        
        return $summary;
    }

    private function identifyPotentialCauses(): array
    {
        $causes = [];
        
        // Analyze incident type and affected systems
        switch ($this->incident_type) {
            case 'data_breach':
                $causes = ['weak_passwords', 'unauthorized_access', 'system_vulnerability', 'insider_threat'];
                break;
            case 'malware_attack':
                $causes = ['outdated_software', 'lack_antivirus', 'suspicious_email', 'unsecured_network'];
                break;
            case 'phishing':
                $causes = ['lack_training', 'sophisticated_attack', 'social_engineering', 'weak_filters'];
                break;
            case 'system_failure':
                $causes = ['hardware_failure', 'software_bug', 'configuration_error', 'capacity_issues'];
                break;
            default:
                $causes = ['human_error', 'process_failure', 'technical_issue', 'external_threat'];
        }
        
        return $causes;
    }

    private function determineRootCause(): string
    {
        // This would typically involve more sophisticated analysis
        $causes = $this->identifyPotentialCauses();
        
        // For now, return the most likely cause based on incident type
        switch ($this->incident_type) {
            case 'data_breach':
                return 'unauthorized_access';
            case 'malware_attack':
                return 'outdated_software';
            case 'phishing':
                return 'lack_training';
            case 'system_failure':
                return 'hardware_failure';
            default:
                return 'human_error';
        }
    }

    private function identifyContributingFactors(): array
    {
        $factors = [];
        
        // Analyze timeline and evidence for contributing factors
        if (!empty($this->timeline)) {
            foreach ($this->timeline as $event) {
                if (isset($event['contributing_factor'])) {
                    $factors[] = $event['contributing_factor'];
                }
            }
        }
        
        return array_unique($factors);
    }

    private function generateRecommendations(): array
    {
        $recommendations = [];
        $rootCause = $this->determineRootCause();
        
        switch ($rootCause) {
            case 'weak_passwords':
                $recommendations = ['implement_strong_password_policy', 'enable_two_factor_authentication', 'regular_password_audits'];
                break;
            case 'unauthorized_access':
                $recommendations = ['review_access_controls', 'implement_least_privilege', 'regular_access_audits'];
                break;
            case 'outdated_software':
                $recommendations = ['implement_patch_management', 'regular_security_updates', 'vulnerability_scanning'];
                break;
            case 'lack_training':
                $recommendations = ['security_awareness_training', 'phishing_simulation', 'regular_training_sessions'];
                break;
            default:
                $recommendations = ['review_security_policies', 'enhance_monitoring', 'incident_response_improvement'];
        }
        
        return $recommendations;
    }

    public function calculateFinancialImpact(): array
    {
        $impact = [
            'direct_costs' => 0,
            'indirect_costs' => 0,
            'total_impact' => 0,
            'breakdown' => [],
        ];
        
        if (!empty($this->financial_impact)) {
            foreach ($this->financial_impact as $item) {
                $cost = $item['amount'] ?? 0;
                $category = $item['category'] ?? 'other';
                
                if ($item['type'] === 'direct') {
                    $impact['direct_costs'] += $cost;
                } else {
                    $impact['indirect_costs'] += $cost;
                }
                
                $impact['breakdown'][$category] = ($impact['breakdown'][$category] ?? 0) + $cost;
            }
        }
        
        $impact['total_impact'] = $impact['direct_costs'] + $impact['indirect_costs'];
        
        return $impact;
    }

    public function calculateReputationalImpact(): array
    {
        $impact = [
            'severity' => 'low',
            'affected_stakeholders' => [],
            'mitigation_required' => [],
        ];
        
        if (!empty($this->reputational_impact)) {
            $impact['severity'] = $this->reputational_impact['severity'] ?? 'low';
            $impact['affected_stakeholders'] = $this->reputational_impact['stakeholders'] ?? [];
            $impact['mitigation_required'] = $this->reputational_impact['mitigation'] ?? [];
        }
        
        return $impact;
    }

    public function calculateComplianceImpact(): array
    {
        $impact = [
            'violations' => [],
            'penalties' => 0,
            'remediation_required' => [],
        ];
        
        if (!empty($this->compliance_impact)) {
            $impact['violations'] = $this->compliance_impact['violations'] ?? [];
            $impact['penalties'] = $this->compliance_impact['penalties'] ?? 0;
            $impact['remediation_required'] = $this->compliance_impact['remediation'] ?? [];
        }
        
        return $impact;
    }

    public function generateIncidentReport(): array
    {
        return [
            'incident_id' => $this->id,
            'title' => $this->title,
            'incident_type' => $this->incident_type,
            'severity' => $this->severity,
            'status' => $this->status,
            'risk_score' => $this->calculateRiskScore(),
            'reported_at' => $this->reported_at,
            'investigated_at' => $this->investigated_at,
            'resolved_at' => $this->resolved_at,
            'description' => $this->description,
            'impact_assessment' => $this->impact_assessment,
            'affected_systems' => $this->affected_systems,
            'affected_data' => $this->affected_data,
            'detection_method' => $this->detection_method,
            'timeline' => $this->timeline,
            'evidence' => $this->evidence,
            'resolution_details' => $this->resolution_details,
            'lessons_learned' => $this->lessons_learned,
            'preventive_measures' => $this->preventive_measures,
            'root_cause_analysis' => $this->root_cause_analysis,
            'action_items' => $this->action_items,
            'financial_impact' => $this->calculateFinancialImpact(),
            'reputational_impact' => $this->calculateReputationalImpact(),
            'compliance_impact' => $this->calculateComplianceImpact(),
            'escalation_level' => $this->escalation_level,
            'external_parties_involved' => $this->external_parties_involved,
            'follow_up_required' => $this->follow_up_required,
            'follow_up_date' => $this->follow_up_date,
            'generated_at' => now(),
        ];
    }

    private function sendEscalationNotification($level, $reason): void
    {
        // Implementation for sending escalation notifications
        $this->notifications_sent[] = [
            'type' => 'escalation',
            'level' => $level,
            'reason' => $reason,
            'sent_at' => now(),
            'sent_by' => auth()->id(),
        ];
        
        $this->save();
    }

    private function sendResolutionNotification(): void
    {
        // Implementation for sending resolution notifications
        $this->notifications_sent[] = [
            'type' => 'resolution',
            'sent_at' => now(),
            'sent_by' => auth()->id(),
        ];
        
        $this->save();
    }

    public function isOverdue(): bool
    {
        if ($this->status === 'resolved') {
            return false;
        }
        
        // Consider overdue if not resolved within 30 days for critical incidents
        if ($this->severity === 'critical' && $this->created_at->diffInDays(now()) > 30) {
            return true;
        }
        
        // Consider overdue if not resolved within 60 days for high incidents
        if ($this->severity === 'high' && $this->created_at->diffInDays(now()) > 60) {
            return true;
        }
        
        return false;
    }

    public function getResolutionTime(): int
    {
        if (!$this->resolved_at) {
            return 0;
        }
        
        return $this->created_at->diffInHours($this->resolved_at);
    }

    public static function getIncidentMetrics($filters = []): array
    {
        $query = self::query();
        
        // Apply filters
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        
        if (isset($filters['property_id'])) {
            $query->where('property_id', $filters['property_id']);
        }
        
        if (isset($filters['date_range'])) {
            switch ($filters['date_range']) {
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
            }
        }
        
        $incidents = $query->get();
        
        return [
            'total_incidents' => $incidents->count(),
            'critical_incidents' => $incidents->where('severity', 'critical')->count(),
            'high_incidents' => $incidents->where('severity', 'high')->count(),
            'medium_incidents' => $incidents->where('severity', 'medium')->count(),
            'low_incidents' => $incidents->where('severity', 'low')->count(),
            'open_incidents' => $incidents->whereIn('status', ['reported', 'investigating'])->count(),
            'closed_incidents' => $incidents->where('status', 'resolved')->count(),
            'overdue_incidents' => $incidents->filter(function ($incident) {
                return $incident->isOverdue();
            })->count(),
            'average_resolution_time' => $incidents->where('resolved_at')->avg(function ($incident) {
                return $incident->getResolutionTime();
            }),
            'incidents_by_type' => $incidents->groupBy('incident_type')->map->count(),
            'incidents_by_severity' => $incidents->groupBy('severity')->map->count(),
            'trend' => $incidents->groupBy(function ($incident) {
                return $incident->created_at->format('Y-m');
            })->map->count(),
        ];
    }
}
