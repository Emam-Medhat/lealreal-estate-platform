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
        if (!Schema::hasTable('properties')) {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->string('property_code')->unique();
            $table->string('title');
            $table->text('description');
            $table->enum('property_type', ['apartment', 'villa', 'house', 'land', 'commercial']);
            $table->enum('listing_type', ['sale', 'rent']);
            $table->decimal('price', 12, 2);
            $table->string('currency', 3)->default('SAR');
            $table->decimal('area', 10, 2);
            $table->enum('area_unit', ['sq_m', 'sq_ft'])->default('sq_m');
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->integer('floors')->nullable();
            $table->year('year_built')->nullable();
            $table->enum('status', ['draft', 'active', 'inactive', 'sold', 'rented'])->default('draft');
            $table->boolean('featured')->default(false);
            $table->boolean('premium')->default(false);
            $table->string('address');
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('country');
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->json('amenities')->nullable();
            $table->json('nearby_places')->nullable();
            $table->string('virtual_tour_url')->nullable();
            $table->string('video_url')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('inquiries_count')->default(0);
            $table->integer('favorites_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['agent_id', 'status']);
            $table->index(['property_type', 'listing_type']);
            $table->index(['city', 'country']);
            $table->index('price');
            $table->index('featured');
            $table->index('premium');
            $table->index('views_count');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
