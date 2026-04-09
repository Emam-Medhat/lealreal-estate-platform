<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefiTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pool_id',
        'position_id',
        'type',
        'amount',
        'fee',
        'tx_hash',
        'status',
        'executed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'fee' => 'decimal:8',
        'executed_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pool(): BelongsTo
    {
        return $this->belongsTo(DefiPool::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(DefiPosition::class);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 4) . ' ' . explode('/', $this->pool->token_pair)[0];
    }

    public function getFormattedFeeAttribute(): string
    {
        return number_format($this->fee, 4) . ' ' . explode('/', $this->pool->token_pair)[0];
    }

    public function getNetAmountAttribute(): float
    {
        if ($this->type === 'withdraw') {
            return $this->amount - $this->fee;
        }
        return $this->amount;
    }
}
