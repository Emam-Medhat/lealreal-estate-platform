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
        if (!Schema::hasTable('preventive_maintenance')) {
        Schema::create('preventive_maintenance', function (Blueprint $table) {
            $table->id();
            $table->string('pm_number')->unique();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('maintenance_team_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('service_provider_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description');
            $table->text('description_ar')->nullable();
            $table->enum('category', ['hvac', 'plumbing', 'electrical', 'structural', 'safety', 'fire', 'elevator', 'pool', 'landscaping', 'pest_control', 'cleaning', 'security', 'other'])->default('other');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'semi_annually', 'annually', 'biennially', 'custom'])->default('monthly');
            $table->integer('frequency_interval')->default(1);
            $table->enum('status', ['active', 'inactive', 'suspended', 'completed', 'overdue'])->default('active');
            $table->date('start_date');
            $table->date('last_performed')->nullable();
            $table->date('next_due')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('estimated_duration')->nullable(); // in minutes
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->json('checklist')->nullable();
            $table->json('materials_needed')->nullable();
            $table->json('tools_needed')->nullable();
            $table->json('safety_requirements')->nullable();
            $table->json('access_requirements')->nullable();
            $table->json('performance_metrics')->nullable();
            $table->json('maintenance_history')->nullable();
            $table->json('photos')->nullable();
            $table->json('documents')->nullable();
            $table->json('notifications')->nullable();
            $table->boolean('auto_schedule')->default(true);
            $table->integer('advance_notice_days')->default(7);
            $table->boolean('requires_permit')->default(false);
            $table->string('permit_number')->nullable();
            $table->date('permit_expiry')->nullable();
            $table->boolean('is_critical')->default(false);
            $table->text('impact_description')->nullable();
            $table->text('impact_description_ar')->nullable();
            $table->json('compliance_requirements')->nullable();
            $table->json('regulatory_references')->nullable();
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['pm_number']);
            $table->index(['property_id']);
            $table->index(['maintenance_team_id']);
            $table->index(['service_provider_id']);
            $table->index(['category']);
            $table->index(['priority']);
            $table->index(['frequency']);
            $table->index(['status']);
            $table->index(['next_due']);
            $table->index(['is_critical']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preventive_maintenance');
    }
};
