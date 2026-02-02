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
        if (!Schema::hasTable('project_task_time_logs')) {
        Schema::create('project_task_time_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('hours', 5, 2);
            $table->text('description')->nullable();
            $table->date('log_date');
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('project_tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['task_id', 'log_date']);
            $table->index(['user_id', 'log_date']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_task_time_logs');
    }
};
