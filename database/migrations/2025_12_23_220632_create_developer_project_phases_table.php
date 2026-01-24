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
        Schema::create('developer_project_phases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('developer_projects')->onDelete('cascade');
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('phase_number')->unique();
            $table->enum('status', ['planning', 'under_construction', 'completed', 'on_hold', 'cancelled'])->default('planning');
            $table->date('start_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->date('expected_completion_date')->nullable();
            $table->decimal('total_area', 12, 2)->nullable();
            $table->string('area_unit')->default('sqm');
            $table->integer('total_units')->default(0);
            $table->integer('completed_units')->default(0);
            $table->decimal('total_cost', 15, 2)->nullable();
            $table->string('currency')->default('SAR');
            $table->decimal('construction_progress', 5, 2)->default(0); // percentage
            $table->json('construction_company')->nullable();
            $table->json('contractor_details')->nullable();
            $table->json('supervision_company')->nullable();
            $table->json('engineering_consultant')->nullable();
            $table->json('architecture_firm')->nullable();
            $table->json('materials_used')->nullable();
            $table->json('construction_methods')->nullable();
            $table->json('safety_measures')->nullable();
            $table->json('quality_standards')->nullable();
            $table->json('permits_required')->nullable();
            $table->json('permits_obtained')->nullable();
            $table->json('inspections')->nullable();
            $table->json('certifications')->nullable();
            $table->json('milestones')->nullable();
            $table->json('media_gallery')->nullable();
            $table->json('progress_photos')->nullable();
            $table->json('progress_videos')->nullable();
            $table->json('live_camera_feeds')->nullable();
            $table->json('bim_models')->nullable();
            $table->json('technical_specifications')->nullable();
            $table->json('unit_types')->nullable();
            $table->json('amenities')->nullable();
            $table->json('facilities')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('allow_visits')->default(false);
            $table->json('visit_schedule')->nullable();
            $table->json('contact_information')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['project_id', 'status']);
            $table->index(['phase_number']);
            $table->index(['start_date']);
            $table->index(['completion_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_project_phases');
    }
};
