<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class InvestmentScenario extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_financial_analysis_id',
        'scenario_name',
        'scenario_type',
        'description',
        'purchase_price',
        'down_payment_percentage',
        'loan_amount',
        'interest_rate',
        'loan_term_years',
        'rental_income_growth_rate',
        'expense_growth_rate',
        'vacancy_rate',
        'appreciation_rate',
        'inflation_rate',
        'holding_period_years',
        'selling_costs_percentage',
        'capital_gains_tax_rate',
        'renovation_costs',
        'property_management_fee',
        'insurance_costs',
        'property_tax_rate',
        'maintenance_reserve_percentage',
        'exit_strategy',
        'risk_tolerance_level',
        'investment_objective',
        'cash_flow_projections',
        'roi_calculations',
        'risk_metrics',
        'sensitivity_analysis',
        'monte_carlo_results',
        'probability_of_success',
        'best_case_value',
        'worst_case_value',
        'expected_value',
        'confidence_interval_lower',
        'confidence_interval_upper',
        'scenario_assumptions',
        'market_conditions',
        'economic_assumptions',
        'notes',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'down_payment_percentage' => 'decimal:3',
        'loan_amount' => 'decimal:2',
        'interest_rate' => 'decimal:3',
        'loan_term_years' => 'integer',
        'rental_income_growth_rate' => 'decimal:3',
        'expense_growth_rate' => 'decimal:3',
        'vacancy_rate' => 'decimal:3',
        'appreciation_rate' => 'decimal:3',
        'inflation_rate' => 'decimal:3',
        'holding_period_years' => 'integer',
        'selling_costs_percentage' => 'decimal:3',
        'capital_gains_tax_rate' => 'decimal:3',
        'renovation_costs' => 'decimal:2',
        'property_management_fee' => 'decimal:2',
        'insurance_costs' => 'decimal:2',
        'property_tax_rate' => 'decimal:3',
        'maintenance_reserve_percentage' => 'decimal:3',
        'cash_flow_projections' => 'json',
        'roi_calculations' => 'json',
        'risk_metrics' => 'json',
        'sensitivity_analysis' => 'json',
        'monte_carlo_results' => 'json',
        'probability_of_success' => 'decimal:3',
        'best_case_value' => 'decimal:2',
        'worst_case_value' => 'decimal:2',
        'expected_value' => 'decimal:2',
        'confidence_interval_lower' => 'decimal:2',
        'confidence_interval_upper' => 'decimal:2',
        'scenario_assumptions' => 'json',
        'market_conditions' => 'json',
        'economic_assumptions' => 'json',
        'notes' => 'text',
        'is_active' => 'boolean'
    ];

    public function propertyFinancialAnalysis(): BelongsTo
    {
        return $this->belongsTo(PropertyFinancialAnalysis::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByAnalysis(Builder $query, $analysisId): Builder
    {
        return $query->where('property_financial_analysis_id', $analysisId);
    }

    public function scopeByType(Builder $query, $type): Builder
    {
        return $query->where('scenario_type', $type);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function calculateTotalInvestment(): float
    {
        return $this->purchase_price + $this->renovation_costs;
    }

    public function calculateDownPayment(): float
    {
        return $this->purchase_price * ($this->down_payment_percentage / 100);
    }

    public function calculateEquityRequired(): float
    {
        return $this->calculateDownPayment() + $this->renovation_costs;
    }

    public function calculateMonthlyMortgagePayment(): float
    {
        if ($this->loan_amount <= 0 || $this->interest_rate <= 0 || $this->loan_term_years <= 0) {
            return 0;
        }

        $monthlyRate = $this->interest_rate / 12;
        $totalPayments = $this->loan_term_years * 12;
        
        if ($monthlyRate == 0) {
            return $this->loan_amount / $totalPayments;
        }

        return $this->loan_amount * 
            ($monthlyRate * pow(1 + $monthlyRate, $totalPayments)) / 
            (pow(1 + $monthlyRate, $totalPayments) - 1);
    }

    public function calculateAnnualDebtService(): float
    {
        return $this->calculateMonthlyMortgagePayment() * 12;
    }

    public function calculateExitValue(): float
    {
        $futureValue = $this->purchase_price * pow(1 + ($this->appreciation_rate / 100), $this->holding_period_years);
        return $futureValue * (1 - ($this->selling_costs_percentage / 100));
    }

    public function calculateNetProceeds(): float
    {
        $exitValue = $this->calculateExitValue();
        $loanBalance = $this->calculateLoanBalance();
        $capitalGainsTax = ($exitValue - $this->purchase_price) * ($this->capital_gains_tax_rate / 100);
        
        return $exitValue - $loanBalance - $capitalGainsTax;
    }

    public function calculateLoanBalance(): float
    {
        if ($this->loan_amount <= 0 || $this->interest_rate <= 0 || $this->loan_term_years <= 0) {
            return 0;
        }

        $monthlyRate = $this->interest_rate / 12;
        $totalPayments = $this->loan_term_years * 12;
        $paymentsMade = min($this->holding_period_years * 12, $totalPayments);
        
        if ($monthlyRate == 0) {
            return $this->loan_amount - ($this->loan_amount / $totalPayments * $paymentsMade);
        }

        $monthlyPayment = $this->calculateMonthlyMortgagePayment();
        $remainingPayments = $totalPayments - $paymentsMade;
        
        return $monthlyPayment * 
            (1 - pow(1 + $monthlyRate, -$remainingPayments)) / $monthlyRate;
    }

    public function calculateTotalReturn(): float
    {
        $netProceeds = $this->calculateNetProceeds();
        $equityRequired = $this->calculateEquityRequired();
        
        return $equityRequired > 0 ? (($netProceeds - $equityRequired) / $equityRequired) * 100 : 0;
    }

    public function calculateAnnualizedReturn(): float
    {
        $totalReturn = $this->calculateTotalReturn() / 100;
        $holdingPeriod = $this->holding_period_years;
        
        if ($holdingPeriod <= 0) {
            return 0;
        }

        return (pow(1 + $totalReturn, 1 / $holdingPeriod) - 1) * 100;
    }

    public function getScenarioMetrics(): array
    {
        return [
            'total_investment' => $this->calculateTotalInvestment(),
            'equity_required' => $this->calculateEquityRequired(),
            'down_payment' => $this->calculateDownPayment(),
            'loan_amount' => $this->loan_amount,
            'monthly_mortgage' => $this->calculateMonthlyMortgagePayment(),
            'annual_debt_service' => $this->calculateAnnualDebtService(),
            'exit_value' => $this->calculateExitValue(),
            'net_proceeds' => $this->calculateNetProceeds(),
            'total_return_percentage' => $this->calculateTotalReturn(),
            'annualized_return_percentage' => $this->calculateAnnualizedReturn(),
            'holding_period_years' => $this->holding_period_years,
            'probability_of_success' => $this->probability_of_success,
            'expected_value' => $this->expected_value,
            'best_case_value' => $this->best_case_value,
            'worst_case_value' => $this->worst_case_value
        ];
    }

    public function assessScenarioRisk(): string
    {
        $riskFactors = 0;
        
        if ($this->vacancy_rate > 0.1) $riskFactors++;
        if ($this->interest_rate > 0.08) $riskFactors++;
        if ($this->appreciation_rate < 0.02) $riskFactors++;
        if ($this->probability_of_success < 0.7) $riskFactors++;
        if ($this->calculateAnnualizedReturn() < 8) $riskFactors++;
        
        if ($riskFactors <= 1) return 'low';
        if ($riskFactors <= 3) return 'medium';
        return 'high';
    }

    public function getRiskMetrics(): array
    {
        return [
            'risk_level' => $this->assessScenarioRisk(),
            'probability_of_success' => $this->probability_of_success,
            'confidence_interval_lower' => $this->confidence_interval_lower,
            'confidence_interval_upper' => $this->confidence_interval_upper,
            'value_range' => $this->confidence_interval_upper - $this->confidence_interval_lower,
            'downside_risk' => $this->expected_value - $this->worst_case_value,
            'upside_potential' => $this->best_case_value - $this->expected_value,
            'risk_reward_ratio' => $this->calculateRiskRewardRatio()
        ];
    }

    private function calculateRiskRewardRatio(): float
    {
        $downside = $this->expected_value - $this->worst_case_value;
        $upside = $this->best_case_value - $this->expected_value;
        
        return $downside > 0 ? $upside / $downside : 0;
    }
}
