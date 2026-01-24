<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cap_rate_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_financial_analysis_id')->constrained()->onDelete('cascade');
            $table->date('calculation_date');
            $table->decimal('net_operating_income', 12, 2);
            $table->decimal('property_value', 12, 2);
            $table->decimal('capitalization_rate', 8, 4);
            $table->decimal('market_cap_rate', 8, 4)->nullable();
            $table->integer('comparable_properties_count')->default(0);
            $table->string('market_trend')->nullable();
            $table->decimal('location_factor', 5, 3)->default(0);
            $table->decimal('property_condition_factor', 5, 3)->default(0);
            $table->decimal('age_factor', 5, 3)->default(0);
            $table->decimal('size_factor', 5, 3)->default(0);
            $table->decimal('amenities_factor', 5, 3)->default(0);
            $table->decimal('risk_adjusted_cap_rate', 8, 4)->nullable();
            $table->decimal('projected_cap_rate', 8, 4)->nullable();
            $table->json('historical_cap_rates')->nullable();
            $table->string('market_segment')->nullable();
            $table->string('property_class')->nullable();
            $table->string('neighborhood_quality')->nullable();
            $table->decimal('rent_growth_rate', 5, 3)->nullable();
            $table->decimal('expense_growth_rate', 5, 3)->nullable();
            $table->decimal('vacancy_trend', 5, 3)->nullable();
            $table->string('calculation_method')->default('direct');
            $table->json('data_sources')->nullable();
            $table->integer('confidence_level')->default(70);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_financial_analysis_id', 'calculation_date'], 'cap_calc_date_idx');
            $table->index('market_segment');
            $table->index('property_class');
            $table->index('calculation_method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cap_rate_calculations');
    }
};
