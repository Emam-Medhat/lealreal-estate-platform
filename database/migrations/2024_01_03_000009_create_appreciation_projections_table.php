<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('appreciation_projections')) {
        Schema::create('appreciation_projections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_financial_analysis_id')->constrained()->onDelete('cascade');
            $table->integer('projection_year');
            $table->decimal('projected_value', 18, 2);
            $table->decimal('current_value', 18, 2);
            $table->decimal('appreciation_rate', 8, 4);
            $table->decimal('cumulative_appreciation_rate', 8, 4);
            $table->decimal('annual_appreciation_amount', 15, 2);
            $table->decimal('cumulative_appreciation_amount', 15, 2);
            $table->string('projection_model');
            $table->json('market_factors')->nullable();
            $table->json('property_factors')->nullable();
            $table->json('economic_assumptions')->nullable();
            $table->decimal('confidence_level', 5, 3);
            $table->decimal('risk_adjusted_value', 18, 2)->nullable();
            $table->decimal('optimistic_value', 18, 2)->nullable();
            $table->decimal('pessimistic_value', 18, 2)->nullable();
            $table->decimal('base_case_value', 18, 2)->nullable();
            $table->string('market_cycle_phase')->nullable();
            $table->decimal('inflation_adjusted_value', 18, 2)->nullable();
            $table->decimal('real_appreciation_rate', 8, 4)->nullable();
            $table->decimal('nominal_appreciation_rate', 8, 4);
            $table->decimal('rent_growth_projection', 8, 4)->nullable();
            $table->decimal('expense_growth_projection', 8, 4)->nullable();
            $table->decimal('cap_rate_projection', 8, 4)->nullable();
            $table->decimal('demand_supply_ratio', 5, 3)->nullable();
            $table->decimal('market_saturation_level', 5, 3)->nullable();
            $table->json('development_pipeline')->nullable();
            $table->decimal('infrastructure_impact', 15, 2)->nullable();
            $table->decimal('zoning_changes_impact', 15, 2)->nullable();
            $table->json('demographic_trends')->nullable();
            $table->decimal('employment_growth_rate', 8, 4)->nullable();
            $table->decimal('population_growth_rate', 8, 4)->nullable();
            $table->decimal('interest_rate_projection', 8, 4)->nullable();
            $table->decimal('gdp_growth_projection', 8, 4)->nullable();
            $table->decimal('consumer_confidence_index', 5, 3)->nullable();
            $table->decimal('construction_cost_index', 5, 3)->nullable();
            $table->decimal('rental_vacancy_projection', 5, 3)->nullable();
            $table->decimal('property_tax_rate_projection', 8, 4)->nullable();
            $table->decimal('insurance_cost_projection', 8, 4)->nullable();
            $table->decimal('maintenance_cost_projection', 8, 4)->nullable();
            $table->decimal('utility_cost_projection', 8, 4)->nullable();
            $table->json('regulatory_environment')->nullable();
            $table->decimal('technology_impact', 15, 2)->nullable();
            $table->json('environmental_factors')->nullable();
            $table->json('climate_risk_factors')->nullable();
            $table->decimal('sustainability_impact', 15, 2)->nullable();
            $table->json('urbanization_trends')->nullable();
            $table->json('transportation_development')->nullable();
            $table->decimal('school_quality_impact', 15, 2)->nullable();
            $table->json('crime_rate_trends')->nullable();
            $table->decimal('healthcare_access_impact', 15, 2)->nullable();
            $table->decimal('recreational_facilities_impact', 15, 2)->nullable();
            $table->decimal('shopping_center_proximity', 15, 2)->nullable();
            $table->decimal('public_transit_access', 15, 2)->nullable();
            $table->decimal('highway_access', 15, 2)->nullable();
            $table->decimal('airport_proximity', 15, 2)->nullable();
            $table->decimal('waterfront_access', 15, 2)->nullable();
            $table->decimal('park_proximity', 15, 2)->nullable();
            $table->decimal('view_quality_score', 5, 3)->nullable();
            $table->decimal('noise_level_impact', 15, 2)->nullable();
            $table->decimal('air_quality_index', 5, 3)->nullable();
            $table->json('natural_disaster_risk')->nullable();
            $table->decimal('flood_zone_risk', 5, 3)->nullable();
            $table->decimal('wildfire_risk', 5, 3)->nullable();
            $table->decimal('earthquake_risk', 5, 3)->nullable();
            $table->string('projection_methodology');
            $table->json('data_sources')->nullable();
            $table->string('validation_method')->nullable();
            $table->decimal('historical_accuracy', 5, 3)->nullable();
            $table->decimal('projection_variance', 5, 3)->nullable();
            $table->json('sensitivity_analysis')->nullable();
            $table->json('scenario_analysis')->nullable();
            $table->json('monte_carlo_simulation')->nullable();
            $table->decimal('expert_consensus', 5, 3)->nullable();
            $table->json('machine_learning_model')->nullable();
            $table->json('arima_forecast')->nullable();
            $table->json('regression_analysis')->nullable();
            $table->json('comparative_analysis')->nullable();
            $table->json('trend_analysis')->nullable();
            $table->json('cycle_analysis')->nullable();
            $table->text('projection_notes')->nullable();
            $table->json('assumptions')->nullable();
            $table->text('limitations')->nullable();
            $table->decimal('confidence_interval_lower', 18, 2)->nullable();
            $table->decimal('confidence_interval_upper', 18, 2)->nullable();
            $table->decimal('standard_error', 15, 2)->nullable();
            $table->decimal('mean_squared_error', 15, 2)->nullable();
            $table->decimal('r_squared_value', 5, 3)->nullable();
            $table->decimal('adjusted_r_squared', 5, 3)->nullable();
            $table->json('aic_bic_scores')->nullable();
            $table->json('model_selection_criteria')->nullable();
            $table->json('backtesting_results')->nullable();
            $table->decimal('out_of_sample_performance', 5, 3)->nullable();
            $table->json('cross_validation_results')->nullable();
            $table->decimal('projection_quality_score', 5, 3)->nullable();
            $table->string('validation_status')->default('pending');
            $table->text('review_comments')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('approval_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_financial_analysis_id', 'projection_year'], 'app_proj_year_idx');
            $table->index('projection_year');
            $table->index('projection_model');
            $table->index('validation_status');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('appreciation_projections');
    }
};
