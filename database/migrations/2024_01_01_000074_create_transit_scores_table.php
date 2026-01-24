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
        Schema::create('transit_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained('metaverse_properties')->onDelete('cascade');
            $table->foreignId('analytics_id')->nullable()->constrained('geospatial_analytics')->onDelete('cascade');
            $table->decimal('transit_score', 5, 2)->nullable();
            $table->decimal('bus_score', 5, 2)->nullable();
            $table->decimal('metro_score', 5, 2)->nullable();
            $table->decimal('tram_score', 5, 2)->nullable();
            $table->decimal('train_score', 5, 2)->nullable();
            $table->json('transit_options')->nullable();
            $table->json('accessibility_metrics')->nullable();
            $table->json('service_frequency')->nullable();
            $table->json('coverage_analysis')->nullable();
            $table->json('improvement_suggestions')->nullable();
            $table->json('analysis_parameters')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['property_id']);
            $table->index('analytics_id');
            $table->index('status');
            $table->index('transit_score');
            $table->index('bus_score');
            $table->index('metro_score');
            $table->index('tram_score');
            $table->index('train_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transit_scores');
    }
};
