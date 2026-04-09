<?php

namespace App\Models\Sustainability;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarbonFootprint extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_sustainability_id',
        'energy_footprint',
        'transport_footprint',
        'waste_footprint',
        'water_footprint',
        'materials_footprint',
        'total_footprint',
        'co2_saved',
        'net_footprint',
        'calculation_method',
        'data_source',
        'calculated_at',
        'calculated_by',
        'notes',
    ];

    protected $casts = [
        'energy_footprint' => 'decimal:2',
        'transport_footprint' => 'decimal:2',
        'waste_footprint' => 'decimal:2',
        'water_footprint' => 'decimal:2',
        'materials_footprint' => 'decimal:2',
        'total_footprint' => 'decimal:2',
        'co2_saved' => 'decimal:2',
        'net_footprint' => 'decimal:2',
        'calculated_at' => 'datetime',
    ];

    // Relationships
    public function propertySustainability(): BelongsTo
    {
        return $this->belongsTo(PropertySustainability::class);
    }

    public function calculator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    // Scopes
    public function scopeHighFootprint($query)
    {
        return $query->where('total_footprint', '>', 100);
    }

    public function scopeLowFootprint($query)
    {
        return $query->where('total_footprint', '<', 50);
    }

    public function scopeRecent($query)
    {
        return $query->where('calculated_at', '>=', now()->subMonths(6));
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('calculation_method', $method);
    }

    // Attributes
    public function getFootprintCategoryAttribute(): string
    {
        if ($this->total_footprint < 20) return 'منخفض جداً';
        if ($this->total_footprint < 40) return 'منخفض';
        if ($this->total_footprint < 60) return 'متوسط';
        if ($this->total_footprint < 80) return 'مرتفع';
        return 'مرتفع جداً';
    }

    public function getReductionPercentageAttribute(): float
    {
        if ($this->total_footprint == 0) return 0;
        return round(($this->co2_saved / $this->total_footprint) * 100, 1);
    }

    public function getEnergyContributionAttribute(): float
    {
        if ($this->total_footprint == 0) return 0;
        return round(($this->energy_footprint / $this->total_footprint) * 100, 1);
    }

    public function getTransportContributionAttribute(): float
    {
        if ($this->total_footprint == 0) return 0;
        return round(($this->transport_footprint / $this->total_footprint) * 100, 1);
    }

    public function getWasteContributionAttribute(): float
    {
        if ($this->total_footprint == 0) return 0;
        return round(($this->waste_footprint / $this->total_footprint) * 100, 1);
    }

    public function getWaterContributionAttribute(): float
    {
        if ($this->total_footprint == 0) return 0;
        return round(($this->water_footprint / $this->total_footprint) * 100, 1);
    }

    public function getMaterialsContributionAttribute(): float
    {
        if ($this->total_footprint == 0) return 0;
        return round(($this->materials_footprint / $this->total_footprint) * 100, 1);
    }

    // Methods
    public function getLargestContributor(): array
    {
        $contributors = [
            'energy' => $this->energy_footprint,
            'transport' => $this->transport_footprint,
            'waste' => $this->waste_footprint,
            'water' => $this->water_footprint,
            'materials' => $this->materials_footprint,
        ];

        $maxCategory = array_keys($contributors, max($contributors))[0];
        $maxValue = $contributors[$maxCategory];

        return [
            'category' => $maxCategory,
            'value' => $maxValue,
            'percentage' => $this->getContributionPercentage($maxCategory),
        ];
    }

    private function getContributionPercentage(string $category): float
    {
        $method = $category . '_contribution';
        return $this->$method;
    }

    public function getImprovementPotential(): array
    {
        $potential = [];

        if ($this->energy_footprint > 30) {
            $potential[] = [
                'category' => 'الطاقة',
                'current_value' => $this->energy_footprint,
                'potential_reduction' => $this->energy_footprint * 0.4, // 40% reduction potential
                'recommendations' => ['تركيب ألواح شمسية', 'تحسين العزل', 'أجهزة موفرة للطاقة'],
            ];
        }

        if ($this->transport_footprint > 20) {
            $potential[] = [
                'category' => 'النقل',
                'current_value' => $this->transport_footprint,
                'potential_reduction' => $this->transport_footprint * 0.3, // 30% reduction potential
                'recommendations' => ['النقل العام', 'العمل عن بعد', 'السيارات الكهربائية'],
            ];
        }

        if ($this->waste_footprint > 15) {
            $potential[] = [
                'category' => 'النفايات',
                'current_value' => $this->waste_footprint,
                'potential_reduction' => $this->waste_footprint * 0.5, // 50% reduction potential
                'recommendations' => ['زيادة إعادة التدوير', 'الكمبوست', 'تقليل الاستهلاك'],
            ];
        }

        return $potential;
    }

    public function getBenchmarkComparison(): array
    {
        $benchmarks = [
            'residential' => 45,
            'commercial' => 65,
            'industrial' => 85,
            'mixed' => 55,
        ];

        $propertyType = $this->propertySustainability->property->type ?? 'mixed';
        $benchmark = $benchmarks[$propertyType] ?? $benchmarks['mixed'];

        return [
            'benchmark' => $benchmark,
            'difference' => $this->total_footprint - $benchmark,
            'performance' => $this->total_footprint <= $benchmark ? 'أفضل من المتوسط' : 'أسوأ من المتوسط',
            'percentile' => $this->calculatePercentile($this->total_footprint, $benchmark),
        ];
    }

    private function calculatePercentile(float $value, float $benchmark): float
    {
        // Simplified percentile calculation
        $difference = $benchmark - $value;
        $percentile = 50 + ($difference * 1.5); // Rough approximation
        
        return max(0, min(100, $percentile));
    }

    public function getTrendData(): array
    {
        $previousFootprints = $this->propertySustainability
            ->carbonFootprints()
            ->where('id', '!=', $this->id)
            ->orderBy('calculated_at', 'desc')
            ->take(5)
            ->get();

        if ($previousFootprints->isEmpty()) {
            return [
                'trend' => 'no_data',
                'change' => 0,
                'direction' => 'neutral',
            ];
        }

        $previousValue = $previousFootprints->first()->total_footprint;
        $change = $this->total_footprint - $previousValue;
        $changePercentage = $previousValue > 0 ? ($change / $previousValue) * 100 : 0;

        return [
            'trend' => $change < 0 ? 'improving' : ($change > 0 ? 'worsening' : 'stable'),
            'change' => round($change, 2),
            'change_percentage' => round($changePercentage, 1),
            'direction' => $change < 0 ? 'down' : ($change > 0 ? 'up' : 'stable'),
        ];
    }

    public function generateReductionPlan(): array
    {
        $plan = [];
        $totalPotential = 0;

        foreach ($this->getImprovementPotential() as $potential) {
            $plan[] = [
                'category' => $potential['category'],
                'current_footprint' => $potential['current_value'],
                'target_footprint' => $potential['current_value'] - $potential['potential_reduction'],
                'reduction_amount' => $potential['potential_reduction'],
                'reduction_percentage' => round(($potential['potential_reduction'] / $potential['current_value']) * 100, 1),
                'recommendations' => $potential['recommendations'],
                'estimated_cost' => $this->estimateCost($potential['category']),
                'timeline' => $this->estimateTimeline($potential['category']),
            ];
            $totalPotential += $potential['potential_reduction'];
        }

        return [
            'plan' => $plan,
            'total_reduction_potential' => $totalPotential,
            'target_footprint' => max(0, $this->total_footprint - $totalPotential),
            'target_reduction_percentage' => round(($totalPotential / $this->total_footprint) * 100, 1),
        ];
    }

    private function estimateCost(string $category): string
    {
        $costs = [
            'energy' => 'مرتفع',
            'transport' => 'منخفض',
            'waste' => 'منخفض إلى متوسط',
            'water' => 'متوسط',
            'materials' => 'مرتفع',
        ];

        return $costs[$category] ?? 'متوسط';
    }

    private function estimateTimeline(string $category): string
    {
        $timelines = [
            'energy' => '6-12 شهر',
            'transport' => '1-3 أشهر',
            'waste' => '1-2 أشهر',
            'water' => '3-6 أشهر',
            'materials' => '12-24 شهر',
        ];

        return $timelines[$category] ?? '3-6 أشهر';
    }

    public function calculateOffsetRequirements(): array
    {
        if ($this->net_footprint <= 0) {
            return [
                'offset_required' => false,
                'offset_amount' => 0,
                'estimated_cost' => 0,
            ];
        }

        // Carbon offset cost estimation (simplified)
        $offsetCostPerTon = 25; // $25 per ton CO2
        $offsetAmount = $this->net_footprint / 1000; // Convert to tons
        $estimatedCost = $offsetAmount * $offsetCostPerTon;

        return [
            'offset_required' => true,
            'offset_amount_tons' => round($offsetAmount, 2),
            'estimated_cost' => round($estimatedCost, 2),
            'offset_projects' => [
                'renewable_energy' => 'طاقة متجددة',
                'reforestation' => 'إعادة التشجير',
                'energy_efficiency' => 'كفاءة الطاقة',
                'methane_capture' => '捕捉 الميثان',
            ],
        ];
    }

    public function getComplianceStatus(): array
    {
        $thresholds = [
            'excellent' => 20,
            'good' => 40,
            'acceptable' => 60,
            'poor' => 80,
        ];

        $status = 'poor';
        foreach ($thresholds as $level => $threshold) {
            if ($this->total_footprint <= $threshold) {
                $status = $level;
                break;
            }
        }

        return [
            'status' => $status,
            'compliant' => in_array($status, ['excellent', 'good', 'acceptable']),
            'regulatory_risk' => $status === 'poor' ? 'high' : ($status === 'acceptable' ? 'medium' : 'low'),
            'recommendation' => $this->getComplianceRecommendation($status),
        ];
    }

    private function getComplianceRecommendation(string $status): string
    {
        $recommendations = [
            'excellent' => 'الحفاظ على الأداء الممتاز ومواصلة التحسين',
            'good' => 'مواصلة التحسينات والعمل نحو التميز',
            'acceptable' => 'تحسين الأداء للوصول إلى مستوى جيد',
            'poor' => 'اتخاذ إجراءات عاجلة لتحسين الأداء',
        ];

        return $recommendations[$status] ?? 'تحسين الأداء العام';
    }

    // Events
    protected static function booted()
    {
        static::created(function ($carbonFootprint) {
            // Update property sustainability carbon footprint
            $carbonFootprint->propertySustainability->update([
                'carbon_footprint' => $carbonFootprint->total_footprint,
            ]);
        });

        static::updated(function ($carbonFootprint) {
            // Update property sustainability if total footprint changed
            if ($carbonFootprint->wasChanged('total_footprint')) {
                $carbonFootprint->propertySustainability->update([
                    'carbon_footprint' => $carbonFootprint->total_footprint,
                ]);
            }
        });
    }
}
