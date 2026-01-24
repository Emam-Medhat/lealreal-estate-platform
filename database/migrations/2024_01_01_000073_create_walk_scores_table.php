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
        Schema::create('walk_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained('metaverse_properties')->onDelete('cascade');
            $table->foreignId('analytics_id')->nullable()->constrained('geospatial_analytics')->onDelete('cascade');
            $table->decimal('walk_score', 5, 2)->nullable();
            $table->decimal('transit_score', 5, 2)->nullable();
            $table->decimal('bike_score', 5, 2)->nullable();
            $table->json('amenity_scores')->nullable();
            $table->json('walkability_factors')->nullable();
            $table->json('nearby_amenities')->nullable();
            $table->json('pedestrian_infrastructure')->nullable();
            $table->json('safety_metrics')->nullable();
            $table->json('improvement_suggestions')->nullable();
            $table->json('analysis_parameters')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['property_id']);
            $table->index('analytics_id');
            $table->index('status');
            $table->index('walk_score');
            $table->index('transit_score');
            $table->index('bike_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('walk_scores');
    }
};
