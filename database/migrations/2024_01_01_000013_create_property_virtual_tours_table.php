<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('property_virtual_tours')) {
        Schema::create('property_virtual_tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('tour_type'); // 360, video, interactive
            $table->string('file_path');
            $table->string('thumbnail_path')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->json('tour_data')->nullable(); // waypoints, hotspots, etc.
            $table->json('settings')->nullable();
            $table->boolean('is_public')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('property_virtual_tours');
    }
};
