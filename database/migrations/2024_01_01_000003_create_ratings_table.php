<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('ratings')) {
            if (!Schema::hasTable('ratings')) {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('ratingable');
            $table->integer('rating');
            $table->string('category')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'ratingable_type', 'ratingable_id']);
            $table->index('rating');
            $table->index('category');
            $table->unique(['user_id', 'ratingable_type', 'ratingable_id', 'category'], 'ratings_unique');
        });
        }
        }
    }

    public function down()
    {
        Schema::dropIfExists('ratings');
    }
};
