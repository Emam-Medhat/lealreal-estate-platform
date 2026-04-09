<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'type', // daily, weekly, monthly
        'data',
        'period_start',
        'period_end',
        'calculated_at'
    ];

    protected $casts = [
        'data' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'calculated_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
