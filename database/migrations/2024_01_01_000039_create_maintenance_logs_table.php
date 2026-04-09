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
        if (!Schema::hasTable('maintenance_logs')) {
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_request_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('work_order_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('action_type', ['created', 'assigned', 'started', 'paused', 'resumed', 'completed', 'cancelled', 'note_added', 'status_changed', 'priority_changed', 'cost_updated', 'scheduled', 'rescheduled', 'inspection', 'material_added', 'material_used', 'photo_added', 'document_uploaded', 'customer_contacted', 'provider_contacted', 'other'])->default('other');
            $table->text('description');
            $table->text('description_ar')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('location')->nullable();
            $table->string('location_ar')->nullable();
            $table->decimal('duration', 5, 2)->nullable(); // in minutes
            $table->decimal('cost', 10, 2)->nullable();
            $table->json('materials_used')->nullable();
            $table->json('tools_used')->nullable();
            $table->json('photos')->nullable();
            $table->json('documents')->nullable();
            $table->string('weather_condition')->nullable();
            $table->integer('temperature')->nullable();
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable();
            $table->boolean('is_billable')->default(true);
            $table->string('reference_number')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['maintenance_request_id']);
            $table->index(['work_order_id']);
            $table->index(['property_id']);
            $table->index(['user_id']);
            $table->index(['action_type']);
            $table->index(['created_at']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};
