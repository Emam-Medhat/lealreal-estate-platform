<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('property_appreciation_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained('metaverse_properties')->onDelete('cascade');
            $table->foreignId('analytics_id')->nullable()->constrained('geospatial_analytics')->onDelete('cascade');
            $table->string('analysis_method');
            $table->string('time_period');
            $table->decimal('analysis_radius', 8, 2)->nullable();
            $table->boolean('include_economic_factors')->default(true);
            $table->boolean('include_market_sentiment')->default(false);
            $table->json('weight_factors')->nullable();
            $table->decimal('current_value', 15, 2)->nullable();
            $table->decimal('annual_appreciation_rate', 5, 2)->nullable();
            $table->decimal('projected_value_5yr', 15, 2)->nullable();
            $table->decimal('projected_value_10yr', 15, 2)->nullable();
            $table->string('market_trend');
            $table->json('appreciation_drivers')->nullable();
            $table->json('risk_factors')->nullable();
            $table->json('investment_recommendations')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['property_id', 'analysis_method']);
            $table->index('analytics_id');
            $table->index('status');
            $table->index('analysis_method');
            $table->index('market_trend');
            $table->index('annual_appreciation_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_appreciation_maps');
    }
};
