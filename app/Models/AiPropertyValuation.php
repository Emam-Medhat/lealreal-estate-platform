<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class AiPropertyValuation extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'valuation_date',
        'estimated_value',
        'confidence_score',
        'valuation_method',
        'market_analysis',
        'comparable_properties',
        'adjustment_factors',
        'final_recommendation',
        'ai_model_version',
        'valuation_metadata',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'valuation_date' => 'date',
        'estimated_value' => 'decimal:2',
        'confidence_score' => 'integer',
        'market_analysis' => 'array',
        'comparable_properties' => 'array',
        'adjustment_factors' => 'array',
        'valuation_metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the property that owns the valuation.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the user that requested the valuation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created the valuation.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the valuation.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include valuations with high confidence.
     */
    public function scopeHighConfidence($query)
    {
        return $query->where('confidence_score', '>=', 80);
    }

    /**
     * Scope a query to only include recent valuations.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope a query to only include valuations with specific status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get formatted estimated value.
     */
    public function getFormattedEstimatedValueAttribute(): string
    {
        return number_format($this->estimated_value, 2) . ' ريال';
    }

    /**
     * Get confidence level text.
     */
    public function getConfidenceLevelAttribute(): string
    {
        if ($this->confidence_score >= 90) return 'عالي جداً';
        if ($this->confidence_score >= 80) return 'عالي';
        if ($this->confidence_score >= 70) return 'متوسط';
        if ($this->confidence_score >= 60) return 'منخفض';
        return 'منخفض جداً';
    }

    /**
     * Get status label in Arabic.
     */
    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'pending' => 'قيد الانتظار',
            'processing' => 'قيد المعالجة',
            'completed' => 'مكتمل',
            'failed' => 'فشل',
            'cancelled' => 'ملغي',
        ];

        return $statuses[$this->status] ?? 'غير معروف';
    }

    /**
     * Check if valuation is recent (within last 30 days).
     */
    public function isRecent(): bool
    {
        return $this->created_at->diffInDays(Carbon::now()) <= 30;
    }

    /**
     * Get valuation accuracy based on confidence score.
     */
    public function getAccuracyAttribute(): string
    {
        if ($this->confidence_score >= 90) return 'دقيق جداً';
        if ($this->confidence_score >= 80) return 'دقيق';
        if ($this->confidence_score >= 70) return 'مقبول';
        return 'غير دقيق';
    }

    /**
     * Get comparable properties count.
     */
    public function getComparableCountAttribute(): int
    {
        return count($this->comparable_properties ?? []);
    }

    /**
     * Get market trend based on market analysis.
     */
    public function getMarketTrendAttribute(): string
    {
        $analysis = $this->market_analysis ?? [];
        
        if (isset($analysis['trend'])) {
            $trends = [
                'increasing' => 'متصاعد',
                'stable' => 'مستقر',
                'decreasing' => 'منخفض',
            ];
            return $trends[$analysis['trend']] ?? 'غير معروف';
        }

        return 'غير محدد';
    }

    /**
     * Get valuation method label.
     */
    public function getValuationMethodLabelAttribute(): string
    {
        $methods = [
            'comparable_sales' => 'مقارنة المبيعات',
            'income_approach' => 'طريقة الدخل',
            'cost_approach' => 'طريقة التكلفة',
            'ai_hybrid' => 'ذكاء اصطناعي مركب',
        ];

        return $methods[$this->valuation_method] ?? 'غير معروف';
    }

    /**
     * Check if valuation needs update.
     */
    public function needsUpdate(): bool
    {
        return $this->created_at->diffInDays(Carbon::now()) > 90;
    }

    /**
     * Get value change percentage compared to previous valuation.
     */
    public function getValueChangeAttribute(): ?float
    {
        $previousValuation = static::where('property_id', $this->property_id)
            ->where('id', '<', $this->id)
            ->orderBy('id', 'desc')
            ->first();

        if (!$previousValuation) {
            return null;
        }

        return (($this->estimated_value - $previousValuation->estimated_value) / $previousValuation->estimated_value) * 100;
    }

    /**
     * Get value change direction.
     */
    public function getValueChangeDirectionAttribute(): ?string
    {
        $change = $this->value_change;
        
        if ($change === null) {
            return null;
        }

        if ($change > 0) return 'زيادة';
        if ($change < 0) return 'نقصان';
        return 'لا تغيير';
    }

    /**
     * Get risk assessment based on confidence and market factors.
     */
    public function getRiskAssessmentAttribute(): array
    {
        $riskFactors = [];
        $riskScore = 0;

        // Confidence score risk
        if ($this->confidence_score < 70) {
            $riskFactors[] = 'انخفاض دقة التقييم';
            $riskScore += 20;
        }

        // Market analysis risk
        $analysis = $this->market_analysis ?? [];
        if (isset($analysis['volatility']) && $analysis['volatility'] > 0.3) {
            $riskFactors[] = 'تقلبات سوق عالية';
            $riskScore += 15;
        }

        // Comparable properties risk
        if ($this->comparable_count < 3) {
            $riskFactors[] = 'عدد كافٍ من العقارات المقارنة';
            $riskScore += 10;
        }

        return [
            'risk_score' => min(100, $riskScore),
            'risk_factors' => $riskFactors,
            'risk_level' => $riskScore >= 50 ? 'مرتفع' : ($riskScore >= 25 ? 'متوسط' : 'منخفض'),
        ];
    }

    /**
     * Get investment recommendation based on valuation.
     */
    public function getInvestmentRecommendationAttribute(): string
    {
        $risk = $this->risk_assessment;
        $confidence = $this->confidence_score;

        if ($risk['risk_score'] >= 50 || $confidence < 70) {
            return 'غير موصى به - يحتاج دراسة إضافية';
        }

        if ($confidence >= 85 && $risk['risk_score'] < 25) {
            return 'موصى به بشدة - فرصة استثمارية ممتازة';
        }

        if ($confidence >= 75 && $risk['risk_score'] < 35) {
            return 'موصى به - فرصة استثمارية جيدة';
        }

        return 'محايد - يحتاج تقييم شخصي';
    }

    /**
     * Create a new valuation with AI simulation.
     */
    public static function createAiValuation(array $data): self
    {
        // Simulate AI valuation process
        $marketAnalysis = [
            'trend' => ['increasing', 'stable', 'decreasing'][array_rand(['increasing', 'stable', 'decreasing'])],
            'volatility' => rand(10, 40) / 100,
            'liquidity' => rand(60, 95),
            'demand_score' => rand(50, 90),
        ];

        $comparableProperties = [];
        $comparableCount = rand(3, 8);
        
        for ($i = 0; $i < $comparableCount; $i++) {
            $comparableProperties[] = [
                'property_id' => rand(1000, 9999),
                'distance_km' => rand(1, 5),
                'similarity_score' => rand(70, 95),
                'price_per_sqm' => rand(2000, 6000),
                'adjustment_factor' => rand(-10, 10) / 100,
            ];
        }

        $adjustmentFactors = [
            'location_adjustment' => rand(-15, 20) / 100,
            'condition_adjustment' => rand(-10, 15) / 100,
            'size_adjustment' => rand(-5, 10) / 100,
            'market_adjustment' => rand(-8, 12) / 100,
        ];

        $baseValue = rand(300000, 2000000);
        $totalAdjustment = array_sum($adjustmentFactors);
        $estimatedValue = $baseValue * (1 + $totalAdjustment);
        $confidenceScore = rand(70, 95);

        return static::create([
            'property_id' => $data['property_id'],
            'user_id' => $data['user_id'] ?? auth()->id(),
            'valuation_date' => now(),
            'estimated_value' => $estimatedValue,
            'confidence_score' => $confidenceScore,
            'valuation_method' => $data['valuation_method'] ?? 'ai_hybrid',
            'market_analysis' => $marketAnalysis,
            'comparable_properties' => $comparableProperties,
            'adjustment_factors' => $adjustmentFactors,
            'final_recommendation' => $confidenceScore >= 80 ? 'شراء موصى به' : 'يحتاج دراسة إضافية',
            'ai_model_version' => '2.1.0',
            'valuation_metadata' => [
                'processing_time' => rand(1.5, 4.2) . 's',
                'data_points_analyzed' => rand(50, 200),
                'model_confidence' => $confidenceScore,
                'last_updated' => now()->toDateTimeString(),
            ],
            'status' => 'completed',
            'created_by' => $data['created_by'] ?? auth()->id(),
        ]);
    }

    /**
     * Update valuation with new market data.
     */
    public function updateWithMarketData(array $marketData): bool
    {
        $this->market_analysis = array_merge($this->market_analysis ?? [], $marketData);
        
        // Recalculate estimated value based on new market data
        if (isset($marketData['price_change'])) {
            $this->estimated_value = $this->estimated_value * (1 + $marketData['price_change']);
        }

        return $this->save();
    }

    /**
     * Get valuation summary for dashboard.
     */
    public function getDashboardSummary(): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'estimated_value' => $this->formatted_estimated_value,
            'confidence_level' => $this->confidence_level,
            'status' => $this->status_label,
            'valuation_date' => $this->valuation_date->format('Y-m-d'),
            'is_recent' => $this->isRecent(),
            'market_trend' => $this->market_trend,
            'investment_recommendation' => $this->investment_recommendation,
        ];
    }
}
