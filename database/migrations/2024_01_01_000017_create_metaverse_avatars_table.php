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
        if (!Schema::hasTable('metaverse_avatars')) {
        Schema::create('metaverse_avatars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('display_name');
            $table->string('avatar_type');
            $table->string('gender');
            $table->json('appearance');
            $table->json('clothing')->nullable();
            $table->json('accessories')->nullable();
            $table->json('skills')->nullable();
            $table->json('preferences')->nullable();
            $table->text('bio')->nullable();
            $table->json('personality_traits')->nullable();
            $table->string('language', 10)->default('en');
            $table->string('timezone')->default('UTC');
            $table->json('privacy_settings')->nullable();
            $table->foreignId('current_world_id')->nullable()->constrained('virtual_worlds')->onDelete('set null');
            $table->string('current_location')->nullable();
            $table->string('current_activity')->nullable();
            $table->boolean('is_online')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_active_at')->nullable();
            $table->string('avatar_image_path')->nullable();
            $table->string('model_path')->nullable();
            $table->string('model_file_type')->nullable();
            $table->integer('model_file_size')->nullable();
            $table->integer('inventory_slots')->default(50);
            $table->integer('experience_points')->default(0);
            $table->integer('level')->default(1);
            $table->integer('reputation_points')->default(0);
            $table->integer('achievement_points')->default(0);
            $table->string('social_rank')->default('newcomer');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['user_id', 'is_active']);
            $table->index(['current_world_id', 'is_online']);
            $table->index(['avatar_type', 'is_active']);
            $table->index(['gender', 'is_active']);
            $table->index(['level', 'is_active']);
            $table->index(['social_rank', 'is_active']);
            $table->index(['is_online', 'is_active']);
            $table->index(['experience_points', 'is_active']);
            $table->index(['reputation_points', 'is_active']);
            $table->index(['created_at', 'is_active']);
            $table->index(['updated_at', 'is_active']);

            // Regular index for search (removed full-text due to length limit)
            $table->index('name');
            $table->index('display_name');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metaverse_avatars');
    }
};
