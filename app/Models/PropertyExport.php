<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyExport extends Model
{
    protected $fillable = [
        'external_integration_id',
        'file_path',
        'status',
        'exported_at',
        'records_exported',
        'error_details',
    ];

    protected $casts = [
        'exported_at' => 'datetime',
        'error_details' => 'array',
    ];

    public function externalIntegration()
    {
        return $this->belongsTo(ExternalIntegration::class);
    }
}
