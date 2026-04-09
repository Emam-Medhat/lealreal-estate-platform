<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarbonFootprint extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'total_carbon',
        'energy_carbon',
        'transport_carbon',
        'waste_carbon',
        'water_carbon',
        'materials_carbon',
        'carbon_sources',
        'reduction_measures',
        'baseline_carbon',
        'reduction_target',
        'assessment_date',
        'next_assessment_date',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'carbon_sources' => 'array',
        'reduction_measures' => 'array',
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

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeReducing($query)
    {
        return $query->where('status', 'reducing');
    }

    public function scopeTargetMet($query)
    {
        return $query->where('status', 'target_met');
    }

    public function getStatusAttribute($value): string
    {
        return match($value) {
            'active' => 'نشط',
            'reducing' => 'يقلل',
            'target_met' => 'الهدف تم تحقيقه',
            'exceeded' => 'تجاوز',
            default => $value,
        };
    }

    public function getReductionPercentage(): float
    {
        if ($this->baseline_carbon <= 0) {
            return 0;
        }

        $reduction = $this->baseline_carbon - $this->total_carbon;
        return ($reduction / $this->baseline_carbon) * 100;
    }

    public function isTargetMet(): bool
    {
        return $this->getReductionPercentage() >= $this->reduction_target;
    }
}
