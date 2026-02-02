<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DefiPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pool_id',
        'amount',
        'shares',
        'earned_rewards',
        'status',
        'opened_at',
        'closed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'shares' => 'decimal:8',
        'earned_rewards' => 'decimal:8',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pool(): BelongsTo
    {
        return $this->belongsTo(DefiPool::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(DefiTransaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getTotalValueAttribute(): float
    {
        return $this->amount + $this->earned_rewards;
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 4) . ' ' . explode('/', $this->pool->token_pair)[0];
    }

    public function getFormattedRewardsAttribute(): string
    {
        return number_format($this->earned_rewards, 4) . ' ' . explode('/', $this->pool->token_pair)[0];
    }
}
