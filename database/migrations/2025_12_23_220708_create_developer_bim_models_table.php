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
        Schema::create('developer_bim_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('developer_projects')->onDelete('cascade');
            $table->foreignId('phase_id')->nullable()->constrained('developer_project_phases')->onDelete('cascade');
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('model_number')->unique();
            $table->enum('model_type', ['architectural', 'structural', 'mechanical', 'electrical', 'plumbing', 'fire_protection', 'civil', 'landscape', 'interior', 'facades']);
            $table->enum('status', ['draft', 'review', 'approved', 'published', 'archived'])->default('draft');
            $table->enum('format', ['ifc', 'rvt', 'dwg', 'skp', '3dm', 'obj', 'fbx', 'dae']);
            $table->string('version')->default('1.0');
            $table->string('software_used');
            $table->string('software_version')->nullable();
            $table->json('model_files')->nullable();
            $table->string('main_model_file');
            $table->string('thumbnail_image')->nullable();
            $table->string('preview_image')->nullable();
            $table->json('metadata')->nullable();
            $table->json('technical_specifications')->nullable();
            $table->json('model_parameters')->nullable();
            $table->json('material_specifications')->nullable();
            $table->json('structural_information')->nullable();
            $table->json('system_requirements')->nullable();
            $table->json('coordinate_system')->nullable();
            $table->json('level_of_development')->nullable();
            $table->json('classification_systems')->nullable();
            $table->json('property_sets')->nullable();
            $table->json('family_types')->nullable();
            $table->json('worksharing_details')->nullable();
            $table->json('clash_detection_results')->nullable();
            $table->json('coordination_issues')->nullable();
            $table->json('model_checks')->nullable();
            $table->json('quality_assurance')->nullable();
            $table->json('interference_checks')->nullable();
            $table->json('design_validation')->nullable();
            $table->json('quantity_takeoffs')->nullable();
            $table->json('cost_estimation')->nullable();
            $table->json('schedule_integration')->nullable();
            $table->json('facility_management')->nullable();
            $table->json('asset_information')->nullable();
            $table->json('maintenance_data')->nullable();
            $table->json('lifecycle_information')->nullable();
            $table->json('security_settings')->nullable();
            $table->json('access_permissions')->nullable();
            $table->json('version_history')->nullable();
            $table->json('change_logs')->nullable();
            $table->json('collaboration_data')->nullable();
            $table->json('integration_links')->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('allow_download')->default(false);
            $table->boolean('enable_viewer')->default(true);
            $table->string('viewer_url')->nullable();
            $table->json('viewer_settings')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('last_updated')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('view_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->json('analytics')->nullable();
            $table->timestamps();
            
            $table->index(['developer_id', 'status']);
            $table->index(['project_id']);
            $table->index(['phase_id']);
            $table->index(['model_type']);
            $table->index(['published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_bim_models');
    }
};
