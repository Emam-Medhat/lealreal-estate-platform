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
        if (!Schema::hasTable('project_task_dependencies')) {
        Schema::create('project_task_dependencies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('dependency_task_id');
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('project_tasks')->onDelete('cascade');
            $table->foreign('dependency_task_id')->references('id')->on('project_tasks')->onDelete('cascade');

            $table->unique(['task_id', 'dependency_task_id']);
            $table->index(['task_id']);
            $table->index(['dependency_task_id']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_task_dependencies');
    }
};
