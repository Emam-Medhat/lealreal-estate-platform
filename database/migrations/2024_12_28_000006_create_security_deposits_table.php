<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('security_deposits')) {
        Schema::create('security_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('deposit_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->decimal('received_amount', 10, 2)->default(0);
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->date('due_date');
            $table->datetime('received_date')->nullable();
            $table->datetime('refund_date')->nullable();
            $table->enum('status', ['pending', 'received', 'refunded', 'partially_refunded'])->default('pending');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'credit_card', 'online', 'other'])->nullable();
            $table->enum('refund_method', ['cash', 'bank_transfer', 'check', 'credit_card', 'online', 'other'])->nullable();
            $table->decimal('deductions', 10, 2)->default(0);
            $table->json('deduction_reasons')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('receipt_number')->nullable();
            $table->string('refund_receipt_number')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['lease_id', 'status']);
            $table->index(['tenant_id', 'status']);
            $table->index(['status', 'due_date']);
            $table->index(['received_date']);
            $table->index(['refund_date']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('security_deposits');
    }
};
