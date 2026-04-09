<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rental_income_projections')) {
        Schema::create('rental_income_projections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_financial_analysis_id')->constrained()->onDelete('cascade');
            $table->integer('projection_year');
            $table->integer('projection_month')->nullable();
            $table->decimal('gross_rental_income', 15, 2);
            $table->decimal('other_income', 15, 2);
            $table->decimal('vacancy_loss', 15, 2);
            $table->decimal('credit_loss', 15, 2);
            $table->decimal('concession_loss', 15, 2);
            $table->decimal('effective_gross_income', 15, 2);
            $table->decimal('rent_per_unit', 12, 2);
            $table->integer('number_of_units');
            $table->decimal('occupancy_rate', 5, 3);
            $table->decimal('rent_growth_rate', 8, 4);
            $table->decimal('market_rent_trend', 8, 4);
            $table->decimal('seasonal_adjustment', 5, 3);
            $table->decimal('lease_renewal_rate', 5, 3);
            $table->decimal('turnover_rate', 5, 3);
            $table->decimal('rent_control_impact', 15, 2)->nullable();
            $table->decimal('section_8_income', 15, 2)->nullable();
            $table->decimal('housing_assistance_income', 15, 2)->nullable();
            $table->decimal('parking_income', 15, 2)->nullable();
            $table->decimal('storage_income', 15, 2)->nullable();
            $table->decimal('laundry_income', 15, 2)->nullable();
            $table->decimal('vending_income', 15, 2)->nullable();
            $table->decimal('pet_rent_income', 15, 2)->nullable();
            $table->decimal('late_fee_income', 15, 2)->nullable();
            $table->decimal('application_fee_income', 15, 2)->nullable();
            $table->decimal('administrative_fee_income', 15, 2)->nullable();
            $table->decimal('utility_income', 15, 2)->nullable();
            $table->decimal('internet_cable_income', 15, 2)->nullable();
            $table->decimal('amenity_fee_income', 15, 2)->nullable();
            $table->decimal('guest_rental_income', 15, 2)->nullable();
            $table->decimal('event_space_income', 15, 2)->nullable();
            $table->decimal('advertising_income', 15, 2)->nullable();
            $table->decimal('cell_tower_income', 15, 2)->nullable();
            $table->decimal('solar_panel_income', 15, 2)->nullable();
            $table->decimal('other_miscellaneous_income', 15, 2)->nullable();
            $table->json('rent_adjustment_factors')->nullable();
            $table->json('market_demand_factors')->nullable();
            $table->json('economic_factors')->nullable();
            $table->json('demographic_factors')->nullable();
            $table->json('competition_factors')->nullable();
            $table->decimal('infrastructure_impact', 15, 2)->nullable();
            $table->decimal('zoning_regulations_impact', 15, 2)->nullable();
            $table->decimal('property_improvements_impact', 15, 2)->nullable();
            $table->json('seasonal_variations')->nullable();
            $table->json('regional_trends')->nullable();
            $table->json('neighborhood_development')->nullable();
            $table->json('transportation_development')->nullable();
            $table->decimal('school_district_quality', 5, 3)->nullable();
            $table->decimal('crime_rate_impact', 5, 3)->nullable();
            $table->decimal('employment_growth_impact', 8, 4)->nullable();
            $table->decimal('population_growth_impact', 8, 4)->nullable();
            $table->decimal('inflation_adjustment', 8, 4)->nullable();
            $table->json('rent_control_regulations')->nullable();
            $table->decimal('rent_stabilization_impact', 15, 2)->nullable();
            $table->json('affordable_housing_requirements')->nullable();
            $table->decimal('inclusionary_zoning_impact', 15, 2)->nullable();
            $table->json('market_segment_analysis')->nullable();
            $table->json('tenant_demographics')->nullable();
            $table->json('income_level_trends')->nullable();
            $table->json('household_size_trends')->nullable();
            $table->json('age_demographics')->nullable();
            $table->json('employment_sectors')->nullable();
            $table->json('migration_patterns')->nullable();
            $table->json('housing_preferences')->nullable();
            $table->json('lifestyle_trends')->nullable();
            $table->json('technology_adoption')->nullable();
            $table->decimal('remote_work_impact', 5, 3)->nullable();
            $table->decimal('urban_suburban_shift', 5, 3)->nullable();
            $table->json('multi_family_trends')->nullable();
            $table->json('single_family_rental_trends')->nullable();
            $table->json('luxury_rental_trends')->nullable();
            $table->json('affordable_housing_trends')->nullable();
            $table->json('student_housing_trends')->nullable();
            $table->json('senior_housing_trends')->nullable();
            $table->json('military_housing_trends')->nullable();
            $table->json('corporate_housing_trends')->nullable();
            $table->json('vacation_rental_trends')->nullable();
            $table->string('projection_methodology');
            $table->json('data_sources')->nullable();
            $table->json('market_survey_data')->nullable();
            $table->json('comparable_properties')->nullable();
            $table->json('historical_performance')->nullable();
            $table->json('lease_expiration_schedule')->nullable();
            $table->decimal('renewal_probability', 5, 3)->nullable();
            $table->json('market_lease_terms')->nullable();
            $table->json('concession_trends')->nullable();
            $table->json('incentive_programs')->nullable();
            $table->decimal('marketing_effectiveness', 5, 3)->nullable();
            $table->decimal('property_reputation', 5, 3)->nullable();
            $table->decimal('online_reviews_impact', 5, 3)->nullable();
            $table->decimal('social_media_presence', 5, 3)->nullable();
            $table->decimal('website_performance', 5, 3)->nullable();
            $table->decimal('lead_conversion_rates', 5, 3)->nullable();
            $table->decimal('tour_to_application_ratio', 5, 3)->nullable();
            $table->decimal('application_to_lease_ratio', 5, 3)->nullable();
            $table->decimal('average_lease_term', 8, 2)->nullable();
            $table->json('lease_renewal_terms')->nullable();
            $table->json('early_termination_clauses')->nullable();
            $table->json('rent_increase_policies')->nullable();
            $table->decimal('expense_reimbursement_income', 15, 2)->nullable();
            $table->decimal('triple_net_income', 15, 2)->nullable();
            $table->decimal('percentage_rent_income', 15, 2)->nullable();
            $table->decimal('common_area_maintenance_income', 15, 2)->nullable();
            $table->decimal('real_estate_tax_reimbursement', 15, 2)->nullable();
            $table->decimal('insurance_reimbursement', 15, 2)->nullable();
            $table->decimal('capital_improvement_reimbursement', 15, 2)->nullable();
            $table->decimal('projection_confidence', 5, 3);
            $table->json('risk_factors')->nullable();
            $table->json('sensitivity_analysis')->nullable();
            $table->json('scenario_analysis')->nullable();
            $table->json('monte_carlo_simulation')->nullable();
            $table->text('projection_notes')->nullable();
            $table->json('assumptions')->nullable();
            $table->text('limitations')->nullable();
            $table->string('validation_status')->default('pending');
            $table->text('review_comments')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('approval_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_financial_analysis_id', 'projection_year', 'projection_month'], 'rental_proj_idx');
            $table->index('projection_year');
            $table->index('projection_month');
            $table->index('validation_status');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_income_projections');
    }
};
