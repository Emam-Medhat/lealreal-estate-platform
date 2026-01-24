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
        Schema::create('tax_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_tax_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('tax_filing_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('payment_number')->unique();
            $table->decimal('amount', 12, 2);
            $table->decimal('penalty_amount', 12, 2)->default(0);
            $table->decimal('interest_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'credit_card', 'debit_card', 'online_payment', 'mobile_payment', 'installment', 'other'])->default('bank_transfer');
            $table->string('payment_reference')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('check_number')->nullable();
            $table->date('check_date')->nullable();
            $table->string('card_last_four')->nullable();
            $table->string('card_brand')->nullable();
            $table->string('mobile_provider')->nullable();
            $table->string('mobile_number')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded', 'partially_refunded'])->default('pending');
            $table->date('payment_date');
            $table->date('processed_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->date('failed_date')->nullable();
            $table->date('cancelled_date')->nullable();
            $table->date('refunded_date')->nullable();
            $table->text('payment_notes')->nullable();
            $table->text('payment_notes_ar')->nullable();
            $table->text('failure_reason')->nullable();
            $table->text('failure_reason_ar')->nullable();
            $table->text('refund_reason')->nullable();
            $table->text('refund_reason_ar')->nullable();
            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->string('refund_reference')->nullable();
            $table->json('payment_details')->nullable();
            $table->json('payment_breakdown')->nullable();
            $table->json('installment_details')->nullable();
            $table->json('receipt_details')->nullable();
            $table->json('confirmation_details')->nullable();
            $table->json('correspondence')->nullable();
            $table->json('documents')->nullable();
            $table->json('notes')->nullable();
            $table->json('notes_ar')->nullable();
            $table->boolean('auto_payment')->default(false);
            $table->string('auto_payment_token')->nullable();
            $table->date('auto_payment_next_date')->nullable();
            $table->json('auto_payment_details')->nullable();
            $table->boolean('recurring_payment')->default(false);
            $table->enum('recurring_frequency', ['weekly', 'bi_weekly', 'monthly', 'quarterly', 'semi_annually', 'annually'])->nullable();
            $table->date('recurring_next_date')->nullable();
            $table->date('recurring_end_date')->nullable();
            $table->json('recurring_details')->nullable();
            $table->boolean('installment_plan')->default(false);
            $table->integer('installment_number')->nullable();
            $table->integer('total_installments')->nullable();
            $table->date('next_installment_date')->nullable();
            $table->json('installment_schedule')->nullable();
            $table->boolean('receipt_generated')->default(false);
            $table->string('receipt_path')->nullable();
            $table->timestamp('receipt_generated_at')->nullable();
            $table->boolean('notification_sent')->default(false);
            $table->timestamp('notification_sent_at')->nullable();
            $table->json('payment_notifications')->nullable();
            $table->json('payment_reminders')->nullable();
            $table->json('audit_trail')->nullable();
            $table->json('compliance_checks')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('refunded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['payment_number']);
            $table->index(['property_tax_id']);
            $table->index(['tax_filing_id']);
            $table->index(['user_id']);
            $table->index(['status']);
            $table->index(['payment_method']);
            $table->index(['payment_date']);
            $table->index(['processed_date']);
            $table->index(['completed_date']);
            $table->index(['failed_date']);
            $table->index(['cancelled_date']);
            $table->index(['refunded_date']);
            $table->index(['processed_by']);
            $table->index(['completed_by']);
            $table->index(['cancelled_by']);
            $table->index(['refunded_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_payments');
    }
};
