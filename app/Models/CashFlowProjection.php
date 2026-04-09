<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class CashFlowProjection extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_financial_analysis_id',
        'roi_calculation_id',
        'projection_year',
        'rental_income',
        'other_income',
        'operating_expenses',
        'capital_expenditures',
        'financing_costs',
        'tax_payments',
        'net_operating_income',
        'cash_flow_before_tax',
        'tax_benefits',
        'cash_flow_after_tax',
        'loan_principal_payment',
        'loan_interest_payment',
        'total_cash_flow',
        'cumulative_cash_flow',
        'vacancy_rate',
        'inflation_rate',
        'appreciation_rate',
        'property_value',
        'loan_balance',
        'equity_position',
        'cash_flow_per_unit',
        'debt_service_coverage_ratio',
        'breakeven_occupancy_rate',
        'scenario_type',
        'assumptions',
        'notes'
    ];

    protected $casts = [
        'projection_year' => 'integer',
        'rental_income' => 'decimal:2',
        'other_income' => 'decimal:2',
        'operating_expenses' => 'decimal:2',
        'capital_expenditures' => 'decimal:2',
        'financing_costs' => 'decimal:2',
        'tax_payments' => 'decimal:2',
        'net_operating_income' => 'decimal:2',
        'cash_flow_before_tax' => 'decimal:2',
        'tax_benefits' => 'decimal:2',
        'cash_flow_after_tax' => 'decimal:2',
        'loan_principal_payment' => 'decimal:2',
        'loan_interest_payment' => 'decimal:2',
        'total_cash_flow' => 'decimal:2',
        'cumulative_cash_flow' => 'decimal:2',
        'vacancy_rate' => 'decimal:3',
        'inflation_rate' => 'decimal:3',
        'appreciation_rate' => 'decimal:3',
        'property_value' => 'decimal:2',
        'loan_balance' => 'decimal:2',
        'equity_position' => 'decimal:2',
        'cash_flow_per_unit' => 'decimal:2',
        'debt_service_coverage_ratio' => 'decimal:3',
        'breakeven_occupancy_rate' => 'decimal:3',
        'assumptions' => 'json',
        'notes' => 'text'
    ];

    public function propertyFinancialAnalysis(): BelongsTo
    {
        return $this->belongsTo(PropertyFinancialAnalysis::class);
    }

    public function roiCalculation(): BelongsTo
    {
        return $this->belongsTo(RoiCalculation::class);
    }

    public function scopeByAnalysis(Builder $query, $analysisId): Builder
    {
        return $query->where('property_financial_analysis_id', $analysisId);
    }

    public function scopeByYear(Builder $query, $year): Builder
    {
        return $query->where('projection_year', $year);
    }

    public function scopeByScenario(Builder $query, $scenarioType): Builder
    {
        return $query->where('scenario_type', $scenarioType);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('projection_year');
    }

    public function calculateEffectiveGrossIncome(): float
    {
        return $this->rental_income * (1 - $this->vacancy_rate) + $this->other_income;
    }

    public function calculateNetCashFlow(): float
    {
        return $this->cash_flow_after_tax - $this->loan_principal_payment;
    }

    public function calculateCashOnCashReturn(): float
    {
        $roi = $this->roiCalculation;
        if (!$roi || $roi->total_investment <= 0) {
            return 0;
        }

        return ($this->calculateNetCashFlow() / $roi->total_investment) * 100;
    }

    public function calculateReturnOnEquity(): float
    {
        if ($this->equity_position <= 0) {
            return 0;
        }

        return ($this->calculateNetCashFlow() / $this->equity_position) * 100;
    }

    public function getCashFlowMetrics(): array
    {
        return [
            'effective_gross_income' => $this->calculateEffectiveGrossIncome(),
            'net_operating_income' => $this->net_operating_income,
            'cash_flow_before_tax' => $this->cash_flow_before_tax,
            'cash_flow_after_tax' => $this->cash_flow_after_tax,
            'net_cash_flow' => $this->calculateNetCashFlow(),
            'cash_on_cash_return' => $this->calculateCashOnCashReturn(),
            'return_on_equity' => $this->calculateReturnOnEquity(),
            'debt_service_coverage_ratio' => $this->debt_service_coverage_ratio,
            'cumulative_cash_flow' => $this->cumulative_cash_flow,
            'property_value' => $this->property_value,
            'loan_balance' => $this->loan_balance,
            'equity_position' => $this->equity_position
        ];
    }

    public function assessCashFlowQuality(): string
    {
        if ($this->debt_service_coverage_ratio >= 1.5 && $this->total_cash_flow > 0) {
            return 'excellent';
        } elseif ($this->debt_service_coverage_ratio >= 1.2 && $this->total_cash_flow > 0) {
            return 'good';
        } elseif ($this->debt_service_coverage_ratio >= 1.0 && $this->total_cash_flow >= 0) {
            return 'acceptable';
        } else {
            return 'poor';
        }
    }
}
