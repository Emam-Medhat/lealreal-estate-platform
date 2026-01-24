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
        Schema::create('ai_models', function (Blueprint $table) {
            $table->id();
            $table->string('model_name', 100);
            $table->string('model_type', 50); // neural_network, random_forest, etc.
            $table->string('model_category', 50); // pricing, fraud_detection, etc.
            $table->string('version', 20);
            $table->text('description')->nullable();
            $table->json('model_parameters')->nullable();
            $table->json('training_config')->nullable();
            $table->json('performance_metrics')->nullable();
            $table->decimal('accuracy', 5, 2)->nullable(); // 0.00-100.00
            $table->decimal('precision', 5, 2)->nullable(); // 0.00-100.00
            $table->decimal('recall', 5, 2)->nullable(); // 0.00-100.00
            $table->decimal('f1_score', 5, 2)->nullable(); // 0.00-100.00
            $table->json('training_data_stats')->nullable();
            $table->json('validation_data_stats')->nullable();
            $table->timestamp('trained_at');
            $table->timestamp('deployed_at')->nullable();
            $table->string('status', 20)->default('training');
            $table->boolean('is_active')->default(false);
            $table->boolean('is_production')->default(false);
            $table->string('model_file_path', 500)->nullable();
            $table->json('model_metadata')->nullable();
            $table->integer('training_epochs')->nullable();
            $table->decimal('training_time', 8, 3)->nullable(); // in hours
            $table->json('hyperparameters')->nullable();
            $table->json('feature_importance')->nullable();
            $table->json('limitations')->nullable();
            $table->text('usage_guidelines')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->unique(['model_name', 'version']);
            $table->index('model_type');
            $table->index('model_category');
            $table->index('status');
            $table->index('is_active');
            $table->index('is_production');
            $table->index('trained_at');
            $table->index('deployed_at');
            $table->index('accuracy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_models');
    }
};
