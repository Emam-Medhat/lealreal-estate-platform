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
        Schema::create('heatmaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained('metaverse_properties')->onDelete('cascade');
            $table->foreignId('analytics_id')->nullable()->constrained('geospatial_analytics')->onDelete('cascade');
            $table->string('heatmap_type');
            $table->json('data_points')->nullable();
            $table->json('intensity_levels')->nullable();
            $table->string('color_scheme')->default('viridis');
            $table->json('bounds')->nullable();
            $table->integer('zoom_level')->default(12);
            $table->integer('grid_size')->default(50);
            $table->json('analysis_parameters')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['property_id', 'heatmap_type']);
            $table->index('analytics_id');
            $table->index('status');
            $table->index('heatmap_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heatmaps');
    }
};
