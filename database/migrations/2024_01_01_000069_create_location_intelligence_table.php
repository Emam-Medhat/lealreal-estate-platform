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
        Schema::create('location_intelligence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained('metaverse_properties')->onDelete('cascade');
            $table->foreignId('analytics_id')->nullable()->constrained('geospatial_analytics')->onDelete('cascade');
            $table->string('intelligence_type');
            $table->decimal('location_score', 5, 2)->nullable();
            $table->decimal('investment_potential', 5, 2)->nullable();
            $table->json('market_analysis')->nullable();
            $table->json('competitive_analysis')->nullable();
            $table->json('growth_indicators')->nullable();
            $table->json('risk_factors')->nullable();
            $table->json('recommendations')->nullable();
            $table->json('analysis_parameters')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['property_id', 'intelligence_type']);
            $table->index('analytics_id');
            $table->index('status');
            $table->index('intelligence_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_intelligence');
    }
};
