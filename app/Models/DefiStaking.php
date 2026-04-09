<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefiStaking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'investor_id',
        'staking_purpose',
        'token_address',
        'token_symbol',
        'amount_staked',
        'staking_period_days',
        'apy_rate',
        'reward_frequency',
        'lockup_period',
        'early_withdrawal_penalty',
        'minimum_staking_amount',
        'maximum_staking_amount',
        'auto_compound',
        'rewards_token_address',
        'rewards_token_symbol',
        'smart_contract_address',
        'blockchain_network',
        'protocol_name',
        'protocol_version',
        'risk_level',
        'status',
        'expected_rewards',
        'total_rewards_earned',
        'current_value',
        'unstake_date',
        'last_rewards_update',
        'documents',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'documents' => 'array',
        'amount_staked' => 'decimal:18,8',
        'staking_period_days' => 'integer',
        'apy_rate' => 'decimal:8,4',
        'early_withdrawal_penalty' => 'decimal:8,4',
        'minimum_staking_amount' => 'decimal:18,8',
        'maximum_staking_amount' => 'decimal:18,8',
        'auto_compound' => 'boolean',
        'expected_rewards' => 'decimal:18,8',
        'total_rewards_earned' => 'decimal:18,8',
        'current_value' => 'decimal:18,8',
        'unstake_date' => 'datetime',
        'last_rewards_update' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    public function token(): BelongsTo
    {
        return $this->belongsTo(CryptoToken::class, 'token_address', 'address');
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(DefiStakingReward::class);
    }

    public function unstakes(): HasMany
    {
        return $this->hasMany(DefiStakingUnstake::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(DefiStakingClaim::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByProtocol($query, $protocol)
    {
        return $query->where('protocol_name', $protocol);
    }

    public function scopeByBlockchain($query, $network)
    {
        return $query->where('blockchain_network', $network);
    }

    public function scopeByRiskLevel($query, $risk)
    {
        return $query->where('risk_level', $risk);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isLocked(): bool
    {
        return $this->lockup_period !== 'none';
    }

    public function getDaysStakedAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    public function getDaysRemainingAttribute(): int
    {
        if (!$this->unstake_date) return 0;
        return max(0, now()->diffInDays($this->unstake_date));
    }

    public function getProgressPercentageAttribute(): float
    {
        $totalDays = $this->staking_period_days;
        if ($totalDays == 0) return 0;
        
        $daysStaked = $this->getDaysStakedAttribute();
        return min(100, ($daysStaked / $totalDays) * 100);
    }

    public function getEstimatedDailyRewardsAttribute(): float
    {
        if ($this->amount_staked == 0) return 0;
        
        $dailyRate = $this->apy_rate / 100 / 365;
        return $this->amount_staked * $dailyRate;
    }

    public function getEstimatedMonthlyRewardsAttribute(): float
    {
        return $this->getEstimatedDailyRewardsAttribute() * 30;
    }

    public function getEstimatedYearlyRewardsAttribute(): float
    {
        return $this->amount_staked * ($this->apy_rate / 100);
    }

    public function getActualDailyRewardsAttribute(): float
    {
        if ($this->getDaysStakedAttribute() == 0) return 0;
        
        return $this->total_rewards_earned / $this->getDaysStakedAttribute();
    }

    public function getActualMonthlyRewardsAttribute(): float
    {
        return $this->getActualDailyRewardsAttribute() * 30;
    }

    public function getActualYearlyRewardsAttribute(): float
    {
        return $this->getActualDailyRewardsAttribute() * 365;
    }

    public function getRoiAttribute(): float
    {
        if ($this->amount_staked == 0) return 0;
        
        return ($this->total_rewards_earned / $this->amount_staked) * 100;
    }

    public function getProjectedTotalRewardsAttribute(): float
    {
        return $this->amount_staked * ($this->apy_rate / 100) * ($this->staking_period_days / 365);
    }

    public function getProjectedRoiAttribute(): float
    {
        if ($this->amount_staked == 0) return 0;
        
        return ($this->getProjectedTotalRewardsAttribute() / $this->amount_staked) * 100;
    }

    public function getAmountStakedFormattedAttribute(): string
    {
        return number_format($this->amount_staked, 8);
    }

    public function getCurrentValueFormattedAttribute(): string
    {
        return number_format($this->current_value, 8);
    }

    public function getTotalRewardsEarnedFormattedAttribute(): string
    {
        return number_format($this->total_rewards_earned, 8);
    }

    public function getExpectedRewardsFormattedAttribute(): string
    {
        return number_format($this->expected_rewards, 8);
    }

    public function getApyRateFormattedAttribute(): string
    {
        return number_format($this->apy_rate, 2) . '%';
    }

    public function getRoiFormattedAttribute(): string
    {
        return number_format($this->getRoiAttribute(), 2) . '%';
    }

    public function getProjectedRoiFormattedAttribute(): string
    {
        return number_format($this->getProjectedRoiAttribute(), 2) . '%';
    }

    public function getStakingPurposeAttribute(): string
    {
        return $this->staking_purpose ?? '';
    }

    public function getTokenSymbolAttribute(): string
    {
        return $this->token_symbol ?? '';
    }

    public function getProtocolNameAttribute(): string
    {
        return $this->protocol_name ?? '';
    }

    public function getProtocolVersionAttribute(): string
    {
        return $this->protocol_version ?? '';
    }

    public function getRiskLevelAttribute(): string
    {
        return $this->risk_level ?? 'medium';
    }

    public function getStatusAttribute(): string
    {
        return $this->status ?? 'active';
    }

    public function getRewardFrequencyAttribute(): string
    {
        return $this->reward_frequency ?? 'continuous';
    }

    public function getLockupPeriodAttribute(): string
    {
        return $this->lockup_period ?? 'none';
    }

    public function getBlockchainNetworkAttribute(): string
    {
        return $this->blockchain_network ?? 'ethereum';
    }

    public function getUnstakeDateFormattedAttribute(): string
    {
        return $this->unstake_date ? $this->unstake_date->format('Y-m-d H:i:s') : '';
    }

    public function getDocumentsCountAttribute(): int
    {
        return count($this->documents ?? []);
    }

    public function getNotesAttribute(): string
    {
        return $this->notes ?? '';
    }

    public function getDocumentsAttribute(): array
    {
        return $this->documents ?? [];
    }

    public function getPerformanceComparisonAttribute(): array
    {
        return [
            'actual_apy' => $this->getRoiAttribute(),
            'expected_apy' => $this->apy_rate,
            'performance_ratio' => $this->apy_rate > 0 ? ($this->getRoiAttribute() / $this->apy_rate) : 0,
            'days_staked' => $this->getDaysStakedAttribute(),
            'total_days' => $this->staking_period_days,
            'completion_percentage' => $this->getProgressPercentageAttribute(),
        ];
    }

    public function getStakingStatusAttribute(): string
    {
        if (!$this->isActive()) return $this->status;
        
        $daysRemaining = $this->getDaysRemainingAttribute();
        if ($daysRemaining <= 0) return 'Ready to Unstake';
        if ($daysRemaining <= 7) return 'Expiring Soon';
        if ($daysRemaining <= 30) return 'Ending Soon';
        return 'Active';
    }

    public function getCompoundEffectAttribute(): float
    {
        if (!$this->auto_compound) return 0;
        
        $dailyRate = $this->apy_rate / 100 / 365;
        $days = $this->getDaysStakedAttribute();
        
        return $this->amount_staked * (pow(1 + $dailyRate, $days) - 1 - ($dailyRate * $days));
    }
}
