<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('project_task_dependencies')) {
        Schema::create('project_task_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('project_tasks')->onDelete('cascade');
            $table->foreignId('dependency_task_id')->constrained('project_tasks')->onDelete('cascade');
            $table->enum('dependency_type', ['finish_to_start', 'start_to_start', 'finish_to_finish', 'start_to_finish'])->default('finish_to_start');
            $table->integer('lag_days')->default(0);
            $table->timestamps();

            $table->unique(['task_id', 'dependency_task_id']);
            $table->index(['task_id']);
            $table->index(['dependency_task_id']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('project_task_dependencies');
    }
};
