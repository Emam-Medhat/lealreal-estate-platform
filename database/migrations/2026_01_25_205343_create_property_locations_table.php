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
        if (!Schema::hasTable('property_locations')) {
        Schema::create('property_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('postal_code')->nullable();
            $table->string('country')->default('United States');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('district')->nullable();
            $table->string('county')->nullable();
            $table->text('directions')->nullable();
            $table->string('google_maps_url')->nullable();
            $table->json('nearby_places')->nullable(); // JSON array of nearby amenities
            $table->decimal('distance_to_city_center', 8, 2)->nullable(); // in kilometers
            $table->integer('walking_score')->nullable(); // 0-100
            $table->integer('transit_score')->nullable(); // 0-100
            $table->integer('bike_score')->nullable(); // 0-100
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['city', 'state']);
            $table->index(['latitude', 'longitude']);
            $table->index(['neighborhood']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_locations');
    }
};
