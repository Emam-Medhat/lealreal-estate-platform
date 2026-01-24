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
        Schema::create('local_businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('neighborhood_id')->constrained()->onDelete('cascade');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('category', 50)->default('other');
            $table->string('status', 20)->default('active');
            $table->string('address', 500)->nullable();
            $table->decimal('latitude', 8, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('website', 500)->nullable();
            $table->json('opening_hours')->nullable();
            $table->json('services')->nullable();
            $table->json('products')->nullable();
            $table->json('specialties')->nullable();
            $table->json('price_range')->nullable();
            $table->json('payment_methods')->nullable();
            $table->json('delivery_options')->nullable();
            $table->string('contact_person', 255)->nullable();
            $table->json('social_media')->nullable();
            $table->json('images')->nullable();
            $table->string('logo', 500)->nullable();
            $table->json('gallery')->nullable();
            $table->boolean('verified')->default(false);
            $table->boolean('featured')->default(false);
            $table->json('tags')->nullable();
            $table->json('metadata')->nullable();
            $table->decimal('rating', 2, 1)->default(0);
            $table->integer('review_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['neighborhood_id']);
            $table->index(['category']);
            $table->index(['status']);
            $table->index(['verified']);
            $table->index(['featured']);
            $table->index(['rating']);
            $table->index(['view_count']);

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
        Schema::dropIfExists('local_businesses');
    }
};
