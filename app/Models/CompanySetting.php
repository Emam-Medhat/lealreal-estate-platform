<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'key',
        'value',
        'type', // string, json, boolean, etc.
        'group', // e.g. 'general', 'notifications'
        'is_public',
        'description',
        'created_by'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        // Value might be cast dynamically based on type, or just handled as string/json in code.
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
