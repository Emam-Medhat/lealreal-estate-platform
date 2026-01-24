<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class RoiCalculation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_financial_analysis_id',
        'calculation_date',
        'total_investment',
        'annual_cash_flow',
        'property_value_appreciation',
        'tax_benefits',
        'loan_principal_paydown',
        'total_return',
        'roi_percentage',
        'cash_on_cash_return',
        'internal_rate_of_return',
        'net_present_value',
        'payback_period_years',
        'profitability_index',
        'calculation_method',
        'assumptions',
        'scenario_type',
        'risk_adjusted_roi',
        'inflation_adjusted_roi',
        'notes'
    ];

    protected $casts = [
        'calculation_date' => 'date',
        'total_investment' => 'decimal:2',
        'annual_cash_flow' => 'decimal:2',
        'property_value_appreciation' => 'decimal:2',
        'tax_benefits' => 'decimal:2',
        'loan_principal_paydown' => 'decimal:2',
        'total_return' => 'decimal:2',
        'roi_percentage' => 'decimal:3',
        'cash_on_cash_return' => 'decimal:3',
        'internal_rate_of_return' => 'decimal:3',
        'net_present_value' => 'decimal:2',
        'payback_period_years' => 'decimal:2',
        'profitability_index' => 'decimal:3',
        'assumptions' => 'json',
        'risk_adjusted_roi' => 'decimal:3',
        'inflation_adjusted_roi' => 'decimal:3',
        'notes' => 'text'
    ];

    public function propertyFinancialAnalysis(): BelongsTo
    {
        return $this->belongsTo(PropertyFinancialAnalysis::class);
    }

    public function cashFlowProjections(): HasMany
    {
        return $this->hasMany(CashFlowProjection::class);
    }

    public function scopeByAnalysis(Builder $query, $analysisId): Builder
    {
        return $query->where('property_financial_analysis_id', $analysisId);
    }

    public function scopeByScenario(Builder $query, $scenarioType): Builder
    {
        return $query->where('scenario_type', $scenarioType);
    }

    public function scopeByMethod(Builder $query, $method): Builder
    {
        return $query->where('calculation_method', $method);
    }

    public function calculateAnnualizedReturn(): float
    {
        if ($this->total_investment <= 0 || $this->payback_period_years <= 0) {
            return 0;
        }

        $totalGain = $this->total_return - $this->total_investment;
        $annualizedReturn = pow(1 + ($totalGain / $this->total_investment), 1 / $this->payback_period_years) - 1;
        
        return $annualizedReturn * 100;
    }

    public function getRoiMetrics(): array
    {
        return [
            'roi_percentage' => $this->roi_percentage,
            'cash_on_cash_return' => $this->cash_on_cash_return,
            'internal_rate_of_return' => $this->internal_rate_of_return,
            'net_present_value' => $this->net_present_value,
            'payback_period_years' => $this->payback_period_years,
            'profitability_index' => $this->profitability_index,
            'annualized_return' => $this->calculateAnnualizedReturn(),
            'risk_adjusted_roi' => $this->risk_adjusted_roi,
            'inflation_adjusted_roi' => $this->inflation_adjusted_roi,
            'total_investment' => $this->total_investment,
            'total_return' => $this->total_return
        ];
    }

    public function assessInvestmentQuality(): string
    {
        if ($this->roi_percentage >= 15 && $this->cash_on_cash_return >= 12) {
            return 'excellent';
        } elseif ($this->roi_percentage >= 10 && $this->cash_on_cash_return >= 8) {
            return 'good';
        } elseif ($this->roi_percentage >= 5 && $this->cash_on_cash_return >= 4) {
            return 'acceptable';
        } else {
            return 'poor';
        }
    }
}
