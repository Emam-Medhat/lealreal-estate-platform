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
        if (!Schema::hasTable('project_documents')) {
        Schema::create('project_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('uploaded_by');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path'); // Path to stored file
            $table->string('file_name'); // Original file name
            $table->string('file_type'); // MIME type
            $table->integer('file_size'); // File size in bytes
            $table->enum('category', ['contract', 'plan', 'report', 'invoice', 'permit', 'photo', 'other'])->default('other');
            $table->enum('status', ['draft', 'pending_review', 'approved', 'rejected'])->default('draft');
            $table->date('document_date')->nullable(); // Date of the document
            $table->date('expiry_date')->nullable(); // Expiry date if applicable
            $table->json('tags')->nullable(); // Array of tags
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('cascade');

            $table->index(['project_id', 'category']);
            $table->index(['project_id', 'status']);
            $table->index(['uploaded_by']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_documents');
    }
};
