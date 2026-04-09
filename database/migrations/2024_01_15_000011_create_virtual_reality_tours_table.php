<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('virtual_reality_tours')) {
        Schema::create('virtual_reality_tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('tour_type'); // 360, vr, ar, 3d_walkthrough
            $table->string('file_path');
            $table->string('thumbnail_path')->nullable();
            $table->decimal('file_size', 10, 2); // in MB
            $table->integer('duration_seconds')->nullable(); // tour duration
            $table->json('hotspots')->nullable(); // interactive points in tour
            $table->integer('view_count')->default(0);
            $table->decimal('rating', 3, 2)->default(0); // average rating
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('virtual_reality_tours');
    }
};
