<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationLog extends Model
{
    protected $fillable = [
        'external_integration_id',
        'level',
        'message',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function externalIntegration()
    {
        return $this->belongsTo(ExternalIntegration::class);
    }
}
