<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('project_milestones')) {
        Schema::create('project_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('phase_id')->nullable()->constrained('project_phases')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('due_date');
            $table->enum('priority', ['low', 'medium', 'high', 'critical']);
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'overdue']);
            $table->integer('completion_percentage')->default(0);
            $table->json('deliverables')->nullable();
            $table->text('completion_notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'phase_id']);
            $table->index(['status', 'due_date']);
            $table->index(['priority']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('project_milestones');
    }
};
