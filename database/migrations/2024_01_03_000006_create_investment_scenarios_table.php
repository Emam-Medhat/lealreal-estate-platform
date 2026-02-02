<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('investment_scenarios')) {
        Schema::create('investment_scenarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_financial_analysis_id')->constrained()->onDelete('cascade');
            $table->string('scenario_name');
            $table->string('scenario_type');
            $table->text('description')->nullable();
            $table->decimal('purchase_price', 15, 2);
            $table->decimal('down_payment_percentage', 5, 3);
            $table->decimal('loan_amount', 15, 2);
            $table->decimal('interest_rate', 5, 3);
            $table->integer('loan_term_years');
            $table->decimal('rental_income_growth_rate', 5, 3);
            $table->decimal('expense_growth_rate', 5, 3);
            $table->decimal('vacancy_rate', 5, 3);
            $table->decimal('appreciation_rate', 5, 3);
            $table->decimal('inflation_rate', 5, 3);
            $table->integer('holding_period_years');
            $table->decimal('selling_costs_percentage', 5, 3);
            $table->decimal('capital_gains_tax_rate', 5, 3);
            $table->decimal('renovation_costs', 15, 2)->default(0);
            $table->decimal('property_management_fee', 12, 2)->nullable();
            $table->decimal('insurance_costs', 12, 2)->nullable();
            $table->decimal('property_tax_rate', 5, 3);
            $table->decimal('maintenance_reserve_percentage', 5, 3);
            $table->string('exit_strategy')->nullable();
            $table->string('risk_tolerance_level')->default('moderate');
            $table->string('investment_objective')->nullable();
            $table->json('cash_flow_projections')->nullable();
            $table->json('roi_calculations')->nullable();
            $table->json('risk_metrics')->nullable();
            $table->json('sensitivity_analysis')->nullable();
            $table->json('monte_carlo_results')->nullable();
            $table->decimal('probability_of_success', 5, 3)->nullable();
            $table->decimal('best_case_value', 15, 2)->nullable();
            $table->decimal('worst_case_value', 15, 2)->nullable();
            $table->decimal('expected_value', 15, 2)->nullable();
            $table->decimal('confidence_interval_lower', 15, 2)->nullable();
            $table->decimal('confidence_interval_upper', 15, 2)->nullable();
            $table->json('scenario_assumptions')->nullable();
            $table->json('market_conditions')->nullable();
            $table->json('economic_assumptions')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_financial_analysis_id', 'scenario_type'], 'inv_scenario_idx');
            $table->index('scenario_type');
            $table->index('is_active');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_scenarios');
    }
};
