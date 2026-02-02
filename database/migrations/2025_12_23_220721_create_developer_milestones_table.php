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
        if (!Schema::hasTable('developer_milestones')) {
        Schema::create('developer_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('developer_projects')->onDelete('cascade');
            $table->foreignId('phase_id')->nullable()->constrained('developer_project_phases')->onDelete('cascade');
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('milestone_number');
            $table->enum('milestone_type', ['planning', 'design', 'permit', 'construction', 'inspection', 'delivery', 'payment', 'documentation']);
            $table->enum('status', ['pending', 'in_progress', 'completed', 'delayed', 'cancelled'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->date('planned_date');
            $table->date('actual_date')->nullable();
            $table->date('delayed_date')->nullable();
            $table->integer('duration_days')->nullable();
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->decimal('budget_allocated', 15, 2)->nullable();
            $table->decimal('budget_spent', 15, 2)->nullable();
            $table->string('currency')->default('SAR');
            $table->json('deliverables')->nullable();
            $table->json('requirements')->nullable();
            $table->json('dependencies')->nullable();
            $table->json('risks')->nullable();
            $table->json('mitigation_plan')->nullable();
            $table->json('resources_required')->nullable();
            $table->json('resources_allocated')->nullable();
            $table->json('team_members')->nullable();
            $table->json('contractors_involved')->nullable();
            $table->json('suppliers_involved')->nullable();
            $table->json('quality_standards')->nullable();
            $table->json('safety_requirements')->nullable();
            $table->json('inspection_criteria')->nullable();
            $table->json('approval_process')->nullable();
            $table->json('documentation_required')->nullable();
            $table->json('supporting_documents')->nullable();
            $table->json('progress_reports')->nullable();
            $table->json('photos')->nullable();
            $table->json('videos')->nullable();
            $table->json('certificates')->nullable();
            $table->json('approvals')->nullable();
            $table->json('change_orders')->nullable();
            $table->json('issues_encountered')->nullable();
            $table->json('lessons_learned')->nullable();
            $table->json('next_steps')->nullable();
            $table->json('key_metrics')->nullable();
            $table->json('performance_indicators')->nullable();
            $table->boolean('is_critical')->default(false);
            $table->boolean('is_gate')->default(false);
            $table->boolean('requires_approval')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['developer_id', 'status']);
            $table->index(['project_id', 'planned_date']);
            $table->index(['phase_id']);
            $table->index(['milestone_type']);
            $table->index(['planned_date']);
            $table->index(['priority']);
            $table->index(['is_critical']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_milestones');
    }
};
