<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class AiLeadScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'user_id',
        'scoring_model',
        'total_score',
        'demographic_score',
        'behavioral_score',
        'engagement_score',
        'source_quality_score',
        'timing_score',
        'budget_score',
        'property_match_score',
        'scoring_factors',
        'risk_assessment',
        'conversion_probability',
        'lead_quality_level',
        'priority_level',
        'recommended_actions',
        'next_best_action',
        'optimal_contact_time',
        'ai_model_version',
        'scoring_metadata',
        'confidence_level',
        'status',
        'last_scored_at',
        'score_history',
        'improvement_suggestions',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_score' => 'decimal:2',
        'demographic_score' => 'decimal:2',
        'behavioral_score' => 'decimal:2',
        'engagement_score' => 'decimal:2',
        'source_quality_score' => 'decimal:2',
        'timing_score' => 'decimal:2',
        'budget_score' => 'decimal:2',
        'property_match_score' => 'decimal:2',
        'scoring_factors' => 'array',
        'risk_assessment' => 'array',
        'conversion_probability' => 'decimal:2',
        'recommended_actions' => 'array',
        'next_best_action' => 'array',
        'scoring_metadata' => 'array',
        'confidence_level' => 'decimal:2',
        'last_scored_at' => 'datetime',
        'score_history' => 'array',
        'improvement_suggestions' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the lead that owns the score.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get the user who owns the lead score.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created the score.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the score.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include high-scoring leads.
     */
    public function scopeHighScore($query, $threshold = 80.0)
    {
        return $query->where('total_score', '>=', $threshold);
    }

    /**
     * Scope a query to only include leads by quality level.
     */
    public function scopeByQuality($query, $level)
    {
        return $query->where('lead_quality_level', $level);
    }

    /**
     * Scope a query to only include high-priority leads.
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority_level', ['urgent', 'high']);
    }

    /**
     * Scope a query to only include recently scored leads.
     */
    public function scopeRecentlyScored($query, $hours = 24)
    {
        return $query->where('last_scored_at', '>=', Carbon::now()->subHours($hours));
    }

    /**
     * Get scoring model label in Arabic.
     */
    public function getScoringModelLabelAttribute(): string
    {
        $models = [
            'demographic_behavioral' => 'ديموغرافي وسلوكي',
            'engagement_based' => 'قائم على التفاعل',
            'predictive_ml' => 'تعلم الآلة التنبؤي',
            'hybrid_advanced' => 'هجين متقدم',
            'custom_weighted' => 'موزون مخصص',
            'neural_network' => 'شبكة عصبية',
        ];

        return $models[$this->scoring_model] ?? 'غير معروف';
    }

    /**
     * Get lead quality level label in Arabic.
     */
    public function getLeadQualityLevelLabelAttribute(): string
    {
        $levels = [
            'excellent' => 'ممتاز',
            'very_good' => 'جيد جداً',
            'good' => 'جيد',
            'average' => 'متوسط',
            'poor' => 'ضعيف',
            'very_poor' => 'ضعيف جداً',
        ];

        return $levels[$this->lead_quality_level] ?? 'غير معروف';
    }

    /**
     * Get priority level label in Arabic.
     */
    public function getPriorityLevelLabelAttribute(): string
    {
        $levels = [
            'urgent' => 'عاجل',
            'high' => 'مرتفع',
            'medium' => 'متوسط',
            'low' => 'منخفض',
            'very_low' => 'منخفض جداً',
        ];

        return $levels[$this->priority_level] ?? 'غير معروف';
    }

    /**
     * Get status label in Arabic.
     */
    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'converted' => 'تم التحويل',
            'lost' => 'فقد',
            'archived' => 'مؤرشف',
        ];

        return $statuses[$this->status] ?? 'غير معروف';
    }

    /**
     * Get confidence level text.
     */
    public function getConfidenceLevelTextAttribute(): string
    {
        if ($this->confidence_level >= 0.9) return 'عالي جداً';
        if ($this->confidence_level >= 0.8) return 'عالي';
        if ($this->confidence_level >= 0.7) return 'متوسط';
        if ($this->confidence_level >= 0.6) return 'منخفض';
        return 'منخفض جداً';
    }

    /**
     * Get conversion probability percentage.
     */
    public function getConversionProbabilityPercentageAttribute(): string
    {
        return round($this->conversion_probability * 100, 1) . '%';
    }

    /**
     * Get score breakdown.
     */
    public function getScoreBreakdownAttribute(): array
    {
        return [
            'demographic' => $this->demographic_score,
            'behavioral' => $this->behavioral_score,
            'engagement' => $this->engagement_score,
            'source_quality' => $this->source_quality_score,
            'timing' => $this->timing_score,
            'budget' => $this->budget_score,
            'property_match' => $this->property_match_score,
        ];
    }

    /**
     * Get highest scoring factor.
     */
    public function getHighestScoringFactorAttribute(): array
    {
        $factors = $this->score_breakdown;
        $maxScore = max($factors);
        $factor = array_search($maxScore, $factors);
        
        return [
            'factor' => $factor,
            'score' => $maxScore,
            'label' => $this->getFactorLabel($factor),
        ];
    }

    /**
     * Get lowest scoring factor.
     */
    public function getLowestScoringFactorAttribute(): array
    {
        $factors = $this->score_breakdown;
        $minScore = min($factors);
        $factor = array_search($minScore, $factors);
        
        return [
            'factor' => $factor,
            'score' => $minScore,
            'label' => $this->getFactorLabel($factor),
        ];
    }

    /**
     * Get factor label in Arabic.
     */
    private function getFactorLabel(string $factor): string
    {
        $labels = [
            'demographic' => 'الديموغرافيا',
            'behavioral' => 'السلوك',
            'engagement' => 'التفاعل',
            'source_quality' => 'جودة المصدر',
            'timing' => 'التوقيت',
            'budget' => 'الميزانية',
            'property_match' => 'مطابقة العقار',
        ];

        return $labels[$factor] ?? 'غير معروف';
    }

    /**
     * Check if lead is hot (high score and high conversion probability).
     */
    public function isHotLead(): bool
    {
        return $this->total_score >= 85 && $this->conversion_probability >= 0.7;
    }

    /**
     * Check if lead needs immediate attention.
     */
    public function needsImmediateAttention(): bool
    {
        return $this->priority_level === 'urgent' || 
               ($this->total_score >= 90 && $this->conversion_probability >= 0.8);
    }

    /**
     * Check if score is recent (within last 24 hours).
     */
    public function isRecentScore(): bool
    {
        return $this->last_scored_at && 
               $this->last_scored_at->diffInHours(Carbon::now()) <= 24;
    }

    /**
     * Check if score needs updating.
     */
    public function needsUpdate(): bool
    {
        return !$this->last_scored_at || 
               $this->last_scored_at->diffInDays(Carbon::now()) > 7;
    }

    /**
     * Get risk level.
     */
    public function getRiskLevelAttribute(): string
    {
        $risk = $this->risk_assessment ?? [];
        
        if (isset($risk['risk_score'])) {
            $score = $risk['risk_score'];
            
            if ($score >= 80) return 'مرتفع جداً';
            if ($score >= 60) return 'مرتفع';
            if ($score >= 40) return 'متوسط';
            if ($score >= 20) return 'منخفض';
            return 'منخفض جداً';
        }
        
        return 'غير محدد';
    }

    /**
     * Get optimal contact time formatted.
     */
    public function getOptimalContactTimeFormattedAttribute(): string
    {
        $time = $this->optimal_contact_time ?? [];
        
        if (isset($time['day']) && isset($time['time'])) {
            $days = [
                'sunday' => 'الأحد',
                'monday' => 'الإثنين',
                'tuesday' => 'الثلاثاء',
                'wednesday' => 'الأربعاء',
                'thursday' => 'الخميس',
                'friday' => 'الجمعة',
                'saturday' => 'السبت',
            ];
            
            return $days[$time['day']] . ' ' . $time['time'];
        }
        
        return 'غير محدد';
    }

    /**
     * Get score trend.
     */
    public function getScoreTrendAttribute(): string
    {
        $history = $this->score_history ?? [];
        
        if (count($history) < 2) {
            return 'غير محدد';
        }
        
        $recent = array_slice($history, -2);
        $previous = $recent[0]['score'] ?? 0;
        $current = $recent[1]['score'] ?? 0;
        
        if ($current > $previous * 1.05) return 'تحسن';
        if ($current < $previous * 0.95) return 'تراجع';
        return 'مستقر';
    }

    /**
     * Get score trend percentage.
     */
    public function getScoreTrendPercentageAttribute(): float
    {
        $history = $this->score_history ?? [];
        
        if (count($history) < 2) {
            return 0;
        }
        
        $recent = array_slice($history, -2);
        $previous = $recent[0]['score'] ?? 1;
        $current = $recent[1]['score'] ?? 0;
        
        return round((($current - $previous) / $previous) * 100, 2);
    }

    /**
     * Add score to history.
     */
    public function addToHistory(float $score): void
    {
        $history = $this->score_history ?? [];
        $history[] = [
            'score' => $score,
            'date' => now()->toDateTimeString(),
            'model_version' => $this->ai_model_version,
        ];
        
        // Keep only last 30 entries
        $this->score_history = array_slice($history, -30);
    }

    /**
     * Create a new AI lead score.
     */
    public static function scoreLead(array $data): self
    {
        $leadId = $data['lead_id'];
        $scoringModel = $data['scoring_model'] ?? 'predictive_ml';
        
        // Simulate AI scoring algorithm
        $demographicScore = rand(40, 95);
        $behavioralScore = rand(30, 90);
        $engagementScore = rand(20, 85);
        $sourceQualityScore = rand(50, 90);
        $timingScore = rand(60, 95);
        $budgetScore = rand(35, 85);
        $propertyMatchScore = rand(45, 90);
        
        // Calculate weighted total score
        $weights = [
            'demographic' => 0.15,
            'behavioral' => 0.20,
            'engagement' => 0.25,
            'source_quality' => 0.10,
            'timing' => 0.15,
            'budget' => 0.10,
            'property_match' => 0.05,
        ];
        
        $totalScore = (
            $demographicScore * $weights['demographic'] +
            $behavioralScore * $weights['behavioral'] +
            $engagementScore * $weights['engagement'] +
            $sourceQualityScore * $weights['source_quality'] +
            $timingScore * $weights['timing'] +
            $budgetScore * $weights['budget'] +
            $propertyMatchScore * $weights['property_match']
        );
        
        // Determine quality level
        if ($totalScore >= 90) $qualityLevel = 'excellent';
        elseif ($totalScore >= 80) $qualityLevel = 'very_good';
        elseif ($totalScore >= 70) $qualityLevel = 'good';
        elseif ($totalScore >= 60) $qualityLevel = 'average';
        elseif ($totalScore >= 50) $qualityLevel = 'poor';
        else $qualityLevel = 'very_poor';
        
        // Determine priority level
        if ($totalScore >= 85) $priorityLevel = 'urgent';
        elseif ($totalScore >= 75) $priorityLevel = 'high';
        elseif ($totalScore >= 65) $priorityLevel = 'medium';
        elseif ($totalScore >= 55) $priorityLevel = 'low';
        else $priorityLevel = 'very_low';
        
        // Calculate conversion probability
        $conversionProbability = min(0.95, ($totalScore / 100) * 1.2);
        
        // Generate scoring factors
        $scoringFactors = [
            'demographic_factors' => [
                'age_group' => rand(60, 90) / 100,
                'income_level' => rand(50, 85) / 100,
                'location_match' => rand(70, 95) / 100,
                'family_size' => rand(40, 80) / 100,
            ],
            'behavioral_factors' => [
                'website_visits' => rand(2, 15),
                'page_views' => rand(5, 25),
                'time_on_site' => rand(120, 1800), // seconds
                'return_visits' => rand(0, 5),
            ],
            'engagement_factors' => [
                'email_opens' => rand(60, 95) / 100,
                'click_through_rate' => rand(10, 40) / 100,
                'form_completions' => rand(1, 5),
                'social_engagement' => rand(20, 70) / 100,
            ],
        ];
        
        // Generate risk assessment
        $riskScore = max(0, 100 - $totalScore + rand(-10, 10));
        $riskAssessment = [
            'risk_score' => $riskScore,
            'risk_factors' => [
                'budget_mismatch' => $budgetScore < 50,
                'timing_issues' => $timingScore < 60,
                'low_engagement' => $engagementScore < 40,
                'poor_source' => $sourceQualityScore < 60,
            ],
            'mitigation_strategies' => [
                'improve_nurturing' => $engagementScore < 60,
                'budget_qualification' => $budgetScore < 70,
                'timing_optimization' => $timingScore < 70,
            ],
        ];
        
        // Generate recommended actions
        $recommendedActions = [];
        if ($engagementScore < 60) {
            $recommendedActions[] = 'زيادة حملات التغذية';
        }
        if ($budgetScore < 70) {
            $recommendedActions[] = 'تأهيل الميزانية';
        }
        if ($timingScore < 70) {
            $recommendedActions[] = 'تحسين توقيت التواصل';
        }
        if ($totalScore >= 80) {
            $recommendedActions[] = 'تسريع عملية البيع';
        }
        
        // Generate next best action
        $nextBestAction = [
            'action' => $totalScore >= 80 ? 'immediate_contact' : 'nurturing_campaign',
            'priority' => $priorityLevel,
            'timing' => $totalScore >= 85 ? 'asap' : 'within_24h',
            'channel' => $engagementScore >= 70 ? 'phone' : 'email',
        ];
        
        // Generate optimal contact time
        $optimalContactTime = [
            'day' => ['monday', 'tuesday', 'wednesday', 'thursday'][array_rand(['monday', 'tuesday', 'wednesday', 'thursday'])],
            'time' => ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00'][array_rand(['09:00', '10:00', '11:00', '14:00', '15:00', '16:00'])],
            'timezone' => 'Asia/Riyadh',
        ];
        
        // Generate improvement suggestions
        $improvementSuggestions = [];
        if ($demographicScore < 70) {
            $improvementSuggestions[] = 'جمع بيانات ديموغرافية أكثر دقة';
        }
        if ($behavioralScore < 70) {
            $improvementSuggestions[] = 'تحليل السلوك الرقمي بشكل أعمق';
        }
        if ($engagementScore < 70) {
            $improvementSuggestions[] = 'تحسين استراتيجيات التفاعل';
        }

        $leadScore = static::create([
            'lead_id' => $leadId,
            'user_id' => $data['user_id'] ?? auth()->id(),
            'scoring_model' => $scoringModel,
            'total_score' => round($totalScore, 2),
            'demographic_score' => round($demographicScore, 2),
            'behavioral_score' => round($behavioralScore, 2),
            'engagement_score' => round($engagementScore, 2),
            'source_quality_score' => round($sourceQualityScore, 2),
            'timing_score' => round($timingScore, 2),
            'budget_score' => round($budgetScore, 2),
            'property_match_score' => round($propertyMatchScore, 2),
            'scoring_factors' => $scoringFactors,
            'risk_assessment' => $riskAssessment,
            'conversion_probability' => round($conversionProbability, 2),
            'lead_quality_level' => $qualityLevel,
            'priority_level' => $priorityLevel,
            'recommended_actions' => $recommendedActions,
            'next_best_action' => $nextBestAction,
            'optimal_contact_time' => $optimalContactTime,
            'ai_model_version' => '9.2.3',
            'scoring_metadata' => [
                'processing_time' => rand(0.3, 1.8) . 's',
                'data_points_analyzed' => rand(50, 200),
                'model_confidence' => rand(75, 95) / 100,
                'scoring_date' => now()->toDateTimeString(),
                'algorithm_version' => 'ensemble_v5',
            ],
            'confidence_level' => rand(75, 95) / 100,
            'status' => 'active',
            'last_scored_at' => now(),
            'score_history' => [],
            'improvement_suggestions' => $improvementSuggestions,
            'created_by' => $data['created_by'] ?? auth()->id(),
        ]);
        
        // Add initial score to history
        $leadScore->addToHistory($totalScore);
        $leadScore->save();
        
        return $leadScore;
    }

    /**
     * Update lead score.
     */
    public function updateScore(): bool
    {
        // Add current score to history
        $this->addToHistory($this->total_score);
        
        // Simulate score recalculation
        $scoreChange = rand(-5, 10) / 100;
        $newScore = max(0, min(100, $this->total_score * (1 + $scoreChange)));
        
        $this->total_score = round($newScore, 2);
        $this->last_scored_at = now();
        
        // Recalculate quality and priority levels
        if ($this->total_score >= 90) {
            $this->lead_quality_level = 'excellent';
            $this->priority_level = 'urgent';
        } elseif ($this->total_score >= 80) {
            $this->lead_quality_level = 'very_good';
            $this->priority_level = 'high';
        } elseif ($this->total_score >= 70) {
            $this->lead_quality_level = 'good';
            $this->priority_level = 'medium';
        } else {
            $this->lead_quality_level = 'average';
            $this->priority_level = 'low';
        }
        
        // Recalculate conversion probability
        $this->conversion_probability = min(0.95, ($this->total_score / 100) * 1.2);
        
        return $this->save();
    }

    /**
     * Get lead score summary for dashboard.
     */
    public function getDashboardSummary(): array
    {
        return [
            'id' => $this->id,
            'lead_id' => $this->lead_id,
            'total_score' => $this->total_score,
            'quality_level' => $this->lead_quality_level_label,
            'priority_level' => $this->priority_level_label,
            'conversion_probability' => $this->conversion_probability_percentage,
            'confidence_level' => $this->confidence_level_text,
            'is_hot_lead' => $this->isHotLead(),
            'needs_attention' => $this->needsImmediateAttention(),
            'risk_level' => $this->risk_level,
            'score_trend' => $this->score_trend,
            'last_scored' => $this->last_scored_at?->format('Y-m-d H:i'),
            'is_recent' => $this->isRecentScore(),
        ];
    }

    /**
     * Get detailed scoring report.
     */
    public function getDetailedReport(): array
    {
        return [
            'basic_info' => [
                'lead_id' => $this->lead_id,
                'scoring_model' => $this->scoring_model_label,
                'total_score' => $this->total_score,
                'quality_level' => $this->lead_quality_level_label,
                'priority_level' => $this->priority_level_label,
                'status' => $this->status_label,
                'confidence_level' => $this->confidence_level_text,
            ],
            'score_breakdown' => [
                'scores' => $this->score_breakdown,
                'highest_factor' => $this->highest_scoring_factor,
                'lowest_factor' => $this->lowest_scoring_factor,
            ],
            'analysis' => [
                'scoring_factors' => $this->scoring_factors,
                'risk_assessment' => $this->risk_assessment,
                'risk_level' => $this->risk_level,
            ],
            'predictions' => [
                'conversion_probability' => $this->conversion_probability_percentage,
                'recommended_actions' => $this->recommended_actions,
                'next_best_action' => $this->next_best_action,
                'optimal_contact_time' => $this->optimal_contact_time_formatted,
            ],
            'performance' => [
                'score_trend' => $this->score_trend,
                'trend_percentage' => $this->score_trend_percentage,
                'score_history' => $this->score_history,
                'improvement_suggestions' => $this->improvement_suggestions,
            ],
            'metadata' => [
                'last_scored_at' => $this->last_scored_at?->format('Y-m-d H:i:s'),
                'is_recent_score' => $this->isRecentScore(),
                'needs_update' => $this->needsUpdate(),
                'is_hot_lead' => $this->isHotLead(),
                'needs_immediate_attention' => $this->needsImmediateAttention(),
            ],
        ];
    }
}
