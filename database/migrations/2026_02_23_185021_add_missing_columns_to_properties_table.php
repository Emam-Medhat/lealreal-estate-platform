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
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('properties', 'zoning')) {
                $table->string('zoning')->nullable()->after('energy_rating');
            }
            if (!Schema::hasColumn('properties', 'zoning_type')) {
                $table->string('zoning_type')->nullable()->after('zoning');
            }
            if (!Schema::hasColumn('properties', 'ownership_type')) {
                $table->string('ownership_type')->nullable()->after('zoning_type');
            }
            if (!Schema::hasColumn('properties', 'registration_number')) {
                $table->string('registration_number')->nullable()->after('ownership_type');
            }
            if (!Schema::hasColumn('properties', 'deed_number')) {
                $table->string('deed_number')->nullable()->after('registration_number');
            }
            if (!Schema::hasColumn('properties', 'land_use')) {
                $table->string('land_use')->nullable()->after('deed_number');
            }
            if (!Schema::hasColumn('properties', 'occupancy_permit')) {
                $table->string('occupancy_permit')->nullable()->after('land_use');
            }
            if (!Schema::hasColumn('properties', 'building_permit')) {
                $table->string('building_permit')->nullable()->after('occupancy_permit');
            }
            if (!Schema::hasColumn('properties', 'listed_date')) {
                $table->date('listed_date')->nullable()->after('building_permit');
            }
            if (!Schema::hasColumn('properties', 'available_from')) {
                $table->date('available_from')->nullable()->after('listed_date');
            }
            if (!Schema::hasColumn('properties', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('available_from');
            }
            if (!Schema::hasColumn('properties', 'sold_date')) {
                $table->date('sold_date')->nullable()->after('expiry_date');
            }
            if (!Schema::hasColumn('properties', 'heating_system')) {
                $table->string('heating_system')->nullable()->after('sold_date');
            }
            if (!Schema::hasColumn('properties', 'water_heating')) {
                $table->string('water_heating')->nullable()->after('heating_system');
            }
            if (!Schema::hasColumn('properties', 'insulation')) {
                $table->string('insulation')->nullable()->after('water_heating');
            }
            if (!Schema::hasColumn('properties', 'ventilation')) {
                $table->string('ventilation')->nullable()->after('insulation');
            }
            if (!Schema::hasColumn('properties', 'air_conditioning')) {
                $table->string('air_conditioning')->nullable()->after('ventilation');
            }
            if (!Schema::hasColumn('properties', 'solar_panels')) {
                $table->boolean('solar_panels')->nullable()->after('air_conditioning');
            }
            if (!Schema::hasColumn('properties', 'double_glazing')) {
                $table->boolean('double_glazing')->nullable()->after('solar_panels');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            //
        });
    }
};
