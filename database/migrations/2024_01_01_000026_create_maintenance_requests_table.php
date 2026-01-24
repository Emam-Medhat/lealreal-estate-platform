<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('request_number')->unique();
            $table->string('title');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'emergency']);
            $table->enum('category', ['plumbing', 'electrical', 'hvac', 'structural', 'general', 'cosmetic', 'safety', 'other']);
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->datetime('due_date')->nullable();
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_team_id')->nullable();
            $table->foreignId('service_provider_id')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('assigned_at')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->datetime('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->foreignId('emergency_repair_id')->nullable();
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index(['property_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['service_provider_id', 'status']);
            $table->index('due_date');
            $table->index('request_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_requests');
    }
};
