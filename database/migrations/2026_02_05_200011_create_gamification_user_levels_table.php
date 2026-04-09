<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gamification_user_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('level')->default(1);
            $table->bigInteger('total_points')->default(0);
            $table->bigInteger('current_points')->default(0);
            $table->timestamp('leveled_up_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id']);
            $table->index(['level']);
            $table->index(['total_points']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gamification_user_levels');
    }
};
