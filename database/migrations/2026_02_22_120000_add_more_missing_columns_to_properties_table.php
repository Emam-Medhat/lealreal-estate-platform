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
            if (!Schema::hasColumn('properties', 'parks')) $table->json('parks')->nullable();
            if (!Schema::hasColumn('properties', 'universities')) $table->json('universities')->nullable();
            if (!Schema::hasColumn('properties', 'metro_stations')) $table->json('metro_stations')->nullable();
            if (!Schema::hasColumn('properties', 'cinemas')) $table->json('cinemas')->nullable();
            if (!Schema::hasColumn('properties', 'gyms')) $table->json('gyms')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['parks', 'universities', 'metro_stations', 'cinemas', 'gyms']);
        });
    }
};
