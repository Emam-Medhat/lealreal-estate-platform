<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('lease_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('inspection_number')->unique();
            $table->enum('inspection_type', ['move_in', 'move_out', 'routine', 'emergency', 'pre_renewal']);
            $table->datetime('scheduled_date');
            $table->datetime('completed_date')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled', 'rescheduled'])->default('scheduled');
            $table->foreignId('inspector_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('inspection_notes')->nullable();
            $table->json('checklist_items')->nullable();
            $table->enum('overall_condition', ['excellent', 'good', 'fair', 'poor', 'damaged'])->nullable();
            $table->decimal('estimated_damages', 10, 2)->default(0);
            $table->json('photos')->nullable();
            $table->json('videos')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('tenant_comments')->nullable();
            $table->boolean('tenant_present')->default(false);
            $table->boolean('requires_follow_up')->default(false);
            $table->datetime('follow_up_date')->nullable();
            $table->text('follow_up_notes')->nullable();
            $table->json('documents')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['lease_id', 'inspection_type']);
            $table->index(['property_id', 'status']);
            $table->index(['tenant_id', 'status']);
            $table->index(['status', 'scheduled_date']);
            $table->index(['inspector_id']);
            $table->index(['completed_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_inspections');
    }
};
