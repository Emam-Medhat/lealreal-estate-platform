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
        if (!Schema::hasTable('demographic_analyses')) {
        Schema::create('demographic_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained('metaverse_properties')->onDelete('cascade');
            $table->foreignId('analytics_id')->nullable()->constrained('geospatial_analytics')->onDelete('cascade');
            $table->decimal('population_density', 10, 2)->nullable();
            $table->decimal('median_income', 12, 2)->nullable();
            $table->decimal('employment_rate', 5, 2)->nullable();
            $table->json('age_distribution')->nullable();
            $table->json('education_levels')->nullable();
            $table->json('household_composition')->nullable();
            $table->json('ethnic_diversity')->nullable();
            $table->json('migration_patterns')->nullable();
            $table->json('demographic_trends')->nullable();
            $table->json('analysis_parameters')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['property_id']);
            $table->index('analytics_id');
            $table->index('status');
            $table->index('median_income');
            $table->index('employment_rate');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demographic_analyses');
    }
};
