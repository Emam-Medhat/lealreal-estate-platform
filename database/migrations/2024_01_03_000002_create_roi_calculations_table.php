<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roi_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_financial_analysis_id')->constrained()->onDelete('cascade');
            $table->date('calculation_date');
            $table->decimal('total_investment', 12, 2);
            $table->decimal('annual_cash_flow', 12, 2);
            $table->decimal('property_value_appreciation', 12, 2);
            $table->decimal('tax_benefits', 12, 2);
            $table->decimal('loan_principal_paydown', 12, 2);
            $table->decimal('total_return', 12, 2);
            $table->decimal('roi_percentage', 8, 3);
            $table->decimal('cash_on_cash_return', 8, 3);
            $table->decimal('internal_rate_of_return', 8, 3);
            $table->decimal('net_present_value', 12, 2);
            $table->decimal('payback_period_years', 8, 2);
            $table->decimal('profitability_index', 8, 3);
            $table->string('calculation_method');
            $table->json('assumptions')->nullable();
            $table->string('scenario_type')->default('base_case');
            $table->decimal('risk_adjusted_roi', 8, 3)->nullable();
            $table->decimal('inflation_adjusted_roi', 8, 3)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_financial_analysis_id', 'calculation_date'], 'roi_calc_date_idx');
            $table->index('scenario_type');
            $table->index('calculation_method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roi_calculations');
    }
};
