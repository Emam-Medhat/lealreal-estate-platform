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
        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->onDelete('cascade');
            $table->string('filename');
            $table->string('original_name')->nullable();
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->string('path');
            $table->string('url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('message_id');
            $table->index('mime_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_attachments');
    }
};
