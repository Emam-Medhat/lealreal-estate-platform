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
        Schema::create('capital_gains_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('transaction_number')->unique();
            $table->enum('transaction_type', ['sale', 'exchange', 'gift', 'inheritance', 'foreclosure', 'other'])->default('sale');
            $table->date('transaction_date');
            $table->decimal('sale_price', 12, 2)->default(0);
            $table->decimal('original_cost', 12, 2)->default(0);
            $table->decimal('adjusted_basis', 12, 2)->default(0);
            $table->decimal('capital_gain', 12, 2)->default(0);
            $table->decimal('capital_loss', 12, 2)->default(0);
            $table->decimal('net_gain', 12, 2)->default(0);
            $table->decimal('taxable_gain', 12, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('tax_paid', 12, 2)->default(0);
            $table->decimal('tax_due', 12, 2)->default(0);
            $table->integer('holding_period_days')->default(0);
            $table->enum('holding_period', ['short_term', 'long_term'])->default('short_term');
            $table->date('acquisition_date');
            $table->date('disposition_date');
            $table->enum('disposition_reason', ['sale', 'exchange', 'gift', 'inheritance', 'foreclosure', 'condemnation', 'abandonment', 'other'])->default('sale');
            $table->text('disposition_reason_details')->nullable();
            $table->text('disposition_reason_details_ar')->nullable();
            $table->enum('tax_year', ['current', 'previous', 'deferred'])->default('current');
            $table->integer('tax_year_value');
            $table->enum('status', ['pending', 'calculated', 'filed', 'paid', 'overdue', 'cancelled', 'refunded'])->default('pending');
            $table->date('calculation_date')->nullable();
            $table->date('filing_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('overdue_date')->nullable();
            $table->date('cancellation_date')->nullable();
            $table->date('refund_date')->nullable();
            $table->json('acquisition_details')->nullable();
            $table->json('disposition_details')->nullable();
            $table->json('improvements')->nullable();
            $table->json('depreciation')->nullable();
            $table->json('adjustments')->nullable();
            $table->json('expenses')->nullable();
            $table->json('commissions')->nullable();
            $table->json('fees')->nullable();
            $table->json('closing_costs')->nullable();
            $table->json('transaction_costs')->nullable();
            $table->json('basis_adjustments')->nullable();
            $table->json('exclusions')->nullable();
            $table->json('exemptions')->nullable();
            $table->json('deductions')->nullable();
            $table->json('credits')->nullable();
            $table->json('calculation_details')->nullable();
            $table->json('tax_calculation')->nullable();
            $table->json('payment_schedule')->nullable();
            $table->json('supporting_documents')->nullable();
            $table->json('sale_documents')->nullable();
            $table->json('purchase_documents')->nullable();
            $table->json('improvement_documents')->nullable();
            $table->json('expense_documents')->nullable();
            $table->json('tax_documents')->nullable();
            $table->json('legal_documents')->nullable();
            $table->json('financial_documents')->nullable();
            $table->json('property_documents')->nullable();
            $table->json('correspondence')->nullable();
            $table->json('notes')->nullable();
            $table->json('notes_ar')->nullable();
            $table->text('calculation_notes')->nullable();
            $table->text('calculation_notes_ar')->nullable();
            $table->text('filing_notes')->nullable();
            $table->text('filing_notes_ar')->nullable();
            $table->text('payment_notes')->nullable();
            $table->text('payment_notes_ar')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('cancellation_reason_ar')->nullable();
            $table->text('refund_reason')->nullable();
            $table->text('refund_reason_ar')->nullable();
            $table->boolean('primary_residence')->default(false);
            $table->boolean('exclusion_applied')->default(false);
            $table->decimal('exclusion_amount', 12, 2)->default(0);
            $table->enum('exclusion_type', ['section_121', 'section_1031', 'other'])->nullable();
            $table->json('exclusion_details')->nullable();
            $table->boolean('like_kind_exchange')->default(false);
            $table->json('exchange_details')->nullable();
            $table->boolean('installment_sale')->default(false);
            $table->json('installment_details')->nullable();
            $table->boolean('deferred')->default(false);
            $table->json('deferral_details')->nullable();
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
            $table->boolean('electronically_signed')->default(false);
            $table->timestamp('signed_at')->nullable();
            $table->string('digital_signature')->nullable();
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
            $table->foreignId('calculated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('filed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('paid_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('refunded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['transaction_number']);
            $table->index(['property_id']);
            $table->index(['user_id']);
            $table->index(['transaction_type']);
            $table->index(['transaction_date']);
            $table->index(['acquisition_date']);
            $table->index(['disposition_date']);
            $table->index(['holding_period']);
            $table->index(['tax_year_value']);
            $table->index(['status']);
            $table->index(['calculation_date']);
            $table->index(['filing_date']);
            $table->index(['payment_date']);
            $table->index(['due_date']);
            $table->index(['overdue_date']);
            $table->index(['primary_residence']);
            $table->index(['exclusion_applied']);
            $table->index(['like_kind_exchange']);
            $table->index(['installment_sale']);
            $table->index(['deferred']);
            $table->index(['appeal_status']);
            $table->index(['audit_status']);
            $table->index(['calculated_by']);
            $table->index(['filed_by']);
            $table->index(['paid_by']);
            $table->index(['cancelled_by']);
            $table->index(['refunded_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capital_gains_taxes');
    }
};
