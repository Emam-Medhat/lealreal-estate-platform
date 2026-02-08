<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gamification_user_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('challenge_id');
            $table->string('status')->default('active');
            $table->integer('progress')->default(0);
            $table->timestamp('joined_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'challenge_id']);
            $table->index(['user_id']);
            $table->index(['challenge_id']);
            $table->index(['status']);
            $table->index(['joined_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gamification_user_challenges');
    }
};
