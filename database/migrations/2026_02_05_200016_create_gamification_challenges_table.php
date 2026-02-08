<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gamification_challenges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('type');
            $table->string('difficulty');
            $table->json('requirements');
            $table->integer('reward_points');
            $table->foreignId('reward_badge_id')->nullable()->constrained('gamification_badges');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->integer('max_participants')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type']);
            $table->index(['difficulty']);
            $table->index(['is_active']);
            $table->index(['start_date']);
            $table->index(['end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gamification_challenges');
    }
};
