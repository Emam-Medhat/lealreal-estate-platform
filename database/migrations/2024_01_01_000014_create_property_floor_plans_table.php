<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('property_floor_plans')) {
        Schema::create('property_floor_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('thumbnail_path')->nullable();
            $table->decimal('area', 10, 2);
            $table->string('area_unit');
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->json('rooms')->nullable(); // room dimensions, names
            $table->json('dimensions')->nullable(); // width, length
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('property_floor_plans');
    }
};
