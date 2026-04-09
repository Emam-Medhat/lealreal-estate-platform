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
        if (!Schema::hasTable('ai_market_insights')) {
        Schema::create('ai_market_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('insight_type', 50);
            $table->string('market_area', 100);
            $table->string('property_type', 50);
            $table->string('price_range', 100)->nullable();
            $table->json('market_trends')->nullable();
            $table->json('demand_analysis')->nullable();
            $table->json('supply_analysis')->nullable();
            $table->json('competitor_analysis')->nullable();
            $table->json('investment_opportunities')->nullable();
            $table->json('risk_factors')->nullable();
            $table->json('seasonal_patterns')->nullable();
            $table->json('economic_indicators')->nullable();
            $table->json('demographic_trends')->nullable();
            $table->json('infrastructure_development')->nullable();
            $table->json('regulatory_changes')->nullable();
            $table->json('market_forecast')->nullable();
            $table->json('recommendations')->nullable();
            $table->decimal('insight_score', 3, 2); // 0.00-10.00
            $table->decimal('reliability_score', 3, 2); // 0.00-10.00
            $table->string('time_horizon', 30);
            $table->string('ai_model_version', 20);
            $table->json('insight_metadata')->nullable();
            $table->decimal('confidence_level', 3, 2); // 0.00-1.00
            $table->string('status', 20)->default('published');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->integer('view_count')->default(0);
            $table->decimal('accuracy_rating', 3, 2)->nullable(); // 0.00-5.00
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['property_id', 'insight_type']);
            $table->index('user_id');
            $table->index('insight_type');
            $table->index('market_area');
            $table->index('property_type');
            $table->index('status');
            $table->index('published_at');
            $table->index('expires_at');
            $table->index('insight_score');
            $table->index('time_horizon');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_market_insights');
    }
};
