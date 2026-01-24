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
        Schema::create('staking_pools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address', 42)->unique();
            $table->string('token_address', 42);
            $table->string('token_symbol');
            $table->string('reward_token_address', 42)->nullable();
            $table->string('reward_token_symbol')->nullable();
            $table->decimal('total_staked', 30, 18)->default(0);
            $table->decimal('total_rewards', 30, 18)->default(0);
            $table->decimal('apy', 8, 4);
            $table->decimal('reward_rate', 8, 4)->nullable();
            $table->integer('lock_period_days')->default(0);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('stakers_count')->default(0);
            $table->decimal('min_stake_amount', 30, 18)->default(0);
            $table->decimal('max_stake_amount', 30, 18)->nullable();
            $table->decimal('early_unstake_penalty', 8, 4)->default(0);
            $table->json('reward_schedule')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('address');
            $table->index('token_address');
            $table->index('reward_token_address');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staking_pools');
    }
};
