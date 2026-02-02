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
    }
};
