<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestorRoiCalculation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'investor_id',
        'portfolio_id',
        'calculation_type',
        'initial_investment',
        'current_value',
        'total_returns',
        'roi_percentage',
        'annualized_roi',
        'holding_period_days',
        'risk_adjusted_roi',
        'benchmark_comparison',
        'calculation_method',
        'assumptions',
        'notes',
        'calculated_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'benchmark_comparison' => 'array',
        'assumptions' => 'array',
        'initial_investment' => 'decimal:15,2',
        'current_value' => 'decimal:15,2',
        'total_returns' => 'decimal:15,2',
        'roi_percentage' => 'decimal:10,4',
        'annualized_roi' => 'decimal:10,4',
        'risk_adjusted_roi' => 'decimal:10,4',
        'holding_period_days' => 'integer',
        'calculated_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(InvestorPortfolio::class);
    }

    // Scopes
    public function scopeByCalculationType($query, $type)
    {
        return $query->where('calculation_type', $type);
    }

    public function scopeByCalculationMethod($query, $method)
    {
        return $query->where('calculation_method', $method);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('calculated_at', [$startDate, $endDate]);
    }

    // Helper methods
    public function isPositive(): bool
    {
        return $this->roi_percentage > 0;
    }

    public function isNegative(): bool
    {
        return $this->roi_percentage < 0;
    }

    public function getRoiFormattedAttribute(): string
    {
        return number_format($this->roi_percentage, 4) . '%';
    }

    public function getAnnualizedRoiFormattedAttribute(): string
    {
        return number_format($this->annualized_roi, 4) . '%';
    }

    public function getRiskAdjustedRoiFormattedAttribute(): string
    {
        return number_format($this->risk_adjusted_roi, 4) . '%';
    }

    public function getGainLossAttribute(): float
    {
        return $this->current_value - $this->initial_investment;
    }

    public function getGainLossFormattedAttribute(): string
    {
        $amount = $this->getGainLossAttribute();
        return ($amount >= 0 ? '+' : '') . number_format($amount, 2);
    }

    public function getInitialInvestmentFormattedAttribute(): string
    {
        return number_format($this->initial_investment, 2);
    }

    public function getCurrentValueFormattedAttribute(): string
    {
        return number_format($this->current_value, 2);
    }

    public function getTotalReturnsFormattedAttribute(): string
    {
        return number_format($this->total_returns, 2);
    }

    public function getHoldingPeriodAttribute(): string
    {
        $days = $this->holding_period_days;
        if ($days < 30) return $days . ' days';
        if ($days < 365) return round($days / 30) . ' months';
        return round($days / 365, 1) . ' years';
    }

    public function getCalculationTypeAttribute(): string
    {
        return $this->calculation_type ?? 'simple';
    }

    public function getCalculationMethodAttribute(): string
    {
        return $this->calculation_method ?? 'time_weighted';
    }

    public function getCalculatedAtFormattedAttribute(): string
    {
        return $this->calculated_at->format('Y-m-d H:i:s');
    }

    public function getBenchmarkComparisonAttribute(): array
    {
        return $this->benchmark_comparison ?? [];
    }

    public function getAssumptionsAttribute(): array
    {
        return $this->assumptions ?? [];
    }

    public function getPerformanceLevelAttribute(): string
    {
        $roi = $this->roi_percentage;
        if ($roi > 20) return 'Excellent';
        if ($roi > 15) return 'Very Good';
        if ($roi > 10) return 'Good';
        if ($roi > 5) return 'Fair';
        if ($roi > 0) return 'Poor';
        return 'Loss';
    }

    public function getRiskAdjustedPerformanceAttribute(): array
    {
        return [
            'original_roi' => $this->roi_percentage,
            'risk_adjusted_roi' => $this->risk_adjusted_roi,
            'adjustment_factor' => $this->roi_percentage > 0 ? ($this->risk_adjusted_roi / $this->roi_percentage) : 0,
        ];
    }

    public function getBenchmarkPerformanceAttribute(): array
    {
        $benchmarks = $this->benchmark_comparison ?? [];
        $performance = [];
        
        foreach ($benchmarks as $benchmark) {
            $performance[] = [
                'benchmark' => $benchmark['benchmark'] ?? 'Unknown',
                'benchmark_value' => $benchmark['value'] ?? 0,
                'portfolio_value' => $this->roi_percentage,
                'outperformance' => $this->roi_percentage - ($benchmark['value'] ?? 0),
            ];
        }
        
        return $performance;
    }
}
