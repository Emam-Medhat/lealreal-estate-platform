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
        if (!Schema::hasTable('crypto_transactions')) {
        Schema::create('crypto_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('hash', 66)->unique();
            $table->string('from_address', 42);
            $table->string('to_address', 42);
            $table->string('contract_address', 42)->nullable();
            $table->decimal('amount', 30, 18);
            $table->string('currency', 10)->default('ETH');
            $table->decimal('gas_price', 20, 8)->nullable();
            $table->integer('gas_limit')->nullable();
            $table->integer('gas_used')->nullable();
            $table->decimal('gas_fee', 20, 8)->nullable();
            $table->string('nonce')->nullable();
            $table->string('block_hash', 64)->nullable();
            $table->integer('block_number')->nullable();
            $table->integer('transaction_index')->nullable();
            $table->string('input_data')->nullable();
            $table->json('logs')->nullable();
            $table->enum('type', ['transfer', 'contract_call', 'contract_deployment', 'token_transfer'])->default('transfer');
            $table->enum('status', ['pending', 'confirmed', 'failed', 'replaced'])->default('pending');
            $table->timestamp('confirmed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('confirmations')->default(0);
            $table->json('metadata')->nullable();
            $table->foreignId('wallet_id')->nullable()->constrained('crypto_wallets')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('hash');
            $table->index('from_address');
            $table->index('to_address');
            $table->index('contract_address');
            $table->index('status');
            $table->index('type');
            $table->index('block_number');
            $table->index('wallet_id');
            $table->index('user_id');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crypto_transactions');
    }
};
