<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'activity_type',
        'action',
        'data',
        'created_at',
    ];

    protected $casts = [
        'data' => 'json',
        'created_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
