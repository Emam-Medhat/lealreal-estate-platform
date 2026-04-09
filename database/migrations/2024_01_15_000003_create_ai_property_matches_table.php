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
        if (!Schema::hasTable('ai_property_matches')) {
        Schema::create('ai_property_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('buyer_profile_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('match_score', 5, 2); // 0.00-100.00
            $table->json('compatibility_factors')->nullable();
            $table->json('matching_criteria')->nullable();
            $table->json('property_analysis')->nullable();
            $table->json('buyer_preferences')->nullable();
            $table->string('recommendation_level', 30);
            $table->decimal('match_confidence', 3, 2); // 0.00-1.00
            $table->decimal('price_suitability', 3, 2); // 0.00-1.00
            $table->decimal('location_match', 3, 2); // 0.00-1.00
            $table->decimal('feature_match', 3, 2); // 0.00-1.00
            $table->json('market_timing')->nullable();
            $table->string('ai_model_version', 20);
            $table->json('matching_metadata')->nullable();
            $table->string('status', 20)->default('pending');
            $table->boolean('contacted')->default(false);
            $table->timestamp('contacted_at')->nullable();
            $table->boolean('viewing_scheduled')->default(false);
            $table->timestamp('viewing_date')->nullable();
            $table->boolean('offer_made')->default(false);
            $table->decimal('offer_amount', 15, 2)->nullable();
            $table->boolean('deal_closed')->default(false);
            $table->decimal('deal_amount', 15, 2)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'property_id']);
            $table->index('buyer_profile_id');
            $table->index('match_score');
            $table->index('recommendation_level');
            $table->index('status');
            $table->index('contacted');
            $table->index('viewing_scheduled');
            $table->index('offer_made');
            $table->index('deal_closed');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_property_matches');
    }
};
