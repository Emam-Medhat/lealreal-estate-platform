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
        if (!Schema::hasTable('developer_metaverse_previews')) {
        Schema::create('developer_metaverse_previews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('developer_projects')->onDelete('cascade');
            $table->foreignId('phase_id')->nullable()->constrained('developer_project_phases')->onDelete('cascade');
            $table->foreignId('unit_id')->nullable()->constrained('developer_project_units')->onDelete('cascade');
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->enum('preview_type', ['virtual_tour', '3d_model', 'ar_experience', 'vr_experience', 'interactive_showcase', 'digital_twin']);
            $table->enum('status', ['draft', 'processing', 'ready', 'published', 'archived'])->default('draft');
            $table->enum('platform', ['web', 'mobile', 'vr_headset', 'ar_glasses', 'all_platforms']);
            $table->string('preview_url')->nullable();
            $table->string('embed_code')->nullable();
            $table->string('thumbnail_image')->nullable();
            $table->string('preview_image')->nullable();
            $table->json('preview_files')->nullable();
            $table->json('model_files')->nullable();
            $table->json('texture_files')->nullable();
            $table->json('animation_files')->nullable();
            $table->json('sound_files')->nullable();
            $table->json('interactive_elements')->nullable();
            $table->json('navigation_points')->nullable();
            $table->json('hotspots')->nullable();
            $table->json('information_points')->nullable();
            $table->json('camera_settings')->nullable();
            $table->json('lighting_settings')->nullable();
            $table->json('render_settings')->nullable();
            $table->json('quality_settings')->nullable();
            $table->json('performance_settings')->nullable();
            $table->json('device_requirements')->nullable();
            $table->json('system_requirements')->nullable();
            $table->json('accessibility_options')->nullable();
            $table->json('language_options')->nullable();
            $table->json('user_controls')->nullable();
            $table->json('interaction_modes')->nullable();
            $table->json('viewing_angles')->nullable();
            $table->json('zoom_levels')->nullable();
            $table->json('measurement_tools')->nullable();
            $table->json('comparison_tools')->nullable();
            $table->json('customization_options')->nullable();
            $table->json('sharing_options')->nullable();
            $table->json('analytics_tracking')->nullable();
            $table->json('user_data_collection')->nullable();
            $table->json('privacy_settings')->nullable();
            $table->json('security_settings')->nullable();
            $table->json('monetization_options')->nullable();
            $table->json('integration_apis')->nullable();
            $table->boolean('is_public')->default(true);
            $table->boolean('allow_download')->default(false);
            $table->boolean('enable_sharing')->default(true);
            $table->boolean('require_registration')->default(false);
            $table->boolean('has_audio')->default(false);
            $table->boolean('has_subtitles')->default(false);
            $table->boolean('is_interactive')->default(true);
            $table->boolean('supports_multiplayer')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('last_updated')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('view_count')->default(0);
            $table->integer('interaction_count')->default(0);
            $table->integer('share_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->json('user_analytics')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['developer_id', 'status']);
            $table->index(['project_id']);
            $table->index(['phase_id']);
            $table->index(['unit_id']);
            $table->index(['preview_type']);
            $table->index(['platform']);
            $table->index(['published_at']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_metaverse_previews');
    }
};
