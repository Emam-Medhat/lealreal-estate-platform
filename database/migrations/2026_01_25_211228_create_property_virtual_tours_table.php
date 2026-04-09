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
        if (!Schema::hasTable('property_virtual_tours')) {
        Schema::create('property_virtual_tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('tour_url')->nullable(); // External tour URL
            $table->text('embed_code')->nullable(); // HTML embed code
            $table->string('tour_type')->default('360'); // 360, video, matterport, etc.
            $table->text('description')->nullable();
            $table->integer('duration_seconds')->nullable(); // For video tours
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['property_id', 'tour_type']);
            $table->index(['is_featured']);
            $table->index(['sort_order']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_virtual_tours');
    }
};
