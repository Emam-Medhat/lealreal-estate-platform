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
        Schema::create('neighborhood_boundaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('neighborhood_id')->constrained()->onDelete('cascade');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('boundary_type', 50)->default('administrative');
            $table->string('status', 20)->default('active');
            $table->json('coordinates')->nullable();
            $table->json('bounds')->nullable();
            $table->json('center_point')->nullable();
            $table->decimal('area_size', 10, 2)->nullable();
            $table->decimal('perimeter', 10, 2)->nullable();
            $table->json('elevation_data')->nullable();
            $table->json('land_use')->nullable();
            $table->json('zoning_info')->nullable();
            $table->json('infrastructure')->nullable();
            $table->json('natural_features')->nullable();
            $table->json('access_points')->nullable();
            $table->json('landmarks')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['neighborhood_id']);
            $table->index(['boundary_type']);
            $table->index(['status']);
            $table->index(['area_size']);

            // Regular index for search (removed full-text due to length limit)
            $table->index('name');
            $table->index('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neighborhood_boundaries');
    }
};
