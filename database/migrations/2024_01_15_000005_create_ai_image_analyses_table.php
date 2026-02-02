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
        if (!Schema::hasTable('ai_image_analyses')) {
        Schema::create('ai_image_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('image_path', 500);
            $table->string('image_hash', 64)->unique();
            $table->string('analysis_type', 50);
            $table->json('detected_objects')->nullable();
            $table->json('room_types')->nullable();
            $table->decimal('quality_score', 3, 2); // 0.00-10.00
            $table->decimal('aesthetic_score', 3, 2); // 0.00-10.00
            $table->json('lighting_analysis')->nullable();
            $table->json('color_analysis')->nullable();
            $table->json('composition_analysis')->nullable();
            $table->json('clutter_analysis')->nullable();
            $table->json('renovation_suggestions')->nullable();
            $table->json('staging_recommendations')->nullable();
            $table->json('image_enhancements')->nullable();
            $table->string('ai_model_version', 20);
            $table->json('analysis_metadata')->nullable();
            $table->decimal('processing_time', 8, 3); // in seconds
            $table->decimal('confidence_level', 3, 2); // 0.00-1.00
            $table->string('status', 20)->default('completed');
            $table->string('enhanced_image_path', 500)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['property_id', 'analysis_type']);
            $table->index('user_id');
            $table->index('image_hash');
            $table->index('analysis_type');
            $table->index('status');
            $table->index('quality_score');
            $table->index('aesthetic_score');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_image_analyses');
    }
};
