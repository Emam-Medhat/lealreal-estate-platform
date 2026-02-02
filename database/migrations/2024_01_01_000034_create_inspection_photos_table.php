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
        if (!Schema::hasTable('inspection_photos')) {
        Schema::create('inspection_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('photo_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('title')->nullable();
            $table->string('title_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->enum('photo_type', ['exterior', 'interior', 'structural', 'electrical', 'plumbing', 'hvac', 'roof', 'foundation', 'defect', 'overall', 'detail', 'before', 'after', 'other']);
            $table->string('location')->nullable();
            $table->string('location_ar')->nullable();
            $table->json('tags')->nullable();
            $table->json('metadata')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('taken_at')->nullable();
            $table->string('camera_device')->nullable();
            $table->string('camera_settings')->nullable();
            $table->integer('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_public')->default(true);
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['inspection_id', 'photo_type']);
            $table->index(['property_id']);
            $table->index(['is_primary']);
            $table->index(['taken_at']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_photos');
    }
};
