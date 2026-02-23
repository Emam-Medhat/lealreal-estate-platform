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
        Schema::table('ai_price_predictions', function (Blueprint $table) {
            $table->decimal('current_price', 15, 2)->nullable()->change();
            $table->decimal('predicted_price_1m', 15, 2)->nullable()->change();
            $table->decimal('predicted_price_3m', 15, 2)->nullable()->change();
            $table->decimal('predicted_price_6m', 15, 2)->nullable()->change();
            $table->decimal('predicted_price_1y', 15, 2)->nullable()->change();
            $table->decimal('confidence_score', 3, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_price_predictions', function (Blueprint $table) {
            $table->decimal('current_price', 15, 2)->nullable(false)->change();
            $table->decimal('predicted_price_1m', 15, 2)->nullable(false)->change();
            $table->decimal('predicted_price_3m', 15, 2)->nullable(false)->change();
            $table->decimal('predicted_price_6m', 15, 2)->nullable(false)->change();
            $table->decimal('predicted_price_1y', 15, 2)->nullable(false)->change();
            $table->decimal('confidence_score', 3, 2)->nullable(false)->change();
        });
    }
};
