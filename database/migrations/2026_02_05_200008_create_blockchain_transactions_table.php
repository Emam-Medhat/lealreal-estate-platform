<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blockchain_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('contract_address', 42);
            $table->string('function');
            $table->json('parameters');
            $table->string('tx_hash', 66)->unique();
            $table->string('network');
            $table->bigInteger('gas_used')->nullable();
            $table->decimal('cost', 15, 8)->nullable();
            $table->string('status')->default('pending');
            $table->json('result')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->index(['contract_address']);
            $table->index(['tx_hash']);
            $table->index(['network']);
            $table->index(['status']);
            $table->index(['executed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blockchain_transactions');
    }
};
