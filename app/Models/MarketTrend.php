<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketTrend extends Model
{
    use HasFactory;

    protected $fillable = [
        'trend_type',
        'trend_name',
        'description',
        'value',
        'previous_value',
        'change_percentage',
        'trend_direction',
        'confidence_level',
        'data_points',
        'period_start',
        'period_end',
        'category',
        'region',
        'source',
        'metadata',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'previous_value' => 'decimal:2',
        'change_percentage' => 'decimal:2',
        'confidence_level' => 'decimal:2',
        'data_points' => 'array',
        'period_start' => 'date',
        'period_end' => 'date',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function competitorData(): HasMany
    {
        return $this->hasMany(CompetitorData::class);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('trend_type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    public function scopeByDirection($query, $direction)
    {
        return $query->where('trend_direction', $direction);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>', now()->subDays($days));
    }

    public function scopeHighConfidence($query, $threshold = 80)
    {
        return $query->where('confidence_level', '>=', $threshold);
    }

    public function isPriceTrend()
    {
        return $this->trend_type === 'price';
    }

    public function isDemandTrend()
    {
        return $this->trend_type === 'demand';
    }

    public function isSupplyTrend()
    {
        return $this->trend_type === 'supply';
    }

    public function isVolumeTrend()
    {
        return $this->trend_type === 'volume';
    }

    public function isIncreasing()
    {
        return $this->trend_direction === 'increasing';
    }

    public function isDecreasing()
    {
        return $this->trend_direction === 'decreasing';
    }

    public function isStable()
    {
        return $this->trend_direction === 'stable';
    }

    public function getTrendLabel()
    {
        return match($this->trend_type) {
            'price' => 'Price Trend',
            'demand' => 'Demand Trend',
            'supply' => 'Supply Trend',
            'volume' => 'Volume Trend',
            default => 'General Trend'
        };
    }

    public function getDirectionLabel()
    {
        return match($this->trend_direction) {
            'increasing' => '↑ Increasing',
            'decreasing' => '↓ Decreasing',
            'stable' => '→ Stable',
            default => 'Unknown'
        };
    }

    public function getCategoryLabel()
    {
        return match($this->category) {
            'residential' => 'Residential',
            'commercial' => 'Commercial',
            'industrial' => 'Industrial',
            'land' => 'Land',
            default => 'General'
        };
    }

    public function getConfidenceLevel()
    {
        if ($this->confidence_level >= 90) return 'very_high';
        if ($this->confidence_level >= 80) return 'high';
        if ($this->confidence_level >= 70) return 'medium';
        if ($this->confidence_level >= 60) return 'low';
        return 'very_low';
    }

    public function getTrendMagnitude()
    {
        $change = abs($this->change_percentage);
        
        if ($change >= 20) return 'very_high';
        if ($change >= 10) return 'high';
        if ($change >= 5) return 'medium';
        if ($change >= 2) return 'low';
        return 'very_low';
    }

    public function getPeriodDuration()
    {
        return $this->period_start->diffInDays($this->period_end);
    }

    public function getPeriodLabel()
    {
        $duration = $this->getPeriodDuration();
        
        if ($duration <= 7) return 'Weekly';
        if ($duration <= 30) return 'Monthly';
        if ($duration <= 90) return 'Quarterly';
        if ($duration <= 365) return 'Yearly';
        return 'Long-term';
    }

    public function getDataPointCount()
    {
        return count($this->data_points ?? []);
    }

    public function getAverageValue()
    {
        if (empty($this->data_points)) {
            return $this->value;
        }

        return array_sum($this->data_points) / count($this->data_points);
    }

    public function getMinValue()
    {
        if (empty($this->data_points)) {
            return $this->value;
        }

        return min($this->data_points);
    }

    public function getMaxValue()
    {
        if (empty($this->data_points)) {
            return $this->value;
        }

        return max($this->data_points);
    }

    public function getVolatility()
    {
        if (empty($this->data_points) || count($this->data_points) < 2) {
            return 0;
        }

        $mean = $this->getAverageValue();
        $variance = 0;

        foreach ($this->data_points as $point) {
            $variance += pow($point - $mean, 2);
        }

        $stdDev = sqrt($variance / count($this->data_points));
        
        return $mean > 0 ? ($stdDev / $mean) * 100 : 0;
    }

    public function getVolatilityLevel()
    {
        $volatility = $this->getVolatility();
        
        if ($volatility >= 20) return 'very_high';
        if ($volatility >= 10) return 'high';
        if ($volatility >= 5) return 'medium';
        if ($volatility >= 2) return 'low';
        return 'very_low';
    }

    public function getTrendStrength()
    {
        $change = abs($this->change_percentage);
        $confidence = $this->confidence_level;
        
        return ($change * $confidence) / 100;
    }

    public function getTrendStrengthLevel()
    {
        $strength = $this->getTrendStrength();
        
        if ($strength >= 15) return 'very_strong';
        if ($strength >= 10) return 'strong';
        if ($strength >= 5) return 'moderate';
        if ($strength >= 2) return 'weak';
        return 'very_weak';
    }

    public function getSeasonalPattern()
    {
        if (empty($this->data_points)) {
            return null;
        }

        // Simple seasonal pattern detection
        $data = $this->data_points;
        $midPoint = count($data) / 2;
        
        $firstHalf = array_slice($data, 0, $midPoint);
        $secondHalf = array_slice($data, $midPoint);
        
        $firstAvg = array_sum($firstHalf) / count($firstHalf);
        $secondAvg = array_sum($secondHalf) / count($secondHalf);
        
        $difference = ($secondAvg - $firstAvg) / $firstAvg * 100;
        
        if ($difference > 10) return 'increasing_seasonal';
        if ($difference < -10) return 'decreasing_seasonal';
        return 'stable_seasonal';
    }

    public function getForecast($periods = 1)
    {
        if (empty($this->data_points) || count($this->data_points) < 2) {
            return null;
        }

        // Simple linear forecast
        $data = $this->data_points;
        $n = count($data);
        
        $sumX = 0; $sumY = 0; $sumXY = 0; $sumX2 = 0;
        
        foreach ($data as $i => $value) {
            $sumX += $i;
            $sumY += $value;
            $sumXY += $i * $value;
            $sumX2 += $i * $i;
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;

        $forecast = [];
        for ($i = 1; $i <= $periods; $i++) {
            $forecastValue = $intercept + $slope * ($n + $i);
            $forecast[] = max(0, $forecastValue);
        }

        return $forecast;
    }

    public function getInsights()
    {
        $insights = [];
        
        if ($this->isIncreasing() && $this->getTrendMagnitude() === 'high') {
            $insights[] = 'Strong upward trend detected - potential growth opportunity';
        }
        
        if ($this->isDecreasing() && $this->getTrendMagnitude() === 'high') {
            $insights[] = 'Significant downward trend - requires attention';
        }
        
        if ($this->getVolatilityLevel() === 'high') {
            $insights[] = 'High volatility detected - market uncertainty';
        }
        
        if ($this->getConfidenceLevel() === 'low') {
            $insights[] = 'Low confidence in trend - more data needed';
        }
        
        if ($this->getSeasonalPattern() === 'increasing_seasonal') {
            $insights[] = 'Seasonal upward pattern detected';
        }
        
        return $insights;
    }

    public function getRecommendations()
    {
        $recommendations = [];
        
        if ($this->isPriceTrend() && $this->isIncreasing()) {
            $recommendations[] = 'Consider pricing strategies for upward market';
        }
        
        if ($this->isDemandTrend() && $this->isDecreasing()) {
            $recommendations[] = 'Focus on marketing to boost demand';
        }
        
        if ($this->isSupplyTrend() && $this->isIncreasing()) {
            $recommendations[] = 'Monitor supply levels to avoid oversaturation';
        }
        
        if ($this->getVolatilityLevel() === 'high') {
            $recommendations[] = 'Implement risk management strategies';
        }
        
        return $recommendations;
    }

    public function generateReport()
    {
        return [
            'trend_info' => [
                'id' => $this->id,
                'name' => $this->trend_name,
                'type' => $this->trend_type,
                'type_label' => $this->getTrendLabel(),
                'category' => $this->category,
                'category_label' => $this->getCategoryLabel(),
                'region' => $this->region,
                'description' => $this->description,
                'source' => $this->source
            ],
            'values' => [
                'current_value' => $this->value,
                'previous_value' => $this->previous_value,
                'change_percentage' => $this->change_percentage,
                'direction' => $this->trend_direction,
                'direction_label' => $this->getDirectionLabel(),
                'magnitude' => $this->getTrendMagnitude(),
                'strength' => $this->getTrendStrength(),
                'strength_level' => $this->getTrendStrengthLevel()
            ],
            'period' => [
                'start_date' => $this->period_start->format('Y-m-d'),
                'end_date' => $this->period_end->format('Y-m-d'),
                'duration' => $this->getPeriodDuration(),
                'period_label' => $this->getPeriodLabel()
            ],
            'statistics' => [
                'data_points_count' => $this->getDataPointCount(),
                'average_value' => $this->getAverageValue(),
                'min_value' => $this->getMinValue(),
                'max_value' => $this->getMaxValue(),
                'volatility' => $this->getVolatility(),
                'volatility_level' => $this->getVolatilityLevel(),
                'seasonal_pattern' => $this->getSeasonalPattern()
            ],
            'confidence' => [
                'level' => $this->confidence_level,
                'level_label' => $this->getConfidenceLevel()
            ],
            'analysis' => [
                'insights' => $this->getInsights(),
                'recommendations' => $this->getRecommendations(),
                'forecast' => $this->getForecast(3)
            ],
            'metadata' => $this->metadata,
            'created_at' => $this->created_at->toDateString()
        ];
    }

    public function exportToJson()
    {
        return [
            'id' => $this->id,
            'trend_type' => $this->trend_type,
            'trend_name' => $this->trend_name,
            'description' => $this->description,
            'value' => $this->value,
            'previous_value' => $this->previous_value,
            'change_percentage' => $this->change_percentage,
            'trend_direction' => $this->trend_direction,
            'confidence_level' => $this->confidence_level,
            'data_points' => $this->data_points,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'category' => $this->category,
            'region' => $this->region,
            'source' => $this->source,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
