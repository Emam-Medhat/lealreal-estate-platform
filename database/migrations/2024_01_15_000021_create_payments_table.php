<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('transaction_id')->unique();
            $table->decimal('amount', 15, 2);
            $table->string('currency')->default('SAR');
            $table->enum('payment_method', ['credit_card', 'bank_transfer', 'wallet', 'crypto', 'cash']);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded', 'cancelled'])->default('pending');
            $table->enum('type', ['payment', 'refund', 'commission', 'fee', 'deposit']);
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // gateway response, etc.
            $table->string('gateway')->nullable(); // stripe, paypal, etc.
            $table->string('gateway_transaction_id')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->decimal('refunded_amount', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
