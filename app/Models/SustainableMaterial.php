<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SustainableMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'material_name',
        'material_category',
        'supplier',
        'sustainability_score',
        'recycled_content',
        'certification_status',
        'certification_body',
        'environmental_impact',
        'material_properties',
        'lifespan_years',
        'is_renewable',
        'is_locally_sourced',
        'carbon_footprint_data',
        'installation_date',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'environmental_impact' => 'array',
        'material_properties' => 'array',
        'carbon_footprint_data' => 'array',
        'installation_date' => 'date',
        'is_renewable' => 'boolean',
        'is_locally_sourced' => 'boolean',
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

    public function scopePlanned($query)
    {
        return $query->where('status', 'planned');
    }

    public function scopeInstalled($query)
    {
        return $query->where('status', 'installed');
    }

    public function scopeInUse($query)
    {
        return $query->where('status', 'in_use');
    }

    public function scopeReplaced($query)
    {
        return $query->where('status', 'replaced');
    }

    public function getCertificationStatusAttribute($value): string
    {
        return match($value) {
            'none' => 'لا يوجد',
            'pending' => 'معلق',
            'certified' => 'معتمد',
            'expired' => 'منتهي الصلاحية',
            default => $value,
        };
    }

    public function getStatusAttribute($value): string
    {
        return match($value) {
            'planned' => 'مخطط',
            'installed' => 'مثبت',
            'in_use' => 'قيد الاستخدام',
            'replaced' => 'تم استبداله',
            'disposed' => 'تم التخلص منه',
            default => $value,
        };
    }

    public function isEcoFriendly(): bool
    {
        return $this->sustainability_score >= 70 && $this->recycled_content >= 50;
    }

    public function isLocallySourced(): bool
    {
        return $this->is_locally_sourced;
    }

    public function isRenewable(): bool
    {
        return $this->is_renewable;
    }

    public function getCarbonFootprint(): float
    {
        return $this->carbon_footprint_data['total_carbon'] ?? 0;
    }

    public function getMaterialGrade(): string
    {
        if ($this->sustainability_score >= 90) return 'A+';
        if ($this->sustainability_score >= 85) return 'A';
        if ($this->sustainability_score >= 80) return 'B+';
        if ($this->sustainability_score >= 75) return 'B';
        if ($this->sustainability_score >= 70) return 'C+';
        if ($this->sustainability_score >= 65) return 'C';
        if ($this->sustainability_score >= 60) return 'D';
        return 'F';
    }
}
