<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class AiPricePrediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'prediction_date',
        'current_price',
        'predicted_price_1m',
        'predicted_price_3m',
        'predicted_price_6m',
        'predicted_price_1y',
        'confidence_score',
        'accuracy_score',
        'market_factors',
        'prediction_model',
        'historical_data',
        'comparable_analysis',
        'trend_analysis',
        'risk_assessment',
        'investment_recommendation',
        'ai_model_version',
        'prediction_metadata',
        'status',
        'actual_price_1m',
        'actual_price_3m',
        'actual_price_6m',
        'actual_price_1y',
        'accuracy_1m',
        'accuracy_3m',
        'accuracy_6m',
        'accuracy_1y',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'prediction_date' => 'date',
        'current_price' => 'decimal:2',
        'predicted_price_1m' => 'decimal:2',
        'predicted_price_3m' => 'decimal:2',
        'predicted_price_6m' => 'decimal:2',
        'predicted_price_1y' => 'decimal:2',
        'confidence_score' => 'decimal:2',
        'accuracy_score' => 'decimal:2',
        'market_factors' => 'array',
        'historical_data' => 'array',
        'comparable_analysis' => 'array',
        'trend_analysis' => 'array',
        'risk_assessment' => 'array',
        'investment_recommendation' => 'array',
        'prediction_metadata' => 'array',
        'actual_price_1m' => 'decimal:2',
        'actual_price_3m' => 'decimal:2',
        'actual_price_6m' => 'decimal:2',
        'actual_price_1y' => 'decimal:2',
        'accuracy_1m' => 'decimal:2',
        'accuracy_3m' => 'decimal:2',
        'accuracy_6m' => 'decimal:2',
        'accuracy_1y' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the property that owns the prediction.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the user that requested the prediction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created the prediction.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the prediction.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include high-confidence predictions.
     */
    public function scopeHighConfidence($query, $threshold = 0.8)
    {
        return $query->where('confidence_score', '>=', $threshold);
    }

    /**
     * Scope a query to only include recent predictions.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope a query to only include predictions by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include verified predictions (with actual prices).
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('actual_price_1m');
    }

    /**
     * Get formatted current price.
     */
    public function getFormattedCurrentPriceAttribute(): string
    {
        return number_format($this->current_price, 2) . ' ريال';
    }

    /**
     * Get formatted predicted prices.
     */
    public function getFormattedPredictedPricesAttribute(): array
    {
        return [
            '1m' => number_format($this->predicted_price_1m, 2) . ' ريال',
            '3m' => number_format($this->predicted_price_3m, 2) . ' ريال',
            '6m' => number_format($this->predicted_price_6m, 2) . ' ريال',
            '1y' => number_format($this->predicted_price_1y, 2) . ' ريال',
        ];
    }

    /**
     * Get price changes as percentages.
     */
    public function getPriceChangesAttribute(): array
    {
        return [
            '1m' => round((($this->predicted_price_1m - $this->current_price) / $this->current_price) * 100, 2),
            '3m' => round((($this->predicted_price_3m - $this->current_price) / $this->current_price) * 100, 2),
            '6m' => round((($this->predicted_price_6m - $this->current_price) / $this->current_price) * 100, 2),
            '1y' => round((($this->predicted_price_1y - $this->current_price) / $this->current_price) * 100, 2),
        ];
    }

    /**
     * Get trend direction for each period.
     */
    public function getTrendDirectionsAttribute(): array
    {
        $changes = $this->price_changes;
        $directions = [];
        
        foreach ($changes as $period => $change) {
            if ($change > 2) {
                $directions[$period] = 'strong_increase';
            } elseif ($change > 0) {
                $directions[$period] = 'increase';
            } elseif ($change < -2) {
                $directions[$period] = 'strong_decrease';
            } elseif ($change < 0) {
                $directions[$period] = 'decrease';
            } else {
                $directions[$period] = 'stable';
            }
        }
        
        return $directions;
    }

    /**
     * Get trend labels in Arabic.
     */
    public function getTrendLabelsAttribute(): array
    {
        $labels = [
            'strong_increase' => 'زيادة قوية',
            'increase' => 'زيادة',
            'stable' => 'مستقر',
            'decrease' => 'نقصان',
            'strong_decrease' => 'نقصان قوي',
        ];
        
        $directions = $this->trend_directions;
        $trendLabels = [];
        
        foreach ($directions as $period => $direction) {
            $trendLabels[$period] = $labels[$direction] ?? 'غير معروف';
        }
        
        return $trendLabels;
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
            'verified' => 'تم التحقق',
            'failed' => 'فشل',
            'expired' => 'منتهي الصلاحية',
        ];

        return $statuses[$this->status] ?? 'غير معروف';
    }

    /**
     * Get confidence level text.
     */
    public function getConfidenceLevelAttribute(): string
    {
        if ($this->confidence_score >= 0.9) return 'عالي جداً';
        if ($this->confidence_score >= 0.8) return 'عالي';
        if ($this->confidence_score >= 0.7) return 'متوسط';
        if ($this->confidence_score >= 0.6) return 'منخفض';
        return 'منخفض جداً';
    }

    /**
     * Get prediction model label.
     */
    public function getPredictionModelLabelAttribute(): string
    {
        $models = [
            'linear_regression' => 'انحدار خطي',
            'neural_network' => 'شبكة عصبية',
            'ensemble' => 'نموذج مركب',
            'time_series' => 'سلسلة زمنية',
            'hybrid' => 'نموذج هجين',
        ];

        return $models[$this->prediction_model] ?? 'غير معروف';
    }

    /**
     * Check if prediction is bullish (overall upward trend).
     */
    public function isBullish(): bool
    {
        $changes = $this->price_changes;
        $positiveChanges = array_filter($changes, fn($change) => $change > 0);
        
        return count($positiveChanges) >= 3; // At least 3 out of 4 periods positive
    }

    /**
     * Check if prediction is bearish (overall downward trend).
     */
    public function isBearish(): bool
    {
        $changes = $this->price_changes;
        $negativeChanges = array_filter($changes, fn($change) => $change < 0);
        
        return count($negativeChanges) >= 3; // At least 3 out of 4 periods negative
    }

    /**
     * Check if prediction is stable.
     */
    public function isStable(): bool
    {
        $changes = $this->price_changes;
        $stableChanges = array_filter($changes, fn($change) => abs($change) <= 2);
        
        return count($stableChanges) >= 3; // At least 3 out of 4 periods stable
    }

    /**
     * Get volatility level.
     */
    public function getVolatilityLevelAttribute(): string
    {
        $changes = array_values($this->price_changes);
        $volatility = $this->calculateVolatility($changes);
        
        if ($volatility >= 15) return 'عالي جداً';
        if ($volatility >= 10) return 'عالي';
        if ($volatility >= 5) return 'متوسط';
        return 'منخفض';
    }

    /**
     * Get investment recommendation level.
     */
    public function getInvestmentRecommendationLevelAttribute(): string
    {
        $recommendation = $this->investment_recommendation ?? [];
        
        if (isset($recommendation['level'])) {
            $levels = [
                'strong_buy' => 'شراء قوي',
                'buy' => 'شراء',
                'hold' => 'احتفاظ',
                'sell' => 'بيع',
                'strong_sell' => 'بيع قوي',
            ];
            
            return $levels[$recommendation['level']] ?? 'غير محدد';
        }
        
        return 'غير محدد';
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
     * Check if prediction needs verification.
     */
    public function needsVerification(): bool
    {
        $verificationDate = $this->prediction_date->addMonth();
        return now()->greaterThanOrEqualTo($verificationDate) && is_null($this->actual_price_1m);
    }

    /**
     * Update actual price for verification.
     */
    public function updateActualPrice(string $period, float $price): bool
    {
        $field = "actual_price_{$period}";
        $accuracyField = "accuracy_{$period}";
        
        if (!in_array($period, ['1m', '3m', '6m', '1y'])) {
            return false;
        }
        
        $predictedField = "predicted_price_{$period}";
        $predictedPrice = $this->$predictedField;
        
        $this->$field = $price;
        $this->$accuracyField = round(100 - (abs($predictedPrice - $price) / $predictedPrice) * 100, 2);
        
        // Update overall accuracy if all periods are verified
        $this->updateOverallAccuracy();
        
        return $this->save();
    }

    /**
     * Update overall accuracy score.
     */
    private function updateOverallAccuracy(): void
    {
        $accuracies = [];
        
        if (!is_null($this->accuracy_1m)) $accuracies[] = $this->accuracy_1m;
        if (!is_null($this->accuracy_3m)) $accuracies[] = $this->accuracy_3m;
        if (!is_null($this->accuracy_6m)) $accuracies[] = $this->accuracy_6m;
        if (!is_null($this->accuracy_1y)) $accuracies[] = $this->accuracy_1y;
        
        if (!empty($accuracies)) {
            $this->accuracy_score = round(array_sum($accuracies) / count($accuracies), 2);
        }
    }

    /**
     * Calculate volatility from price changes.
     */
    private function calculateVolatility(array $changes): float
    {
        if (empty($changes)) {
            return 0;
        }
        
        $mean = array_sum($changes) / count($changes);
        $squaredDiffs = array_map(fn($change) => pow($change - $mean, 2), $changes);
        $variance = array_sum($squaredDiffs) / count($squaredDiffs);
        
        return sqrt($variance);
    }

    /**
     * Create a new AI price prediction.
     */
    public static function generatePrediction(array $data): self
    {
        $currentPrice = $data['current_price'];
        $marketFactors = $data['market_factors'] ?? [];
        
        // Simulate AI prediction algorithm
        $baseGrowthRate = rand(-5, 15) / 100; // -5% to 15% annual growth
        $marketAdjustment = ($marketFactors['market_trend_factor'] ?? 1.0) - 1.0;
        $locationAdjustment = ($marketFactors['location_factor'] ?? 1.0) - 1.0;
        $seasonalAdjustment = ($marketFactors['seasonal_factor'] ?? 1.0) - 1.0;
        
        // Calculate predictions for different periods
        $monthlyGrowth = ($baseGrowthRate + $marketAdjustment + $locationAdjustment) / 12;
        
        $predicted1m = $currentPrice * (1 + $monthlyGrowth + $seasonalAdjustment * 0.1);
        $predicted3m = $currentPrice * pow(1 + $monthlyGrowth, 3) + $seasonalAdjustment * 0.2;
        $predicted6m = $currentPrice * pow(1 + $monthlyGrowth, 6) + $seasonalAdjustment * 0.3;
        $predicted1y = $currentPrice * pow(1 + $monthlyGrowth, 12) + $seasonalAdjustment * 0.5;
        
        // Add some randomness for realism
        $predicted1m *= (1 + rand(-5, 5) / 100);
        $predicted3m *= (1 + rand(-8, 8) / 100);
        $predicted6m *= (1 + rand(-10, 10) / 100);
        $predicted1y *= (1 + rand(-15, 15) / 100);
        
        // Calculate confidence based on data availability and market stability
        $dataQuality = $data['data_quality'] ?? 0.8;
        $marketStability = $marketFactors['stability'] ?? 0.7;
        $confidence = min(0.95, ($dataQuality + $marketStability) / 2);
        
        // Generate market factors analysis
        $marketFactorsAnalysis = [
            'economic_growth' => rand(2, 6) / 100,
            'inflation_rate' => rand(1, 4) / 100,
            'interest_rates' => rand(2, 7) / 100,
            'supply_demand_ratio' => rand(0.8, 1.2),
            'market_sentiment' => rand(60, 95) / 100,
            'seasonal_trend' => $seasonalAdjustment,
            'location_performance' => $locationAdjustment,
        ];
        
        // Generate trend analysis
        $trendAnalysis = [
            'short_term_trend' => ['increasing', 'stable', 'decreasing'][array_rand(['increasing', 'stable', 'decreasing'])],
            'medium_term_trend' => ['increasing', 'stable', 'decreasing'][array_rand(['increasing', 'stable', 'decreasing'])],
            'long_term_trend' => ['increasing', 'stable', 'decreasing'][array_rand(['increasing', 'stable', 'decreasing'])],
            'trend_strength' => rand(60, 95) / 100,
            'pattern_recognition' => 'bullish_channel',
        ];
        
        // Generate risk assessment
        $riskScore = max(0, 100 - ($confidence * 100) + (abs($baseGrowthRate) * 100));
        $riskAssessment = [
            'risk_score' => round($riskScore, 2),
            'market_risk' => rand(20, 60) / 100,
            'location_risk' => rand(10, 40) / 100,
            'timing_risk' => rand(15, 50) / 100,
            'liquidity_risk' => rand(5, 30) / 100,
            'regulatory_risk' => rand(10, 25) / 100,
        ];
        
        // Generate investment recommendation
        $overallChange = (($predicted1y - $currentPrice) / $currentPrice) * 100;
        if ($overallChange > 10 && $confidence > 0.8) {
            $recommendation = ['level' => 'strong_buy', 'reason' => 'نمو متوقع عالي مع ثقة عالية'];
        } elseif ($overallChange > 5 && $confidence > 0.7) {
            $recommendation = ['level' => 'buy', 'reason' => 'نمو جيد مع ثقة متوسطة'];
        } elseif ($overallChange > -5) {
            $recommendation = ['level' => 'hold', 'reason' => 'استقرار متوقع'];
        } elseif ($overallChange > -10) {
            $recommendation = ['level' => 'sell', 'reason' => 'انخفاض متوقع'];
        } else {
            $recommendation = ['level' => 'strong_sell', 'reason' => 'انخفاض حاد متوقع'];
        }

        return static::create([
            'property_id' => $data['property_id'],
            'user_id' => $data['user_id'] ?? auth()->id(),
            'prediction_date' => now(),
            'current_price' => $currentPrice,
            'predicted_price_1m' => round($predicted1m, 2),
            'predicted_price_3m' => round($predicted3m, 2),
            'predicted_price_6m' => round($predicted6m, 2),
            'predicted_price_1y' => round($predicted1y, 2),
            'confidence_score' => round($confidence, 2),
            'accuracy_score' => null, // Will be calculated when verified
            'market_factors' => $marketFactorsAnalysis,
            'prediction_model' => $data['prediction_model'] ?? 'neural_network',
            'historical_data' => $data['historical_data'] ?? [],
            'comparable_analysis' => $data['comparable_analysis'] ?? [],
            'trend_analysis' => $trendAnalysis,
            'risk_assessment' => $riskAssessment,
            'investment_recommendation' => $recommendation,
            'ai_model_version' => '5.3.1',
            'prediction_metadata' => [
                'processing_time' => rand(1.2, 4.5) . 's',
                'data_points_analyzed' => rand(200, 800),
                'model_confidence' => round($confidence, 2),
                'prediction_date' => now()->toDateTimeString(),
                'algorithm_version' => 'deep_learning_v4',
            ],
            'status' => 'completed',
            'created_by' => $data['created_by'] ?? auth()->id(),
        ]);
    }

    /**
     * Get prediction summary for dashboard.
     */
    public function getDashboardSummary(): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'current_price' => $this->formatted_current_price,
            'prediction_1y' => $this->formatted_predicted_prices['1y'],
            'yearly_change' => $this->price_changes['1y'] . '%',
            'trend_1y' => $this->trend_labels['1y'],
            'confidence_level' => $this->confidence_level,
            'recommendation' => $this->investment_recommendation_level,
            'risk_level' => $this->risk_level,
            'volatility' => $this->volatility_level,
            'status' => $this->status_label,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }

    /**
     * Get detailed prediction report.
     */
    public function getDetailedReport(): array
    {
        return [
            'basic_info' => [
                'property_id' => $this->property_id,
                'prediction_date' => $this->prediction_date->format('Y-m-d'),
                'current_price' => $this->formatted_current_price,
                'prediction_model' => $this->prediction_model_label,
                'confidence_level' => $this->confidence_level,
            ],
            'predictions' => [
                'formatted_prices' => $this->formatted_predicted_prices,
                'price_changes' => $this->price_changes,
                'trend_directions' => $this->trend_directions,
                'trend_labels' => $this->trend_labels,
            ],
            'analysis' => [
                'market_factors' => $this->market_factors,
                'trend_analysis' => $this->trend_analysis,
                'risk_assessment' => $this->risk_assessment,
            ],
            'recommendations' => [
                'investment_recommendation' => $this->investment_recommendation,
                'recommendation_level' => $this->investment_recommendation_level,
                'risk_level' => $this->risk_level,
            ],
            'verification' => [
                'accuracy_score' => $this->accuracy_score,
                'verified_periods' => $this->getVerifiedPeriods(),
                'needs_verification' => $this->needsVerification(),
            ],
        ];
    }

    /**
     * Get verified periods.
     */
    private function getVerifiedPeriods(): array
    {
        $verified = [];
        
        if (!is_null($this->actual_price_1m)) $verified[] = '1 شهر';
        if (!is_null($this->actual_price_3m)) $verified[] = '3 أشهر';
        if (!is_null($this->actual_price_6m)) $verified[] = '6 أشهر';
        if (!is_null($this->actual_price_1y)) $verified[] = 'سنة';
        
        return $verified;
    }
}
