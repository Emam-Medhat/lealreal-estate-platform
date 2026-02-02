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
        Schema::create('defi_pools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('token_pair');
            $table->enum('type', ['liquidity', 'staking', 'lending', 'yield']);
            $table->decimal('total_liquidity', 20, 8)->default(0);
            $table->decimal('total_liquidity_usd', 20, 2)->default(0);
            $table->decimal('apy', 10, 2)->default(0);
            $table->decimal('volume_24h', 20, 2)->default(0);
            $table->decimal('fees_24h', 20, 2)->default(0);
            $table->decimal('min_deposit', 20, 8)->default(0);
            $table->decimal('withdraw_fee', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->string('protocol')->nullable(); // uniswap, curve, aave, etc.
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
            $table->index('apy');
        });

        Schema::create('defi_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('pool_id')->constrained('defi_pools')->onDelete('cascade');
            $table->decimal('amount', 20, 8);
            $table->decimal('shares', 20, 8);
            $table->decimal('earned_rewards', 20, 8)->default(0);
            $table->enum('status', ['active', 'withdrawn', 'pending'])->default('active');
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['pool_id', 'status']);
        });

        Schema::create('defi_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('pool_id')->constrained('defi_pools')->onDelete('cascade');
            $table->foreignId('position_id')->nullable()->constrained('defi_positions')->onDelete('cascade');
            $table->enum('type', ['deposit', 'withdraw', 'reward']);
            $table->decimal('amount', 20, 8);
            $table->decimal('fee', 20, 8)->default(0);
            $table->string('tx_hash')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'type']);
            $table->index(['pool_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('defi_transactions');
        Schema::dropIfExists('defi_positions');
        Schema::dropIfExists('defi_pools');
    }
};
