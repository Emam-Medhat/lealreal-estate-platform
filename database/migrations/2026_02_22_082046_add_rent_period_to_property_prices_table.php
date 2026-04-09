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
            if (!Schema::hasColumn('property_prices', 'rent_period')) {
                $table->string('rent_period')->nullable()->after('price_type');
            }
            if (!Schema::hasColumn('property_prices', 'original_price')) {
                $table->decimal('original_price', 15, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('property_prices', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->nullable()->after('original_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_prices', function (Blueprint $table) {
            $table->dropColumn(['rent_period', 'original_price', 'discount_percentage']);
        });
    }
};
