<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tax_benefits')) {
        Schema::create('tax_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_financial_analysis_id')->constrained()->onDelete('cascade');
            $table->integer('tax_year');
            $table->date('calculation_date');
            $table->decimal('depreciation_deduction', 15, 2);
            $table->decimal('mortgage_interest_deduction', 15, 2);
            $table->decimal('property_tax_deduction', 15, 2);
            $table->decimal('operating_expense_deduction', 15, 2);
            $table->decimal('repairs_maintenance_deduction', 15, 2);
            $table->decimal('capital_improvements_amortization', 15, 2);
            $table->decimal('depletion_allowance', 15, 2)->nullable();
            $table->decimal('passive_activity_loss', 15, 2)->nullable();
            $table->decimal('net_operating_loss', 15, 2)->nullable();
            $table->decimal('tax_credit_amount', 15, 2)->nullable();
            $table->decimal('total_tax_benefits', 15, 2);
            $table->decimal('tax_savings_amount', 15, 2);
            $table->decimal('effective_tax_rate', 8, 4);
            $table->decimal('marginal_tax_rate', 8, 4);
            $table->decimal('alternative_minimum_tax_impact', 15, 2)->nullable();
            $table->decimal('state_tax_benefits', 15, 2)->nullable();
            $table->decimal('local_tax_benefits', 15, 2)->nullable();
            $table->decimal('federal_tax_benefits', 15, 2);
            $table->decimal('taxable_income_before_benefits', 15, 2);
            $table->decimal('taxable_income_after_benefits', 15, 2);
            $table->decimal('tax_liability_before_benefits', 15, 2);
            $table->decimal('tax_liability_after_benefits', 15, 2);
            $table->string('depreciation_method')->default('straight_line');
            $table->json('depreciation_schedule')->nullable();
            $table->json('cost_segregation_analysis')->nullable();
            $table->decimal('section_179_deduction', 15, 2)->nullable();
            $table->decimal('bonus_depreciation', 15, 2)->nullable();
            $table->decimal('like_kind_exchange_benefits', 15, 2)->nullable();
            $table->decimal('opportunity_zone_benefits', 15, 2)->nullable();
            $table->decimal('energy_efficiency_credits', 15, 2)->nullable();
            $table->decimal('historic_preservation_credits', 15, 2)->nullable();
            $table->decimal('low_income_housing_credits', 15, 2)->nullable();
            $table->decimal('tax_loss_harvesting', 15, 2)->nullable();
            $table->decimal('carry_forward_losses', 15, 2)->nullable();
            $table->decimal('carry_back_losses', 15, 2)->nullable();
            $table->integer('tax_projection_years')->nullable();
            $table->json('tax_projection_data')->nullable();
            $table->json('tax_optimization_strategies')->nullable();
            $table->json('tax_planning_recommendations')->nullable();
            $table->text('compliance_notes')->nullable();
            $table->json('tax_law_changes_impact')->nullable();
            $table->json('assumptions')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_financial_analysis_id', 'tax_year']);
            $table->index('tax_year');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_benefits');
    }
};
