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
        Schema::create('ai_generated_descriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('description_type', 50);
            $table->json('generated_content');
            $table->string('language', 10)->default('ar');
            $table->string('tone', 50);
            $table->string('target_audience', 50);
            $table->json('key_features')->nullable();
            $table->json('selling_points')->nullable();
            $table->text('call_to_action')->nullable();
            $table->json('seo_keywords')->nullable();
            $table->decimal('quality_score', 3, 2); // 0.00-10.00
            $table->decimal('readability_score', 3, 2); // 0.00-10.00
            $table->decimal('engagement_prediction', 3, 2); // 0.00-10.00
            $table->string('ai_model_version', 20);
            $table->json('generation_metadata')->nullable();
            $table->string('status', 20)->default('draft');
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['property_id', 'description_type']);
            $table->index('user_id');
            $table->index('status');
            $table->index('is_published');
            $table->index('language');
            $table->index('quality_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_generated_descriptions');
    }
};
