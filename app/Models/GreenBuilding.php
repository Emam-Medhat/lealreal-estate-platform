<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GreenBuilding extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'building_name',
        'certification_level',
        'certification_body',
        'certificate_number',
        'certification_date',
        'expiry_date',
        'green_features',
        'energy_efficiency_rating',
        'water_efficiency_rating',
        'waste_reduction_rating',
        'indoor_air_quality_rating',
        'sustainable_materials_rating',
        'building_design_features',
        'innovation_features',
        'regional_priority',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'green_features' => 'array',
        'building_design_features' => 'array',
        'innovation_features' => 'array',
        'regional_priority' => 'array',
        'certification_date' => 'date',
        'expiry_date' => 'date',
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

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCertified($query)
    {
        return $query->where('status', 'certified');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function getCertificationLevelAttribute($value): string
    {
        return match($value) {
            'certified' => 'معتمد',
            'silver' => 'فضي',
            'gold' => 'ذهبي',
            'platinum' => 'بلاتيني',
            default => $value,
        };
    }

    public function getStatusAttribute($value): string
    {
        return match($value) {
            'pending' => 'معلق',
            'certified' => 'معتمد',
            'expired' => 'منتهي الصلاحية',
            'suspended' => 'معلق',
            'revoked' => 'ملغي',
            default => $value,
        };
    }

    public function getOverallRating(): float
    {
        $ratings = [
            $this->energy_efficiency_rating,
            $this->water_efficiency_rating,
            $this->waste_reduction_rating,
            $this->indoor_air_quality_rating,
            $this->sustainable_materials_rating,
        ];
        
        return array_sum($ratings) / count($ratings);
    }

    public function getRatingGrade(): string
    {
        $rating = $this->getOverallRating();
        
        if ($rating >= 90) return 'A+';
        if ($rating >= 85) return 'A';
        if ($rating >= 80) return 'B+';
        if ($rating >= 75) return 'B';
        if ($rating >= 70) return 'C+';
        if ($rating >= 65) return 'C';
        if ($rating >= 60) return 'D';
        return 'F';
    }

    public function isExpiring(): bool
    {
        return $this->expiry_date && $this->expiry_date->diffInDays(now()) <= 90;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getDaysUntilExpiry(): int
    {
        if (!$this->expiry_date) {
            return 0;
        }

        return max(0, $this->expiry_date->diffInDays(now()));
    }

    public function getGreenFeaturesCount(): int
    {
        return count($this->green_features ?? []);
    }

    public function hasInnovationFeatures(): bool
    {
        return !empty($this->innovation_features);
    }
}
