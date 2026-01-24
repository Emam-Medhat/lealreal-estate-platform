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
        Schema::create('community_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('neighborhood_id')->constrained()->onDelete('cascade');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('type', 50)->default('other');
            $table->string('status', 20)->default('active');
            $table->string('address', 500)->nullable();
            $table->decimal('latitude', 8, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('website', 500)->nullable();
            $table->json('opening_hours')->nullable();
            $table->json('facilities')->nullable();
            $table->json('services')->nullable();
            $table->json('accessibility')->nullable();
            $table->integer('capacity')->nullable();
            $table->decimal('area_size', 10, 2)->nullable();
            $table->integer('year_built')->nullable();
            $table->integer('last_renovated')->nullable();
            $table->json('maintenance_info')->nullable();
            $table->json('contact_info')->nullable();
            $table->json('rules')->nullable();
            $table->json('fees')->nullable();
            $table->json('images')->nullable();
            $table->string('main_image', 500)->nullable();
            $table->json('gallery')->nullable();
            $table->boolean('verified')->default(false);
            $table->boolean('featured')->default(false);
            $table->json('tags')->nullable();
            $table->json('metadata')->nullable();
            $table->decimal('rating', 2, 1)->default(0);
            $table->integer('review_count')->default(0);
            $table->integer('visit_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['neighborhood_id']);
            $table->index(['type']);
            $table->index(['status']);
            $table->index(['verified']);
            $table->index(['featured']);
            $table->index(['rating']);
            $table->index(['capacity']);
            $table->index(['area_size']);

            // Spatial index for coordinates (if supported)
            $table->index(['latitude', 'longitude']);

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
        Schema::dropIfExists('community_amenities');
    }
};
