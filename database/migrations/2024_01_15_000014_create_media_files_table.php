<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type'); // image, video, document, audio
            $table->string('mime_type');
            $table->decimal('file_size', 10, 2); // in MB
            $table->json('dimensions')->nullable(); // width, height for images
            $table->integer('duration')->nullable(); // for videos/audio in seconds
            $table->string('alt_text')->nullable();
            $table->text('description')->nullable();
            $table->json('tags')->nullable();
            $table->string('category')->nullable();
            $table->boolean('is_public')->default(false);
            $table->integer('download_count')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('media_files');
    }
};
