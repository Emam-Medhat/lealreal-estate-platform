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
        if (!Schema::hasTable('yield_farms')) {
        Schema::create('yield_farms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address', 42)->unique();
            $table->string('token_a_address', 42);
            $table->string('token_a_symbol');
            $table->string('token_b_address', 42);
            $table->string('token_b_symbol');
            $table->decimal('reserve_a', 30, 18)->default(0);
            $table->decimal('reserve_b', 30, 18)->default(0);
            $table->decimal('total_liquidity', 30, 18)->default(0);
            $table->decimal('lp_token_supply', 30, 18)->default(0);
            $table->decimal('apy', 8, 4);
            $table->decimal('fee', 8, 4)->default(0.003);
            $table->string('reward_token_address', 42)->nullable();
            $table->decimal('reward_rate', 8, 4)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('lp_holders_count')->default(0);
            $table->decimal('min_liquidity', 30, 18)->default(0);
            $table->decimal('max_liquidity', 30, 18)->nullable();
            $table->json('reward_schedule')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('address');
            $table->index('token_a_address');
            $table->index('token_b_address');
            $table->index('reward_token_address');
            $table->index('is_active');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yield_farms');
    }
};
