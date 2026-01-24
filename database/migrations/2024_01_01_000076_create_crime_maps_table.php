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
        Schema::create('crime_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained('metaverse_properties')->onDelete('cascade');
            $table->foreignId('analytics_id')->nullable()->constrained('geospatial_analytics')->onDelete('cascade');
            $table->decimal('safety_score', 5, 2)->nullable();
            $table->decimal('crime_rate', 5, 2)->nullable();
            $table->json('crime_types')->nullable();
            $table->json('crime_trends')->nullable();
            $table->json('incident_data')->nullable();
            $table->json('police_presence')->nullable();
            $table->json('neighborhood_watch')->nullable();
            $table->json('security_measures')->nullable();
            $table->json('safety_recommendations')->nullable();
            $table->json('comparative_analysis')->nullable();
            $table->json('analysis_parameters')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['property_id']);
            $table->index('analytics_id');
            $table->index('status');
            $table->index('safety_score');
            $table->index('crime_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crime_maps');
    }
};
