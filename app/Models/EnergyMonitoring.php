<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EnergyMonitoring extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'device_id',
        'consumption_kwh',
        'savings_amount',
        'efficiency_score',
        'status',
        'monitoring_type',
        'last_reading_at',
        'notes'
    ];

    protected $casts = [
        'last_reading_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function property()
    {
        return $this->belongsTo(SmartProperty::class);
    }

    public function device()
    {
        return $this->belongsTo(IotDevice::class);
    }
}
