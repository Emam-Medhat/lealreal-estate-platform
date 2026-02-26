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
            // Add missing columns only if they don't exist
            if (!Schema::hasColumn('properties', 'currency')) {
                $table->string('currency', 10)->nullable()->default('SAR')->after('price');
            }
            if (!Schema::hasColumn('properties', 'virtual_tour_url')) {
                $table->text('virtual_tour_url')->nullable()->after('land_area_unit');
            }
            if (!Schema::hasColumn('properties', 'video_url')) {
                $table->text('video_url')->nullable()->after('virtual_tour_url');
            }
            if (!Schema::hasColumn('properties', 'slug')) {
                $table->text('slug')->nullable()->after('video_url');
            }
            if (!Schema::hasColumn('properties', 'nearby_places')) {
                $table->text('nearby_places')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('properties', 'schools')) {
                $table->text('schools')->nullable()->after('nearby_places');
            }
            if (!Schema::hasColumn('properties', 'hospitals')) {
                $table->text('hospitals')->nullable()->after('schools');
            }
            if (!Schema::hasColumn('properties', 'shopping_centers')) {
                $table->text('shopping_centers')->nullable()->after('hospitals');
            }
            if (!Schema::hasColumn('properties', 'restaurants')) {
                $table->text('restaurants')->nullable()->after('shopping_centers');
            }
            if (!Schema::hasColumn('properties', 'parks')) {
                $table->text('parks')->nullable()->after('restaurants');
            }
            if (!Schema::hasColumn('properties', 'universities')) {
                $table->text('universities')->nullable()->after('parks');
            }
            if (!Schema::hasColumn('properties', 'metro_stations')) {
                $table->text('metro_stations')->nullable()->after('universities');
            }
            if (!Schema::hasColumn('properties', 'cinemas')) {
                $table->text('cinemas')->nullable()->after('metro_stations');
            }
            if (!Schema::hasColumn('properties', 'gyms')) {
                $table->text('gyms')->nullable()->after('cinemas');
            }
            if (!Schema::hasColumn('properties', 'transportation')) {
                $table->text('transportation')->nullable()->after('gyms');
            }
            if (!Schema::hasColumn('properties', 'ownership_type')) {
                $table->string('ownership_type')->nullable()->after('transportation');
            }
            if (!Schema::hasColumn('properties', 'deed_number')) {
                $table->string('deed_number')->nullable()->after('ownership_type');
            }
            if (!Schema::hasColumn('properties', 'registration_number')) {
                $table->string('registration_number')->nullable()->after('deed_number');
            }
            if (!Schema::hasColumn('properties', 'zoning')) {
                $table->string('zoning')->nullable()->after('registration_number');
            }
            if (!Schema::hasColumn('properties', 'building_permit')) {
                $table->string('building_permit')->nullable()->after('zoning');
            }
            if (!Schema::hasColumn('properties', 'occupancy_permit')) {
                $table->string('occupancy_permit')->nullable()->after('building_permit');
            }
            if (!Schema::hasColumn('properties', 'energy_rating')) {
                $table->string('energy_rating')->nullable()->after('occupancy_permit');
            }
            if (!Schema::hasColumn('properties', 'solar_panels')) {
                $table->boolean('solar_panels')->nullable()->default(false)->after('energy_rating');
            }
            if (!Schema::hasColumn('properties', 'water_heating')) {
                $table->string('water_heating')->nullable()->after('solar_panels');
            }
            if (!Schema::hasColumn('properties', 'insulation')) {
                $table->string('insulation')->nullable()->after('water_heating');
            }
            if (!Schema::hasColumn('properties', 'double_glazing')) {
                $table->boolean('double_glazing')->nullable()->default(false)->after('insulation');
            }
            if (!Schema::hasColumn('properties', 'air_conditioning')) {
                $table->string('air_conditioning')->nullable()->after('double_glazing');
            }
            if (!Schema::hasColumn('properties', 'specifications')) {
                $table->text('specifications')->nullable()->after('air_conditioning');
            }
            if (!Schema::hasColumn('properties', 'materials')) {
                $table->text('materials')->nullable()->after('specifications');
            }
            if (!Schema::hasColumn('properties', 'interior_features')) {
                $table->text('interior_features')->nullable()->after('materials');
            }
            if (!Schema::hasColumn('properties', 'exterior_features')) {
                $table->text('exterior_features')->nullable()->after('interior_features');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $columns = [
                'currency', 'virtual_tour_url', 'video_url', 'slug', 'nearby_places', 'schools', 
                'hospitals', 'shopping_centers', 'restaurants', 'parks', 'universities', 
                'metro_stations', 'cinemas', 'gyms', 'transportation', 'ownership_type', 
                'deed_number', 'registration_number', 'zoning', 'building_permit', 
                'occupancy_permit', 'energy_rating', 'solar_panels', 'water_heating', 
                'insulation', 'double_glazing', 'air_conditioning', 'specifications', 
                'materials', 'interior_features', 'exterior_features'
            ];
            $table->dropColumn($columns);
        });
    }
};
