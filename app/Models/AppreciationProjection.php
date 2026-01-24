<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class AppreciationProjection extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_financial_analysis_id',
        'projection_year',
        'projected_value',
        'current_value',
        'appreciation_rate',
        'cumulative_appreciation_rate',
        'annual_appreciation_amount',
        'cumulative_appreciation_amount',
        'projection_model',
        'market_factors',
        'property_factors',
        'economic_assumptions',
        'confidence_level',
        'risk_adjusted_value',
        'optimistic_value',
        'pessimistic_value',
        'base_case_value',
        'market_cycle_phase',
        'inflation_adjusted_value',
        'real_appreciation_rate',
        'nominal_appreciation_rate',
        'rent_growth_projection',
        'expense_growth_projection',
        'cap_rate_projection',
        'demand_supply_ratio',
        'market_saturation_level',
        'development_pipeline',
        'infrastructure_impact',
        'zoning_changes_impact',
        'demographic_trends',
        'employment_growth_rate',
        'population_growth_rate',
        'interest_rate_projection',
        'gdp_growth_projection',
        'consumer_confidence_index',
        'construction_cost_index',
        'rental_vacancy_projection',
        'property_tax_rate_projection',
        'insurance_cost_projection',
        'maintenance_cost_projection',
        'utility_cost_projection',
        'regulatory_environment',
        'technology_impact',
        'environmental_factors',
        'climate_risk_factors',
        'sustainability_impact',
        'urbanization_trends',
        'transportation_development',
        'school_quality_impact',
        'crime_rate_trends',
        'healthcare_access_impact',
        'recreational_facilities_impact',
        'shopping_center_proximity',
        'public_transit_access',
        'highway_access',
        'airport_proximity',
        'waterfront_access',
        'park_proximity',
        'view_quality_score',
        'noise_level_impact',
        'air_quality_index',
        'natural_disaster_risk',
        'flood_zone_risk',
        'wildfire_risk',
        'earthquake_risk',
        'projection_methodology',
        'data_sources',
        'validation_method',
        'historical_accuracy',
        'projection_variance',
        'sensitivity_analysis',
        'scenario_analysis',
        'monte_carlo_simulation',
        'expert_consensus',
        'machine_learning_model',
        'arima_forecast',
        'regression_analysis',
        'comparative_analysis',
        'trend_analysis',
        'cycle_analysis',
        'projection_notes',
        'assumptions',
        'limitations',
        'confidence_interval_lower',
        'confidence_interval_upper',
        'standard_error',
        'mean_squared_error',
        'r_squared_value',
        'adjusted_r_squared',
        'aic_bic_scores',
        'model_selection_criteria',
        'backtesting_results',
        'out_of_sample_performance',
        'cross_validation_results',
        'projection_quality_score',
        'validation_status',
        'review_comments',
        'approved_by',
        'approval_date'
    ];

    protected $casts = [
        'projection_year' => 'integer',
        'projected_value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'appreciation_rate' => 'decimal:4',
        'cumulative_appreciation_rate' => 'decimal:4',
        'annual_appreciation_amount' => 'decimal:2',
        'cumulative_appreciation_amount' => 'decimal:2',
        'market_factors' => 'json',
        'property_factors' => 'json',
        'economic_assumptions' => 'json',
        'confidence_level' => 'decimal:3',
        'risk_adjusted_value' => 'decimal:2',
        'optimistic_value' => 'decimal:2',
        'pessimistic_value' => 'decimal:2',
        'base_case_value' => 'decimal:2',
        'inflation_adjusted_value' => 'decimal:2',
        'real_appreciation_rate' => 'decimal:4',
        'nominal_appreciation_rate' => 'decimal:4',
        'rent_growth_projection' => 'decimal:4',
        'expense_growth_projection' => 'decimal:4',
        'cap_rate_projection' => 'decimal:4',
        'demand_supply_ratio' => 'decimal:3',
        'market_saturation_level' => 'decimal:3',
        'development_pipeline' => 'json',
        'infrastructure_impact' => 'decimal:2',
        'zoning_changes_impact' => 'decimal:2',
        'demographic_trends' => 'json',
        'employment_growth_rate' => 'decimal:4',
        'population_growth_rate' => 'decimal:4',
        'interest_rate_projection' => 'decimal:4',
        'gdp_growth_projection' => 'decimal:4',
        'consumer_confidence_index' => 'decimal:3',
        'construction_cost_index' => 'decimal:3',
        'rental_vacancy_projection' => 'decimal:3',
        'property_tax_rate_projection' => 'decimal:4',
        'insurance_cost_projection' => 'decimal:4',
        'maintenance_cost_projection' => 'decimal:4',
        'utility_cost_projection' => 'decimal:4',
        'regulatory_environment' => 'json',
        'technology_impact' => 'decimal:2',
        'environmental_factors' => 'json',
        'climate_risk_factors' => 'json',
        'sustainability_impact' => 'decimal:2',
        'urbanization_trends' => 'json',
        'transportation_development' => 'json',
        'school_quality_impact' => 'decimal:2',
        'crime_rate_trends' => 'json',
        'healthcare_access_impact' => 'decimal:2',
        'recreational_facilities_impact' => 'decimal:2',
        'shopping_center_proximity' => 'decimal:2',
        'public_transit_access' => 'decimal:2',
        'highway_access' => 'decimal:2',
        'airport_proximity' => 'decimal:2',
        'waterfront_access' => 'decimal:2',
        'park_proximity' => 'decimal:2',
        'view_quality_score' => 'decimal:3',
        'noise_level_impact' => 'decimal:2',
        'air_quality_index' => 'decimal:3',
        'natural_disaster_risk' => 'json',
        'flood_zone_risk' => 'decimal:3',
        'wildfire_risk' => 'decimal:3',
        'earthquake_risk' => 'decimal:3',
        'data_sources' => 'json',
        'historical_accuracy' => 'decimal:3',
        'projection_variance' => 'decimal:3',
        'sensitivity_analysis' => 'json',
        'scenario_analysis' => 'json',
        'monte_carlo_simulation' => 'json',
        'expert_consensus' => 'decimal:3',
        'machine_learning_model' => 'json',
        'arima_forecast' => 'json',
        'regression_analysis' => 'json',
        'comparative_analysis' => 'json',
        'trend_analysis' => 'json',
        'cycle_analysis' => 'json',
        'projection_notes' => 'text',
        'assumptions' => 'json',
        'limitations' => 'text',
        'confidence_interval_lower' => 'decimal:2',
        'confidence_interval_upper' => 'decimal:2',
        'standard_error' => 'decimal:2',
        'mean_squared_error' => 'decimal:2',
        'r_squared_value' => 'decimal:3',
        'adjusted_r_squared' => 'decimal:3',
        'aic_bic_scores' => 'json',
        'model_selection_criteria' => 'json',
        'backtesting_results' => 'json',
        'out_of_sample_performance' => 'decimal:3',
        'cross_validation_results' => 'json',
        'projection_quality_score' => 'decimal:3',
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

    public function scopeByModel(Builder $query, $model): Builder
    {
        return $query->where('projection_model', $model);
    }

    public function scopeValidated(Builder $query): Builder
    {
        return $query->where('validation_status', 'validated');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('projection_year');
    }

    public function calculateValueAppreciation(): float
    {
        return $this->projected_value - $this->current_value;
    }

    public function calculateValueAppreciationPercentage(): float
    {
        return $this->current_value > 0 
            ? (($this->projected_value - $this->current_value) / $this->current_value) * 100 
            : 0;
    }

    public function calculateRealAppreciation(): float
    {
        $inflationRate = $this->economic_assumptions['inflation_rate'] ?? 0.03;
        return $this->appreciation_rate - $inflationRate;
    }

    public function calculateRiskAdjustedReturn(): float
    {
        return $this->current_value > 0 
            ? (($this->risk_adjusted_value - $this->current_value) / $this->current_value) * 100 
            : 0;
    }

    public function calculateProjectionRange(): float
    {
        return $this->confidence_interval_upper - $this->confidence_interval_lower;
    }

    public function calculateProjectionAccuracy(): float
    {
        $range = $this->calculateProjectionRange();
        $midpoint = ($this->confidence_interval_upper + $this->confidence_interval_lower) / 2;
        
        return $midpoint > 0 ? ($range / $midpoint) * 100 : 0;
    }

    public function assessProjectionReliability(): string
    {
        $accuracy = $this->calculateProjectionAccuracy();
        $confidence = $this->confidence_level;
        
        if ($accuracy <= 10 && $confidence >= 0.8) {
            return 'high';
        } elseif ($accuracy <= 20 && $confidence >= 0.6) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    public function getProjectionSummary(): array
    {
        return [
            'projection_year' => $this->projection_year,
            'current_value' => $this->current_value,
            'projected_value' => $this->projected_value,
            'appreciation_rate' => $this->appreciation_rate * 100,
            'cumulative_appreciation_rate' => $this->cumulative_appreciation_rate * 100,
            'annual_appreciation_amount' => $this->annual_appreciation_amount,
            'cumulative_appreciation_amount' => $this->cumulative_appreciation_amount,
            'value_appreciation' => $this->calculateValueAppreciation(),
            'value_appreciation_percentage' => $this->calculateValueAppreciationPercentage(),
            'real_appreciation_rate' => $this->calculateRealAppreciation() * 100,
            'projection_model' => $this->projection_model,
            'confidence_level' => $this->confidence_level * 100,
            'reliability' => $this->assessProjectionReliability()
        ];
    }

    public function getScenarioAnalysis(): array
    {
        return [
            'base_case_value' => $this->base_case_value,
            'optimistic_value' => $this->optimistic_value,
            'pessimistic_value' => $this->pessimistic_value,
            'risk_adjusted_value' => $this->risk_adjusted_value,
            'inflation_adjusted_value' => $this->inflation_adjusted_value,
            'upside_potential' => $this->optimistic_value - $this->base_case_value,
            'downside_risk' => $this->base_case_value - $this->pessimistic_value,
            'risk_reward_ratio' => $this->calculateRiskRewardRatio()
        ];
    }

    public function getMarketFactors(): array
    {
        return [
            'market_factors' => $this->market_factors,
            'property_factors' => $this->property_factors,
            'economic_assumptions' => $this->economic_assumptions,
            'market_cycle_phase' => $this->market_cycle_phase,
            'demand_supply_ratio' => $this->demand_supply_ratio,
            'market_saturation_level' => $this->market_saturation_level,
            'development_pipeline' => $this->development_pipeline
        ];
    }

    public function getLocationFactors(): array
    {
        return [
            'infrastructure_impact' => $this->infrastructure_impact,
            'zoning_changes_impact' => $this->zoning_changes_impact,
            'school_quality_impact' => $this->school_quality_impact,
            'recreational_facilities_impact' => $this->recreational_facilities_impact,
            'shopping_center_proximity' => $this->shopping_center_proximity,
            'public_transit_access' => $this->public_transit_access,
            'highway_access' => $this->highway_access,
            'airport_proximity' => $this->airport_proximity,
            'waterfront_access' => $this->waterfront_access,
            'park_proximity' => $this->park_proximity
        ];
    }

    public function getQualityFactors(): array
    {
        return [
            'view_quality_score' => $this->view_quality_score,
            'noise_level_impact' => $this->noise_level_impact,
            'air_quality_index' => $this->air_quality_index,
            'natural_disaster_risk' => $this->natural_disaster_risk,
            'flood_zone_risk' => $this->flood_zone_risk,
            'wildfire_risk' => $this->wildfire_risk,
            'earthquake_risk' => $this->earthquake_risk
        ];
    }

    public function getModelValidation(): array
    {
        return [
            'projection_methodology' => $this->projection_methodology,
            'data_sources' => $this->data_sources,
            'validation_method' => $this->validation_method,
            'historical_accuracy' => $this->historical_accuracy,
            'projection_variance' => $this->projection_variance,
            'r_squared_value' => $this->r_squared_value,
            'adjusted_r_squared' => $this->adjusted_r_squared,
            'standard_error' => $this->standard_error,
            'mean_squared_error' => $this->mean_squared_error,
            'projection_quality_score' => $this->projection_quality_score,
            'validation_status' => $this->validation_status
        ];
    }

    public function getAdvancedAnalytics(): array
    {
        return [
            'sensitivity_analysis' => $this->sensitivity_analysis,
            'scenario_analysis' => $this->scenario_analysis,
            'monte_carlo_simulation' => $this->monte_carlo_simulation,
            'machine_learning_model' => $this->machine_learning_model,
            'arima_forecast' => $this->arima_forecast,
            'regression_analysis' => $this->regression_analysis,
            'comparative_analysis' => $this->comparative_analysis,
            'trend_analysis' => $this->trend_analysis,
            'cycle_analysis' => $this->cycle_analysis,
            'backtesting_results' => $this->backtesting_results,
            'cross_validation_results' => $this->cross_validation_results
        ];
    }

    private function calculateRiskRewardRatio(): float
    {
        $downside = $this->base_case_value - $this->pessimistic_value;
        $upside = $this->optimistic_value - $this->base_case_value;
        
        return $downside > 0 ? $upside / $downside : 0;
    }

    public function calculateCompoundAnnualGrowthRate(): float
    {
        if ($this->projection_year <= 1) {
            return $this->appreciation_rate * 100;
        }

        $years = $this->projection_year;
        $startValue = $this->current_value;
        $endValue = $this->projected_value;

        if ($startValue <= 0) {
            return 0;
        }

        return (pow($endValue / $startValue, 1 / $years) - 1) * 100;
    }

    public function assessMarketPosition(): string
    {
        $marketAppreciation = $this->market_factors['market_appreciation_rate'] ?? 0.05;
        $propertyAppreciation = $this->appreciation_rate;

        if ($propertyAppreciation > $marketAppreciation * 1.2) {
            return 'outperforming';
        } elseif ($propertyAppreciation > $marketAppreciation * 0.8) {
            return 'market';
        } else {
            return 'underperforming';
        }
    }

    public function getProjectionConfidence(): array
    {
        return [
            'confidence_level' => $this->confidence_level * 100,
            'confidence_interval_lower' => $this->confidence_interval_lower,
            'confidence_interval_upper' => $this->confidence_interval_upper,
            'projection_range' => $this->calculateProjectionRange(),
            'projection_accuracy' => $this->calculateProjectionAccuracy(),
            'reliability' => $this->assessProjectionReliability(),
            'validation_status' => $this->validation_status,
            'expert_consensus' => $this->expert_consensus * 100
        ];
    }
}
