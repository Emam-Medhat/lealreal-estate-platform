<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DefiPool extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'token_pair',
        'type',
        'total_liquidity',
        'total_liquidity_usd',
        'apy',
        'volume_24h',
        'fees_24h',
        'min_deposit',
        'withdraw_fee',
        'is_active',
        'description',
        'protocol'
    ];

    protected $casts = [
        'total_liquidity' => 'decimal:8',
        'total_liquidity_usd' => 'decimal:2',
        'apy' => 'decimal:2',
        'volume_24h' => 'decimal:2',
        'fees_24h' => 'decimal:2',
        'min_deposit' => 'decimal:8',
        'withdraw_fee' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function positions(): HasMany
    {
        return $this->hasMany(DefiPosition::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(DefiTransaction::class);
    }

    public function activePositions(): HasMany
    {
        return $this->hasMany(DefiPosition::class, 'pool_id')->where('status', 'active');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByApy($query, string $direction = 'desc')
    {
        return $query->orderBy('apy', $direction);
    }

    public function getFormattedApyAttribute(): string
    {
        return number_format($this->apy, 2) . '%';
    }

    public function getFormattedLiquidityAttribute(): string
    {
        return number_format($this->total_liquidity, 4) . ' ' . explode('/', $this->token_pair)[0];
    }

    public function getFormattedLiquidityUsdAttribute(): string
    {
        return '$' . number_format($this->total_liquidity_usd, 2);
    }
}
