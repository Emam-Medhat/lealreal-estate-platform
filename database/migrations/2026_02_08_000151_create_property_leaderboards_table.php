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
        Schema::create('property_leaderboards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('points');
            $table->string('period')->default('all_time');
            $table->string('category')->default('general');
            $table->bigInteger('score')->default(0);
            $table->integer('rank')->nullable();
            $table->integer('previous_rank')->nullable();
            $table->integer('change')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'period', 'category']);
            $table->index(['user_id', 'type', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_leaderboards');
    }
};
