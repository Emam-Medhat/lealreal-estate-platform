<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSync extends Model
{
    protected $fillable = [
        'external_integration_id',
        'sync_type',
        'status',
        'started_at',
        'completed_at',
        'records_synced',
        'error_details',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'error_details' => 'array',
    ];

    public function externalIntegration()
    {
        return $this->belongsTo(ExternalIntegration::class);
    }
}
