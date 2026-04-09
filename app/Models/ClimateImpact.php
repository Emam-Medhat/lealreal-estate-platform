<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClimateImpact extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'carbon_footprint',
        'energy_consumption',
        'water_usage',
        'waste_generation',
        'biodiversity_impact',
        'air_quality_impact',
        'water_quality_impact',
        'soil_impact',
        'impact_factors',
        'mitigation_measures',
        'impact_level',
        'climate_risk_assessment',
        'adaptation_strategies',
        'assessment_date',
        'next_assessment_date',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'impact_factors' => 'array',
        'mitigation_measures' => 'array',
        'climate_risk_assessment' => 'array',
        'adaptation_strategies' => 'array',
        'assessment_date' => 'date',
        'next_assessment_date' => 'date',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(SmartProperty::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeAssessed($query)
    {
        return $query->where('status', 'assessed');
    }

    public function scopeMonitoring($query)
    {
        return $query->where('status', 'monitoring');
    }

    public function scopeMitigating($query)
    {
        return $query->where('status', 'mitigating');
    }

    public function scopeCompliant($query)
    {
        return $query->where('status', 'compliant');
    }

    public function getImpactLevelAttribute($value): string
    {
        return match($value) {
            'low' => 'منخفض',
            'moderate' => 'متوسط',
            'high' => 'عالي',
            'severe' => 'شديد',
            default => $value,
        };
    }

    public function getStatusAttribute($value): string
    {
        return match($value) {
            'assessed' => 'تم التقييم',
            'monitoring' => 'تحت المراقبة',
            'mitigating' => 'يتم التخفيف',
            'compliant' => 'متوافق',
            default => $value,
        };
    }

    public function getOverallImpactScore(): float
    {
        $impacts = [
            'biodiversity' => $this->biodiversity_impact,
            'air_quality' => $this->air_quality_impact,
            'water_quality' => $this->water_quality_impact,
            'soil' => $this->soil_impact,
        ];
        
        return array_sum($impacts) / count($impacts);
    }

    public function getImpactGrade(): string
    {
        $score = $this->getOverallImpactScore();
        
        if ($score >= 4.5) return 'F';
        if ($score >= 3.5) return 'D';
        if ($score >= 2.5) return 'C';
        if ($score >= 1.5) return 'B';
        if ($score >= 0.5) return 'A';
        return 'A+';
    }

    public function getCarbonIntensity(): float
    {
        if ($this->energy_consumption <= 0) {
            return 0;
        }

        return $this->carbon_footprint / $this->energy_consumption;
    }

    public function hasHighRiskFactors(): bool
    {
        return isset($this->climate_risk_assessment['risk_factors']) &&
               in_array('high', $this->climate_risk_assessment['risk_factors']);
    }

    public function getMitigationPriority(): string
    {
        if ($this->impact_level === 'severe') return 'فورية';
        if ($this->impact_level === 'high') return 'عالية';
        if ($this->impact_level === 'moderate') return 'متوسطة';
        return 'منخفضة';
    }
}
