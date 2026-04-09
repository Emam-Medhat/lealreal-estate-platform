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
        Schema::table('ai_property_valuations', function (Blueprint $table) {
            if (!Schema::hasColumn('ai_property_valuations', 'data_points')) {
                $table->json('data_points')->nullable();
            }
            if (!Schema::hasColumn('ai_property_valuations', 'property_features')) {
                $table->json('property_features')->nullable();
            }
            if (!Schema::hasColumn('ai_property_valuations', 'market_conditions')) {
                $table->json('market_conditions')->nullable();
            }
            if (!Schema::hasColumn('ai_property_valuations', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('ai_property_valuations', 'metadata')) {
                $table->json('metadata')->nullable();
            }
            // Make sure valuation_model exists if it's not there (migration had valuation_method)
            if (!Schema::hasColumn('ai_property_valuations', 'valuation_model') && Schema::hasColumn('ai_property_valuations', 'valuation_method')) {
                 // Rename or add alias? Controller uses valuation_model. Model uses valuation_method.
                 // Let's add valuation_model as column if missing.
                 $table->string('valuation_model', 50)->nullable();
            } elseif (!Schema::hasColumn('ai_property_valuations', 'valuation_model')) {
                 $table->string('valuation_model', 50)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_property_valuations', function (Blueprint $table) {
            $table->dropColumn([
                'data_points', 
                'property_features', 
                'market_conditions', 
                'notes',
                'metadata',
                'valuation_model'
            ]);
        });
    }
};
