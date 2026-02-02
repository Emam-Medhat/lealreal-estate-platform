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
        if (!Schema::hasTable('vat_records')) {
        Schema::create('vat_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('record_number')->unique();
            $table->enum('record_type', ['sales', 'purchases', 'returns', 'adjustments', 'refunds', 'corrections'])->default('sales');
            $table->enum('period_type', ['monthly', 'quarterly', 'annually'])->default('monthly');
            $table->date('period_start');
            $table->date('period_end');
            $table->string('period_description');
            $table->string('period_description_ar')->nullable();
            $table->decimal('taxable_sales', 12, 2)->default(0);
            $table->decimal('taxable_purchases', 12, 2)->default(0);
            $table->decimal('exempt_sales', 12, 2)->default(0);
            $table->decimal('exempt_purchases', 12, 2)->default(0);
            $table->decimal('zero_rated_sales', 12, 2)->default(0);
            $table->decimal('zero_rated_purchases', 12, 2)->default(0);
            $table->decimal('vat_rate', 5, 2)->default(0);
            $table->decimal('vat_collected', 12, 2)->default(0);
            $table->decimal('vat_paid', 12, 2)->default(0);
            $table->decimal('vat_refunded', 12, 2)->default(0);
            $table->decimal('vat_adjustment', 12, 2)->default(0);
            $table->decimal('net_vat_liability', 12, 2)->default(0);
            $table->decimal('vat_payment', 12, 2)->default(0);
            $table->decimal('vat_refund', 12, 2)->default(0);
            $table->decimal('vat_balance', 12, 2)->default(0);
            $table->decimal('total_sales', 12, 2)->default(0);
            $table->decimal('total_purchases', 12, 2)->default(0);
            $table->decimal('total_vat_sales', 12, 2)->default(0);
            $table->decimal('total_vat_purchases', 12, 2)->default(0);
            $table->decimal('gross_sales', 12, 2)->default(0);
            $table->decimal('gross_purchases', 12, 2)->default(0);
            $table->decimal('net_sales', 12, 2)->default(0);
            $table->decimal('net_purchases', 12, 2)->default(0);
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'paid', 'refunded', 'cancelled'])->default('draft');
            $table->date('submission_date')->nullable();
            $table->date('review_date')->nullable();
            $table->date('approval_date')->nullable();
            $table->date('rejection_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->date('refund_date')->nullable();
            $table->date('cancellation_date')->nullable();
            $table->json('sales_details')->nullable();
            $table->json('purchase_details')->nullable();
            $table->json('exempt_sales_details')->nullable();
            $table->json('exempt_purchases_details')->nullable();
            $table->json('zero_rated_sales_details')->nullable();
            $table->json('zero_rated_purchases_details')->nullable();
            $table->json('vat_collected_details')->nullable();
            $table->json('vat_paid_details')->nullable();
            $table->json('vat_refunded_details')->nullable();
            $table->json('vat_adjustment_details')->nullable();
            $table->json('calculation_details')->nullable();
            $table->json('breakdown_details')->nullable();
            $table->json('supporting_documents')->nullable();
            $table->json('sales_documents')->nullable();
            $table->json('purchase_documents')->nullable();
            $table->json('tax_documents')->nullable();
            $table->json('financial_documents')->nullable();
            $table->json('correspondence')->nullable();
            $table->json('notes')->nullable();
            $table->json('notes_ar')->nullable();
            $table->text('submission_notes')->nullable();
            $table->text('submission_notes_ar')->nullable();
            $table->text('review_notes')->nullable();
            $table->text('review_notes_ar')->nullable();
            $table->text('approval_notes')->nullable();
            $table->text('approval_notes_ar')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('rejection_reason_ar')->nullable();
            $table->text('payment_notes')->nullable();
            $table->text('payment_notes_ar')->nullable();
            $table->text('refund_notes')->nullable();
            $table->text('refund_notes_ar')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('cancellation_reason_ar')->nullable();
            $table->boolean('electronically_signed')->default(false);
            $table->timestamp('signed_at')->nullable();
            $table->string('digital_signature')->nullable();
            $table->boolean('auto_calculated')->default(false);
            $table->timestamp('auto_calculated_at')->nullable();
            $table->boolean('auto_submitted')->default(false);
            $table->timestamp('auto_submitted_at')->nullable();
            $table->boolean('auto_payment')->default(false);
            $table->string('auto_payment_token')->nullable();
            $table->date('auto_payment_next_date')->nullable();
            $table->json('auto_payment_details')->nullable();
            $table->boolean('notification_sent')->default(false);
            $table->timestamp('notification_sent_at')->nullable();
            $table->json('payment_notifications')->nullable();
            $table->json('payment_reminders')->nullable();
            $table->json('audit_trail')->nullable();
            $table->json('compliance_checks')->nullable();
            $table->json('validation_checks')->nullable();
            $table->json('reconciliation_details')->nullable();
            $table->json('adjustment_history')->nullable();
            $table->json('correction_history')->nullable();
            $table->boolean('appeal_filed')->default(false);
            $table->date('appeal_date')->nullable();
            $table->text('appeal_reason')->nullable();
            $table->text('appeal_reason_ar')->nullable();
            $table->enum('appeal_status', ['pending', 'approved', 'rejected', 'withdrawn'])->nullable();
            $table->date('appeal_decision_date')->nullable();
            $table->text('appeal_decision')->nullable();
            $table->text('appeal_decision_ar')->nullable();
            $table->json('appeal_documents')->nullable();
            $table->boolean('audit_filed')->default(false);
            $table->date('audit_date')->nullable();
            $table->enum('audit_status', ['pending', 'in_progress', 'completed', 'closed'])->nullable();
            $table->date('audit_completion_date')->nullable();
            $table->text('audit_findings')->nullable();
            $table->text('audit_findings_ar')->nullable();
            $table->json('audit_documents')->nullable();
            $table->json('audit_details')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('paid_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('refunded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['record_number']);
            $table->index(['user_id']);
            $table->index(['property_id']);
            $table->index(['record_type']);
            $table->index(['period_type']);
            $table->index(['period_start']);
            $table->index(['period_end']);
            $table->index(['status']);
            $table->index(['submission_date']);
            $table->index(['review_date']);
            $table->index(['approval_date']);
            $table->index(['rejection_date']);
            $table->index(['payment_date']);
            $table->index(['refund_date']);
            $table->index(['cancellation_date']);
            $table->index(['electronically_signed']);
            $table->index(['auto_calculated']);
            $table->index(['auto_submitted']);
            $table->index(['auto_payment']);
            $table->index(['notification_sent']);
            $table->index(['appeal_status']);
            $table->index(['audit_status']);
            $table->index(['submitted_by']);
            $table->index(['reviewed_by']);
            $table->index(['approved_by']);
            $table->index(['rejected_by']);
            $table->index(['paid_by']);
            $table->index(['refunded_by']);
            $table->index(['cancelled_by']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vat_records');
    }
};
