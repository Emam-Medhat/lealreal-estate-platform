<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyImport extends Model
{
    protected $fillable = [
        'external_integration_id',
        'file_path',
        'status',
        'processed_at',
        'records_imported',
        'error_details',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'error_details' => 'array',
    ];

    public function externalIntegration()
    {
        return $this->belongsTo(ExternalIntegration::class);
    }
}
