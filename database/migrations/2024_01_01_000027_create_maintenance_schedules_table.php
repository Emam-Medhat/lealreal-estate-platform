<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('schedule_number')->unique();
            $table->string('title');
            $table->text('description');
            $table->enum('maintenance_type', ['inspection', 'cleaning', 'service', 'replacement', 'testing', 'repair', 'maintenance', 'other']);
            $table->datetime('scheduled_date');
            $table->integer('estimated_duration')->comment('in minutes');
            $table->enum('priority', ['low', 'medium', 'high', 'emergency']);
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled', 'rescheduled'])->default('scheduled');
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->foreignId('maintenance_team_id')->nullable();
            $table->foreignId('service_provider_id')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('assigned_at')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->datetime('rescheduled_at')->nullable();
            $table->foreignId('rescheduled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('reschedule_reason')->nullable();
            $table->datetime('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('completion_notes')->nullable();
            $table->json('attachments')->nullable();
            $table->foreignId('preventive_maintenance_id')->nullable();
            $table->foreignId('maintenance_request_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['status', 'scheduled_date']);
            $table->index(['property_id', 'scheduled_date']);
            $table->index(['maintenance_team_id', 'status']);
            $table->index(['service_provider_id', 'status']);
            $table->index('schedule_number');
            $table->index(['scheduled_date', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_schedules');
    }
};
