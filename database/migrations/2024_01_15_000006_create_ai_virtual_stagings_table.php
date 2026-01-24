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
        Schema::create('ai_virtual_stagings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('original_image_path', 500);
            $table->string('staged_image_path', 500);
            $table->string('room_type', 50);
            $table->string('staging_style', 50);
            $table->string('furniture_style', 50);
            $table->string('color_scheme', 50);
            $table->string('target_audience', 50);
            $table->decimal('quality_score', 3, 2); // 0.00-10.00
            $table->decimal('realism_score', 3, 2); // 0.00-10.00
            $table->decimal('aesthetic_appeal', 3, 2); // 0.00-10.00
            $table->json('furniture_items')->nullable();
            $table->json('decor_elements')->nullable();
            $table->json('lighting_setup')->nullable();
            $table->json('spatial_arrangement')->nullable();
            $table->decimal('style_consistency', 3, 2); // 0.00-1.00
            $table->decimal('market_appeal', 3, 2); // 0.00-1.00
            $table->string('ai_model_version', 20);
            $table->json('staging_metadata')->nullable();
            $table->decimal('processing_time', 8, 3); // in seconds
            $table->decimal('confidence_level', 3, 2); // 0.00-1.00
            $table->string('status', 20)->default('completed');
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->integer('view_count')->default(0);
            $table->decimal('engagement_score', 3, 2)->default(0); // 0.00-10.00
            $table->decimal('feedback_rating', 3, 2)->nullable(); // 0.00-5.00
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['property_id', 'room_type']);
            $table->index('user_id');
            $table->index('staging_style');
            $table->index('status');
            $table->index('is_published');
            $table->index('quality_score');
            $table->index('realism_score');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_virtual_stagings');
    }
};
