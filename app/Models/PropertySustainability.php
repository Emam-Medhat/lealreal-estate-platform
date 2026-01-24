<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertySustainability extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'eco_score',
        'carbon_footprint',
        'energy_efficiency',
        'water_efficiency',
        'waste_reduction',
        'sustainability_metrics',
        'certifications',
        'sustainability_level',
        'last_assessment_date',
        'next_assessment_date',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'sustainability_metrics' => 'array',
        'certifications' => 'array',
        'last_assessment_date' => 'date',
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

    public function carbonFootprints(): HasMany
    {
        return $this->hasMany(CarbonFootprint::class);
    }

    public function energyEfficiency(): HasMany
    {
        return $this->hasMany(EnergyEfficiency::class);
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

    public function environmentalAudits(): HasMany
    {
        return $this->hasMany(EnvironmentalAudit::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCertified($query)
    {
        return $query->where('status', 'certified');
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('sustainability_level', $level);
    }

    public function getSustainabilityLevelAttribute($value): string
    {
        return match($value) {
            'basic' => 'أساسي',
            'intermediate' => 'متوسط',
            'advanced' => 'متقدم',
            'excellent' => 'ممتاز',
            default => $value,
        };
    }

    public function getStatusAttribute($value): string
    {
        return match($value) {
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'pending' => 'معلق',
            'certified' => 'معتمد',
            default => $value,
        };
    }
}
