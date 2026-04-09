<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefiStakingReward extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'staking_id',
        'reward_amount',
        'reward_token_address',
        'reward_token_symbol',
        'reward_date',
        'transaction_hash',
        'block_network',
        'gas_fee',
        'net_reward_amount',
        'reward_type',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'reward_amount' => 'decimal:18,8',
        'net_reward_amount' => 'decimal:18,8',
        'gas_fee' => 'decimal:18,8',
        'reward_date' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function staking(): BelongsTo
    {
        return $this->belongsTo(DefiStaking::class);
    }

    public function rewardToken(): BelongsTo
    {
        return $this->belongsTo(CryptoToken::class, 'reward_token_address', 'address');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('reward_type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('reward_date', [$startDate, $endDate]);
    }

    public function scopeByNetwork($query, $network)
    {
        return $query->where('block_network', $network);
    }

    // Helper methods
    public function getRewardAmountFormattedAttribute(): string
    {
        return number_format($this->reward_amount, 8);
    }

    public function getNetRewardAmountFormattedAttribute(): string
    {
        return number_format($this->net_reward_amount, 8);
    }

    public function getGasFeeFormattedAttribute(): string
    {
        return number_format($this->gas_fee, 8);
    }

    public function getRewardDateFormattedAttribute(): string
    {
        return $this->reward_date->format('Y-m-d H:i:s');
    }

    public function getTransactionHashAttribute(): string
    {
        return $this->transaction_hash ?? '';
    }

    public function getBlockNetworkAttribute(): string
    {
        return $this->block_network ?? '';
    }

    public function getRewardTypeAttribute(): string
    {
        return $this->reward_type ?? 'staking';
    }

    public function getRewardTokenSymbolAttribute(): string
    {
        return $this->reward_token_symbol ?? '';
    }

    public function getRewardTokenAddressAttribute(): string
    {
        return $this->reward_token_address ?? '';
    }

    public function getNotesAttribute(): string
    {
        return $this->notes ?? '';
    }

    public function getExplorerUrlAttribute(): string
    {
        $networks = [
            'ethereum' => 'https://etherscan.io/tx/',
            'polygon' => 'https://polygonscan.com/tx/',
            'bnb_chain' => 'https://bscscan.com/tx/',
            'avalanche' => 'https://snowtrace.io/tx/',
            'arbitrum' => 'https://arbiscan.io/tx/',
        ];

        $baseUrl = $networks[$this->block_network] ?? 'https://etherscan.io/tx/';
        return $this->transaction_hash ? $baseUrl . $this->transaction_hash : '';
    }

    public function getFeePercentageAttribute(): float
    {
        if ($this->reward_amount == 0) return 0;
        return ($this->gas_fee / $this->reward_amount) * 100;
    }

    public function getFeePercentageFormattedAttribute(): string
    {
        return number_format($this->getFeePercentageAttribute(), 4) . '%';
    }

    public function getRecentAttribute(): bool
    {
        return $this->reward_date->diffInHours(now()) < 24;
    }

    public function getFormattedRewardWithSymbolAttribute(): string
    {
        $symbol = $this->reward_token_symbol ? ' ' . $this->reward_token_symbol : '';
        return $this->getRewardAmountFormattedAttribute() . $symbol;
    }

    public function getFormattedNetRewardWithSymbolAttribute(): string
    {
        $symbol = $this->reward_token_symbol ? ' ' . $this->reward_token_symbol : '';
        return $this->getNetRewardAmountFormattedAttribute() . $symbol;
    }

    public function getRewardStatusAttribute(): string
    {
        switch ($this->reward_type) {
            case 'staking':
                return '🔒 Staking Reward';
            case 'bonus':
                return '🎁 Bonus Reward';
            case 'compound':
                return '🔄 Compound Reward';
            default:
                return '📋 Reward';
        }
    }

    public function isHighValueAttribute(): bool
    {
        return $this->reward_amount > 100; // Consider rewards over 100 tokens as high value
    }

    public function getDailyAverageAttribute(): float
    {
        if (!$this->staking) return 0;
        
        $daysStaked = $this->staking->getDaysStakedAttribute();
        if ($daysStaked == 0) return 0;
        
        return $this->net_reward_amount / $daysStaked;
    }

    public function getDailyAverageFormattedAttribute(): string
    {
        return number_format($this->getDailyAverageAttribute(), 8);
    }

    public function getMonthlyEstimateAttribute(): float
    {
        return $this->getDailyAverageAttribute() * 30;
    }

    public function getMonthlyEstimateFormattedAttribute(): string
    {
        return number_format($this->getMonthlyEstimateAttribute(), 8);
    }

    public function getYearlyEstimateAttribute(): float
    {
        return $this->getDailyAverageAttribute() * 365;
    }

    public function getYearlyEstimateFormattedAttribute(): string
    {
        return number_format($this->getYearlyEstimateAttribute(), 8);
    }
}
