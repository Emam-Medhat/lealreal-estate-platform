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
        if (!Schema::hasTable('work_orders')) {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('work_order_number')->unique();
            $table->foreignId('maintenance_request_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description');
            $table->text('description_ar')->nullable();
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'paused', 'completed', 'cancelled'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'emergency'])->default('medium');
            $table->enum('type', ['repair', 'maintenance', 'installation', 'inspection', 'replacement', 'other'])->default('repair');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_team_id')->nullable()->constrained('maintenance_teams')->onDelete('set null');
            $table->foreignId('service_provider_id')->nullable()->constrained('service_providers')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('resumed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->time('scheduled_time')->nullable();
            $table->integer('estimated_duration')->nullable(); // in minutes
            $table->integer('actual_duration')->nullable(); // in minutes
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->decimal('labor_cost', 10, 2)->nullable();
            $table->decimal('material_cost', 10, 2)->nullable();
            $table->decimal('other_cost', 10, 2)->nullable();
            $table->string('location')->nullable();
            $table->string('location_ar')->nullable();
            $table->text('access_instructions')->nullable();
            $table->text('access_instructions_ar')->nullable();
            $table->text('safety_requirements')->nullable();
            $table->text('safety_requirements_ar')->nullable();
            $table->text('completion_notes')->nullable();
            $table->text('completion_notes_ar')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('cancellation_reason_ar')->nullable();
            $table->json('materials_used')->nullable();
            $table->json('tools_used')->nullable();
            $table->json('photos_before')->nullable();
            $table->json('photos_after')->nullable();
            $table->json('attachments')->nullable();
            $table->json('checklist')->nullable();
            $table->boolean('requires_permit')->default(false);
            $table->string('permit_number')->nullable();
            $table->date('permit_issued_date')->nullable();
            $table->date('permit_expiry_date')->nullable();
            $table->boolean('customer_approval_required')->default(false);
            $table->timestamp('customer_approved_at')->nullable();
            $table->string('customer_approval_notes')->nullable();
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable();
            $table->timestamps();
            
            $table->index(['work_order_number']);
            $table->index(['status']);
            $table->index(['priority']);
            $table->index(['type']);
            $table->index(['assigned_to']);
            $table->index(['assigned_team_id']);
            $table->index(['service_provider_id']);
            $table->index(['scheduled_date']);
            $table->index(['created_by']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
