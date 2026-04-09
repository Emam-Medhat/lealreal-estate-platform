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
        if (!Schema::hasTable('developer_construction_updates')) {
        Schema::create('developer_construction_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('developer_projects')->onDelete('cascade');
            $table->foreignId('phase_id')->nullable()->constrained('developer_project_phases')->onDelete('cascade');
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description');
            $table->text('description_ar')->nullable();
            $table->enum('update_type', ['progress', 'milestone', 'delay', 'issue', 'achievement', 'inspection', 'permit', 'safety', 'quality', 'announcement']);
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->date('update_date');
            $table->date('next_update_date')->nullable();
            $table->decimal('progress_percentage', 5, 2)->nullable();
            $table->json('progress_details')->nullable();
            $table->json('work_completed')->nullable();
            $table->json('work_in_progress')->nullable();
            $table->json('upcoming_work')->nullable();
            $table->json('challenges')->nullable();
            $table->json('solutions')->nullable();
            $table->json('achievements')->nullable();
            $table->json('inspections')->nullable();
            $table->json('quality_checks')->nullable();
            $table->json('safety_incidents')->nullable();
            $table->json('material_deliveries')->nullable();
            $table->json('equipment_status')->nullable();
            $table->json('workforce_details')->nullable();
            $table->json('weather_conditions')->nullable();
            $table->json('schedule_impact')->nullable();
            $table->json('budget_impact')->nullable();
            $table->json('contractor_updates')->nullable();
            $table->json('supplier_updates')->nullable();
            $table->json('regulatory_compliance')->nullable();
            $table->json('permits_status')->nullable();
            $table->json('technical_issues')->nullable();
            $table->json('design_changes')->nullable();
            $table->json('client_requests')->nullable();
            $table->json('media_gallery')->nullable();
            $table->json('progress_photos')->nullable();
            $table->json('progress_videos')->nullable();
            $table->json('documents')->nullable();
            $table->json('reports')->nullable();
            $table->json('live_camera_feeds')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('video_update')->nullable();
            $table->json('contact_information')->nullable();
            $table->boolean('is_public')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('send_notifications')->default(true);
            $table->boolean('allow_comments')->default(true);
            $table->json('notification_settings')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('view_count')->default(0);
            $table->integer('comment_count')->default(0);
            $table->integer('share_count')->default(0);
            $table->json('seo_settings')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['developer_id', 'status']);
            $table->index(['project_id', 'update_date']);
            $table->index(['phase_id']);
            $table->index(['update_type']);
            $table->index(['priority']);
            $table->index(['published_at']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_construction_updates');
    }
};
