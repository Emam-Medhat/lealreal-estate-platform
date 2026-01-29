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
        Schema::table('user_wallets', function (Blueprint $table) {
            // Add missing columns that the model expects
            $table->decimal('pending_balance', 15, 2)->default(0)->after('frozen_balance');
            $table->decimal('total_deposited', 15, 2)->default(0)->after('pending_balance');
            $table->decimal('total_withdrawn', 15, 2)->default(0)->after('total_deposited');
            $table->decimal('total_spent', 15, 2)->default(0)->after('total_withdrawn');
            $table->decimal('total_earned', 15, 2)->default(0)->after('total_spent');
            $table->enum('wallet_type', ['fiat', 'crypto', 'mixed'])->default('fiat')->after('total_earned');
            $table->boolean('is_active')->default(true)->after('wallet_type');
            $table->boolean('is_verified')->default(false)->after('is_active');
            $table->integer('verification_level')->default(0)->after('is_verified');
            $table->decimal('daily_limit', 15, 2)->default(10000)->after('verification_level');
            $table->decimal('monthly_limit', 15, 2)->default(100000)->after('daily_limit');
            $table->integer('transaction_count')->default(0)->after('monthly_limit');
            $table->timestamp('last_transaction_at')->nullable()->after('transaction_count');
            $table->string('wallet_address')->nullable()->after('last_transaction_at');
            $table->string('blockchain_network')->nullable()->after('wallet_address');
            $table->text('private_key_encrypted')->nullable()->after('blockchain_network');
            $table->text('public_key')->nullable()->after('private_key_encrypted');
            $table->text('mnemonic_encrypted')->nullable()->after('public_key');
            $table->json('metadata')->nullable()->after('mnemonic_encrypted');
            
            // Change status column to match model expectations
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_wallets', function (Blueprint $table) {
            // Drop the added columns
            $table->dropColumn([
                'pending_balance',
                'total_deposited',
                'total_withdrawn',
                'total_spent',
                'total_earned',
                'wallet_type',
                'is_active',
                'is_verified',
                'verification_level',
                'daily_limit',
                'monthly_limit',
                'transaction_count',
                'last_transaction_at',
                'wallet_address',
                'blockchain_network',
                'private_key_encrypted',
                'public_key',
                'mnemonic_encrypted',
                'metadata'
            ]);
            
            // Add back the original status column
            $table->enum('status', ['active', 'frozen', 'suspended'])->default('active');
        });
    }
};
