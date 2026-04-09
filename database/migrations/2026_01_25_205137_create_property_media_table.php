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
        if (!Schema::hasTable('property_media')) {
        Schema::create('property_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->enum('media_type', ['image', 'video', 'virtual_tour', 'floor_plan', 'document'])->default('image');
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('file_size');
            $table->string('file_type');
            $table->string('caption')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['property_id', 'media_type']);
            $table->index(['property_id', 'sort_order']);
            $table->index(['is_featured']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_media');
    }
};
