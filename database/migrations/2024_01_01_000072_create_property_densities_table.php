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
        Schema::create('property_densities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained('metaverse_properties')->onDelete('cascade');
            $table->foreignId('analytics_id')->nullable()->constrained('geospatial_analytics')->onDelete('cascade');
            $table->string('density_level');
            $table->decimal('density_score', 5, 2)->nullable();
            $table->integer('property_count')->nullable();
            $table->decimal('area_size', 10, 2)->nullable();
            $table->json('density_distribution')->nullable();
            $table->json('property_types')->nullable();
            $table->json('density_trends')->nullable();
            $table->json('development_potential')->nullable();
            $table->decimal('analysis_radius', 8, 2)->nullable();
            $table->json('analysis_parameters')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['property_id', 'density_level']);
            $table->index('analytics_id');
            $table->index('status');
            $table->index('density_level');
            $table->index('density_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_densities');
    }
};
