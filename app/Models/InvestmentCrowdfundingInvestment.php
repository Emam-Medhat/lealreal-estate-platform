<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestmentCrowdfundingInvestment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'campaign_id',
        'investor_id',
        'investment_amount',
        'equity_percentage',
        'status',
        'investment_date',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'investment_amount' => 'decimal:15,2',
        'equity_percentage' => 'decimal:8,4',
        'investment_date' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(InvestmentCrowdfunding::class);
    }

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('investment_date', [$startDate, $endDate]);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getInvestmentAmountFormattedAttribute(): string
    {
        return number_format($this->investment_amount, 2);
    }

    public function getEquityPercentageFormattedAttribute(): string
    {
        return number_format($this->equity_percentage, 4) . '%';
    }

    public function getInvestmentDateFormattedAttribute(): string
    {
        return $this->investment_date->format('Y-m-d H:i:s');
    }

    public function getStatusAttribute(): string
    {
        return $this->status ?? 'pending';
    }

    public function getNotesAttribute(): string
    {
        return $this->notes ?? '';
    }

    public function getHoldingPeriodAttribute(): string
    {
        $days = $this->investment_date->diffInDays(now());
        if ($days < 30) return $days . ' days';
        if ($days < 365) return round($days / 30) . ' months';
        return round($days / 365, 1) . ' years';
    }

    public function getProjectedValueAttribute(): float
    {
        if (!$this->campaign || !$this->campaign->projected_return_rate) return $this->investment_amount;
        
        $years = $this->investment_date->diffInYears(now());
        if ($years == 0) $years = 1;
        
        return $this->investment_amount * pow(1 + ($this->campaign->projected_return_rate / 100), $years);
    }

    public function getProjectedValueFormattedAttribute(): string
    {
        return number_format($this->getProjectedValueAttribute(), 2);
    }

    public function getProjectedGainAttribute(): float
    {
        return $this->getProjectedValueAttribute() - $this->investment_amount;
    }

    public function getProjectedGainFormattedAttribute(): string
    {
        $gain = $this->getProjectedGainAttribute();
        return ($gain >= 0 ? '+' : '') . number_format($gain, 2);
    }

    public function getProjectedGainPercentageAttribute(): float
    {
        return $this->investment_amount > 0 ? ($this->getProjectedGainAttribute() / $this->investment_amount) * 100 : 0;
    }

    public function getProjectedGainPercentageFormattedAttribute(): string
    {
        $percentage = $this->getProjectedGainPercentageAttribute();
        return ($percentage >= 0 ? '+' : '') . number_format($percentage, 2) . '%';
    }
}
