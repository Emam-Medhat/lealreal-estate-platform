<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('project_tasks')) {
        Schema::create('project_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('phase_id')->nullable()->constrained('project_phases')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('priority', ['low', 'medium', 'high', 'critical']);
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'on_hold', 'cancelled']);
            $table->date('start_date');
            $table->date('due_date');
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->default(0);
            $table->integer('progress_percentage')->default(0);
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'phase_id']);
            $table->index(['assignee_id', 'status']);
            $table->index(['priority', 'due_date']);
            $table->index(['status', 'progress_percentage']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('project_tasks');
    }
};
