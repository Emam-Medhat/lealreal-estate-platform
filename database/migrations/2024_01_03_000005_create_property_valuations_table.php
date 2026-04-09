<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('property_valuations')) {
        Schema::create('property_valuations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_financial_analysis_id')->constrained()->onDelete('cascade');
            $table->date('valuation_date');
            $table->string('valuation_method');
            $table->decimal('property_value', 15, 2);
            $table->decimal('comparable_sales_value', 15, 2)->nullable();
            $table->decimal('income_approach_value', 15, 2)->nullable();
            $table->decimal('cost_approach_value', 15, 2)->nullable();
            $table->decimal('residual_method_value', 15, 2)->nullable();
            $table->decimal('automated_valuation_value', 15, 2)->nullable();
            $table->decimal('final_valuation', 15, 2);
            $table->decimal('confidence_score', 5, 3)->default(0.7);
            $table->decimal('valuation_range_low', 15, 2);
            $table->decimal('valuation_range_high', 15, 2);
            $table->json('adjustment_factors')->nullable();
            $table->json('comparable_properties')->nullable();
            $table->json('market_conditions')->nullable();
            $table->string('property_condition')->nullable();
            $table->string('location_quality')->nullable();
            $table->json('economic_factors')->nullable();
            $table->json('zoning_restrictions')->nullable();
            $table->decimal('development_potential', 15, 2)->nullable();
            $table->string('highest_and_best_use')->nullable();
            $table->decimal('replacement_cost', 15, 2)->nullable();
            $table->decimal('depreciation', 15, 2)->nullable();
            $table->decimal('land_value', 15, 2)->nullable();
            $table->decimal('improvement_value', 15, 2)->nullable();
            $table->decimal('capitalization_rate_used', 8, 4)->nullable();
            $table->decimal('discount_rate_used', 8, 4)->nullable();
            $table->decimal('terminal_growth_rate', 8, 4)->nullable();
            $table->text('valuation_notes')->nullable();
            $table->json('assumptions')->nullable();
            $table->json('data_sources')->nullable();
            $table->string('valuer_name')->nullable();
            $table->string('valuation_purpose')->default('investment');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_financial_analysis_id', 'valuation_date'], 'prop_val_date_idx');
            $table->index('valuation_method');
            $table->index('valuation_purpose');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('property_valuations');
    }
};
