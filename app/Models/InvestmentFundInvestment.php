<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestmentFundInvestment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fund_id',
        'investor_id',
        'investment_amount',
        'units_purchased',
        'status',
        'investment_date',
        'auto_reinvest',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'investment_amount' => 'decimal:15,2',
        'units_purchased' => 'decimal:15,6',
        'investment_date' => 'datetime',
        'auto_reinvest' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function fund(): BelongsTo
    {
        return $this->belongsTo(InvestmentFund::class);
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

    public function getUnitsPurchasedFormattedAttribute(): string
    {
        return number_format($this->units_purchased, 6);
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

    public function getCurrentValueAttribute(): float
    {
        if (!$this->fund || !$this->fund->current_nav) return 0;
        return $this->units_purchased * $this->fund->current_nav;
    }

    public function getCurrentValueFormattedAttribute(): string
    {
        return number_format($this->getCurrentValueAttribute(), 2);
    }

    public function getGainLossAttribute(): float
    {
        return $this->getCurrentValueAttribute() - $this->investment_amount;
    }

    public function getGainLossPercentageAttribute(): float
    {
        if ($this->investment_amount == 0) return 0;
        return ($this->getGainLossAttribute() / $this->investment_amount) * 100;
    }

    public function getGainLossFormattedAttribute(): string
    {
        $amount = $this->getGainLossAttribute();
        return ($amount >= 0 ? '+' : '') . number_format($amount, 2);
    }

    public function getGainLossPercentageFormattedAttribute(): string
    {
        $percentage = $this->getGainLossPercentageAttribute();
        return ($percentage >= 0 ? '+' : '') . number_format($percentage, 2) . '%';
    }

    public function getHoldingPeriodAttribute(): string
    {
        $days = $this->investment_date->diffInDays(now());
        if ($days < 30) return $days . ' days';
        if ($days < 365) return round($days / 30) . ' months';
        return round($days / 365, 1) . ' years';
    }
}
