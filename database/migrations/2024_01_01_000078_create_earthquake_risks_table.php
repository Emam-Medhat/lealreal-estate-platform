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
        if (!Schema::hasTable('earthquake_risks')) {
        Schema::create('earthquake_risks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained('metaverse_properties')->onDelete('cascade');
            $table->foreignId('analytics_id')->nullable()->constrained('geospatial_analytics')->onDelete('cascade');
            $table->decimal('risk_score', 5, 2)->nullable();
            $table->string('risk_level');
            $table->string('seismic_zone');
            $table->decimal('fault_line_distance', 8, 2)->nullable();
            $table->json('soil_type')->nullable();
            $table->json('building_code_compliance')->nullable();
            $table->json('structural_assessment')->nullable();
            $table->json('historical_earthquakes')->nullable();
            $table->json('probability_magnitude')->nullable();
            $table->decimal('potential_damage', 12, 2)->nullable();
            $table->json('mitigation_recommendations')->nullable();
            $table->json('retrofitting_needs')->nullable();
            $table->json('analysis_parameters')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['property_id', 'risk_level']);
            $table->index('analytics_id');
            $table->index('status');
            $table->index('risk_level');
            $table->index('seismic_zone');
            $table->index('risk_score');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('earthquake_risks');
    }
};
