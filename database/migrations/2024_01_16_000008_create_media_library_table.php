<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('media_library')) {
        Schema::create('media_library', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('original_name');
            $table->string('mime_type');
            $table->string('file_path');
            $table->string('file_size');
            $table->string('dimensions')->nullable(); // for images: widthxheight
            $table->enum('type', ['image', 'video', 'document', 'audio', 'other'])->default('other');
            $table->string('alt_text')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // additional file info
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['type', 'uploaded_by']);
            $table->index(['mime_type']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('media_library');
    }
};
