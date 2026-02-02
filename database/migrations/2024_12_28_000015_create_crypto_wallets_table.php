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
        if (!Schema::hasTable('crypto_wallets')) {
        Schema::create('crypto_wallets', function (Blueprint $table) {
            $table->id();
            $table->string('address', 42)->unique();
            $table->string('name');
            $table->string('private_key')->nullable();
            $table->text('mnemonic')->nullable();
            $table->enum('type', ['main', 'secondary', 'cold', 'hot'])->default('main');
            $table->enum('blockchain', ['ethereum', 'polygon', 'bsc', 'arbitrum'])->default('ethereum');
            $table->decimal('balance', 30, 18)->default(0);
            $table->decimal('balance_usd', 20, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_activity')->nullable();
            $table->integer('transaction_count')->default(0);
            $table->json('tokens')->nullable();
            $table->json('settings')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->string('encryption_method')->nullable();
            $table->timestamp('backed_up_at')->nullable();
            $table->enum('status', ['active', 'inactive', 'locked', 'compromised'])->default('active');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('address');
            $table->index('user_id');
            $table->index('type');
            $table->index('blockchain');
            $table->index('status');
            $table->index('is_active');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crypto_wallets');
    }
};
