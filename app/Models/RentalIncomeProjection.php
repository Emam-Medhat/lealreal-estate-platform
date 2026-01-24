<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class RentalIncomeProjection extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_financial_analysis_id',
        'projection_year',
        'projection_month',
        'gross_rental_income',
        'other_income',
        'vacancy_loss',
        'credit_loss',
        'concession_loss',
        'effective_gross_income',
        'rent_per_unit',
        'number_of_units',
        'occupancy_rate',
        'rent_growth_rate',
        'market_rent_trend',
        'seasonal_adjustment',
        'lease_renewal_rate',
        'turnover_rate',
        'rent_control_impact',
        'section_8_income',
        'housing_assistance_income',
        'parking_income',
        'storage_income',
        'laundry_income',
        'vending_income',
        'pet_rent_income',
        'late_fee_income',
        'application_fee_income',
        'administrative_fee_income',
        'utility_income',
        'internet_cable_income',
        'amenity_fee_income',
        'guest_rental_income',
        'event_space_income',
        'advertising_income',
        'cell_tower_income',
        'solar_panel_income',
        'other_miscellaneous_income',
        'rent_adjustment_factors',
        'market_demand_factors',
        'economic_factors',
        'demographic_factors',
        'competition_factors',
        'infrastructure_impact',
        'zoning_regulations_impact',
        'property_improvements_impact',
        'seasonal_variations',
        'regional_trends',
        'neighborhood_development',
        'transportation_development',
        'school_district_quality',
        'crime_rate_impact',
        'employment_growth_impact',
        'population_growth_impact',
        'inflation_adjustment',
        'rent_control_regulations',
        'rent_stabilization_impact',
        'affordable_housing_requirements',
        'inclusionary_zoning_impact',
        'market_segment_analysis',
        'tenant_demographics',
        'income_level_trends',
        'household_size_trends',
        'age_demographics',
        'employment_sectors',
        'migration_patterns',
        'housing_preferences',
        'lifestyle_trends',
        'technology_adoption',
        'remote_work_impact',
        'urban_suburban_shift',
        'multi_family_trends',
        'single_family_rental_trends',
        'luxury_rental_trends',
        'affordable_housing_trends',
        'student_housing_trends',
        'senior_housing_trends',
        'military_housing_trends',
        'corporate_housing_trends',
        'vacation_rental_trends',
        'projection_methodology',
        'data_sources',
        'market_survey_data',
        'comparable_properties',
        'historical_performance',
        'lease_expiration_schedule',
        'renewal_probability',
        'market_lease_terms',
        'concession_trends',
        'incentive_programs',
        'marketing_effectiveness',
        'property_reputation',
        'online_reviews_impact',
        'social_media_presence',
        'website_performance',
        'lead_conversion_rates',
        'tour_to_application_ratio',
        'application_to_lease_ratio',
        'average_lease_term',
        'lease_renewal_terms',
        'early_termination_clauses',
        'rent_increase_policies',
        'expense_reimbursement_income',
        'triple_net_income',
        'percentage_rent_income',
        'common_area_maintenance_income',
        'real_estate_tax_reimbursement',
        'insurance_reimbursement',
        'capital_improvement_reimbursement',
        'projection_confidence',
        'risk_factors',
        'sensitivity_analysis',
        'scenario_analysis',
        'monte_carlo_simulation',
        'projection_notes',
        'assumptions',
        'limitations',
        'validation_status',
        'review_comments',
        'approved_by',
        'approval_date'
    ];

    protected $casts = [
        'projection_year' => 'integer',
        'projection_month' => 'integer',
        'gross_rental_income' => 'decimal:2',
        'other_income' => 'decimal:2',
        'vacancy_loss' => 'decimal:2',
        'credit_loss' => 'decimal:2',
        'concession_loss' => 'decimal:2',
        'effective_gross_income' => 'decimal:2',
        'rent_per_unit' => 'decimal:2',
        'number_of_units' => 'integer',
        'occupancy_rate' => 'decimal:3',
        'rent_growth_rate' => 'decimal:4',
        'market_rent_trend' => 'decimal:4',
        'seasonal_adjustment' => 'decimal:3',
        'lease_renewal_rate' => 'decimal:3',
        'turnover_rate' => 'decimal:3',
        'rent_control_impact' => 'decimal:2',
        'section_8_income' => 'decimal:2',
        'housing_assistance_income' => 'decimal:2',
        'parking_income' => 'decimal:2',
        'storage_income' => 'decimal:2',
        'laundry_income' => 'decimal:2',
        'vending_income' => 'decimal:2',
        'pet_rent_income' => 'decimal:2',
        'late_fee_income' => 'decimal:2',
        'application_fee_income' => 'decimal:2',
        'administrative_fee_income' => 'decimal:2',
        'utility_income' => 'decimal:2',
        'internet_cable_income' => 'decimal:2',
        'amenity_fee_income' => 'decimal:2',
        'guest_rental_income' => 'decimal:2',
        'event_space_income' => 'decimal:2',
        'advertising_income' => 'decimal:2',
        'cell_tower_income' => 'decimal:2',
        'solar_panel_income' => 'decimal:2',
        'other_miscellaneous_income' => 'decimal:2',
        'rent_adjustment_factors' => 'json',
        'market_demand_factors' => 'json',
        'economic_factors' => 'json',
        'demographic_factors' => 'json',
        'competition_factors' => 'json',
        'infrastructure_impact' => 'decimal:2',
        'zoning_regulations_impact' => 'decimal:2',
        'property_improvements_impact' => 'decimal:2',
        'seasonal_variations' => 'json',
        'regional_trends' => 'json',
        'neighborhood_development' => 'json',
        'transportation_development' => 'json',
        'school_district_quality' => 'decimal:3',
        'crime_rate_impact' => 'decimal:3',
        'employment_growth_impact' => 'decimal:4',
        'population_growth_impact' => 'decimal:4',
        'inflation_adjustment' => 'decimal:4',
        'rent_control_regulations' => 'json',
        'rent_stabilization_impact' => 'decimal:2',
        'affordable_housing_requirements' => 'json',
        'inclusionary_zoning_impact' => 'decimal:2',
        'market_segment_analysis' => 'json',
        'tenant_demographics' => 'json',
        'income_level_trends' => 'json',
        'household_size_trends' => 'json',
        'age_demographics' => 'json',
        'employment_sectors' => 'json',
        'migration_patterns' => 'json',
        'housing_preferences' => 'json',
        'lifestyle_trends' => 'json',
        'technology_adoption' => 'json',
        'remote_work_impact' => 'decimal:3',
        'urban_suburban_shift' => 'decimal:3',
        'multi_family_trends' => 'json',
        'single_family_rental_trends' => 'json',
        'luxury_rental_trends' => 'json',
        'affordable_housing_trends' => 'json',
        'student_housing_trends' => 'json',
        'senior_housing_trends' => 'json',
        'military_housing_trends' => 'json',
        'corporate_housing_trends' => 'json',
        'vacation_rental_trends' => 'json',
        'projection_methodology' => 'string',
        'data_sources' => 'json',
        'market_survey_data' => 'json',
        'comparable_properties' => 'json',
        'historical_performance' => 'json',
        'lease_expiration_schedule' => 'json',
        'renewal_probability' => 'decimal:3',
        'market_lease_terms' => 'json',
        'concession_trends' => 'json',
        'incentive_programs' => 'json',
        'marketing_effectiveness' => 'decimal:3',
        'property_reputation' => 'decimal:3',
        'online_reviews_impact' => 'decimal:3',
        'social_media_presence' => 'decimal:3',
        'website_performance' => 'decimal:3',
        'lead_conversion_rates' => 'decimal:3',
        'tour_to_application_ratio' => 'decimal:3',
        'application_to_lease_ratio' => 'decimal:3',
        'average_lease_term' => 'decimal:2',
        'lease_renewal_terms' => 'json',
        'early_termination_clauses' => 'json',
        'rent_increase_policies' => 'json',
        'expense_reimbursement_income' => 'decimal:2',
        'triple_net_income' => 'decimal:2',
        'percentage_rent_income' => 'decimal:2',
        'common_area_maintenance_income' => 'decimal:2',
        'real_estate_tax_reimbursement' => 'decimal:2',
        'insurance_reimbursement' => 'decimal:2',
        'capital_improvement_reimbursement' => 'decimal:2',
        'projection_confidence' => 'decimal:3',
        'risk_factors' => 'json',
        'sensitivity_analysis' => 'json',
        'scenario_analysis' => 'json',
        'monte_carlo_simulation' => 'json',
        'projection_notes' => 'text',
        'assumptions' => 'json',
        'limitations' => 'text',
        'validation_status' => 'string',
        'review_comments' => 'text',
        'approval_date' => 'date'
    ];

    public function propertyFinancialAnalysis(): BelongsTo
    {
        return $this->belongsTo(PropertyFinancialAnalysis::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeByAnalysis(Builder $query, $analysisId): Builder
    {
        return $query->where('property_financial_analysis_id', $analysisId);
    }

    public function scopeByYear(Builder $query, $year): Builder
    {
        return $query->where('projection_year', $year);
    }

    public function scopeByMonth(Builder $query, $month): Builder
    {
        return $query->where('projection_month', $month);
    }

    public function scopeValidated(Builder $query): Builder
    {
        return $query->where('validation_status', 'validated');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('projection_year')->orderBy('projection_month');
    }

    public function calculateTotalIncome(): float
    {
        return $this->gross_rental_income + $this->other_income;
    }

    public function calculateTotalLosses(): float
    {
        return $this->vacancy_loss + $this->credit_loss + $this->concession_loss;
    }

    public function calculateNetRentalIncome(): float
    {
        return $this->gross_rental_income - $this->calculateTotalLosses();
    }

    public function calculateIncomePerUnit(): float
    {
        return $this->number_of_units > 0 ? $this->effective_gross_income / $this->number_of_units : 0;
    }

    public function calculateRevenuePerSquareFoot($squareFootage): float
    {
        return $squareFootage > 0 ? $this->effective_gross_income / $squareFootage : 0;
    }

    public function calculateRentGrowthImpact(): float
    {
        return $this->gross_rental_income * ($this->rent_growth_rate / 100);
    }

    public function calculateOccupancyImpact(): float
    {
        $potentialIncome = $this->rent_per_unit * $this->number_of_units * 12; // Annual
        return $potentialIncome * (1 - $this->occupancy_rate);
    }

    public function calculateMarketRentPremium(): float
    {
        $marketRent = $this->market_rent_trend * $this->rent_per_unit;
        return $marketRent - $this->rent_per_unit;
    }

    public function calculateSeasonalAdjustmentFactor(): float
    {
        return 1 + $this->seasonal_adjustment;
    }

    public function calculateRenewalIncomeRetention(): float
    {
        return $this->gross_rental_income * $this->lease_renewal_rate;
    }

    public function calculateTurnoverCostImpact(): float
    {
        return $this->gross_rental_income * ($this->turnover_rate * 0.5); // Assuming 50% of monthly rent as turnover cost
    }

    public function assessIncomeStability(): string
    {
        $stabilityScore = 0;
        
        if ($this->occupancy_rate >= 0.95) $stabilityScore += 25;
        elseif ($this->occupancy_rate >= 0.90) $stabilityScore += 20;
        elseif ($this->occupancy_rate >= 0.85) $stabilityScore += 15;
        
        if ($this->lease_renewal_rate >= 0.8) $stabilityScore += 25;
        elseif ($this->lease_renewal_rate >= 0.7) $stabilityScore += 20;
        elseif ($this->lease_renewal_rate >= 0.6) $stabilityScore += 15;
        
        if ($this->turnover_rate <= 0.1) $stabilityScore += 25;
        elseif ($this->turnover_rate <= 0.2) $stabilityScore += 20;
        elseif ($this->turnover_rate <= 0.3) $stabilityScore += 15;
        
        if ($this->rent_growth_rate >= 0.03) $stabilityScore += 25;
        elseif ($this->rent_growth_rate >= 0.02) $stabilityScore += 20;
        elseif ($this->rent_growth_rate >= 0.01) $stabilityScore += 15;
        
        if ($stabilityScore >= 80) return 'excellent';
        if ($stabilityScore >= 60) return 'good';
        if ($stabilityScore >= 40) return 'moderate';
        return 'poor';
    }

    public function getIncomeBreakdown(): array
    {
        return [
            'gross_rental_income' => $this->gross_rental_income,
            'other_income' => $this->other_income,
            'section_8_income' => $this->section_8_income,
            'housing_assistance_income' => $this->housing_assistance_income,
            'parking_income' => $this->parking_income,
            'storage_income' => $this->storage_income,
            'laundry_income' => $this->laundry_income,
            'vending_income' => $this->vending_income,
            'pet_rent_income' => $this->pet_rent_income,
            'late_fee_income' => $this->late_fee_income,
            'application_fee_income' => $this->application_fee_income,
            'administrative_fee_income' => $this->administrative_fee_income,
            'utility_income' => $this->utility_income,
            'internet_cable_income' => $this->internet_cable_income,
            'amenity_fee_income' => $this->amenity_fee_income,
            'guest_rental_income' => $this->guest_rental_income,
            'event_space_income' => $this->event_space_income,
            'advertising_income' => $this->advertising_income,
            'cell_tower_income' => $this->cell_tower_income,
            'solar_panel_income' => $this->solar_panel_income,
            'other_miscellaneous_income' => $this->other_miscellaneous_income
        ];
    }

    public function getLossBreakdown(): array
    {
        return [
            'vacancy_loss' => $this->vacancy_loss,
            'credit_loss' => $this->credit_loss,
            'concession_loss' => $this->concession_loss,
            'total_losses' => $this->calculateTotalLosses(),
            'vacancy_rate' => $this->occupancy_rate,
            'credit_loss_rate' => $this->gross_rental_income > 0 ? ($this->credit_loss / $this->gross_rental_income) * 100 : 0,
            'concession_rate' => $this->gross_rental_income > 0 ? ($this->concession_loss / $this->gross_rental_income) * 100 : 0
        ];
    }

    public function getPerformanceMetrics(): array
    {
        return [
            'effective_gross_income' => $this->effective_gross_income,
            'income_per_unit' => $this->calculateIncomePerUnit(),
            'rent_per_unit' => $this->rent_per_unit,
            'occupancy_rate' => $this->occupancy_rate * 100,
            'lease_renewal_rate' => $this->lease_renewal_rate * 100,
            'turnover_rate' => $this->turnover_rate * 100,
            'rent_growth_rate' => $this->rent_growth_rate * 100,
            'market_rent_trend' => $this->market_rent_trend * 100,
            'income_stability' => $this->assessIncomeStability(),
            'projection_confidence' => $this->projection_confidence * 100
        ];
    }

    public function getMarketFactors(): array
    {
        return [
            'market_demand_factors' => $this->market_demand_factors,
            'economic_factors' => $this->economic_factors,
            'demographic_factors' => $this->demographic_factors,
            'competition_factors' => $this->competition_factors,
            'infrastructure_impact' => $this->infrastructure_impact,
            'zoning_regulations_impact' => $this->zoning_regulations_impact,
            'property_improvements_impact' => $this->property_improvements_impact,
            'seasonal_variations' => $this->seasonal_variations,
            'regional_trends' => $this->regional_trends,
            'neighborhood_development' => $this->neighborhood_development
        ];
    }

    public function getTenantAnalysis(): array
    {
        return [
            'tenant_demographics' => $this->tenant_demographics,
            'income_level_trends' => $this->income_level_trends,
            'household_size_trends' => $this->household_size_trends,
            'age_demographics' => $this->age_demographics,
            'employment_sectors' => $this->employment_sectors,
            'migration_patterns' => $this->migration_patterns,
            'housing_preferences' => $this->housing_preferences,
            'lifestyle_trends' => $this->lifestyle_trends,
            'technology_adoption' => $this->technology_adoption,
            'remote_work_impact' => $this->remote_work_impact
        ];
    }

    public function getRegulatoryImpact(): array
    {
        return [
            'rent_control_regulations' => $this->rent_control_regulations,
            'rent_control_impact' => $this->rent_control_impact,
            'rent_stabilization_impact' => $this->rent_stabilization_impact,
            'affordable_housing_requirements' => $this->affordable_housing_requirements,
            'inclusionary_zoning_impact' => $this->inclusionary_zoning_impact,
            'zoning_regulations_impact' => $this->zoning_regulations_impact
        ];
    }

    public function getMarketingMetrics(): array
    {
        return [
            'marketing_effectiveness' => $this->marketing_effectiveness,
            'property_reputation' => $this->property_reputation,
            'online_reviews_impact' => $this->online_reviews_impact,
            'social_media_presence' => $this->social_media_presence,
            'website_performance' => $this->website_performance,
            'lead_conversion_rates' => $this->lead_conversion_rates,
            'tour_to_application_ratio' => $this->tour_to_application_ratio,
            'application_to_lease_ratio' => $this->application_to_lease_ratio
        ];
    }

    public function getLeaseAnalysis(): array
    {
        return [
            'lease_expiration_schedule' => $this->lease_expiration_schedule,
            'renewal_probability' => $this->renewal_probability,
            'market_lease_terms' => $this->market_lease_terms,
            'concession_trends' => $this->concession_trends,
            'incentive_programs' => $this->incentive_programs,
            'average_lease_term' => $this->average_lease_term,
            'lease_renewal_terms' => $this->lease_renewal_terms,
            'early_termination_clauses' => $this->early_termination_clauses,
            'rent_increase_policies' => $this->rent_increase_policies
        ];
    }

    public function getAdditionalIncome(): array
    {
        return [
            'expense_reimbursement_income' => $this->expense_reimbursement_income,
            'triple_net_income' => $this->triple_net_income,
            'percentage_rent_income' => $this->percentage_rent_income,
            'common_area_maintenance_income' => $this->common_area_maintenance_income,
            'real_estate_tax_reimbursement' => $this->real_estate_tax_reimbursement,
            'insurance_reimbursement' => $this->insurance_reimbursement,
            'capital_improvement_reimbursement' => $this->capital_improvement_reimbursement
        ];
    }

    public function getProjectionQuality(): array
    {
        return [
            'projection_methodology' => $this->projection_methodology,
            'data_sources' => $this->data_sources,
            'market_survey_data' => $this->market_survey_data,
            'comparable_properties' => $this->comparable_properties,
            'historical_performance' => $this->historical_performance,
            'projection_confidence' => $this->projection_confidence * 100,
            'validation_status' => $this->validation_status,
            'risk_factors' => $this->risk_factors,
            'sensitivity_analysis' => $this->sensitivity_analysis,
            'scenario_analysis' => $this->scenario_analysis,
            'monte_carlo_simulation' => $this->monte_carlo_simulation
        ];
    }
}
