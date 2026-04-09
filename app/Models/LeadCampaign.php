<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'start_date',
        'end_date',
        'budget',
        'target_audience',
        'goals',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'budget' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        return $query->where('start_date', '<=', now())
                    ->where(function($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
