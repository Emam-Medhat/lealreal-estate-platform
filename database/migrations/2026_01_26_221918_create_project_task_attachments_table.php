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
        if (!Schema::hasTable('project_task_attachments')) {
        Schema::create('project_task_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('uploaded_by');
            $table->string('file_name'); // Original file name
            $table->string('file_path'); // Stored file path
            $table->string('file_type'); // MIME type
            $table->integer('file_size'); // File size in bytes
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('project_tasks')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('cascade');

            $table->index(['task_id', 'created_at']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_task_attachments');
    }
};
