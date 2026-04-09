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
        if (!Schema::hasTable('virtual_worlds')) {
        Schema::create('virtual_worlds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('world_type');
            $table->string('theme');
            $table->string('access_level');
            $table->integer('max_avatars');
            $table->json('world_settings')->nullable();
            $table->json('environment_settings')->nullable();
            $table->json('physics_settings')->nullable();
            $table->json('graphics_settings')->nullable();
            $table->json('audio_settings')->nullable();
            $table->json('rules_guidelines')->nullable();
            $table->json('monetization_settings')->nullable();
            $table->json('moderation_settings')->nullable();
            $table->string('status')->default('development');
            $table->boolean('is_active')->default(false);
            $table->timestamp('launch_date')->nullable();
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->json('dimensions')->nullable();
            $table->string('world_map_path')->nullable();
            $table->json('landmarks')->nullable();
            $table->json('zones')->nullable();
            $table->integer('current_avatar_count')->default(0);
            $table->integer('max_building_height')->nullable();
            $table->json('building_restrictions')->nullable();
            $table->boolean('weather_system')->default(false);
            $table->boolean('day_night_cycle')->default(false);
            $table->boolean('seasonal_changes')->default(false);
            $table->boolean('ambient_sounds')->default(false);
            $table->json('customization_options')->nullable();
            $table->json('api_settings')->nullable();
            $table->json('integration_settings')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['world_type', 'status']);
            $table->index(['theme', 'status']);
            $table->index(['access_level', 'status']);
            $table->index(['is_active', 'status']);
            $table->index(['creator_id', 'status']);
            $table->index(['max_avatars', 'status']);
            $table->index(['current_avatar_count', 'status']);
            $table->index(['created_at', 'status']);
            $table->index(['updated_at', 'status']);

            // Full-text search index removed due to length limitations
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_worlds');
    }
};
