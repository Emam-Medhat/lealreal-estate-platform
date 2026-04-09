<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'score',
        'factors',
        'notes',
        'calculated_by',
    ];

    protected $casts = [
        'score' => 'integer',
        'factors' => 'array',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function calculatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeHighScore($query, $threshold = 70)
    {
        return $query->where('score', '>=', $threshold);
    }

    public function scopeMediumScore($query, $min = 40, $max = 69)
    {
        return $query->whereBetween('score', [$min, $max]);
    }

    public function scopeLowScore($query, $threshold = 39)
    {
        return $query->where('score', '<=', $threshold);
    }
}
