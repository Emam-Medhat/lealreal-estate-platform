<?php

namespace App\Models\Neighborhood;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class NeighborhoodStatistic extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'neighborhood_id',
        'statistic_type',
        'title',
        'description',
        'period',
        'data_source',
        'collection_method',
        'collection_date',
        'data_points',
        'aggregated_data',
        'trend_analysis',
        'comparative_data',
        'forecast_data',
        'visualization_data',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'neighborhood_id' => 'integer',
        'period' => 'string',
        'collection_date' => 'date',
        'data_points' => 'array',
        'aggregated_data' => 'array',
        'trend_analysis' => 'array',
        'comparative_data' => 'array',
        'forecast_data' => 'array',
        'visualization_data' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the neighborhood that owns the statistic.
     */
    public function neighborhood(): BelongsTo
    {
        return $this->belongsTo(Neighborhood::class, 'neighborhood_id');
    }

    /**
     * Scope a query to filter by neighborhood.
     */
    public function scopeByNeighborhood(Builder $query, int $neighborhoodId): Builder
    {
        return $query->where('neighborhood_id', $neighborhoodId);
    }

    /**
     * Scope a query to filter by statistic type.
     */
    public function scopeByType(Builder $query, string $statisticType): Builder
    {
        return $query->where('statistic_type', $statisticType);
    }

    /**
     * Scope a query to filter by period.
     */
    public function scopeByPeriod(Builder $query, string $period): Builder
    {
        return $query->where('period', $period);
    }

    /**
     * Scope a query to get statistics by date range.
     */
    public function scopeByDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('collection_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to get recent statistics.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('collection_date', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to get statistics with trend analysis.
     */
    public function scopeWithTrendAnalysis(Builder $query): Builder
    {
        return $query->whereNotNull('trend_analysis');
    }

    /**
     * Scope a query to get statistics with forecast data.
     */
    public function scopeWithForecast(Builder $query): Builder
    {
        return $query->whereNotNull('forecast_data');
    }

    /**
     * Scope a query to get statistics with visualization data.
     */
    public function scopeWithVisualization(Builder $query): Builder
    {
        return $query->whereNotNull('visualization_data');
    }

    /**
     * Get the statistic type label.
     */
    public function getStatisticTypeLabelAttribute(): string
    {
        $types = [
            'population' => 'السكان',
            'property' => 'العقارات',
            'business' => 'الأعمال',
            'amenity' => 'المرافق',
            'safety' => 'السلامة',
            'education' => 'التعليم',
            'transportation' => 'المواصلات',
            'healthcare' => 'الرعاية الصحية',
            'recreation' => 'الترفيه',
            'economic' => 'الاقتصاد',
            'demographic' => 'الديموغرافيا',
            'infrastructure' => 'البنية التحتية',
        ];

        return $types[$this->statistic_type] ?? 'غير معروف';
    }

    /**
     * Get the period label.
     */
    public function getPeriodLabelAttribute(): string
    {
        $periods = [
            'daily' => 'يومي',
            'weekly' => 'أسبوعي',
            'monthly' => 'شهري',
            'quarterly' => 'ربعي',
            'yearly' => 'سنوي',
        ];

        return $periods[$this->period] ?? 'غير معروف';
    }

    /**
     * Get the collection date label.
     */
    public function getCollectionDateLabelAttribute(): string
    {
        return $this->collection_date ? $this->collection_date->format('Y-m-d') : 'غير محدد';
    }

    /**
     * Get the data points.
     */
    public function getDataPointsAttribute(): array
    {
        return $this->data_points ?? [];
    }

    /**
     * Get the aggregated data.
     */
    public function getAggregatedDataAttribute(): array
    {
        return $this->aggregated_data ?? [];
    }

    /**
     * Get the trend analysis.
     */
    public function getTrendAnalysisAttribute(): array
    {
        return $this->trend_analysis ?? [];
    }

    /**
     * Get the comparative data.
     */
    public function getComparativeDataAttribute(): array
    {
        return $this->comparative_data ?? [];
    }

    /**
     * Get the forecast data.
     */
    public function getForecastDataAttribute(): array
    {
        return $this->forecast_data ?? [];
    }

    /**
     * Get the visualization data.
     */
    public function getVisualizationDataAttribute(): array
    {
        return $this->visualization_data ?? [];
    }

    /**
     * Get the metadata as JSON.
     */
    public function getMetadataAttribute(): string
    {
        return json_encode($this->metadata ?? []);
    }

    /**
     * Get the total value from aggregated data.
     */
    public function getTotalValueAttribute(): float
    {
        return $this->aggregated_data['total'] ?? 0;
    }

    /**
     * Get the average value from aggregated data.
     */
    public function getAverageValueAttribute(): float
    {
        return $this->aggregated_data['average'] ?? 0;
    }

    /**
     * Get the minimum value from aggregated data.
     */
    public function getMinimumValueAttribute(): float
    {
        return $this->aggregated_data['minimum'] ?? 0;
    }

    /**
     * Get the maximum value from aggregated data.
     */
    public function getMaximumValueAttribute(): float
    {
        return $this->aggregated_data['maximum'] ?? 0;
    }

    /**
     * Get the median value from aggregated data.
     */
    public function getMedianValueAttribute(): float
    {
        return $this->aggregated_data['median'] ?? 0;
    }

    /**
     * Get the count from aggregated data.
     */
    public function getCountValueAttribute(): int
    {
        return $this->aggregated_data['count'] ?? 0;
    }

    /**
     * Get the trend from trend analysis.
     */
    public function getTrendAttribute(): string
    {
        return $this->trend_analysis['trend'] ?? 'unknown';
    }

    /**
     * Get the percentage change from trend analysis.
     */
    public function getPercentageChangeAttribute(): float
    {
        return $this->trend_analysis['percentage_change'] ?? 0;
    }

    /**
     * Get the confidence level from trend analysis.
     */
    public function getConfidenceLevelAttribute(): float
    {
        return $this->trend_analysis['confidence_level'] ?? 0;
    }

    /**
     * Get the analysis period from trend analysis.
     */
    public function getAnalysisPeriodAttribute(): string
    {
        return $this->trend_analysis['analysis_period'] ?? 'unknown';
    }

    /**
     * Get the previous period value from comparative data.
     */
    public function getPreviousPeriodValueAttribute(): float
    {
        return $this->comparative_data['previous_period'] ?? 0;
    }

    /**
     * Get the percentage change from comparative data.
     */
    public function getComparativePercentageChangeAttribute(): float
    {
        return $this->comparative_data['percentage_change'] ?? 0;
    }

    /**
     * Get the benchmark value from comparative data.
     */
    public function getBenchmarkValueAttribute(): float
    {
        return $this->comparative_data['benchmark'] ?? 0;
    }

    /**
     * Get the comparative period from comparative data.
     */
    public function getComparativePeriodAttribute(): string
    {
        return $this->comparative_data['period'] ?? 'unknown';
    }

    /**
     * Get the next period value from forecast data.
     */
    public function getNextPeriodValueAttribute(): float
    {
        return $this->forecast_data['next_period'] ?? 0;
    }

    /**
     * Get the forecast confidence level.
     */
    public function getForecastConfidenceLevelAttribute(): float
    {
        return $this->forecast_data['confidence_level'] ?? 0;
    }

    /**
     * Get the forecast method.
     */
    public function getForecastMethodAttribute(): string
    {
        return $this->forecast_data['method'] ?? 'unknown';
    }

    /**
     * Get the forecast period.
     */
    public function getForecastPeriodAttribute(): string
    {
        return $this->forecast_data['period'] ?? 'unknown';
    }

    /**
     * Get the chart type from visualization data.
     */
    public function getChartTypeAttribute(): string
    {
        return $this->visualization_data['chart_type'] ?? 'line';
    }

    /**
     * Get the color scheme from visualization data.
     */
    public function getColorSchemeAttribute(): array
    {
        return $this->visualization_data['color_scheme'] ?? [];
    }

    /**
     * Get the data format from visualization data.
     */
    public function getDataFormatAttribute(): string
    {
        return $this->visualization_data['data_format'] ?? 'default';
    }

    /**
     * Check if the statistic has data points.
     */
    public function hasDataPoints(): bool
    {
        return !empty($this->data_points);
    }

    /**
     * Check if the statistic has aggregated data.
     */
    public function hasAggregatedData(): bool
    {
        return !empty($this->aggregated_data);
    }

    /**
     * Check if the statistic has trend analysis.
     */
    public function hasTrendAnalysis(): bool
    {
        return !empty($this->trend_analysis);
    }

    /**
     * Check if the statistic has comparative data.
     */
    public function hasComparativeData(): bool
    {
        return !empty($this->comparative_data);
    }

    /**
     * Check if the statistic has forecast data.
     */
    public function hasForecastData(): bool
    {
        return !empty($this->forecast_data);
    }

    /**
     * Check if the statistic has visualization data.
     */
    public function hasVisualizationData(): bool
    {
        return !empty($this->visualization_data);
    }

    /**
     * Check if the trend is increasing.
     */
    public function isTrendIncreasing(): bool
    {
        return $this->trend === 'increasing';
    }

    /**
     * Check if the trend is decreasing.
     */
    public function isTrendDecreasing(): bool
    {
        return $this->trend === 'decreasing';
    }

    /**
     * Check if the trend is stable.
     */
    public function isTrendStable(): bool
    {
        return $this->trend === 'stable';
    }

    /**
     * Check if the trend is volatile.
     */
    public function isTrendVolatile(): bool
    {
        return $this->trend === 'volatile';
    }

    /**
     * Check if the percentage change is positive.
     */
    public function hasPositiveChange(): bool
    {
        return $this->percentage_change > 0;
    }

    /**
     * Check if the percentage change is negative.
     */
    public function hasNegativeChange(): bool
    {
        return $this->percentage_change < 0;
    }

    /**
     * Check if the percentage change is significant.
     */
    public function hasSignificantChange(): bool
    {
        return abs($this->percentage_change) >= 10;
    }

    /**
     * Check if the confidence level is high.
     */
    public function hasHighConfidence(): bool
    {
        return $this->confidence_level >= 80;
    }

    /**
     * Check if the confidence level is medium.
     */
    public function hasMediumConfidence(): bool
    {
        return $this->confidence_level >= 60 && $this->confidence_level < 80;
    }

    /**
     * Check if the confidence level is low.
     */
    public function hasLowConfidence(): bool
    {
        return $this->confidence_level < 60;
    }

    /**
     * Check if the statistic is recent.
     */
    public function isRecent(): bool
    {
        return $this->collection_date && $this->collection_date->diffInDays(now()) <= 30;
    }

    /**
     * Check if the statistic is old.
     */
    public function isOld(): bool
    {
        return $this->collection_date && $this->collection_date->diffInDays(now()) > 365;
    }

    /**
     * Get the trend label.
     */
    public function getTrendLabelAttribute(): string
    {
        $trends = [
            'increasing' => 'متزايد',
            'decreasing' => 'متناقص',
            'stable' => 'مستقر',
            'volatile' => 'متقلب',
        ];

        return $trends[$this->trend] ?? 'غير معروف';
    }

    /**
     * Get the percentage change label.
     */
    public function getPercentageChangeLabelAttribute(): string
    {
        $change = $this->percentage_change;
        
        if ($change > 0) {
            return '+' . number_format($change, 2) . '%';
        } elseif ($change < 0) {
            return number_format($change, 2) . '%';
        } else {
            return '0%';
        }
    }

    /**
     * Get the confidence level label.
     */
    public function getConfidenceLevelLabelAttribute(): string
    {
        $level = $this->confidence_level;
        
        if ($level >= 80) {
            return 'عالي';
        } elseif ($level >= 60) {
            return 'متوسط';
        } else {
            return 'منخفض';
        }
    }

    /**
     * Get the data quality score.
     */
    public function getDataQualityScore(): float
    {
        $score = 0;
        $maxScore = 5;

        if ($this->hasDataPoints()) $score += 1;
        if ($this->hasAggregatedData()) $score += 1;
        if ($this->hasTrendAnalysis()) $score += 1;
        if ($this->hasComparativeData()) $score += 1;
        if ($this->hasVisualizationData()) $score += 1;

        return $score / $maxScore;
    }

    /**
     * Get the data quality label.
     */
    public function getDataQualityLabelAttribute(): string
    {
        $score = $this->data_quality_score;

        if ($score >= 0.8) {
            return 'ممتاز';
        } elseif ($score >= 0.6) {
            return 'جيد جداً';
        } elseif ($score >= 0.4) {
            return 'جيد';
        } elseif ($score >= 0.2) {
            return 'ضعيف';
        } else {
            return 'ضعيف جداً';
        }
    }

    /**
     * Get the full title with neighborhood.
     */
    public function getFullTitleAttribute(): string
    {
        if ($this->neighborhood) {
            return $this->title . ' - ' . $this->neighborhood->name;
        }
        return $this->title;
    }

    /**
     * Get the search index.
     */
    public function getSearchIndex(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'statistic_type' => $this->statistic_type,
            'period' => $this->period,
            'data_source' => $this->data_source,
            'collection_method' => $this->collection_method,
            'collection_date' => $this->collection_date?->format('Y-m-d'),
            'neighborhood' => $this->neighborhood?->name ?? '',
            'city' => $this->neighborhood?->city ?? '',
            'district' => $this->neighborhood?->district ?? '',
            'trend' => $this->trend,
            'total_value' => $this->total_value,
            'average_value' => $this->average_value,
        ];
    }

    /**
     * Bootstrap the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($builder) {
            $builder->whereNull('deleted_at');
        });
    }
}
