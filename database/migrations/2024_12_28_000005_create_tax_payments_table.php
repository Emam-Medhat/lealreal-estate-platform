<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tax_payments')) {
        Schema::create('tax_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_tax_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('tax_filing_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method'); // cash, bank_transfer, credit_card, online
            $table->date('payment_date');
            $table->string('status')->default('pending'); // pending, processing, completed, cancelled
            $table->string('transaction_id')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('confirmation_number')->nullable();
            $table->decimal('processing_fee', 8, 2)->nullable();
            $table->string('receipt_path')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['property_tax_id', 'status']);
            $table->index(['tax_filing_id', 'status']);
            $table->index('payment_date');
            $table->index('transaction_id');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_payments');
    }
};
