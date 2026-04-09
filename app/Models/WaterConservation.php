<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaterConservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'water_consumption_baseline',
        'current_consumption',
        'water_saved',
        'conservation_percentage',
        'conservation_measures',
        'water_usage_breakdown',
        'conservation_level',
        'implemented_fixtures',
        'cost_savings',
        'leak_detection_data',
        'assessment_date',
        'next_assessment_date',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'conservation_measures' => 'array',
        'water_usage_breakdown' => 'array',
        'implemented_fixtures' => 'array',
        'leak_detection_data' => 'array',
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

    public function scopeMonitoring($query)
    {
        return $query->where('status', 'monitoring');
    }

    public function scopeImproving($query)
    {
        return $query->where('status', 'improving');
    }

    public function scopeCertified($query)
    {
        return $query->where('status', 'certified');
    }

    public function getConservationLevelAttribute($value): string
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
            'active' => 'نشط',
            'monitoring' => 'تحت المراقبة',
            'improving' => 'يتم التحسين',
            'certified' => 'معتمد',
            default => $value,
        };
    }

    public function getWaterReductionPercentage(): float
    {
        if ($this->water_consumption_baseline <= 0) {
            return 0;
        }

        $reduction = $this->water_consumption_baseline - $this->current_consumption;
        return ($reduction / $this->water_consumption_baseline) * 100;
    }

    public function getAnnualWaterSavings(): float
    {
        return $this->water_saved * 365; // Daily savings * 365 days
    }

    public function getAnnualCostSavings(): float
    {
        $averageWaterCost = 0.002; // $0.002 per liter
        return $this->getAnnualWaterSavings() * $averageWaterCost;
    }

    public function hasLeaks(): bool
    {
        return isset($this->leak_detection_data['leaks_detected']) && 
               $this->leak_detection_data['leaks_detected'] === true;
    }
}
