<?php

namespace App\Models\Sustainability;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnergyEfficiency extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_sustainability_id',
        'efficiency_rating',
        'energy_consumption',
        'energy_source',
        'renewable_energy_percentage',
        'insulation_rating',
        'hvac_efficiency',
        'lighting_efficiency',
        'appliance_efficiency',
        'solar_panels',
        'solar_capacity',
        'smart_thermostat',
        'energy_monitoring',
        'led_lighting',
        'double_glazing',
        'energy_star_appliances',
        'assessment_date',
        'next_assessment_date',
        'assessed_by',
        'recommendations',
        'potential_savings',
        'notes',
    ];

    protected $casts = [
        'efficiency_rating' => 'decimal:2',
        'energy_consumption' => 'decimal:2',
        'renewable_energy_percentage' => 'decimal:2',
        'insulation_rating' => 'integer',
        'hvac_efficiency' => 'integer',
        'lighting_efficiency' => 'integer',
        'appliance_efficiency' => 'integer',
        'solar_capacity' => 'decimal:2',
        'assessment_date' => 'date',
        'next_assessment_date' => 'date',
        'recommendations' => 'array',
        'potential_savings' => 'decimal:2',
        'solar_panels' => 'boolean',
        'smart_thermostat' => 'boolean',
        'energy_monitoring' => 'boolean',
        'led_lighting' => 'boolean',
        'double_glazing' => 'boolean',
        'energy_star_appliances' => 'boolean',
    ];

    // Relationships
    public function propertySustainability(): BelongsTo
    {
        return $this->belongsTo(PropertySustainability::class);
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    // Scopes
    public function scopeHighEfficiency($query)
    {
        return $query->where('efficiency_rating', '>=', 80);
    }

    public function scopeLowEfficiency($query)
    {
        return $query->where('efficiency_rating', '<', 60);
    }

    public function scopeWithRenewableEnergy($query)
    {
        return $query->where('renewable_energy_percentage', '>', 0);
    }

    public function scopeWithSolarPanels($query)
    {
        return $query->where('solar_panels', true);
    }

    public function scopeRecent($query)
    {
        return $query->where('assessment_date', '>=', now()->subMonths(6));
    }

    // Attributes
    public function getPerformanceLevelAttribute(): string
    {
        if ($this->efficiency_rating >= 90) return 'ممتاز';
        if ($this->efficiency_rating >= 80) return 'جيد جداً';
        if ($this->efficiency_rating >= 70) return 'جيد';
        if ($this->efficiency_rating >= 60) return 'متوسط';
        return 'ضعيف';
    }

    public function getEnergySourceTextAttribute(): string
    {
        return match($this->energy_source) {
            'electricity_grid' => 'شبكة الكهرباء',
            'solar' => 'طاقة شمسية',
            'wind' => 'طاقة رياح',
            'natural_gas' => 'غاز طبيعي',
            'oil' => 'زيت',
            'mixed' => 'مختلط',
            default => 'غير معروف',
        };
    }

    public function getDaysUntilNextAssessmentAttribute(): ?int
    {
        if (!$this->next_assessment_date) return null;
        return now()->diffInDays($this->next_assessment_date, false);
    }

    public function getIsAssessmentOverdueAttribute(): bool
    {
        return $this->next_assessment_date && $this->next_assessment_date->isPast();
    }

    public function getAnnualCostAttribute(): float
    {
        // Simplified cost calculation (assuming $0.15 per kWh)
        return $this->energy_consumption * 0.15 * 12;
    }

    public function getEstimatedSavingsAttribute(): float
    {
        return $this->annual_cost * ($this->potential_savings / 100);
    }

    public function getPaybackPeriodAttribute(): ?float
    {
        if ($this->potential_savings <= 0) return null;
        
        // Simplified payback calculation
        $investmentCost = $this->calculateInvestmentCost();
        $annualSavings = $this->estimated_savings;
        
        return $annualSavings > 0 ? $investmentCost / $annualSavings : null;
    }

    // Methods
    public function calculateEfficiencyRating(): float
    {
        $weights = [
            'consumption' => 0.25,
            'insulation' => 0.20,
            'hvac' => 0.15,
            'lighting' => 0.10,
            'appliances' => 0.10,
            'renewable' => 0.15,
            'smart_features' => 0.05,
        ];

        // Normalize scores to 0-100 scale
        $consumptionScore = max(0, 100 - ($this->energy_consumption / 10));
        $insulationScore = $this->insulation_rating * 10;
        $hvacScore = $this->hvac_efficiency * 10;
        $lightingScore = $this->lighting_efficiency * 10;
        $applianceScore = $this->appliance_efficiency * 10;
        $renewableScore = $this->renewable_energy_percentage;
        
        $smartFeaturesScore = 0;
        if ($this->smart_thermostat) $smartFeaturesScore += 25;
        if ($this->energy_monitoring) $smartFeaturesScore += 25;
        if ($this->led_lighting) $smartFeaturesScore += 25;
        if ($this->double_glazing) $smartFeaturesScore += 25;

        $totalScore = (
            $consumptionScore * $weights['consumption'] +
            $insulationScore * $weights['insulation'] +
            $hvacScore * $weights['hvac'] +
            $lightingScore * $weights['lighting'] +
            $applianceScore * $weights['appliances'] +
            $renewableScore * $weights['renewable'] +
            $smartFeaturesScore * $weights['smart_features']
        );

        return round(min(100, max(0, $totalScore)), 1);
    }

    public function updateEfficiencyRating(): void
    {
        $this->efficiency_rating = $this->calculateEfficiencyRating();
        $this->save();
    }

    public function getImprovementRecommendations(): array
    {
        $recommendations = [];

        if ($this->efficiency_rating < 60) {
            $recommendations[] = [
                'priority' => 'عاجل',
                'category' => 'شامل',
                'action' => 'تحسين شامل لكفاءة الطاقة',
                'potential_saving' => '30-50%',
                'estimated_cost' => 'مرتفع',
                'timeline' => '6-12 شهر',
            ];
        }

        if ($this->energy_consumption > 1000) {
            $recommendations[] = [
                'priority' => 'مرتفع',
                'category' => 'الاستهلاك',
                'action' => 'تقليل استهلاك الطاقة',
                'potential_saving' => '20-30%',
                'estimated_cost' => 'متوسط',
                'timeline' => '3-6 أشهر',
            ];
        }

        if (!$this->solar_panels && $this->renewable_energy_percentage < 30) {
            $recommendations[] = [
                'priority' => 'مرتفع',
                'category' => 'الطاقة المتجددة',
                'action' => 'تركيب ألواح شمسية',
                'potential_saving' => '40-60%',
                'estimated_cost' => 'مرتفع',
                'timeline' => '2-4 أشهر',
            ];
        }

        if ($this->insulation_rating < 7) {
            $recommendations[] = [
                'priority' => 'متوسط',
                'category' => 'العزل',
                'action' => 'تحسين عزل المبنى',
                'potential_saving' => '15-25%',
                'estimated_cost' => 'متوسط',
                'timeline' => '1-3 أشهر',
            ];
        }

        if (!$this->smart_thermostat) {
            $recommendations[] = [
                'priority' => 'منخفض',
                'category' => 'التحكم',
                'action' => 'تركيب منظم حراري ذكي',
                'potential_saving' => '10-15%',
                'estimated_cost' => 'منخفض',
                'timeline' => '1 أسبوع',
            ];
        }

        return $recommendations;
    }

    public function getBenchmarkComparison(): array
    {
        $benchmarks = [
            'residential' => ['avg_rating' => 65, 'avg_consumption' => 800],
            'commercial' => ['avg_rating' => 70, 'avg_consumption' => 1200],
            'industrial' => ['avg_rating' => 60, 'avg_consumption' => 2000],
            'mixed' => ['avg_rating' => 68, 'avg_consumption' => 1000],
        ];

        $propertyType = $this->propertySustainability->property->type ?? 'mixed';
        $benchmark = $benchmarks[$propertyType] ?? $benchmarks['mixed'];

        return [
            'rating_difference' => $this->efficiency_rating - $benchmark['avg_rating'],
            'consumption_difference' => $benchmark['avg_consumption'] - $this->energy_consumption,
            'performance_percentile' => $this->calculatePercentile($this->efficiency_rating, $benchmark['avg_rating']),
            'cost_comparison' => [
                'benchmark_cost' => $benchmark['avg_consumption'] * 0.15 * 12,
                'actual_cost' => $this->annual_cost,
                'savings_vs_benchmark' => max(0, $benchmark['avg_consumption'] * 0.15 * 12 - $this->annual_cost),
            ],
        ];
    }

    private function calculatePercentile(float $value, float $benchmark): float
    {
        $difference = $value - $benchmark;
        $percentile = 50 + ($difference * 2);
        
        return max(0, min(100, $percentile));
    }

    public function getEnergyProfile(): array
    {
        return [
            'consumption_pattern' => $this->analyzeConsumptionPattern(),
            'peak_usage_times' => $this->getPeakUsageTimes(),
            'seasonal_variation' => $this->getSeasonalVariation(),
            'efficiency_trends' => $this->getEfficiencyTrends(),
        ];
    }

    private function analyzeConsumptionPattern(): string
    {
        if ($this->energy_consumption < 500) return 'منخفض';
        if ($this->energy_consumption < 1000) return 'متوسط';
        if ($this->energy_consumption < 1500) return 'مرتفع';
        return 'مرتفع جداً';
    }

    private function getPeakUsageTimes(): array
    {
        // Simplified peak usage analysis
        return [
            'morning_peak' => '6:00 - 9:00',
            'evening_peak' => '18:00 - 22:00',
            'off_peak' => '23:00 - 5:00',
        ];
    }

    private function getSeasonalVariation(): array
    {
        return [
            'summer_multiplier' => 1.5,
            'winter_multiplier' => 1.3,
            'spring_multiplier' => 1.0,
            'autumn_multiplier' => 1.0,
        ];
    }

    private function getEfficiencyTrends(): array
    {
        // Get historical data for trend analysis
        $previousAssessments = $this->propertySustainability
            ->energyEfficiency()
            ->where('id', '!=', $this->id)
            ->orderBy('assessment_date', 'desc')
            ->take(5)
            ->get();

        if ($previousAssessments->isEmpty()) {
            return [
                'trend' => 'no_data',
                'change' => 0,
                'direction' => 'neutral',
            ];
        }

        $previousRating = $previousAssessments->first()->efficiency_rating;
        $change = $this->efficiency_rating - $previousRating;

        return [
            'trend' => $change > 0 ? 'improving' : ($change < 0 ? 'declining' : 'stable'),
            'change' => round($change, 1),
            'direction' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'stable'),
        ];
    }

    public function calculateInvestmentCost(): float
    {
        $cost = 0;

        if (!$this->solar_panels) {
            $cost += 15000; // Solar panel installation
        }

        if ($this->insulation_rating < 7) {
            $cost += 8000; // Insulation improvement
        }

        if (!$this->smart_thermostat) {
            $cost += 500; // Smart thermostat
        }

        if (!$this->led_lighting) {
            $cost += 2000; // LED lighting conversion
        }

        if (!$this->double_glazing) {
            $cost += 10000; // Double glazing
        }

        return $cost;
    }

    public function getRoiAnalysis(): array
    {
        $investmentCost = $this->calculateInvestmentCost();
        $annualSavings = $this->estimated_savings;
        $paybackPeriod = $this->payback_period;

        return [
            'investment_cost' => $investmentCost,
            'annual_savings' => $annualSavings,
            'payback_period_years' => $paybackPeriod,
            'roi_5_years' => $paybackPeriod ? (($annualSavings * 5 - $investmentCost) / $investmentCost) * 100 : 0,
            'roi_10_years' => $paybackPeriod ? (($annualSavings * 10 - $investmentCost) / $investmentCost) * 100 : 0,
            'net_present_value' => $this->calculateNPV($investmentCost, $annualSavings),
        ];
    }

    private function calculateNPV(float $investment, float $annualSavings, int $years = 10, float $discountRate = 0.05): float
    {
        $npv = -$investment;
        
        for ($year = 1; $year <= $years; $year++) {
            $npv += $annualSavings / pow(1 + $discountRate, $year);
        }
        
        return round($npv, 2);
    }

    public function getComplianceStatus(): array
    {
        $standards = [
            'local_building_code' => $this->efficiency_rating >= 50,
            'energy_efficiency_standard' => $this->efficiency_rating >= 65,
            'green_building_requirement' => $this->efficiency_rating >= 70,
            'carbon_emission_limit' => $this->energy_consumption <= 1000,
        ];

        $compliantStandards = array_filter($standards);
        $compliancePercentage = (count($compliantStandards) / count($standards)) * 100;

        return [
            'overall_compliance' => $compliancePercentage >= 75,
            'compliance_percentage' => round($compliancePercentage, 1),
            'standards_met' => $compliantStandards,
            'standards_not_met' => array_keys(array_diff_key($standards, $compliantStandards)),
            'recommendations' => $this->getComplianceRecommendations($standards),
        ];
    }

    private function getComplianceRecommendations(array $standards): array
    {
        $recommendations = [];

        if (!$standards['local_building_code']) {
            $recommendations[] = 'تحسين كفاءة الطاقة للوصول إلى الحد الأدنى من كود البناء المحلي';
        }

        if (!$standards['energy_efficiency_standard']) {
            $recommendations[] = 'ترقية أنظمة الطاقة لتلبية معايير كفاءة الطاقة';
        }

        if (!$standards['green_building_requirement']) {
            $recommendations[] = 'تنفيذ تحسينات إضافية لتلبية متطلبات المباني الخضراء';
        }

        if (!$standards['carbon_emission_limit']) {
            $recommendations[] = 'تقليل استهلاك الطاقة للوصول إلى حدود انبعاثات الكربون';
        }

        return $recommendations;
    }

    // Events
    protected static function booted()
    {
        static::created(function ($energyEfficiency) {
            // Update property sustainability energy efficiency rating
            $energyEfficiency->propertySustainability->update([
                'energy_efficiency_rating' => $energyEfficiency->efficiency_rating,
            ]);
        });

        static::updated(function ($energyEfficiency) {
            // Update property sustainability if efficiency rating changed
            if ($energyEfficiency->wasChanged('efficiency_rating')) {
                $energyEfficiency->propertySustainability->update([
                    'energy_efficiency_rating' => $energyEfficiency->efficiency_rating,
                ]);
            }
        });
    }
}
