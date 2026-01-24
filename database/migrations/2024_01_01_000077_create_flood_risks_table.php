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
        Schema::create('flood_risks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained('metaverse_properties')->onDelete('cascade');
            $table->foreignId('analytics_id')->nullable()->constrained('geospatial_analytics')->onDelete('cascade');
            $table->decimal('risk_score', 5, 2)->nullable();
            $table->string('risk_level');
            $table->string('flood_zone');
            $table->json('elevation_data')->nullable();
            $table->json('historical_floods')->nullable();
            $table->decimal('flood_probability', 5, 2)->nullable();
            $table->decimal('potential_damage', 12, 2)->nullable();
            $table->json('mitigation_measures')->nullable();
            $table->json('insurance_requirements')->nullable();
            $table->json('climate_change_impact')->nullable();
            $table->json('emergency_routes')->nullable();
            $table->json('analysis_parameters')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['property_id', 'risk_level']);
            $table->index('analytics_id');
            $table->index('status');
            $table->index('risk_level');
            $table->index('flood_zone');
            $table->index('risk_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flood_risks');
    }
};
