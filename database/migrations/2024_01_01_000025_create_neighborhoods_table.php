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
        Schema::create('neighborhoods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('city', 100);
            $table->string('district', 100);
            $table->text('description')->nullable();
            $table->string('property_type', 50)->default('residential');
            $table->string('status', 20)->default('active');
            $table->decimal('latitude', 8, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->json('boundaries')->nullable();
            $table->json('features')->nullable();
            $table->json('amenities')->nullable();
            $table->json('transportation')->nullable();
            $table->json('schools')->nullable();
            $table->json('healthcare')->nullable();
            $table->json('shopping')->nullable();
            $table->json('recreation')->nullable();
            $table->decimal('safety_rating', 2, 1)->nullable();
            $table->decimal('walkability_score', 2, 1)->nullable();
            $table->decimal('transit_score', 2, 1)->nullable();
            $table->decimal('green_space_ratio', 5, 2)->nullable();
            $table->decimal('average_price', 10, 2)->nullable();
            $table->json('price_range')->nullable();
            $table->integer('property_count')->default(0);
            $table->integer('resident_count')->default(0);
            $table->decimal('population_density', 8, 2)->nullable();
            $table->string('development_status', 50)->nullable();
            $table->string('infrastructure_quality', 50)->nullable();
            $table->string('community_engagement', 50)->nullable();
            $table->json('metadata')->nullable();
            $table->decimal('rating', 2, 1)->default(0);
            $table->integer('review_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['city', 'district']);
            $table->index(['status']);
            $table->index(['property_type']);
            $table->index(['rating']);
            $table->index(['average_price']);
            $table->index(['resident_count']);
            $table->index(['safety_rating']);
            $table->index(['walkability_score']);
            $table->index(['transit_score']);
            $table->index(['development_status']);

            // Spatial index for coordinates (if supported)
            $table->index(['latitude', 'longitude']);

            // Regular index for search (removed full-text due to length limit)
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neighborhoods');
    }
};
