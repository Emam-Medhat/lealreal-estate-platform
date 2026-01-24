<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class FraudAlert extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'property_id',
        'alert_type',
        'description',
        'evidence',
        'risk_level',
        'fraud_score',
        'affected_parties',
        'estimated_loss',
        'currency',
        'urgency_level',
        'contact_information',
        'additional_notes',
        'status',
        'auto_detected',
        'detection_method',
        'detection_source',
        'investigation_status',
        'investigation_priority',
        'assigned_to',
        'investigation_notes',
        'investigation_findings',
        'resolution_status',
        'resolution_date',
        'resolution_notes',
        'resolution_actions',
        'prevention_measures',
        'legal_actions',
        'regulatory_reporting',
        'compliance_violations',
        'escalation_level',
        'escalated_to',
        'escalated_at',
        'escalation_reason',
        'notification_sent',
        'notification_recipients',
        'external_reporting',
        'reporting_authority',
        'report_reference',
        'reporting_date',
        'follow_up_required',
        'follow_up_date',
        'follow_up_notes',
        'cost_analysis',
        'impact_assessment',
        'lessons_learned',
        'preventive_actions',
        'metadata',
        'created_by',
        'updated_by',
        'resolved_by',
    ];

    protected $casts = [
        'evidence' => 'array',
        'affected_parties' => 'array',
        'estimated_loss' => 'decimal:2',
        'investigation_findings' => 'array',
        'resolution_actions' => 'array',
        'prevention_measures' => 'array',
        'legal_actions' => 'array',
        'regulatory_reporting' => 'boolean',
        'compliance_violations' => 'array',
        'escalated_at' => 'datetime',
        'notification_sent' => 'boolean',
        'notification_recipients' => 'array',
        'external_reporting' => 'boolean',
        'reporting_date' => 'date',
        'follow_up_required' => 'boolean',
        'follow_up_date' => 'date',
        'cost_analysis' => 'array',
        'impact_assessment' => 'array',
        'lessons_learned' => 'array',
        'preventive_actions' => 'array',
        'metadata' => 'array',
        'auto_detected' => 'boolean',
        'resolution_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'escalated_at',
        'resolution_date',
        'reporting_date',
        'follow_up_date',
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

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function escalatedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalated_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function investigationLogs(): HasMany
    {
        return $this->hasMany(FraudInvestigationLog::class, 'fraud_alert_id');
    }

    public function resolutionActions(): HasMany
    {
        return $this->hasMany(FraudResolutionAction::class, 'fraud_alert_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(FraudNotification::class, 'fraud_alert_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeByRiskLevel($query, $level)
    {
        return $query->where('risk_level', $level);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('alert_type', $type);
    }

    public function scopeHighRisk($query)
    {
        return $query->whereIn('risk_level', ['high', 'critical']);
    }

    public function scopeAutoDetected($query)
    {
        return $query->where('auto_detected', true);
    }

    public function scopePendingInvestigation($query)
    {
        return $query->where('investigation_status', 'pending');
    }

    public function scopeEscalated($query)
    {
        return $query->whereNotNull('escalated_at');
    }

    public function scopeRequiresFollowUp($query)
    {
        return $query->where('follow_up_required', true)
            ->where('follow_up_date', '<=', now());
    }

    // Methods
    public function calculateFraudScore(): int
    {
        $score = 0;

        // Base score by risk level
        switch ($this->risk_level) {
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
        switch ($this->alert_type) {
            case 'financial_fraud':
                $score += 25;
                break;
            case 'identity_theft':
                $score += 20;
                break;
            case 'data_breach':
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
        switch ($this->urgency_level) {
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
        if ($this->estimated_loss > 0) {
            if ($this->estimated_loss > 100000) {
                $score += 20;
            } elseif ($this->estimated_loss > 50000) {
                $score += 15;
            } elseif ($this->estimated_loss > 10000) {
                $score += 10;
            } elseif ($this->estimated_loss > 1000) {
                $score += 5;
            }
        }

        return min(100, $score);
    }

    public function assessRisk(): array
    {
        $riskFactors = [];
        $riskScore = $this->calculateFraudScore();

        // Check fraud score
        if ($riskScore > 80) {
            $riskFactors[] = 'نقطة احتيال مرتفعة جداً';
        } elseif ($riskScore > 60) {
            $riskFactors[] = 'نقطة احتيال مرتفعة';
        } elseif ($riskScore > 40) {
            $riskFactors[] = 'نقطة احتيال متوسطة';
        }

        // Check affected parties
        if ($this->affected_parties && count($this->affected_parties) > 5) {
            $riskFactors[] = 'عدد كبير من الأطراف المتأثرة';
        }

        // Check estimated loss
        if ($this->estimated_loss > 50000) {
            $riskFactors[] = 'خسائر مالية كبيرة';
        }

        // Check urgency
        if ($this->urgency_level === 'critical') {
            $riskFactors[] = 'حالة طارئة';
        }

        return [
            'risk_level' => $this->determineRiskLevel($riskScore, $riskFactors),
            'risk_score' => $riskScore,
            'risk_factors' => $riskFactors,
            'recommendations' => $this->generateRiskRecommendations($riskFactors),
            'immediate_actions' => $this->getImmediateActions($riskScore),
        ];
    }

    public function escalate(string $reason, $escalateTo = null, string $level = 'level_1'): bool
    {
        if (!$escalateTo) {
            $escalateTo = $this->getDefaultEscalationRecipient($level);
        }

        $this->update([
            'escalation_level' => $level,
            'escalated_to' => $escalateTo,
            'escalated_at' => now(),
            'escalation_reason' => $reason,
            'status' => 'escalated',
        ]);

        // Send escalation notification
        $this->sendEscalationNotification($escalateTo, $reason);

        return true;
    }

    public function assignInvestigator($userId, string $priority = 'medium'): bool
    {
        $this->update([
            'assigned_to' => $userId,
            'investigation_status' => 'assigned',
            'investigation_priority' => $priority,
        ]);

        // Send assignment notification
        $this->sendAssignmentNotification($userId, $priority);

        return true;
    }

    public function startInvestigation(): bool
    {
        if (!$this->assigned_to) {
            throw new \Exception('No investigator assigned');
        }

        $this->update([
            'investigation_status' => 'in_progress',
        ]);

        // Create investigation log
        FraudInvestigationLog::create([
            'fraud_alert_id' => $this->id,
            'user_id' => $this->assigned_to,
            'action' => 'investigation_started',
            'notes' => 'Investigation started for fraud alert',
            'timestamp' => now(),
        ]);

        return true;
    }

    public function addInvestigationNote(string $note, array $findings = []): void
    {
        FraudInvestigationLog::create([
            'fraud_alert_id' => $this->id,
            'user_id' => auth()->id(),
            'action' => 'note_added',
            'notes' => $note,
            'findings' => $findings,
            'timestamp' => now(),
        ]);

        // Update investigation findings
        if (!empty($findings)) {
            $currentFindings = $this->investigation_findings ?? [];
            $this->update([
                'investigation_findings' => array_merge($currentFindings, $findings),
            'investigation_notes' => $note,
            'updated_at' => now(),
            'updated_by' => auth()->id(),
            ]);
        }
    }

    public function resolve(string $resolutionNotes, array $resolutionActions = []): bool
    {
        $this->update([
            'status' => 'resolved',
            'resolution_date' => now(),
            'resolution_notes' => $resolutionNotes,
            'resolution_actions' => $resolutionActions,
            'resolved_by' => auth()->id(),
        ]);

        // Create resolution actions
        foreach ($resolutionActions as $action) {
            FraudResolutionAction::create([
                'fraud_alert_id' => $this->id,
                'action' => $action['action'],
                'responsible_party' => $action['responsible_party'] ?? auth()->id(),
                'due_date' => $action['due_date'] ?? now()->addDays(7),
                'status' => 'pending',
                'created_at' => now(),
            ]);
        }

        // Send resolution notification
        $this->sendResolutionNotification();

        return true;
    }

    public function generateReport(): array
    {
        return [
            'alert_id' => $this->id,
            'alert_type' => $this->alert_type,
            'risk_level' => $this->risk_level,
            'fraud_score' => $this->fraud_score,
            'status' => $this->status,
            'description' => $this->description,
            'estimated_loss' => $this->estimated_loss,
            'currency' => $this->currency,
            'created_at' => $this->created_at,
            'resolved_at' => $this->resolved_at,
            'investigation_status' => $this->investigation_status,
            'resolution_status' => $this->resolution_status,
            'affected_parties' => $this->affected_parties,
            'evidence' => $this->evidence,
            'investigation_findings' => $this->investigation_findings,
            'resolution_actions' => $this->resolution_actions,
            'prevention_measures' => $this->prevention_measures,
            'cost_analysis' => $this->cost_analysis,
            'impact_assessment' => $this->impact_assessment,
            'lessons_learned' => $this->lessons_learned,
            'preventive_actions' => $this->preventive_actions,
            'generated_at' => now(),
        ];
    }

    public function notifyStakeholders(): void
    {
        $recipients = $this->notification_recipients ?? [];

        foreach ($recipients as $recipient) {
            $this->sendNotification($recipient['email'], $recipient['type'], $recipient['message']);
        }

        $this->update(['notification_sent' => true]);
    }

    public function scheduleFollowUp(\Carbon\Carbon $date, string $notes = ''): void
    {
        $this->update([
            'follow_up_required' => true,
            'follow_up_date' => $date,
            'follow_up_notes' => $notes,
        ]);
    }

    public function checkFollowUp(): bool
    {
        return $this->follow_up_required && 
               $this->follow_up_date && 
               $this->follow_up_date->isPast() &&
               $this->status !== 'resolved';
    }

    public function calculateTotalLoss(): float
    {
        $totalLoss = $this->estimated_loss;

        // Add recovery costs if available
        if ($this->cost_analysis && isset($this->cost_analysis['recovery_costs'])) {
            $totalLoss -= $this->cost_analysis['recovery_costs'];
        }

        return max(0, $totalLoss);
    }

    public function getInvestigationDuration(): ?\Carbon\Carbon
    {
        if (!$this->investigation_status || $this->investigation_status === 'pending') {
            return null;
        }

        $firstLog = $this->investigationLogs()
            ->orderBy('timestamp')
            ->first();

        if (!$firstLog) {
            return null;
        }

        return $firstLog->timestamp;
    }

    public function getResolutionDuration(): ?\Carbon\Carbon
    {
        if (!$this->resolution_date) {
            return null;
        }

        return $this->resolution_date->diff($this->created_at);
    }

    // Private methods
    private function determineRiskLevel(int $score, array $factors): string
    {
        if ($score >= 80 || in_array('حالة طارئة', $factors)) {
            return 'critical';
        } elseif ($score >= 60 || in_array('نقطة احتيال مرتفعة', $factors)) {
            return 'high';
        } elseif ($score >= 40 || in_array('نقطة احتيال متوسطة', $factors)) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    private function generateRiskRecommendations(array $riskFactors): array
    {
        $recommendations = [];

        foreach ($riskFactors as $factor) {
            switch ($factor) {
                case 'نقطة احتيال مرتفعة جداً':
                    $recommendations[] = 'اتخاذ إجراءات فورية لمنع المزيد من الخسائر';
                    break;
                case 'نقطة احتيال مرتفعة':
                    $recommendations[] = 'تعزيز إجراءات الرقابة والمراقبة';
                    break;
                case 'نقطة احتيال متوسطة':
                    $recommendations[] = 'زيادة التدقيق في العمليات الحساسة';
                    break;
                case 'عدد كبير من الأطراف المتأثرة':
                    $recommendations[] = 'حماية جميع الأطراف المتأثرة';
                    break;
                case 'خسائر مالية كبيرة':
                    $recommendations[] = 'تنفيذ إجراءات لتقليل الخسائر المالية';
                    break;
                case 'حالة طارئة':
                    $recommendations[] = 'اتخاذ إجراءات طارئة للتعامل مع الحالة';
                    break;
            }
        }

        return $recommendations;
    }

    private function getImmediateActions(int $riskScore): array
    {
        $actions = [];

        if ($riskScore > 80) {
            $actions[] = 'إيقاف النظام أو الحسابات المتأثرة';
            $actions[] = 'إبلاغ الإدارة العليا والفرق الأمني';
            $actions[] = 'حماية البيانات والوثائق';
        } elseif ($riskScore > 60) {
            $actions[] = 'تعزيز إجراءات الأمان';
            $actions[] = 'مراجعة صلاحيات الوصول';
            $actions[] = 'بدء التحقيق الفوري';
        } elseif ($riskScore > 40) {
            $actions[] = 'مراقعة الحسابات الحالية';
            $actions[] = 'زيادة المراقبة';
            $actions[] = 'إعداد خطة استجابة';
        }

        return $actions;
    }

    private function getDefaultEscalationRecipient(string $level): int
    {
        // Implement logic to get default escalation recipient based on level
        // This would typically return a user ID
        return 1; // Placeholder
    }

    private function sendEscalationNotification(int $userId, string $reason): void
    {
        // Implement escalation notification logic
        \Log::info('Fraud alert escalated', [
            'fraud_alert_id' => $this->id,
            'escalated_to' => $userId,
            'reason' => $reason,
        ]);
    }

    private function sendAssignmentNotification(int $userId, string $priority): void
    {
        // Implement assignment notification logic
        \Log::info('Fraud alert assigned', [
            'fraud_alert_id' => $this->id,
            'assigned_to' => $userId,
            'priority' => $priority,
        ]);
    }

    private function sendResolutionNotification(): void
    {
        // Implement resolution notification logic
        \Log::info('Fraud alert resolved', [
            'fraud_alert_id' => $this->id,
            'resolved_by' => $this->resolved_by,
        ]);
    }

    private function sendNotification(string $email, string $type, string $message): void
    {
        // Implement notification sending logic
        \Log::info('Notification sent', [
            'email' => $email,
            'type' => $type,
            'message' => $message,
        ]);
    }
}
