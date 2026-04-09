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
        Schema::table('properties', function (Blueprint $table) {
            // Location/Nearby - JSON columns
            if (!Schema::hasColumn('properties', 'schools')) $table->json('schools')->nullable();
            if (!Schema::hasColumn('properties', 'hospitals')) $table->json('hospitals')->nullable();
            if (!Schema::hasColumn('properties', 'shopping_centers')) $table->json('shopping_centers')->nullable();
            if (!Schema::hasColumn('properties', 'restaurants')) $table->json('restaurants')->nullable();
            if (!Schema::hasColumn('properties', 'transportation')) $table->json('transportation')->nullable();
            if (!Schema::hasColumn('properties', 'nearby_places')) $table->json('nearby_places')->nullable();

            // Legal/Permits - String columns
            if (!Schema::hasColumn('properties', 'ownership_type')) $table->string('ownership_type')->nullable();
            if (!Schema::hasColumn('properties', 'deed_number')) $table->string('deed_number')->nullable();
            if (!Schema::hasColumn('properties', 'registration_number')) $table->string('registration_number')->nullable();
            if (!Schema::hasColumn('properties', 'zoning')) $table->string('zoning')->nullable();
            if (!Schema::hasColumn('properties', 'building_permit')) $table->string('building_permit')->nullable();
            if (!Schema::hasColumn('properties', 'occupancy_permit')) $table->string('occupancy_permit')->nullable();
            
            // Energy/Features - String/Boolean columns
            if (!Schema::hasColumn('properties', 'energy_rating')) $table->string('energy_rating')->nullable();
            if (!Schema::hasColumn('properties', 'solar_panels')) $table->boolean('solar_panels')->default(false);
            if (!Schema::hasColumn('properties', 'water_heating')) $table->boolean('water_heating')->default(false);
            if (!Schema::hasColumn('properties', 'insulation')) $table->boolean('insulation')->default(false);
            if (!Schema::hasColumn('properties', 'double_glazing')) $table->boolean('double_glazing')->default(false);
            if (!Schema::hasColumn('properties', 'air_conditioning')) $table->boolean('air_conditioning')->default(false);

            // Detailed Features - JSON columns
            if (!Schema::hasColumn('properties', 'specifications')) $table->json('specifications')->nullable();
            if (!Schema::hasColumn('properties', 'materials')) $table->json('materials')->nullable();
            if (!Schema::hasColumn('properties', 'interior_features')) $table->json('interior_features')->nullable();
            if (!Schema::hasColumn('properties', 'exterior_features')) $table->json('exterior_features')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'schools', 'hospitals', 'shopping_centers', 'restaurants', 'transportation', 'nearby_places',
                'ownership_type', 'deed_number', 'registration_number', 'zoning', 'building_permit', 'occupancy_permit',
                'energy_rating', 'solar_panels', 'water_heating', 'insulation', 'double_glazing', 'air_conditioning',
                'specifications', 'materials', 'interior_features', 'exterior_features'
            ]);
        });
    }
};
