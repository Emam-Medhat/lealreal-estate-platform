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
        if (!Schema::hasTable('project_task_checklists')) {
        Schema::create('project_task_checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('project_tasks')->onDelete('cascade');
            $table->foreign('completed_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['task_id', 'is_completed']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_task_checklists');
    }
};
