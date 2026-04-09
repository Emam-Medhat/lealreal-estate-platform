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
        if (!Schema::hasTable('geospatial_analytics')) {
        Schema::create('geospatial_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained('metaverse_properties')->onDelete('cascade');
            $table->string('analysis_type');
            $table->decimal('analysis_radius', 8, 2)->nullable();
            $table->json('analysis_parameters')->nullable();
            $table->json('analysis_results')->nullable();
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->decimal('data_quality_score', 5, 2)->nullable();
            $table->datetime('analysis_date')->nullable();
            $table->string('status')->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['property_id', 'analysis_type']);
            $table->index('status');
            $table->index('analysis_date');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geospatial_analytics');
    }
};
