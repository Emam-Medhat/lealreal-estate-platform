<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class TaxBenefit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_financial_analysis_id',
        'tax_year',
        'calculation_date',
        'depreciation_deduction',
        'mortgage_interest_deduction',
        'property_tax_deduction',
        'operating_expense_deduction',
        'repairs_maintenance_deduction',
        'capital_improvements_amortization',
        'depletion_allowance',
        'passive_activity_loss',
        'net_operating_loss',
        'tax_credit_amount',
        'total_tax_benefits',
        'tax_savings_amount',
        'effective_tax_rate',
        'marginal_tax_rate',
        'alternative_minimum_tax_impact',
        'state_tax_benefits',
        'local_tax_benefits',
        'federal_tax_benefits',
        'taxable_income_before_benefits',
        'taxable_income_after_benefits',
        'tax_liability_before_benefits',
        'tax_liability_after_benefits',
        'depreciation_method',
        'depreciation_schedule',
        'cost_segregation_analysis',
        'section_179_deduction',
        'bonus_depreciation',
        'like_kind_exchange_benefits',
        'opportunity_zone_benefits',
        'energy_efficiency_credits',
        'historic_preservation_credits',
        'low_income_housing_credits',
        'tax_loss_harvesting',
        'carry_forward_losses',
        'carry_back_losses',
        'tax_projection_years',
        'tax_projection_data',
        'tax_optimization_strategies',
        'tax_planning_recommendations',
        'compliance_notes',
        'tax_law_changes_impact',
        'assumptions',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'tax_year' => 'integer',
        'calculation_date' => 'date',
        'depreciation_deduction' => 'decimal:2',
        'mortgage_interest_deduction' => 'decimal:2',
        'property_tax_deduction' => 'decimal:2',
        'operating_expense_deduction' => 'decimal:2',
        'repairs_maintenance_deduction' => 'decimal:2',
        'capital_improvements_amortization' => 'decimal:2',
        'depletion_allowance' => 'decimal:2',
        'passive_activity_loss' => 'decimal:2',
        'net_operating_loss' => 'decimal:2',
        'tax_credit_amount' => 'decimal:2',
        'total_tax_benefits' => 'decimal:2',
        'tax_savings_amount' => 'decimal:2',
        'effective_tax_rate' => 'decimal:4',
        'marginal_tax_rate' => 'decimal:4',
        'alternative_minimum_tax_impact' => 'decimal:2',
        'state_tax_benefits' => 'decimal:2',
        'local_tax_benefits' => 'decimal:2',
        'federal_tax_benefits' => 'decimal:2',
        'taxable_income_before_benefits' => 'decimal:2',
        'taxable_income_after_benefits' => 'decimal:2',
        'tax_liability_before_benefits' => 'decimal:2',
        'tax_liability_after_benefits' => 'decimal:2',
        'depreciation_schedule' => 'json',
        'cost_segregation_analysis' => 'json',
        'section_179_deduction' => 'decimal:2',
        'bonus_depreciation' => 'decimal:2',
        'like_kind_exchange_benefits' => 'decimal:2',
        'opportunity_zone_benefits' => 'decimal:2',
        'energy_efficiency_credits' => 'decimal:2',
        'historic_preservation_credits' => 'decimal:2',
        'low_income_housing_credits' => 'decimal:2',
        'tax_loss_harvesting' => 'decimal:2',
        'carry_forward_losses' => 'decimal:2',
        'carry_back_losses' => 'decimal:2',
        'tax_projection_years' => 'integer',
        'tax_projection_data' => 'json',
        'tax_optimization_strategies' => 'json',
        'tax_planning_recommendations' => 'json',
        'assumptions' => 'json',
        'notes' => 'text'
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

    public function scopeByTaxYear(Builder $query, $year): Builder
    {
        return $query->where('tax_year', $year);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('tax_year', 'desc');
    }

    public function calculateTotalDeductions(): float
    {
        return $this->depreciation_deduction
            + $this->mortgage_interest_deduction
            + $this->property_tax_deduction
            + $this->operating_expense_deduction
            + $this->repairs_maintenance_deduction
            + $this->capital_improvements_amortization
            + $this->depletion_allowance;
    }

    public function calculateTaxReduction(): float
    {
        return $this->taxable_income_before_benefits - $this->taxable_income_after_benefits;
    }

    public function calculateTaxSavingsRate(): float
    {
        return $this->taxable_income_before_benefits > 0 
            ? ($this->calculateTaxReduction() / $this->taxable_income_before_benefits) * 100 
            : 0;
    }

    public function calculateEffectiveTaxBenefitRate(): float
    {
        $totalIncome = $this->taxable_income_before_benefits;
        return $totalIncome > 0 ? ($this->total_tax_benefits / $totalIncome) * 100 : 0;
    }

    public function calculateAfterTaxCashFlowImpact(): float
    {
        return $this->tax_savings_amount;
    }

    public function calculateDepreciationRecapture(): float
    {
        // Simplified calculation - would need more complex logic in reality
        return $this->depreciation_deduction * 0.25; // Assuming 25% recapture rate
    }

    public function assessTaxEfficiency(): string
    {
        $efficiencyRate = $this->calculateEffectiveTaxBenefitRate();
        
        if ($efficiencyRate >= 20) return 'excellent';
        if ($efficiencyRate >= 15) return 'good';
        if ($efficiencyRate >= 10) return 'moderate';
        return 'poor';
    }

    public function getTaxBenefitSummary(): array
    {
        return [
            'tax_year' => $this->tax_year,
            'total_tax_benefits' => $this->total_tax_benefits,
            'tax_savings_amount' => $this->tax_savings_amount,
            'total_deductions' => $this->calculateTotalDeductions(),
            'tax_reduction' => $this->calculateTaxReduction(),
            'tax_savings_rate' => $this->calculateTaxSavingsRate(),
            'effective_tax_benefit_rate' => $this->calculateEffectiveTaxBenefitRate(),
            'effective_tax_rate' => $this->effective_tax_rate,
            'marginal_tax_rate' => $this->marginal_tax_rate,
            'tax_efficiency' => $this->assessTaxEfficiency(),
            'after_tax_cash_flow_impact' => $this->calculateAfterTaxCashFlowImpact()
        ];
    }

    public function getDeductionBreakdown(): array
    {
        return [
            'depreciation_deduction' => $this->depreciation_deduction,
            'mortgage_interest_deduction' => $this->mortgage_interest_deduction,
            'property_tax_deduction' => $this->property_tax_deduction,
            'operating_expense_deduction' => $this->operating_expense_deduction,
            'repairs_maintenance_deduction' => $this->repairs_maintenance_deduction,
            'capital_improvements_amortization' => $this->capital_improvements_amortization,
            'depletion_allowance' => $this->depletion_allowance,
            'passive_activity_loss' => $this->passive_activity_loss,
            'net_operating_loss' => $this->net_operating_loss,
            'tax_credit_amount' => $this->tax_credit_amount
        ];
    }

    public function getTaxJurisdictionBreakdown(): array
    {
        return [
            'federal_tax_benefits' => $this->federal_tax_benefits,
            'state_tax_benefits' => $this->state_tax_benefits,
            'local_tax_benefits' => $this->local_tax_benefits,
            'total_jurisdiction_benefits' => $this->federal_tax_benefits + $this->state_tax_benefits + $this->local_tax_benefits
        ];
    }

    public function getSpecialTaxBenefits(): array
    {
        return [
            'section_179_deduction' => $this->section_179_deduction,
            'bonus_depreciation' => $this->bonus_depreciation,
            'like_kind_exchange_benefits' => $this->like_kind_exchange_benefits,
            'opportunity_zone_benefits' => $this->opportunity_zone_benefits,
            'energy_efficiency_credits' => $this->energy_efficiency_credits,
            'historic_preservation_credits' => $this->historic_preservation_credits,
            'low_income_housing_credits' => $this->low_income_housing_credits
        ];
    }

    public function getTaxLossAnalysis(): array
    {
        return [
            'tax_loss_harvesting' => $this->tax_loss_harvesting,
            'carry_forward_losses' => $this->carry_forward_losses,
            'carry_back_losses' => $this->carry_back_losses,
            'net_operating_loss' => $this->net_operating_loss,
            'passive_activity_loss' => $this->passive_activity_loss
        ];
    }

    public function getTaxProjectionSummary(): array
    {
        return [
            'tax_projection_years' => $this->tax_projection_years,
            'tax_projection_data' => $this->tax_projection_data,
            'tax_optimization_strategies' => $this->tax_optimization_strategies,
            'tax_planning_recommendations' => $this->tax_planning_recommendations
        ];
    }

    public function calculateTaxBenefitIRR(): float
    {
        // Simplified IRR calculation for tax benefits
        // Would need more complex cash flow analysis in reality
        $initialInvestment = $this->propertyFinancialAnalysis->purchase_price ?? 0;
        $annualTaxBenefit = $this->total_tax_benefits;
        
        if ($initialInvestment <= 0 || $annualTaxBenefit <= 0) {
            return 0;
        }
        
        return ($annualTaxBenefit / $initialInvestment) * 100;
    }

    public function assessTaxPlanningOpportunity(): string
    {
        $opportunities = 0;
        
        if ($this->section_179_deduction > 0) $opportunities++;
        if ($this->bonus_depreciation > 0) $opportunities++;
        if ($this->cost_segregation_analysis) $opportunities++;
        if ($this->tax_optimization_strategies) $opportunities++;
        
        if ($opportunities >= 3) return 'high';
        if ($opportunities >= 2) return 'medium';
        return 'low';
    }

    public function getTaxComplianceMetrics(): array
    {
        return [
            'depreciation_method' => $this->depreciation_method,
            'depreciation_schedule' => $this->depreciation_schedule,
            'cost_segregation_analysis' => $this->cost_segregation_analysis,
            'compliance_notes' => $this->compliance_notes,
            'tax_law_changes_impact' => $this->tax_law_changes_impact,
            'alternative_minimum_tax_impact' => $this->alternative_minimum_tax_impact
        ];
    }
}
