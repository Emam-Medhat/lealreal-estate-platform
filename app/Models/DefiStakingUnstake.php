<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefiStakingUnstake extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'staking_id',
        'amount_unstaked',
        'penalty_amount',
        'net_amount',
        'unstake_date',
        'reason',
        'transaction_hash',
        'block_network',
        'gas_fee',
        'status',
        'processed_date',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount_unstaked' => 'decimal:18,8',
        'penalty_amount' => 'decimal:18,8',
        'net_amount' => 'decimal:18,8',
        'gas_fee' => 'decimal:18,8',
        'unstake_date' => 'datetime',
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

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('unstake_date', [$startDate, $endDate]);
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

    public function getAmountUnstakedFormattedAttribute(): string
    {
        return number_format($this->amount_unstaked, 8);
    }

    public function getPenaltyAmountFormattedAttribute(): string
    {
        return number_format($this->penalty_amount, 8);
    }

    public function getNetAmountFormattedAttribute(): string
    {
        return number_format($this->net_amount, 8);
    }

    public function getGasFeeFormattedAttribute(): string
    {
        return number_format($this->gas_fee, 8);
    }

    public function getUnstakeDateFormattedAttribute(): string
    {
        return $this->unstake_date->format('Y-m-d H:i:s');
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

    public function getStatusAttribute(): string
    {
        return $this->status ?? 'pending';
    }

    public function getReasonAttribute(): string
    {
        return $this->reason ?? '';
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

    public function getPenaltyPercentageAttribute(): float
    {
        if ($this->amount_unstaked == 0) return 0;
        return ($this->penalty_amount / $this->amount_unstaked) * 100;
    }

    public function getPenaltyPercentageFormattedAttribute(): string
    {
        return number_format($this->getPenaltyPercentageAttribute(), 4) . '%';
    }

    public function getFeePercentageAttribute(): float
    {
        if ($this->amount_unstaked == 0) return 0;
        return ($this->gas_fee / $this->amount_unstaked) * 100;
    }

    public function getFeePercentageFormattedAttribute(): string
    {
        return number_format($this->getFeePercentageAttribute(), 4) . '%';
    }

    public function getTotalDeductionsAttribute(): float
    {
        return $this->penalty_amount + $this->gas_fee;
    }

    public function getTotalDeductionsFormattedAttribute(): string
    {
        return number_format($this->getTotalDeductionsAttribute(), 8);
    }

    public function getTotalDeductionPercentageAttribute(): float
    {
        if ($this->amount_unstaked == 0) return 0;
        return ($this->getTotalDeductionsAttribute() / $this->amount_unstaked) * 100;
    }

    public function getTotalDeductionPercentageFormattedAttribute(): string
    {
        return number_format($this->getTotalDeductionPercentageAttribute(), 4) . '%';
    }

    public function getRecentAttribute(): bool
    {
        return $this->unstake_date->diffInHours(now()) < 24;
    }

    public function getProcessingTimeAttribute(): ?int
    {
        if (!$this->processed_date) return null;
        return $this->unstake_date->diffInHours($this->processed_date);
    }

    public function getUnstakeStatusAttribute(): string
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

    public function hasPenalty(): bool
    {
        return $this->penalty_amount > 0;
    }

    public function isEarlyUnstake(): bool
    {
        if (!$this->staking) return false;
        return $this->unstake_date->lt($this->staking->unstake_date);
    }

    public function getUnstakeReasonAttribute(): string
    {
        switch ($this->reason) {
            case 'emergency':
                return '🚨 Emergency Unstake';
            case 'profit_taking':
                return '💰 Profit Taking';
            case 'rebalancing':
                return '⚖️ Portfolio Rebalancing';
            case 'lockup_expired':
                return '⏰ Lockup Period Expired';
            default:
                return '📋 Manual Unstake';
        }
    }

    public function getFormattedAmountWithTokenAttribute(): string
    {
        if (!$this->staking) return $this->getAmountUnstakedFormattedAttribute();
        $symbol = $this->staking->token_symbol ? ' ' . $this->staking->token_symbol : '';
        return $this->getAmountUnstakedFormattedAttribute() . $symbol;
    }

    public function getFormattedNetAmountWithTokenAttribute(): string
    {
        if (!$this->staking) return $this->getNetAmountFormattedAttribute();
        $symbol = $this->staking->token_symbol ? ' ' . $this->staking->token_symbol : '';
        return $this->getNetAmountFormattedAttribute() . $symbol;
    }
}
