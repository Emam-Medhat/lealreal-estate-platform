<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gamification_user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('achievement_key');
            $table->integer('points_awarded');
            $table->timestamp('awarded_at');
            $table->timestamps();

            $table->unique(['user_id', 'achievement_key']);
            $table->index(['user_id']);
            $table->index(['achievement_key']);
            $table->index(['awarded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gamification_user_achievements');
    }
};
