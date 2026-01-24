<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class AiFraudAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'alert_type',
        'risk_level',
        'confidence_score',
        'description',
        'evidence',
        'detection_method',
        'fraud_indicators',
        'risk_factors',
        'affected_parties',
        'financial_impact',
        'timeline',
        'investigation_status',
        'investigator_id',
        'investigation_notes',
        'resolution_status',
        'resolution_details',
        'preventive_measures',
        'ai_model_version',
        'detection_metadata',
        'processing_time',
        'false_positive',
        'verified_by',
        'verified_at',
        'escalated',
        'escalated_at',
        'escalated_to',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'evidence' => 'array',
        'fraud_indicators' => 'array',
        'risk_factors' => 'array',
        'affected_parties' => 'array',
        'financial_impact' => 'array',
        'timeline' => 'array',
        'investigation_notes' => 'array',
        'resolution_details' => 'array',
        'preventive_measures' => 'array',
        'detection_metadata' => 'array',
        'processing_time' => 'decimal:3',
        'confidence_score' => 'decimal:2',
        'false_positive' => 'boolean',
        'verified_at' => 'datetime',
        'escalated' => 'boolean',
        'escalated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the property associated with the alert.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the user associated with the alert.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the investigator assigned to the alert.
     */
    public function investigator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'investigator_id');
    }

    /**
     * Get the user who verified the alert.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the user who created the alert.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the alert.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include high-risk alerts.
     */
    public function scopeHighRisk($query)
    {
        return $query->whereIn('risk_level', ['high', 'critical']);
    }

    /**
     * Scope a query to only include alerts by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('alert_type', $type);
    }

    /**
     * Scope a query to only include alerts by investigation status.
     */
    public function scopeByInvestigationStatus($query, $status)
    {
        return $query->where('investigation_status', $status);
    }

    /**
     * Scope a query to only include unverified alerts.
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('verified_at');
    }

    /**
     * Scope a query to only include escalated alerts.
     */
    public function scopeEscalated($query)
    {
        return $query->where('escalated', true);
    }

    /**
     * Scope a query to only include recent alerts.
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', Carbon::now()->subHours($hours));
    }

    /**
     * Get alert type label in Arabic.
     */
    public function getAlertTypeLabelAttribute(): string
    {
        $types = [
            'price_manipulation' => 'تلاعب بالأسعار',
            'fake_listing' => 'إعلان وهمي',
            'identity_fraud' => 'احتيال هوية',
            'document_forgery' => 'تزوير وثائق',
            'money_laundering' => 'غسيل أموال',
            'multiple_listing' => 'إعلانات متعددة',
            'phantom_property' => 'عقار وهمي',
            'title_fraud' => 'احتيال في الملكية',
        ];

        return $types[$this->alert_type] ?? 'غير معروف';
    }

    /**
     * Get risk level label in Arabic.
     */
    public function getRiskLevelLabelAttribute(): string
    {
        $levels = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'مرتفع',
            'critical' => 'حرج',
        ];

        return $levels[$this->risk_level] ?? 'غير معروف';
    }

    /**
     * Get investigation status label in Arabic.
     */
    public function getInvestigationStatusLabelAttribute(): string
    {
        $statuses = [
            'pending' => 'قيد الانتظار',
            'assigned' => 'تم التعيين',
            'investigating' => 'قيد التحقيق',
            'evidence_collection' => 'جمع الأدلة',
            'review' => 'قيد المراجعة',
            'completed' => 'مكتمل',
            'closed' => 'مغلق',
        ];

        return $statuses[$this->investigation_status] ?? 'غير معروف';
    }

    /**
     * Get resolution status label in Arabic.
     */
    public function getResolutionStatusLabelAttribute(): string
    {
        $statuses = [
            'pending' => 'قيد الانتظار',
            'confirmed_fraud' => 'تم تأكيد الاحتيال',
            'false_positive' => 'إيجابية كاذبة',
            'insufficient_evidence' => 'أدلة غير كافية',
            'resolved' => 'تم الحل',
            'dismissed' => 'تم رفضه',
        ];

        return $statuses[$this->resolution_status] ?? 'غير معروف';
    }

    /**
     * Get confidence level text.
     */
    public function getConfidenceLevelTextAttribute(): string
    {
        if ($this->confidence_score >= 0.9) return 'عالي جداً';
        if ($this->confidence_score >= 0.8) return 'عالي';
        if ($this->confidence_score >= 0.7) return 'متوسط';
        if ($this->confidence_score >= 0.6) return 'منخفض';
        return 'منخفض جداً';
    }

    /**
     * Get detection method label in Arabic.
     */
    public function getDetectionMethodLabelAttribute(): string
    {
        $methods = [
            'ai_pattern_recognition' => 'التعرف على الأنماط بالذكاء الاصطناعي',
            'manual_review' => 'مراجعة يدوية',
            'user_report' => 'بلاغ مستخدم',
            'automated_monitoring' => 'مراقبة آلية',
            'data_analysis' => 'تحليل البيانات',
            'behavioral_analysis' => 'التحليل السلوكي',
        ];

        return $methods[$this->detection_method] ?? 'غير معروف';
    }

    /**
     * Get financial impact summary.
     */
    public function getFinancialImpactSummaryAttribute(): string
    {
        $impact = $this->financial_impact ?? [];
        
        if (isset($impact['estimated_loss'])) {
            return number_format($impact['estimated_loss'], 2) . ' ريال';
        }
        
        return 'غير محدد';
    }

    /**
     * Get affected parties count.
     */
    public function getAffectedPartiesCountAttribute(): int
    {
        return count($this->affected_parties ?? []);
    }

    /**
     * Get fraud indicators count.
     */
    public function getFraudIndicatorsCountAttribute(): int
    {
        return count($this->fraud_indicators ?? []);
    }

    /**
     * Get risk factors count.
     */
    public function getRiskFactorsCountAttribute(): int
    {
        return count($this->risk_factors ?? []);
    }

    /**
     * Check if alert is critical.
     */
    public function isCritical(): bool
    {
        return $this->risk_level === 'critical' || 
               ($this->confidence_score >= 0.9 && $this->risk_level === 'high');
    }

    /**
     * Check if alert needs immediate attention.
     */
    public function needsImmediateAttention(): bool
    {
        return $this->isCritical() || 
               ($this->risk_level === 'high' && $this->confidence_score >= 0.8);
    }

    /**
     * Check if alert is verified.
     */
    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }

    /**
     * Check if alert is confirmed fraud.
     */
    public function isConfirmedFraud(): bool
    {
        return $this->resolution_status === 'confirmed_fraud';
    }

    /**
     * Check if alert is false positive.
     */
    public function isFalsePositive(): bool
    {
        return $this->false_positive || $this->resolution_status === 'false_positive';
    }

    /**
     * Get investigation urgency.
     */
    public function getInvestigationUrgencyAttribute(): string
    {
        if ($this->isCritical()) return 'عاجل جداً';
        if ($this->needsImmediateAttention()) return 'عاجل';
        if ($this->risk_level === 'medium') return 'عادي';
        return 'منخفض';
    }

    /**
     * Get time since creation.
     */
    public function getTimeSinceCreationAttribute(): string
    {
        $diff = $this->created_at->diffForHumans(now(), true);
        
        return $diff;
    }

    /**
     * Assign investigator.
     */
    public function assignInvestigator(int $investigatorId): bool
    {
        $this->investigator_id = $investigatorId;
        $this->investigation_status = 'assigned';
        
        return $this->save();
    }

    /**
     * Start investigation.
     */
    public function startInvestigation(): bool
    {
        $this->investigation_status = 'investigating';
        
        return $this->save();
    }

    /**
     * Add investigation note.
     */
    public function addInvestigationNote(string $note): bool
    {
        $notes = $this->investigation_notes ?? [];
        $notes[] = [
            'note' => $note,
            'timestamp' => now()->toDateTimeString(),
            'added_by' => auth()->id(),
        ];
        
        $this->investigation_notes = $notes;
        
        return $this->save();
    }

    /**
     * Verify alert.
     */
    public function verify(int $verifiedBy, bool $isFalsePositive = false): bool
    {
        $this->verified_by = $verifiedBy;
        $this->verified_at = now();
        $this->false_positive = $isFalsePositive;
        
        if ($isFalsePositive) {
            $this->resolution_status = 'false_positive';
        }
        
        return $this->save();
    }

    /**
     * Confirm fraud.
     */
    public function confirmFraud(array $resolutionDetails): bool
    {
        $this->resolution_status = 'confirmed_fraud';
        $this->resolution_details = $resolutionDetails;
        $this->investigation_status = 'completed';
        
        return $this->save();
    }

    /**
     * Dismiss alert.
     */
    public function dismiss(string $reason): bool
    {
        $this->resolution_status = 'dismissed';
        $this->resolution_details = [
            'reason' => $reason,
            'dismissed_by' => auth()->id(),
            'dismissed_at' => now()->toDateTimeString(),
        ];
        $this->investigation_status = 'closed';
        
        return $this->save();
    }

    /**
     * Escalate alert.
     */
    public function escalate(int $escalatedTo): bool
    {
        $this->escalated = true;
        $this->escalated_at = now();
        $this->escalated_to = $escalatedTo;
        
        return $this->save();
    }

    /**
     * Create a new AI fraud alert.
     */
    public static function createAlert(array $data): self
    {
        $alertType = $data['alert_type'] ?? 'fake_listing';
        $riskLevel = $data['risk_level'] ?? 'medium';
        $detectionMethod = $data['detection_method'] ?? 'ai_pattern_recognition';
        
        // Generate fraud indicators based on alert type
        $fraudIndicators = static::generateFraudIndicators($alertType);
        
        // Generate risk factors
        $riskFactors = [
            [
                'factor' => 'unusual_pricing',
                'description' => 'أسعار غير معقولة',
                'severity' => rand(3, 8),
            ],
            [
                'factor' => 'suspicious_patterns',
                'description' => 'أنماط مشبوهة',
                'severity' => rand(2, 7),
            ],
            [
                'factor' => 'information_inconsistency',
                'description' => 'تناقض في المعلومات',
                'severity' => rand(4, 9),
            ],
        ];
        
        // Generate evidence
        $evidence = [
            [
                'type' => 'listing_data',
                'description' => 'بيانات الإعلان المشبوهة',
                'timestamp' => now()->subHours(rand(1, 24))->toDateTimeString(),
                'source' => 'automated_monitoring',
            ],
            [
                'type' => 'user_behavior',
                'description' => 'سلوك المستخدم المشبوه',
                'timestamp' => now()->subHours(rand(1, 12))->toDateTimeString(),
                'source' => 'behavioral_analysis',
            ],
        ];
        
        // Generate affected parties
        $affectedParties = [
            [
                'type' => 'potential_buyers',
                'count' => rand(5, 50),
                'risk_level' => 'medium',
            ],
            [
                'type' => 'platform_users',
                'count' => rand(100, 1000),
                'risk_level' => 'low',
            ],
        ];
        
        // Generate financial impact
        $financialImpact = [
            'estimated_loss' => rand(10000, 500000),
            'potential_scams' => rand(1, 20),
            'currency' => 'SAR',
            'impact_scope' => ['individual', 'platform'],
        ];
        
        // Generate timeline
        $timeline = [
            [
                'event' => 'suspicious_activity_detected',
                'timestamp' => now()->subHours(rand(1, 48))->toDateTimeString(),
                'description' => 'تم اكتشاف نشاط مشبوه',
            ],
            [
                'event' => 'alert_generated',
                'timestamp' => now()->toDateTimeString(),
                'description' => 'تم إنشاء تنبيه الاحتيال',
            ],
        ];
        
        // Generate preventive measures
        $preventiveMeasures = [
            [
                'measure' => 'enhanced_verification',
                'description' => 'تعزيز التحقق من الهويات',
                'priority' => 'high',
                'implementation_time' => 'immediate',
            ],
            [
                'measure' => 'monitoring_increase',
                'description' => 'زيادة المراقبة والتحليل',
                'priority' => 'medium',
                'implementation_time' => '1_week',
            ],
        ];

        return static::create([
            'property_id' => $data['property_id'] ?? null,
            'user_id' => $data['user_id'] ?? null,
            'alert_type' => $alertType,
            'risk_level' => $riskLevel,
            'confidence_score' => rand(70, 95) / 100,
            'description' => $data['description'] ?? 'تم اكتشاف نشاط احتيالي محتمل',
            'evidence' => $evidence,
            'detection_method' => $detectionMethod,
            'fraud_indicators' => $fraudIndicators,
            'risk_factors' => $riskFactors,
            'affected_parties' => $affectedParties,
            'financial_impact' => $financialImpact,
            'timeline' => $timeline,
            'investigation_status' => 'pending',
            'investigator_id' => null,
            'investigation_notes' => [],
            'resolution_status' => 'pending',
            'resolution_details' => [],
            'preventive_measures' => $preventiveMeasures,
            'ai_model_version' => '11.6.2',
            'detection_metadata' => [
                'processing_time' => rand(0.8, 3.2) . 's',
                'data_sources_analyzed' => ['listings', 'user_profiles', 'transactions', 'communications'],
                'pattern_matches' => rand(3, 15),
                'confidence_factors' => [
                    'pattern_strength' => rand(70, 95) / 100,
                    'data_quality' => rand(80, 98) / 100,
                    'historical_accuracy' => rand(85, 96) / 100,
                ],
                'detection_timestamp' => now()->toDateTimeString(),
            ],
            'processing_time' => rand(0.8, 3.2),
            'false_positive' => false,
            'verified_by' => null,
            'verified_at' => null,
            'escalated' => false,
            'escalated_at' => null,
            'escalated_to' => null,
            'created_by' => $data['created_by'] ?? auth()->id(),
        ]);
    }

    /**
     * Generate fraud indicators based on alert type.
     */
    private static function generateFraudIndicators(string $alertType): array
    {
        $indicators = [
            'price_manipulation' => [
                ['indicator' => 'unusually_low_price', 'severity' => 8],
                ['indicator' => 'rapid_price_changes', 'severity' => 7],
                ['indicator' => 'market_deviation', 'severity' => 6],
            ],
            'fake_listing' => [
                ['indicator' => 'stolen_images', 'severity' => 9],
                ['indicator' => 'fake_contact_info', 'severity' => 8],
                ['indicator' => 'inconsistent_details', 'severity' => 7],
            ],
            'identity_fraud' => [
                ['indicator' => 'fake_documents', 'severity' => 9],
                ['indicator' => 'identity_mismatch', 'severity' => 8],
                ['indicator' => 'suspicious_behavior', 'severity' => 6],
            ],
            'document_forgery' => [
                ['indicator' => 'altered_documents', 'severity' => 9],
                ['indicator' => 'invalid_signatures', 'severity' => 8],
                ['indicator' => 'template_usage', 'severity' => 7],
            ],
            'money_laundering' => [
                ['indicator' => 'unusual_transaction_patterns', 'severity' => 8],
                ['indicator' => 'rapid_property_flipping', 'severity' => 7],
                ['indicator' => 'shell_companies', 'severity' => 9],
            ],
        ];

        return $indicators[$alertType] ?? $indicators['fake_listing'];
    }

    /**
     * Get alert summary for dashboard.
     */
    public function getDashboardSummary(): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'user_id' => $this->user_id,
            'alert_type' => $this->alert_type_label,
            'risk_level' => $this->risk_level_label,
            'confidence_score' => $this->confidence_score,
            'confidence_level' => $this->confidence_level_text,
            'investigation_status' => $this->investigation_status_label,
            'resolution_status' => $this->resolution_status_label,
            'is_critical' => $this->isCritical(),
            'needs_immediate_attention' => $this->needsImmediateAttention(),
            'is_verified' => $this->isVerified(),
            'is_confirmed_fraud' => $this->isConfirmedFraud(),
            'is_false_positive' => $this->isFalsePositive(),
            'time_since_creation' => $this->time_since_creation,
            'investigator_id' => $this->investigator_id,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
        ];
    }

    /**
     * Get detailed alert report.
     */
    public function getDetailedReport(): array
    {
        return [
            'basic_info' => [
                'id' => $this->id,
                'alert_type' => $this->alert_type_label,
                'risk_level' => $this->risk_level_label,
                'confidence_score' => $this->confidence_score,
                'confidence_level' => $this->confidence_level_text,
                'detection_method' => $this->detection_method_label,
                'description' => $this->description,
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'time_since_creation' => $this->time_since_creation,
            ],
            'investigation' => [
                'investigation_status' => $this->investigation_status_label,
                'investigator_id' => $this->investigator_id,
                'investigation_notes' => $this->investigation_notes,
                'investigation_urgency' => $this->investigation_urgency,
            ],
            'evidence' => [
                'fraud_indicators' => $this->fraud_indicators,
                'indicators_count' => $this->fraud_indicators_count,
                'evidence' => $this->evidence,
                'risk_factors' => $this->risk_factors,
                'risk_factors_count' => $this->risk_factors_count,
            ],
            'impact' => [
                'affected_parties' => $this->affected_parties,
                'affected_parties_count' => $this->affected_parties_count,
                'financial_impact' => $this->financial_impact,
                'financial_impact_summary' => $this->financial_impact_summary,
            ],
            'timeline' => [
                'timeline' => $this->timeline,
            ],
            'resolution' => [
                'resolution_status' => $this->resolution_status_label,
                'resolution_details' => $this->resolution_details,
                'preventive_measures' => $this->preventive_measures,
                'verified_by' => $this->verified_by,
                'verified_at' => $this->verified_at?->format('Y-m-d H:i:s'),
                'is_verified' => $this->isVerified(),
                'is_confirmed_fraud' => $this->isConfirmedFraud(),
                'is_false_positive' => $this->isFalsePositive(),
            ],
            'escalation' => [
                'escalated' => $this->escalated,
                'escalated_at' => $this->escalated_at?->format('Y-m-d H:i:s'),
                'escalated_to' => $this->escalated_to,
            ],
            'metadata' => [
                'ai_model_version' => $this->ai_model_version,
                'detection_metadata' => $this->detection_metadata,
                'processing_time' => $this->processing_time . 's',
            ],
        ];
    }
}
