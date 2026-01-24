<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefiStakingClaim extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'staking_id',
        'amount_claimed',
        'claim_date',
        'transaction_hash',
        'block_network',
        'gas_fee',
        'net_amount_claimed',
        'claim_type',
        'notes',
        'status',
        'processed_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount_claimed' => 'decimal:18,8',
        'gas_fee' => 'decimal:18,8',
        'net_amount_claimed' => 'decimal:18,8',
        'claim_date' => 'datetime',
        'processed_date' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function staking(): BelongsTo
    {
        return $this->belongsTo(DefiStaking::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('claim_type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('claim_date', [$startDate, $endDate]);
    }

    public function scopeByNetwork($query, $network)
    {
        return $query->where('block_network', $network);
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessed(): bool
    {
        return $this->status === 'processed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function getAmountClaimedFormattedAttribute(): string
    {
        return number_format($this->amount_claimed, 8);
    }

    public function getNetAmountClaimedFormattedAttribute(): string
    {
        return number_format($this->net_amount_claimed, 8);
    }

    public function getGasFeeFormattedAttribute(): string
    {
        return number_format($this->gas_fee, 8);
    }

    public function getClaimDateFormattedAttribute(): string
    {
        return $this->claim_date->format('Y-m-d H:i:s');
    }

    public function getProcessedDateFormattedAttribute(): string
    {
        return $this->processed_date ? $this->processed_date->format('Y-m-d H:i:s') : '';
    }

    public function getTransactionHashAttribute(): string
    {
        return $this->transaction_hash ?? '';
    }

    public function getBlockNetworkAttribute(): string
    {
        return $this->block_network ?? '';
    }

    public function getClaimTypeAttribute(): string
    {
        return $this->claim_type ?? 'rewards';
    }

    public function getStatusAttribute(): string
    {
        return $this->status ?? 'pending';
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
        if ($this->amount_claimed == 0) return 0;
        return ($this->gas_fee / $this->amount_claimed) * 100;
    }

    public function getFeePercentageFormattedAttribute(): string
    {
        return number_format($this->getFeePercentageAttribute(), 4) . '%';
    }

    public function getNetAmountPercentageAttribute(): float
    {
        if ($this->amount_claimed == 0) return 0;
        return ($this->net_amount_claimed / $this->amount_claimed) * 100;
    }

    public function getNetAmountPercentageFormattedAttribute(): string
    {
        return number_format($this->getNetAmountPercentageAttribute(), 4) . '%';
    }

    public function getRecentAttribute(): bool
    {
        return $this->claim_date->diffInHours(now()) < 24;
    }

    public function getProcessingTimeAttribute(): ?int
    {
        if (!$this->processed_date) return null;
        return $this->claim_date->diffInHours($this->processed_date);
    }

    public function getClaimStatusAttribute(): string
    {
        switch ($this->status) {
            case 'processed':
                return '✅ Processed';
            case 'pending':
                return '⏳ Pending';
            case 'failed':
                return '❌ Failed';
            default:
                return '❓ Unknown';
        }
    }

    public function getClaimTypeDisplayAttribute(): string
    {
        switch ($this->claim_type) {
            case 'rewards':
                return '🎁 Reward Claim';
            case 'principal':
                return '💰 Principal Claim';
            case 'bonus':
                return '🎯 Bonus Claim';
            default:
                return '📋 Claim';
        }
    }

    public function getFormattedAmountWithTokenAttribute(): string
    {
        if (!$this->staking) return $this->getAmountClaimedFormattedAttribute();
        $symbol = $this->staking->rewards_token_symbol ? ' ' . $this->staking->rewards_token_symbol : '';
        return $this->getAmountClaimedFormattedAttribute() . $symbol;
    }

    public function getFormattedNetAmountWithTokenAttribute(): string
    {
        if (!$this->staking) return $this->getNetAmountClaimedFormattedAttribute();
        $symbol = $this->staking->rewards_token_symbol ? ' ' . $this->staking->rewards_token_symbol : '';
        return $this->getNetAmountClaimedFormattedAttribute() . $symbol;
    }

    public function isHighValueClaim(): bool
    {
        return $this->amount_claimed > 1000; // Consider claims over 1000 tokens as high value
    }

    public function hasHighFee(): bool
    {
        return $this->gas_fee > 0.01; // Consider fees over 0.01 as high
    }

    public function getClaimEfficiencyAttribute(): float
    {
        if ($this->amount_claimed == 0) return 0;
        return $this->net_amount_claimed / $this->amount_claimed;
    }

    public function getClaimEfficiencyFormattedAttribute(): string
    {
        return number_format($this->getClaimEfficiencyAttribute(), 4);
    }

    public function getClaimSummaryAttribute(): string
    {
        $type = $this->getClaimTypeDisplayAttribute();
        $amount = $this->getFormattedAmountWithTokenAttribute();
        $status = $this->getClaimStatusAttribute();
        
        return "{$type}: {$amount} ({$status})";
    }

    public function getProcessingDaysAttribute(): ?float
    {
        if (!$this->processed_date) return null;
        return $this->claim_date->diffInDays($this->processed_date);
    }

    public function isDelayed(): bool
    {
        $processingDays = $this->getProcessingDaysAttribute();
        return $processingDays && $processingDays > 1; // Consider claims taking more than 1 day as delayed
    }

    public function getPriorityLevelAttribute(): string
    {
        if ($this->isHighValueClaim()) return 'High';
        if ($this->isDelayed()) return 'Medium';
        return 'Normal';
    }
}
