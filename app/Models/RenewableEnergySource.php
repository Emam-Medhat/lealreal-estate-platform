<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RenewableEnergySource extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'source_type',
        'capacity',
        'current_output',
        'energy_generated',
        'carbon_offset',
        'source_details',
        'performance_metrics',
        'installation_date',
        'last_maintenance_date',
        'status',
        'efficiency_rating',
        'maintenance_schedule',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'source_details' => 'array',
        'performance_metrics' => 'array',
        'maintenance_schedule' => 'array',
        'installation_date' => 'date',
        'last_maintenance_date' => 'date',
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

    public function scopeOffline($query)
    {
        return $query->where('status', 'offline');
    }

    public function scopeMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    public function getStatusAttribute($value): string
    {
        return match($value) {
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'maintenance' => 'تحت الصيانة',
            'offline' => 'غير متصل',
            default => $value,
        };
    }

    public function getEfficiencyPercentage(): float
    {
        if ($this->capacity <= 0) {
            return 0;
        }

        return ($this->current_output / $this->capacity) * 100;
    }

    public function getMonthlyEnergyGeneration(): float
    {
        return $this->energy_generated / 12; // Assuming annual generation
    }

    public function getAnnualCarbonOffset(): float
    {
        return $this->carbon_offset;
    }

    public function needsMaintenance(): bool
    {
        if (!$this->last_maintenance_date) {
            return false;
        }

        $daysSinceMaintenance = $this->last_maintenance_date->diffInDays(now());
        return $daysSinceMaintenance > 90; // Maintenance needed every 90 days
    }
}
