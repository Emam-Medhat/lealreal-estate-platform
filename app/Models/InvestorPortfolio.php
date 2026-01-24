<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvestorPortfolio extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'investor_id',
        'investment_name',
        'description',
        'investment_type',
        'sector',
        'amount_invested',
        'current_value',
        'expected_return_rate',
        'expected_return_date',
        'expected_return_amount',
        'risk_level',
        'status',
        'auto_reinvest',
        'minimum_holding_period',
        'notes',
        'documents',
        'images',
        'total_returns',
        'sector_count',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'documents' => 'array',
        'images' => 'array',
        'amount_invested' => 'decimal:15,2',
        'current_value' => 'decimal:15,2',
        'expected_return_rate' => 'decimal:8,4',
        'expected_return_amount' => 'decimal:15,2',
        'total_returns' => 'decimal:15,2',
        'auto_reinvest' => 'boolean',
        'minimum_holding_period' => 'integer',
        'sector_count' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InvestorTransaction::class);
    }

    public function roiCalculations(): HasMany
    {
        return $this->hasMany(InvestorRoiCalculation::class);
    }

    public function riskAssessments(): HasMany
    {
        return $this->hasMany(InvestorRiskAssessment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('investment_type', $type);
    }

    public function scopeBySector($query, $sector)
    {
        return $query->where('sector', $sector);
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

    public function getRoiAttribute(): float
    {
        return $this->amount_invested > 0 ? (($this->current_value - $this->amount_invested) / $this->amount_invested) * 100 : 0;
    }

    public function getGainLossAttribute(): float
    {
        return $this->current_value - $this->amount_invested;
    }

    public function getGainLossPercentageAttribute(): float
    {
        return $this->getRoiAttribute();
    }

    public function getDocumentsCountAttribute(): int
    {
        return count($this->documents ?? []);
    }

    public function getImagesCountAttribute(): int
    {
        return count($this->images ?? []);
    }

    public function getHoldingPeriodDaysAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    public function getExpectedAnnualReturnAttribute(): float
    {
        return $this->amount_invested * ($this->expected_return_rate / 100);
    }

    public function getRiskLevelAttribute(): string
    {
        return $this->risk_level ?? 'medium';
    }

    public function getInvestmentTypeAttribute(): string
    {
        return $this->investment_type ?? 'stocks';
    }

    public function getSectorAttribute(): string
    {
        return $this->sector ?? 'technology';
    }

    public function getStatusAttribute(): string
    {
        return $this->status ?? 'active';
    }

    public function getCurrentValueFormattedAttribute(): string
    {
        return number_format($this->current_value, 2);
    }

    public function getAmountInvestedFormattedAttribute(): string
    {
        return number_format($this->amount_invested, 2);
    }

    public function getExpectedReturnDateFormattedAttribute(): string
    {
        return $this->expected_return_date ? $this->expected_return_date->format('Y-m-d') : 'N/A';
    }
}
