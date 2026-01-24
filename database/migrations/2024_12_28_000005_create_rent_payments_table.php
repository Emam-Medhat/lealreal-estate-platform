<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rent_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained()->onDelete('cascade');
            $table->string('payment_number')->unique();
            $table->integer('payment_number_sequence');
            $table->date('due_date');
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('late_fee', 10, 2)->default(0);
            $table->datetime('payment_date')->nullable();
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'credit_card', 'online', 'other'])->nullable();
            $table->enum('status', ['pending', 'paid', 'overdue', 'partial'])->default('pending');
            $table->string('transaction_id')->nullable();
            $table->string('receipt_number')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('auto_processed')->default(false);
            $table->boolean('reminder_sent')->default(false);
            $table->boolean('late_fee_applied')->default(false);
            $table->boolean('partial_payment')->default(false);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['lease_id', 'due_date']);
            $table->index(['status', 'due_date']);
            $table->index(['payment_date']);
            $table->index(['lease_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rent_payments');
    }
};
