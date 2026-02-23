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
            $table->decimal('predicted_price', 20, 2)->nullable()->change();
            $table->string('ai_model_version')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_price_predictions', function (Blueprint $table) {
            $table->decimal('predicted_price', 15, 2)->nullable()->change();
            $table->string('ai_model_version')->nullable(false)->change();
        });
    }
};
