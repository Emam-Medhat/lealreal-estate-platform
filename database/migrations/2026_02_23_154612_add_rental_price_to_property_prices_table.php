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
        Schema::table('property_prices', function (Blueprint $table) {
            $table->decimal('rental_price', 12, 2)->nullable()->after('price_per_sqm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_prices', function (Blueprint $table) {
            //
        });
    }
};
