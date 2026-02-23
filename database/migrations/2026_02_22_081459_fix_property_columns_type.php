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
            // Change boolean columns to nullable strings to support enum values
            // We use change() to modify existing columns
            // Ensure you have doctrine/dbal installed for column modifications
            
            // water_heating
            if (Schema::hasColumn('properties', 'water_heating')) {
                $table->string('water_heating')->nullable()->change();
            } else {
                $table->string('water_heating')->nullable();
            }

            // insulation
            if (Schema::hasColumn('properties', 'insulation')) {
                $table->string('insulation')->nullable()->change();
            } else {
                $table->string('insulation')->nullable();
            }

            // air_conditioning
            if (Schema::hasColumn('properties', 'air_conditioning')) {
                $table->string('air_conditioning')->nullable()->change();
            } else {
                $table->string('air_conditioning')->nullable();
            }
            
            // Also ensure energy_rating is nullable string if it exists
             if (Schema::hasColumn('properties', 'energy_rating')) {
                $table->string('energy_rating')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Revert changes - note that data loss might occur if converting string back to boolean
            // We'll revert to boolean default false as per original migration
             if (Schema::hasColumn('properties', 'water_heating')) {
                $table->boolean('water_heating')->default(false)->change();
            }
             if (Schema::hasColumn('properties', 'insulation')) {
                $table->boolean('insulation')->default(false)->change();
            }
             if (Schema::hasColumn('properties', 'air_conditioning')) {
                $table->boolean('air_conditioning')->default(false)->change();
            }
        });
    }
};
