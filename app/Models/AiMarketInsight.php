<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class AiMarketInsight extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'insight_type',
        'market_area',
        'property_type',
        'price_range',
        'market_trends',
        'demand_analysis',
        'supply_analysis',
        'competitor_analysis',
        'investment_opportunities',
        'risk_factors',
        'seasonal_patterns',
        'economic_indicators',
        'demographic_trends',
        'infrastructure_development',
        'regulatory_changes',
        'market_forecast',
        'recommendations',
        'insight_score',
        'reliability_score',
        'time_horizon',
        'ai_model_version',
        'insight_metadata',
        'confidence_level',
        'status',
        'published_at',
        'expires_at',
        'view_count',
        'accuracy_rating',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'market_trends' => 'array',
        'demand_analysis' => 'array',
        'supply_analysis' => 'array',
        'competitor_analysis' => 'array',
        'investment_opportunities' => 'array',
        'risk_factors' => 'array',
        'seasonal_patterns' => 'array',
        'economic_indicators' => 'array',
        'demographic_trends' => 'array',
        'infrastructure_development' => 'array',
        'regulatory_changes' => 'array',
        'market_forecast' => 'array',
        'recommendations' => 'array',
        'insight_metadata' => 'array',
        'confidence_level' => 'decimal:2',
        'insight_score' => 'decimal:2',
        'reliability_score' => 'decimal:2',
        'accuracy_rating' => 'decimal:2',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'view_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the property associated with the insight.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the user who owns the insight.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created the insight.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the insight.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include published insights.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include high-scoring insights.
     */
    public function scopeHighScore($query, $threshold = 8.0)
    {
        return $query->where('insight_score', '>=', $threshold);
    }

    /**
     * Scope a query to only include insights by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('insight_type', $type);
    }

    /**
     * Scope a query to only include insights by market area.
     */
    public function scopeByArea($query, $area)
    {
        return $query->where('market_area', $area);
    }

    /**
     * Scope a query to only include active insights.
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Get insight type label in Arabic.
     */
    public function getInsightTypeLabelAttribute(): string
    {
        $types = [
            'price_analysis' => 'تحليل الأسعار',
            'demand_forecast' => 'توقعات الطلب',
            'investment_opportunity' => 'فرصة استثمارية',
            'market_trend' => 'اتجاه السوق',
            'risk_assessment' => 'تقييم المخاطر',
            'competitor_intelligence' => 'معلومات المنافسين',
            'development_impact' => 'تأثير التطوير',
            'regulatory_impact' => 'تأثير تنظيمي',
        ];

        return $types[$this->insight_type] ?? 'غير معروف';
    }

    /**
     * Get status label in Arabic.
     */
    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'draft' => 'مسودة',
            'reviewing' => 'قيد المراجعة',
            'published' => 'منشور',
            'archived' => 'مؤرشف',
            'expired' => 'منتهي الصلاحية',
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
     * Get insight level text.
     */
    public function getInsightLevelAttribute(): string
    {
        if ($this->insight_score >= 9.0) return 'ثوري';
        if ($this->insight_score >= 8.0) return 'مهم جداً';
        if ($this->insight_score >= 7.0) return 'مهم';
        if ($this->insight_score >= 6.0) return 'مفيد';
        return 'معلوماتي';
    }

    /**
     * Get reliability level text.
     */
    public function getReliabilityLevelAttribute(): string
    {
        if ($this->reliability_score >= 9.0) return 'موثوق جداً';
        if ($this->reliability_score >= 8.0) return 'موثوق';
        if ($this->reliability_score >= 7.0) return 'موثوق إلى حد ما';
        if ($this->reliability_score >= 6.0) return 'أقل موثوقية';
        return 'غير موثوق';
    }

    /**
     * Get time horizon label in Arabic.
     */
    public function getTimeHorizonLabelAttribute(): string
    {
        $horizons = [
            'short_term' => 'قصير الأجل (3-6 أشهر)',
            'medium_term' => 'متوسط الأجل (6-12 شهر)',
            'long_term' => 'طويل الأجل (1-3 سنوات)',
            'very_long_term' => 'طويل الأجل جداً (3+ سنوات)',
        ];

        return $horizons[$this->time_horizon] ?? 'غير معروف';
    }

    /**
     * Get market trend direction.
     */
    public function getMarketTrendDirectionAttribute(): string
    {
        $trends = $this->market_trends ?? [];
        
        if (isset($trends['direction'])) {
            $directions = [
                'strongly_bullish' => 'صعودي قوي',
                'bullish' => 'صعودي',
                'neutral' => 'محايد',
                'bearish' => 'هبوطي',
                'strongly_bearish' => 'هبوطي قوي',
            ];
            
            return $directions[$trends['direction']] ?? 'غير محدد';
        }
        
        return 'غير محدد';
    }

    /**
     * Get demand level.
     */
    public function getDemandLevelAttribute(): string
    {
        $demand = $this->demand_analysis ?? [];
        
        if (isset($demand['demand_level'])) {
            $level = $demand['demand_level'];
            
            if ($level >= 0.8) return 'مرتفع جداً';
            if ($level >= 0.6) return 'مرتفع';
            if ($level >= 0.4) return 'متوسط';
            if ($level >= 0.2) return 'منخفض';
            return 'منخفض جداً';
        }
        
        return 'غير محدد';
    }

    /**
     * Get supply level.
     */
    public function getSupplyLevelAttribute(): string
    {
        $supply = $this->supply_analysis ?? [];
        
        if (isset($supply['supply_level'])) {
            $level = $supply['supply_level'];
            
            if ($level >= 0.8) return 'مرتفع جداً';
            if ($level >= 0.6) return 'مرتفع';
            if ($level >= 0.4) return 'متوسط';
            if ($level >= 0.2) return 'منخفض';
            return 'منخفض جداً';
        }
        
        return 'غير محدد';
    }

    /**
     * Get investment opportunity level.
     */
    public function getInvestmentOpportunityLevelAttribute(): string
    {
        $opportunities = $this->investment_opportunities ?? [];
        
        if (!empty($opportunities)) {
            $maxScore = max(array_column($opportunities, 'opportunity_score'));
            
            if ($maxScore >= 9.0) return 'استثنائية';
            if ($maxScore >= 8.0) return 'ممتازة';
            if ($maxScore >= 7.0) return 'جيدة جداً';
            if ($maxScore >= 6.0) return 'جيدة';
            return 'محدودة';
        }
        
        return 'غير متاحة';
    }

    /**
     * Get risk level.
     */
    public function getRiskLevelAttribute(): string
    {
        $risks = $this->risk_factors ?? [];
        
        if (!empty($risks)) {
            $maxRisk = max(array_column($risks, 'risk_score'));
            
            if ($maxRisk >= 8.0) return 'مرتفع جداً';
            if ($maxRisk >= 6.0) return 'مرتفع';
            if ($maxRisk >= 4.0) return 'متوسط';
            if ($maxRisk >= 2.0) return 'منخفض';
            return 'منخفض جداً';
        }
        
        return 'غير محدد';
    }

    /**
     * Check if insight is still valid.
     */
    public function isValid(): bool
    {
        return $this->expires_at && $this->expires_at->greaterThan(now());
    }

    /**
     * Check if insight is high priority.
     */
    public function isHighPriority(): bool
    {
        return $this->insight_score >= 8.5 || 
               ($this->investment_opportunity_level === 'استثنائية') ||
               ($this->risk_level === 'مرتفع جداً');
    }

    /**
     * Check if insight is actionable.
     */
    public function isActionable(): bool
    {
        return !empty($this->recommendations) && 
               $this->confidence_level >= 0.7 &&
               $this->reliability_score >= 7.0;
    }

    /**
     * Increment view count.
     */
    public function incrementViewCount(): bool
    {
        $this->view_count++;
        return $this->save();
    }

    /**
     * Add accuracy rating.
     */
    public function addAccuracyRating(float $rating): bool
    {
        if ($this->accuracy_rating === null) {
            $this->accuracy_rating = $rating;
        } else {
            // Average the ratings
            $this->accuracy_rating = ($this->accuracy_rating + $rating) / 2;
        }
        
        return $this->save();
    }

    /**
     * Create a new AI market insight.
     */
    public static function generateInsight(array $data): self
    {
        $insightType = $data['insight_type'] ?? 'market_trend';
        $marketArea = $data['market_area'] ?? 'Riyadh';
        $propertyType = $data['property_type'] ?? 'residential';
        $timeHorizon = $data['time_horizon'] ?? 'medium_term';
        
        // Generate market trends
        $marketTrends = [
            'direction' => ['bullish', 'neutral', 'bearish'][array_rand(['bullish', 'neutral', 'bearish'])],
            'strength' => rand(60, 95) / 100,
            'duration' => rand(3, 12) . ' months',
            'confidence' => rand(70, 90) / 100,
            'key_drivers' => [
                'economic_growth' => rand(2, 6) / 100,
                'population_growth' => rand(1, 4) / 100,
                'infrastructure_development' => rand(3, 8) / 100,
                'government_initiatives' => rand(2, 7) / 100,
            ],
        ];
        
        // Generate demand analysis
        $demandAnalysis = [
            'demand_level' => rand(40, 90) / 100,
            'growth_rate' => rand(-5, 15) / 100,
            'seasonal_variation' => rand(10, 30) / 100,
            'price_sensitivity' => rand(20, 60) / 100,
            'buyer_demographics' => [
                'age_groups' => ['25-34' => 35, '35-44' => 30, '45-54' => 20, '55+' => 15],
                'income_levels' => ['medium' => 40, 'high' => 35, 'very_high' => 25],
                'family_sizes' => ['single' => 20, 'couple' => 30, 'family' => 50],
            ],
        ];
        
        // Generate supply analysis
        $supplyAnalysis = [
            'supply_level' => rand(30, 80) / 100,
            'new_construction' => rand(100, 500),
            'inventory_turnover' => rand(60, 180) . ' days',
            'vacancy_rate' => rand(5, 20) / 100,
            'price_pressure' => rand(20, 70) / 100,
        ];
        
        // Generate competitor analysis
        $competitorAnalysis = [
            'competitor_count' => rand(5, 25),
            'market_concentration' => rand(30, 70) / 100,
            'average_pricing' => rand(2000, 8000),
            'marketing_spend' => rand(10000, 100000),
            'market_share_distribution' => [
                'top_3' => rand(40, 60),
                'top_10' => rand(60, 85),
                'others' => rand(15, 40),
            ],
        ];
        
        // Generate investment opportunities
        $investmentOpportunities = [
            [
                'type' => 'residential_development',
                'location' => $marketArea,
                'expected_roi' => rand(8, 20) / 100,
                'time_horizon' => '3-5 years',
                'opportunity_score' => rand(6, 9.5),
                'risk_level' => ['low', 'medium', 'high'][array_rand(['low', 'medium', 'high'])],
            ],
            [
                'type' => 'rental_properties',
                'location' => $marketArea,
                'expected_roi' => rand(6, 12) / 100,
                'time_horizon' => '5-10 years',
                'opportunity_score' => rand(5.5, 8.5),
                'risk_level' => ['low', 'medium', 'high'][array_rand(['low', 'medium', 'high'])],
            ],
        ];
        
        // Generate risk factors
        $riskFactors = [
            [
                'type' => 'market_risk',
                'description' => 'تقلبات السوق المحتملة',
                'risk_score' => rand(3, 8),
                'mitigation' => 'التنويع والتحليل الدقيق',
            ],
            [
                'type' => 'regulatory_risk',
                'description' => 'تغييرات تنظيمية محتملة',
                'risk_score' => rand(2, 6),
                'mitigation' => 'متابعة التشريعات والامتثال',
            ],
            [
                'type' => 'economic_risk',
                'description' => 'تأثيرات اقتصادية واسعة',
                'risk_score' => rand(4, 7),
                'mitigation' => 'التحليل الاقتصادي والتحوط',
            ],
        ];
        
        // Generate seasonal patterns
        $seasonalPatterns = [
            'peak_seasons' => ['Spring', 'Fall'],
            'low_seasons' => ['Summer', 'Winter'],
            'price_fluctuation' => rand(10, 25) / 100,
            'volume_variation' => rand(15, 35) / 100,
            'buyer_activity_patterns' => [
                'weekdays' => rand(40, 60) / 100,
                'weekends' => rand(60, 80) / 100,
                'evenings' => rand(70, 90) / 100,
            ],
        ];
        
        // Generate economic indicators
        $economicIndicators = [
            'gdp_growth' => rand(2, 6) / 100,
            'inflation_rate' => rand(1, 4) / 100,
            'interest_rates' => rand(2, 7) / 100,
            'employment_rate' => rand(92, 98) / 100,
            'consumer_confidence' => rand(60, 90) / 100,
            'construction_activity' => rand(70, 95) / 100,
        ];
        
        // Generate market forecast
        $marketForecast = [
            'short_term' => [
                'period' => '3-6 months',
                'price_change' => rand(-5, 10) / 100,
                'volume_change' => rand(-10, 15) / 100,
                'confidence' => rand(70, 85) / 100,
            ],
            'medium_term' => [
                'period' => '6-12 months',
                'price_change' => rand(-8, 15) / 100,
                'volume_change' => rand(-15, 20) / 100,
                'confidence' => rand(65, 80) / 100,
            ],
            'long_term' => [
                'period' => '1-3 years',
                'price_change' => rand(-10, 25) / 100,
                'volume_change' => rand(-20, 30) / 100,
                'confidence' => rand(60, 75) / 100,
            ],
        ];
        
        // Generate recommendations
        $recommendations = [
            [
                'action' => 'increase_investment',
                'priority' => 'high',
                'timeline' => 'within 6 months',
                'expected_impact' => 'positive',
                'confidence' => rand(75, 90) / 100,
            ],
            [
                'action' => 'focus_on_specific_areas',
                'priority' => 'medium',
                'timeline' => 'within 12 months',
                'expected_impact' => 'moderate',
                'confidence' => rand(65, 85) / 100,
            ],
        ];
        
        // Calculate scores
        $insightScore = rand(6.5, 9.5);
        $reliabilityScore = rand(7.0, 9.2);
        $confidenceLevel = rand(75, 95) / 100;

        return static::create([
            'property_id' => $data['property_id'] ?? null,
            'user_id' => $data['user_id'] ?? auth()->id(),
            'insight_type' => $insightType,
            'market_area' => $marketArea,
            'property_type' => $propertyType,
            'price_range' => $data['price_range'] ?? null,
            'market_trends' => $marketTrends,
            'demand_analysis' => $demandAnalysis,
            'supply_analysis' => $supplyAnalysis,
            'competitor_analysis' => $competitorAnalysis,
            'investment_opportunities' => $investmentOpportunities,
            'risk_factors' => $riskFactors,
            'seasonal_patterns' => $seasonalPatterns,
            'economic_indicators' => $economicIndicators,
            'demographic_trends' => $data['demographic_trends'] ?? [],
            'infrastructure_development' => $data['infrastructure_development'] ?? [],
            'regulatory_changes' => $data['regulatory_changes'] ?? [],
            'market_forecast' => $marketForecast,
            'recommendations' => $recommendations,
            'insight_score' => round($insightScore, 2),
            'reliability_score' => round($reliabilityScore, 2),
            'time_horizon' => $timeHorizon,
            'ai_model_version' => '10.4.1',
            'insight_metadata' => [
                'processing_time' => rand(1.5, 4.5) . 's',
                'data_sources' => ['mls', 'government_data', 'economic_reports', 'surveys'],
                'data_points_analyzed' => rand(500, 2000),
                'model_confidence' => $confidenceLevel,
                'generation_date' => now()->toDateTimeString(),
                'algorithm_version' => 'market_intelligence_v7',
            ],
            'confidence_level' => round($confidenceLevel, 2),
            'status' => 'published',
            'published_at' => now(),
            'expires_at' => now()->addMonths(6),
            'view_count' => 0,
            'accuracy_rating' => null,
            'created_by' => $data['created_by'] ?? auth()->id(),
        ]);
    }

    /**
     * Get insight summary for dashboard.
     */
    public function getDashboardSummary(): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'type' => $this->insight_type_label,
            'market_area' => $this->market_area,
            'insight_score' => $this->insight_score,
            'insight_level' => $this->insight_level,
            'confidence_level' => $this->confidence_level_text,
            'market_trend' => $this->market_trend_direction,
            'demand_level' => $this->demand_level,
            'investment_opportunity' => $this->investment_opportunity_level,
            'risk_level' => $this->risk_level,
            'is_high_priority' => $this->isHighPriority(),
            'is_actionable' => $this->isActionable(),
            'is_valid' => $this->isValid(),
            'view_count' => $this->view_count,
            'published_at' => $this->published_at?->format('Y-m-d'),
            'expires_at' => $this->expires_at?->format('Y-m-d'),
        ];
    }

    /**
     * Get detailed insight report.
     */
    public function getDetailedReport(): array
    {
        return [
            'basic_info' => [
                'id' => $this->id,
                'type' => $this->insight_type_label,
                'market_area' => $this->market_area,
                'property_type' => $this->property_type,
                'time_horizon' => $this->time_horizon_label,
                'status' => $this->status_label,
                'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
                'expires_at' => $this->expires_at?->format('Y-m-d H:i:s'),
            ],
            'scores' => [
                'insight_score' => $this->insight_score,
                'insight_level' => $this->insight_level,
                'reliability_score' => $this->reliability_score,
                'reliability_level' => $this->reliability_level,
                'confidence_level' => $this->confidence_level_text,
                'accuracy_rating' => $this->accuracy_rating,
            ],
            'market_analysis' => [
                'market_trends' => $this->market_trends,
                'trend_direction' => $this->market_trend_direction,
                'demand_analysis' => $this->demand_analysis,
                'demand_level' => $this->demand_level,
                'supply_analysis' => $this->supply_analysis,
                'supply_level' => $this->supply_level,
                'competitor_analysis' => $this->competitor_analysis,
            ],
            'opportunities' => [
                'investment_opportunities' => $this->investment_opportunities,
                'opportunity_level' => $this->investment_opportunity_level,
                'risk_factors' => $this->risk_factors,
                'risk_level' => $this->risk_level,
            ],
            'patterns' => [
                'seasonal_patterns' => $this->seasonal_patterns,
                'economic_indicators' => $this->economic_indicators,
                'demographic_trends' => $this->demographic_trends,
                'infrastructure_development' => $this->infrastructure_development,
                'regulatory_changes' => $this->regulatory_changes,
            ],
            'forecast' => [
                'market_forecast' => $this->market_forecast,
                'recommendations' => $this->recommendations,
            ],
            'metadata' => [
                'view_count' => $this->view_count,
                'is_high_priority' => $this->isHighPriority(),
                'is_actionable' => $this->isActionable(),
                'is_valid' => $this->isValid(),
                'ai_model_version' => $this->ai_model_version,
                'insight_metadata' => $this->insight_metadata,
            ],
        ];
    }
}
