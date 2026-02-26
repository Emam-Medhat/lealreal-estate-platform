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
            if (!Schema::hasColumn('properties', 'investment_score')) {
                $table->decimal('investment_score', 4, 2)->nullable()->after('premium');
            }
            if (!Schema::hasColumn('properties', 'roi_percentage')) {
                $table->decimal('roi_percentage', 6, 2)->nullable()->after('investment_score');
            }
            if (!Schema::hasColumn('properties', 'risk_score')) {
                $table->decimal('risk_score', 5, 2)->nullable()->after('roi_percentage');
            }
            if (!Schema::hasColumn('properties', 'liquidity_score')) {
                $table->decimal('liquidity_score', 5, 2)->nullable()->after('risk_score');
            }
            if (!Schema::hasColumn('properties', 'demand_score')) {
                $table->decimal('demand_score', 5, 2)->nullable()->after('liquidity_score');
            }
            if (!Schema::hasColumn('properties', 'expected_appreciation')) {
                $table->decimal('expected_appreciation', 5, 2)->nullable()->after('demand_score');
            }
            if (!Schema::hasColumn('properties', 'predicted_price')) {
                $table->decimal('predicted_price', 14, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('properties', 'appreciation_forecast')) {
                $table->json('appreciation_forecast')->nullable()->after('predicted_price');
            }
            if (!Schema::hasColumn('properties', 'confidence_score')) {
                $table->decimal('confidence_score', 4, 2)->nullable()->after('appreciation_forecast');
            }
            if (!Schema::hasColumn('properties', 'overpriced_flag')) {
                $table->boolean('overpriced_flag')->default(false)->after('confidence_score');
            }
            if (!Schema::hasColumn('properties', 'fraud_score')) {
                $table->unsignedTinyInteger('fraud_score')->nullable()->after('premium');
            }
            if (!Schema::hasColumn('properties', 'fraud_flags')) {
                $table->json('fraud_flags')->nullable()->after('fraud_score');
            }
            if (!Schema::hasColumn('properties', 'fraud_risk_level')) {
                $table->enum('fraud_risk_level', ['low', 'medium', 'high'])->nullable()->after('fraud_flags');
            }
            if (!Schema::hasColumn('properties', 'restricted_at')) {
                $table->timestamp('restricted_at')->nullable()->after('fraud_risk_level');
            }
            if (!Schema::hasColumn('properties', 'requires_manual_review')) {
                $table->boolean('requires_manual_review')->default(false)->after('restricted_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'investment_score',
                'roi_percentage',
                'risk_score',
                'liquidity_score',
                'demand_score',
                'expected_appreciation',
                'predicted_price',
                'appreciation_forecast',
                'confidence_score',
                'overpriced_flag',
                'fraud_score',
                'fraud_flags',
                'fraud_risk_level',
                'restricted_at',
                'requires_manual_review',
            ]);
        });
    }
};
