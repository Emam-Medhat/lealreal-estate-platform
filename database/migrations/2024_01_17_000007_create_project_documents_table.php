<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('project_documents', function (Blueprint $table) {
            $table->id();
            $table->string('documentable_type');
            $table->unsignedBigInteger('documentable_id');
            $table->string('filename');
            $table->string('original_filename');
            $table->string('path');
            $table->integer('size');
            $table->string('mime_type');
            $table->text('description')->nullable();
            $table->integer('version')->default(1);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['documentable_type', 'documentable_id']);
            $table->index(['uploaded_by', 'status']);
            $table->index(['mime_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_documents');
    }
};
