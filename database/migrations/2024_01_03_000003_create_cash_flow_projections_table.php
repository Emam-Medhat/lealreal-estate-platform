<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_flow_projections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_financial_analysis_id')->constrained()->onDelete('cascade');
            $table->foreignId('roi_calculation_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('projection_year');
            $table->decimal('rental_income', 12, 2);
            $table->decimal('other_income', 12, 2);
            $table->decimal('operating_expenses', 12, 2);
            $table->decimal('capital_expenditures', 12, 2);
            $table->decimal('financing_costs', 12, 2);
            $table->decimal('tax_payments', 12, 2);
            $table->decimal('net_operating_income', 12, 2);
            $table->decimal('cash_flow_before_tax', 12, 2);
            $table->decimal('tax_benefits', 12, 2);
            $table->decimal('cash_flow_after_tax', 12, 2);
            $table->decimal('loan_principal_payment', 12, 2);
            $table->decimal('loan_interest_payment', 12, 2);
            $table->decimal('total_cash_flow', 12, 2);
            $table->decimal('cumulative_cash_flow', 12, 2);
            $table->decimal('vacancy_rate', 5, 3);
            $table->decimal('inflation_rate', 5, 3);
            $table->decimal('appreciation_rate', 5, 3);
            $table->decimal('property_value', 12, 2);
            $table->decimal('loan_balance', 12, 2);
            $table->decimal('equity_position', 12, 2);
            $table->decimal('cash_flow_per_unit', 10, 2)->nullable();
            $table->decimal('debt_service_coverage_ratio', 8, 3);
            $table->decimal('breakeven_occupancy_rate', 5, 3);
            $table->string('scenario_type')->default('base_case');
            $table->json('assumptions')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_financial_analysis_id', 'projection_year'], 'cf_proj_year_idx');
            $table->index('scenario_type');
            $table->index('projection_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_flow_projections');
    }
};
