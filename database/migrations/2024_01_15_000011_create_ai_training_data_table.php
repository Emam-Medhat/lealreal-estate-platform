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
        if (!Schema::hasTable('ai_training_data')) {
        Schema::create('ai_training_data', function (Blueprint $table) {
            $table->id();
            $table->string('data_type', 50); // property_images, market_data, user_behavior, etc.
            $table->string('data_category', 50); // residential, commercial, rental, etc.
            $table->string('data_source', 100);
            $table->json('raw_data');
            $table->json('processed_data')->nullable();
            $table->json('labels')->nullable(); // For supervised learning
            $table->json('features')->nullable(); // Extracted features
            $table->json('metadata')->nullable();
            $table->string('model_type', 50); // classification, regression, clustering, etc.
            $table->string('training_purpose', 100); // price_prediction, fraud_detection, etc.
            $table->decimal('quality_score', 3, 2)->nullable(); // 0.00-10.00
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_used_for_training')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->string('model_version', 20)->nullable();
            $table->decimal('training_accuracy', 5, 2)->nullable(); // 0.00-100.00
            $table->json('performance_metrics')->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['data_type', 'data_category']);
            $table->index('data_source');
            $table->index('model_type');
            $table->index('training_purpose');
            $table->index('status');
            $table->index('is_verified');
            $table->index('is_used_for_training');
            $table->index('quality_score');
            $table->index('created_at');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_training_data');
    }
};
