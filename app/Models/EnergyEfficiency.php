<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EnergyEfficiency extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'efficiency_score',
        'energy_consumption_baseline',
        'current_consumption',
        'savings_percentage',
        'efficiency_metrics',
        'recommendations',
        'efficiency_level',
        'applied_measures',
        'cost_savings',
        'assessment_date',
        'next_assessment_date',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'efficiency_metrics' => 'array',
        'recommendations' => 'array',
        'applied_measures' => 'array',
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

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAssessed($query)
    {
        return $query->where('status', 'assessed');
    }

    public function scopeImproving($query)
    {
        return $query->where('status', 'improving');
    }

    public function scopeCertified($query)
    {
        return $query->where('status', 'certified');
    }

    public function getEfficiencyLevelAttribute($value): string
    {
        return match($value) {
            'poor' => 'ضعيف',
            'fair' => 'متوسط',
            'good' => 'جيد',
            'excellent' => 'ممتاز',
            'outstanding' => 'رائع',
            default => $value,
        };
    }

    public function getStatusAttribute($value): string
    {
        return match($value) {
            'pending' => 'معلق',
            'assessed' => 'تم التقييم',
            'improving' => 'يتم التحسين',
            'certified' => 'معتمد',
            default => $value,
        };
    }

    public function getEnergyReduction(): float
    {
        if ($this->energy_consumption_baseline <= 0) {
            return 0;
        }

        $reduction = $this->energy_consumption_baseline - $this->current_consumption;
        return ($reduction / $this->energy_consumption_baseline) * 100;
    }

    public function getAnnualCostSavings(): float
    {
        $energyReduction = $this->energy_consumption_baseline - $this->current_consumption;
        $averageEnergyCost = 0.15; // $0.15 per kWh
        
        return $energyReduction * $averageEnergyCost * 12; // Annual savings
    }
}
