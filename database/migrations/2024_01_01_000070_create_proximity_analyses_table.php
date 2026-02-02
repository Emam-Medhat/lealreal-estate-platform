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
        if (!Schema::hasTable('proximity_analyses')) {
        Schema::create('proximity_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained('metaverse_properties')->onDelete('cascade');
            $table->foreignId('analytics_id')->nullable()->constrained('geospatial_analytics')->onDelete('cascade');
            $table->decimal('proximity_score', 5, 2)->nullable();
            $table->decimal('walk_score', 5, 2)->nullable();
            $table->decimal('transit_score', 5, 2)->nullable();
            $table->json('amenity_scores')->nullable();
            $table->json('distance_analysis')->nullable();
            $table->json('accessibility_metrics')->nullable();
            $table->json('nearby_facilities')->nullable();
            $table->decimal('analysis_radius', 8, 2)->nullable();
            $table->json('analysis_parameters')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['property_id']);
            $table->index('analytics_id');
            $table->index('status');
            $table->index('proximity_score');
            $table->index('walk_score');
            $table->index('transit_score');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proximity_analyses');
    }
};
