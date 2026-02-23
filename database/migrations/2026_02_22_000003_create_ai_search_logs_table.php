<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * ai_search_logs
     *
     * Stores each smart-search call so the RecommendationService can build
     * behavioral profiles (preferred listing types, cities, budgets, …).
     *
     * Written by SmartSearchService::parse() via LogsAiUsage after a
     * successful AI parse.
     *
     * Note: raw_text is NOT stored for GDPR/privacy compliance.
     *       Only the extracted structured filters are kept.
     */
    public function up(): void
    {
        if (Schema::hasTable('ai_search_logs')) {
            return;
        }

        Schema::create('ai_search_logs', function (Blueprint $table) {
            $table->id();

            // Who searched (nullable → guest searches)
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('set null');

            // Extracted filter snapshot
            $table->string('listing_type', 10)->nullable()->index();
            $table->string('property_type', 30)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->decimal('min_price', 14, 2)->nullable();
            $table->decimal('max_price', 14, 2)->nullable();
            $table->string('price_category', 10)->nullable();
            $table->boolean('furnished')->nullable();
            $table->json('special_features')->nullable();

            // Metadata
            $table->string('ip_address', 45)->nullable();
            $table->boolean('cached')->default(false);

            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index(['listing_type', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_search_logs');
    }
};
