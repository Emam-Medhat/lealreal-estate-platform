<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->string('category')->nullable();
            $table->string('difficulty')->default('beginner'); // beginner, intermediate, advanced
            $table->integer('reading_time')->default(0); // in minutes
            $table->integer('views')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->json('seo_data')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'difficulty']);
            $table->index(['author_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('guides');
    }
};
