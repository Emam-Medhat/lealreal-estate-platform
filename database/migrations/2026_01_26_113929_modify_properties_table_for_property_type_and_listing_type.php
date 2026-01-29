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
            $table->unsignedBigInteger('property_type')->change();
            $table->enum('listing_type', ['sale', 'rent', 'lease'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->enum('property_type', ['apartment', 'villa', 'house', 'land', 'commercial'])->change();
            $table->enum('listing_type', ['sale', 'rent'])->change();
        });
    }
};
