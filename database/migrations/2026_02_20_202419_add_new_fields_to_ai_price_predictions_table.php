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
            if (!Schema::hasColumn('ai_price_predictions', 'time_horizon')) {
                $table->string('time_horizon')->nullable();
            }
            if (!Schema::hasColumn('ai_price_predictions', 'market_data')) {
                $table->json('market_data')->nullable();
            }
            if (!Schema::hasColumn('ai_price_predictions', 'property_features')) {
                $table->json('property_features')->nullable();
            }
            if (!Schema::hasColumn('ai_price_predictions', 'economic_indicators')) {
                $table->json('economic_indicators')->nullable();
            }
            if (!Schema::hasColumn('ai_price_predictions', 'predicted_price')) {
                $table->decimal('predicted_price', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('ai_price_predictions', 'confidence_interval')) {
                $table->json('confidence_interval')->nullable();
            }
            if (!Schema::hasColumn('ai_price_predictions', 'accuracy')) {
                $table->decimal('accuracy', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('ai_price_predictions', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('ai_price_predictions', 'metadata')) {
                $table->json('metadata')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_price_predictions', function (Blueprint $table) {
            $table->dropColumn([
                'time_horizon',
                'market_data',
                'property_features',
                'economic_indicators',
                'predicted_price',
                'confidence_interval',
                'accuracy',
                'notes',
                'metadata'
            ]);
        });
    }
};
