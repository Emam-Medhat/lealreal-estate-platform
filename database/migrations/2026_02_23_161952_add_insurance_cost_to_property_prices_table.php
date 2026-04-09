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
            $table->decimal('insurance_cost', 12, 2)->nullable()->after('property_tax');
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
