<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestmentFund extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fund_name',
        'description',
        'fund_type',
        'fund_manager_id',
        'minimum_investment',
        'maximum_investment',
        'expected_return',
        'risk_level',
        'expense_ratio',
        'current_nav',
        'previous_nav',
        'total_assets',
        'investor_count',
        'status',
        'inception_date',
        'documents',
        'holdings',
        'performance_history',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'documents' => 'array',
        'holdings' => 'array',
        'performance_history' => 'array',
        'minimum_investment' => 'decimal:15,2',
        'maximum_investment' => 'decimal:15,2',
        'expected_return' => 'decimal:8,4',
        'expense_ratio' => 'decimal:6,4',
        'current_nav' => 'decimal:10,6',
        'previous_nav' => 'decimal:10,6',
        'total_assets' => 'decimal:15,2',
        'inception_date' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function fundManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fund_manager_id');
    }

    public function investments(): HasMany
    {
        return $this->hasMany(InvestmentFundInvestment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('fund_type', $type);
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

    public function getNavChangeAttribute(): float
    {
        if (!$this->previous_nav || $this->previous_nav == 0) return 0;
        return (($this->current_nav - $this->previous_nav) / $this->previous_nav) * 100;
    }

    public function getNavChangeFormattedAttribute(): string
    {
        $change = $this->getNavChangeAttribute();
        return ($change >= 0 ? '+' : '') . number_format($change, 4) . '%';
    }

    public function getMinimumInvestmentFormattedAttribute(): string
    {
        return number_format($this->minimum_investment, 2);
    }

    public function getMaximumInvestmentFormattedAttribute(): string
    {
        return number_format($this->maximum_investment, 2);
    }

    public function getExpectedReturnFormattedAttribute(): string
    {
        return number_format($this->expected_return, 2) . '%';
    }

    public function getExpenseRatioFormattedAttribute(): string
    {
        return number_format($this->expense_ratio, 4) . '%';
    }

    public function getCurrentNavFormattedAttribute(): string
    {
        return number_format($this->current_nav, 6);
    }

    public function getTotalAssetsFormattedAttribute(): string
    {
        return number_format($this->total_assets, 2);
    }

    public function getDocumentsCountAttribute(): int
    {
        return count($this->documents ?? []);
    }

    public function getHoldingsCountAttribute(): int
    {
        return count($this->holdings ?? []);
    }

    public function getPerformanceHistoryCountAttribute(): int
    {
        return count($this->performance_history ?? []);
    }

    public function getInceptionDateFormattedAttribute(): string
    {
        return $this->inception_date ? $this->inception_date->format('Y-m-d') : '';
    }

    public function getFundAgeAttribute(): string
    {
        if (!$this->inception_date) return 'N/A';
        
        $years = $this->inception_date->diffInYears(now());
        $months = $this->inception_date->diffInMonths(now()) % 12;
        
        if ($years > 0) {
            return $years . ' year' . ($years > 1 ? 's' : '') . ($months > 0 ? ' ' . $months . ' month' . ($months > 1 ? 's' : '') : '');
        }
        
        return $months . ' month' . ($months > 1 ? 's' : '');
    }

    public function getFundTypeAttribute(): string
    {
        return $this->fund_type ?? 'equity';
    }

    public function getRiskLevelAttribute(): string
    {
        return $this->risk_level ?? 'medium';
    }

    public function getStatusAttribute(): string
    {
        return $this->status ?? 'active';
    }

    public function getFundNameAttribute(): string
    {
        return $this->fund_name ?? '';
    }

    public function getDescriptionAttribute(): string
    {
        return $this->description ?? '';
    }

    public function getDocumentsAttribute(): array
    {
        return $this->documents ?? [];
    }

    public function getHoldingsAttribute(): array
    {
        return $this->holdings ?? [];
    }

    public function getPerformanceHistoryAttribute(): array
    {
        return $this->performance_history ?? [];
    }

    public function getTopHoldingsAttribute(): array
    {
        $holdings = $this->holdings ?? [];
        return collect($holdings)
            ->sortByDesc('weight')
            ->take(10)
            ->values()
            ->toArray();
    }

    public function getLatestPerformanceAttribute(): ?array
    {
        $history = $this->performance_history ?? [];
        return collect($history)->last();
    }

    public function getAnnualizedReturnAttribute(): float
    {
        $history = $this->performance_history ?? [];
        if (empty($history)) return 0;
        
        $latest = collect($history)->last();
        $earliest = collect($history)->first();
        
        if (!$latest || !$earliest) return 0;
        
        $years = $this->inception_date ? $this->inception_date->diffInYears(now()) : 1;
        if ($years == 0) $years = 1;
        
        return pow($latest['nav'] / $earliest['nav'], 1 / $years) - 1;
    }
}
