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
        if (!Schema::hasTable('emergency_repairs')) {
        Schema::create('emergency_repairs', function (Blueprint $table) {
            $table->id();
            $table->string('emergency_number')->unique();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('maintenance_request_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('work_order_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('maintenance_team_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('service_provider_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description');
            $table->text('description_ar')->nullable();
            $table->enum('emergency_type', ['water_leak', 'power_outage', 'gas_leak', 'fire', 'structural_damage', 'elevator_malfunction', 'security_breach', 'heating_failure', 'cooling_failure', 'plumbing_blockage', 'electrical_short', 'roof_leak', 'window_breakage', 'door_malfunction', 'other'])->default('other');
            $table->enum('severity', ['low', 'medium', 'high', 'critical', 'life_threatening'])->default('high');
            $table->enum('status', ['reported', 'dispatched', 'in_progress', 'resolved', 'closed', 'false_alarm'])->default('reported');
            $table->timestamp('reported_at');
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->integer('response_time_minutes')->nullable();
            $table->integer('resolution_time_minutes')->nullable();
            $table->string('reported_by');
            $table->string('reported_by_phone')->nullable();
            $table->string('reported_by_email')->nullable();
            $table->enum('contact_method', ['phone', 'email', 'app', 'website', 'in_person', 'other'])->default('phone');
            $table->string('location_description');
            $table->string('location_description_ar')->nullable();
            $table->json('affected_areas')->nullable();
            $table->json('safety_concerns')->nullable();
            $table->json('immediate_actions')->nullable();
            $table->json('temporary_solutions')->nullable();
            $table->json('permanent_solutions')->nullable();
            $table->json('materials_used')->nullable();
            $table->json('equipment_used')->nullable();
            $table->json('photos_before')->nullable();
            $table->json('photos_after')->nullable();
            $table->json('videos')->nullable();
            $table->json('documents')->nullable();
            $table->json('witnesses')->nullable();
            $table->json('authorizations')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->boolean('insurance_claim')->default(false);
            $table->string('claim_number')->nullable();
            $table->text('follow_up_required')->nullable();
            $table->text('follow_up_required_ar')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->json('prevention_measures')->nullable();
            $table->text('lessons_learned')->nullable();
            $table->text('lessons_learned_ar')->nullable();
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['emergency_number']);
            $table->index(['property_id']);
            $table->index(['maintenance_request_id']);
            $table->index(['work_order_id']);
            $table->index(['maintenance_team_id']);
            $table->index(['service_provider_id']);
            $table->index(['emergency_type']);
            $table->index(['severity']);
            $table->index(['status']);
            $table->index(['reported_at']);
            $table->index(['resolved_at']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_repairs');
    }
};
