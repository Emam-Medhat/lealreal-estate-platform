<?php

namespace App\Models\Sustainability;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertySustainability extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_id',
        'energy_efficiency_rating',
        'water_efficiency_rating',
        'waste_management_score',
        'green_space_ratio',
        'renewable_energy_percentage',
        'sustainable_materials_percentage',
        'carbon_footprint',
        'eco_score',
        'certification_status',
        'last_audit_date',
        'next_audit_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'green_space_ratio' => 'decimal:2',
        'renewable_energy_percentage' => 'decimal:2',
        'sustainable_materials_percentage' => 'decimal:2',
        'carbon_footprint' => 'decimal:2',
        'eco_score' => 'decimal:2',
        'last_audit_date' => 'date',
        'next_audit_date' => 'date',
    ];

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function carbonFootprints(): HasMany
    {
        return $this->hasMany(CarbonFootprint::class);
    }

    public function greenCertifications(): HasMany
    {
        return $this->hasMany(GreenCertification::class);
    }

    public function energyEfficiency(): HasMany
    {
        return $this->hasMany(EnergyEfficiency::class);
    }

    public function renewableEnergySources(): HasMany
    {
        return $this->hasMany(RenewableEnergySource::class);
    }

    public function waterConservation(): HasMany
    {
        return $this->hasMany(WaterConservation::class);
    }

    public function sustainableMaterials(): HasMany
    {
        return $this->hasMany(SustainableMaterial::class);
    }

    public function ecoScores(): HasMany
    {
        return $this->hasMany(EcoScore::class);
    }

    public function greenBuildings(): HasMany
    {
        return $this->hasMany(GreenBuilding::class);
    }

    public function climateImpacts(): HasMany
    {
        return $this->hasMany(ClimateImpact::class);
    }

    public function sustainabilityReports(): HasMany
    {
        return $this->hasMany(SustainabilityReport::class);
    }

    // Scopes
    public function scopeCertified($query)
    {
        return $query->where('certification_status', 'certified');
    }

    public function scopeHighPerformance($query)
    {
        return $query->where('eco_score', '>=', 80);
    }

    public function scopeLowCarbon($query)
    {
        return $query->where('carbon_footprint', '<', 50);
    }

    public function scopeWithRenewableEnergy($query)
    {
        return $query->where('renewable_energy_percentage', '>', 0);
    }

    public function scopeNeedingAudit($query)
    {
        return $query->where('next_audit_date', '<=', now()->addDays(30));
    }

    // Attributes
    public function getPerformanceLevelAttribute(): string
    {
        if ($this->eco_score >= 90) return 'ممتاز';
        if ($this->eco_score >= 80) return 'جيد جداً';
        if ($this->eco_score >= 70) return 'جيد';
        if ($this->eco_score >= 60) return 'متوسط';
        return 'ضعيف';
    }

    public function getCertificationStatusTextAttribute(): string
    {
        return match($this->certification_status) {
            'certified' => 'معتمد',
            'in_progress' => 'قيد المعالجة',
            'not_certified' => 'غير معتمد',
            'expired' => 'منتهي الصلاحية',
            default => 'غير معروف',
        };
    }

    public function getDaysUntilNextAuditAttribute(): ?int
    {
        if (!$this->next_audit_date) return null;
        return now()->diffInDays($this->next_audit_date, false);
    }

    public function getIsAuditOverdueAttribute(): bool
    {
        return $this->next_audit_date && $this->next_audit_date->isPast();
    }

    public function getCarbonFootprintCategoryAttribute(): string
    {
        if ($this->carbon_footprint < 20) return 'منخفض جداً';
        if ($this->carbon_footprint < 40) return 'منخفض';
        if ($this->carbon_footprint < 60) return 'متوسط';
        if ($this->carbon_footprint < 80) return 'مرتفع';
        return 'مرتفع جداً';
    }

    // Methods
    public function calculateEcoScore(): float
    {
        $weights = [
            'energy' => 0.25,
            'water' => 0.20,
            'waste' => 0.15,
            'materials' => 0.15,
            'green_space' => 0.10,
            'renewable' => 0.15,
        ];

        $energyScore = $this->energy_efficiency_rating * $weights['energy'];
        $waterScore = $this->water_efficiency_rating * $weights['water'];
        $wasteScore = $this->waste_management_score * $weights['waste'];
        $materialsScore = $this->sustainable_materials_percentage * $weights['materials'];
        $greenSpaceScore = $this->green_space_ratio * 100 * $weights['green_space'];
        $renewableScore = $this->renewable_energy_percentage * $weights['renewable'];

        $totalScore = $energyScore + $waterScore + $wasteScore + $materialsScore + $greenSpaceScore + $renewableScore;

        return round(min(100, max(0, $totalScore)), 1);
    }

    public function updateEcoScore(): void
    {
        $this->eco_score = $this->calculateEcoScore();
        $this->save();
    }

    public function getLatestEcoScore(): ?EcoScore
    {
        return $this->ecoScores()->latest('calculated_at')->first();
    }

    public function getLatestCarbonFootprint(): ?CarbonFootprint
    {
        return $this->carbonFootprints()->latest('calculated_at')->first();
    }

    public function hasActiveCertification(): bool
    {
        return $this->greenCertifications()
            ->where('status', 'active')
            ->where('expiry_date', '>', now())
            ->exists();
    }

    public function getActiveCertifications(): HasMany
    {
        return $this->greenCertifications()
            ->where('status', 'active')
            ->where('expiry_date', '>', now());
    }

    public function getTotalRenewableCapacity(): float
    {
        return $this->renewableEnergySources()
            ->where('status', 'active')
            ->sum('capacity');
    }

    public function getAnnualCarbonSavings(): float
    {
        $totalFootprint = $this->carbon_footprint ?? 0;
        $renewablePercentage = $this->renewable_energy_percentage ?? 0;
        
        return $totalFootprint * ($renewablePercentage / 100) * 0.8; // 80% efficiency assumption
    }

    public function getImprovementRecommendations(): array
    {
        $recommendations = [];

        if ($this->energy_efficiency_rating < 70) {
            $recommendations[] = [
                'category' => 'الطاقة',
                'priority' => 'مرتفع',
                'action' => 'تحسين كفاءة الطاقة',
                'potential_saving' => '20-30%',
            ];
        }

        if ($this->water_efficiency_rating < 70) {
            $recommendations[] = [
                'category' => 'المياه',
                'priority' => 'مرتفع',
                'action' => 'تحسين كفاءة المياه',
                'potential_saving' => '15-25%',
            ];
        }

        if ($this->renewable_energy_percentage < 50) {
            $recommendations[] = [
                'category' => 'الطاقة المتجددة',
                'priority' => 'متوسط',
                'action' => 'زيادة الطاقة المتجددة',
                'potential_saving' => '30-50%',
            ];
        }

        if ($this->sustainable_materials_percentage < 60) {
            $recommendations[] = [
                'category' => 'المواد',
                'priority' => 'متوسط',
                'action' => 'استخدام مواد مستدامة',
                'potential_saving' => '10-20%',
            ];
        }

        return $recommendations;
    }

    public function getBenchmarkComparison(): array
    {
        $propertyType = $this->property->type ?? 'unknown';
        
        $benchmarks = [
            'residential' => ['avg_eco_score' => 65, 'avg_carbon' => 45],
            'commercial' => ['avg_eco_score' => 70, 'avg_carbon' => 55],
            'industrial' => ['avg_eco_score' => 60, 'avg_carbon' => 65],
            'mixed' => ['avg_eco_score' => 68, 'avg_carbon' => 50],
        ];

        $benchmark = $benchmarks[$propertyType] ?? $benchmarks['mixed'];

        return [
            'eco_score_difference' => $this->eco_score - $benchmark['avg_eco_score'],
            'carbon_difference' => $benchmark['avg_carbon'] - $this->carbon_footprint,
            'performance_percentile' => $this->calculatePercentile($this->eco_score, $benchmark['avg_eco_score']),
        ];
    }

    private function calculatePercentile(float $value, float $benchmark): float
    {
        // Simplified percentile calculation
        $difference = $value - $benchmark;
        $percentile = 50 + ($difference * 2); // Rough approximation
        
        return max(0, min(100, $percentile));
    }

    public function generateSustainabilitySummary(): array
    {
        return [
            'overall_score' => $this->eco_score,
            'performance_level' => $this->performance_level,
            'certification_status' => $this->certification_status_text,
            'key_metrics' => [
                'energy_efficiency' => $this->energy_efficiency_rating,
                'water_efficiency' => $this->water_efficiency_rating,
                'carbon_footprint' => $this->carbon_footprint,
                'renewable_energy' => $this->renewable_energy_percentage,
            ],
            'strengths' => $this->identifyStrengths(),
            'weaknesses' => $this->identifyWeaknesses(),
            'recommendations_count' => count($this->improvement_recommendations),
        ];
    }

    private function identifyStrengths(): array
    {
        $strengths = [];

        if ($this->energy_efficiency_rating >= 80) $strengths[] = 'كفاءة طاقة ممتازة';
        if ($this->water_efficiency_rating >= 80) $strengths[] = 'كفاءة مياه ممتازة';
        if ($this->renewable_energy_percentage >= 50) $strengths[] = 'استخدام كبير للطاقة المتجددة';
        if ($this->sustainable_materials_percentage >= 70) $strengths[] = 'استخدام ممتاز للمواد المستدامة';
        if ($this->carbon_footprint < 30) $strengths[] = 'بصمة كربونية منخفضة';
        if ($this->hasActiveCertification()) $strengths[] = 'شهادات خضراء نشطة';

        return $strengths;
    }

    private function identifyWeaknesses(): array
    {
        $weaknesses = [];

        if ($this->energy_efficiency_rating < 60) $weaknesses[] = 'كفاءة طاقة منخفضة';
        if ($this->water_efficiency_rating < 60) $weaknesses[] = 'كفاءة مياه منخفضة';
        if ($this->renewable_energy_percentage < 25) $weaknesses[] = 'استخدام محدود للطاقة المتجددة';
        if ($this->sustainable_materials_percentage < 40) $weaknesses[] = 'استخدام محدود للمواد المستدامة';
        if ($this->carbon_footprint > 60) $weaknesses[] = 'بصمة كربونية مرتفعة';
        if ($this->certification_status === 'not_certified') $weaknesses[] = 'لا توجد شهادات خضراء';

        return $weaknesses;
    }

    // Events
    protected static function booted()
    {
        static::created(function ($propertySustainability) {
            // Create initial eco score
            EcoScore::create([
                'property_sustainability_id' => $propertySustainability->id,
                'overall_score' => $propertySustainability->eco_score,
                'energy_score' => $propertySustainability->energy_efficiency_rating,
                'water_score' => $propertySustainability->water_efficiency_rating,
                'waste_score' => $propertySustainability->waste_management_score,
                'materials_score' => $propertySustainability->sustainable_materials_percentage,
                'calculated_at' => now(),
                'calculated_by' => $propertySustainability->created_by,
            ]);
        });

        static::updated(function ($propertySustainability) {
            // Check if key metrics changed and update eco score
            if ($propertySustainability->wasChanged([
                'energy_efficiency_rating',
                'water_efficiency_rating',
                'waste_management_score',
                'green_space_ratio',
                'renewable_energy_percentage',
                'sustainable_materials_percentage'
            ])) {
                $propertySustainability->updateEcoScore();
            }
        });
    }
}
