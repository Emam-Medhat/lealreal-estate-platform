<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalIntegration extends Model
{
    protected $fillable = [
        'name',
        'provider',
        'type',
        'property_api_id',
        'configuration',
        'status',
        'last_sync_at',
    ];

    protected $casts = [
        'configuration' => 'array',
        'last_sync_at' => 'datetime',
    ];

    public function propertyApi()
    {
        return $this->belongsTo(PropertyApi::class);
    }
}
