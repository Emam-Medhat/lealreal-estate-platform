<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ─── 1. AI Usage Logs ─────────────────────────────────────────────
        if (!Schema::hasTable('ai_usage_logs')) {
            Schema::create('ai_usage_logs', function (Blueprint $table) {
                $table->id();
                $table->string('feature', 50);
                $table->string('model', 50)->nullable();
                $table->unsignedInteger('input_tokens')->default(0);
                $table->unsignedInteger('output_tokens')->default(0);
                $table->decimal('cost_usd', 10, 6)->default(0);
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('ip_address', 45)->nullable();
                $table->json('meta')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index(['feature', 'created_at']);
                $table->index(['user_id', 'created_at']);
            });
        }

        // ─── 2. Smart Search Logs ────────────────────────────────────────
        if (!Schema::hasTable('ai_search_logs')) {
            Schema::create('ai_search_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('ip_address', 45)->nullable();
                $table->text('query_text');
                $table->json('parsed_filters')->nullable();
                $table->string('listing_type', 20)->nullable();
                $table->unsignedInteger('results_count')->default(0);
                $table->boolean('cache_hit')->default(false);
                $table->timestamps();

                $table->index(['user_id', 'created_at']);
            });
        }

        // ─── 3. Property Image Analyses ──────────────────────────────────
        if (!Schema::hasTable('property_image_analyses')) {
            Schema::create('property_image_analyses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
                $table->string('image_path');
                $table->string('status', 20)->default('pending');
                $table->string('room_type', 50)->nullable();
                $table->string('furniture_quality', 30)->nullable();
                $table->string('brightness_level', 20)->nullable();
                $table->unsignedTinyInteger('quality_score')->nullable();
                $table->json('issues')->nullable();
                $table->string('source', 40)->nullable();
                $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['property_id', 'status']);
                $table->index('quality_score');
            });
        }

        // ─── 4. AI Chatbot Sessions ──────────────────────────────────────
        if (!Schema::hasTable('ai_chat_sessions')) {
            Schema::create('ai_chat_sessions', function (Blueprint $table) {
                $table->id();
                $table->string('session_id', 64)->unique();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->json('history')->nullable();
                $table->json('filters')->nullable();
                $table->unsignedInteger('message_count')->default(0);
                $table->timestamps();

                $table->index(['user_id', 'updated_at']);
            });
        }

        // ─── 5. Price Predictions ────────────────────────────────────────
        if (!Schema::hasTable('ai_price_predictions')) {
            Schema::create('ai_price_predictions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->json('input_data');
                $table->decimal('predicted_price_min', 15, 2)->nullable();
                $table->decimal('predicted_price_max', 15, 2)->nullable();
                $table->decimal('predicted_price_mid', 15, 2)->nullable();
                $table->decimal('confidence_score', 5, 4)->nullable();
                $table->unsignedTinyInteger('investment_score')->nullable();
                $table->string('risk_level', 20)->nullable();
                $table->text('valuation_rationale')->nullable();
                $table->string('source', 40)->nullable();
                $table->timestamps();

                $table->index(['property_id', 'created_at']);
            });
        }

        // ─── 6. Recommendation Scores ────────────────────────────────────
        if (!Schema::hasTable('ai_recommendation_scores')) {
            Schema::create('ai_recommendation_scores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
                $table->decimal('total_score', 5, 2)->default(0);
                $table->decimal('price_score', 5, 2)->default(0);
                $table->decimal('location_score', 5, 2)->default(0);
                $table->decimal('bedroom_score', 5, 2)->default(0);
                $table->decimal('behavior_score', 5, 2)->default(0);
                $table->text('reason')->nullable();
                $table->timestamp('calculated_at')->useCurrent();

                $table->unique(['user_id', 'property_id']);
                $table->index(['user_id', 'total_score']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_recommendation_scores');
        Schema::dropIfExists('ai_price_predictions');
        Schema::dropIfExists('ai_chat_sessions');
        Schema::dropIfExists('property_image_analyses');
        Schema::dropIfExists('ai_search_logs');
        Schema::dropIfExists('ai_usage_logs');
    }
};
