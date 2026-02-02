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
        if (!Schema::hasTable('tax_filings')) {
        Schema::create('tax_filings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_tax_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tax_id')->constrained()->onDelete('cascade');
            $table->string('filing_number')->unique();
            $table->enum('filing_type', ['annual', 'quarterly', 'monthly', 'provisional', 'amended', 'final'])->default('annual');
            $table->integer('tax_year');
            $table->enum('period', ['Q1', 'Q2', 'Q3', 'Q4', 'H1', 'H2', 'annual'])->nullable();
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'returned', 'processed', 'cancelled'])->default('draft');
            $table->date('submission_date')->nullable();
            $table->date('review_date')->nullable();
            $table->date('approval_date')->nullable();
            $table->date('rejection_date')->nullable();
            $table->decimal('declared_amount', 12, 2)->default(0);
            $table->decimal('assessed_amount', 12, 2)->default(0);
            $table->decimal('tax_liability', 12, 2)->default(0);
            $table->decimal('tax_paid', 12, 2)->default(0);
            $table->decimal('tax_refund', 12, 2)->default(0);
            $table->decimal('tax_due', 12, 2)->default(0);
            $table->decimal('penalties', 12, 2)->default(0);
            $table->decimal('interest', 12, 2)->default(0);
            $table->decimal('total_due', 12, 2)->default(0);
            $table->json('income_details')->nullable();
            $table->json('expense_details')->nullable();
            $table->json('deduction_details')->nullable();
            $table->json('credit_details')->nullable();
            $table->json('exemption_details')->nullable();
            $table->json('calculation_breakdown')->nullable();
            $table->json('supporting_documents')->nullable();
            $table->json('attachments')->nullable();
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
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('audit_trail')->nullable();
            $table->json('correspondence')->nullable();
            $table->json('payment_schedule')->nullable();
            $table->json('compliance_checks')->nullable();
            $table->boolean('electronically_signed')->default(false);
            $table->timestamp('signed_at')->nullable();
            $table->string('digital_signature')->nullable();
            $table->boolean('auto_filing')->default(false);
            $table->json('filing_preferences')->nullable();
            $table->json('notification_settings')->nullable();
            $table->json('reminder_settings')->nullable();
            $table->boolean('extension_requested')->default(false);
            $table->date('extension_deadline')->nullable();
            $table->text('extension_reason')->nullable();
            $table->text('extension_reason_ar')->nullable();
            $table->boolean('amended')->default(false);
            $table->foreignId('amended_from')->nullable()->constrained('tax_filings')->onDelete('set null');
            $table->text('amendment_reason')->nullable();
            $table->text('amendment_reason_ar')->nullable();
            $table->boolean('appeal_filed')->default(false);
            $table->date('appeal_date')->nullable();
            $table->text('appeal_reason')->nullable();
            $table->text('appeal_reason_ar')->nullable();
            $table->enum('appeal_status', ['pending', 'approved', 'rejected', 'withdrawn'])->nullable();
            $table->date('appeal_decision_date')->nullable();
            $table->text('appeal_decision')->nullable();
            $table->text('appeal_decision_ar')->nullable();
            $table->json('appeal_documents')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['filing_number']);
            $table->index(['property_tax_id']);
            $table->index(['user_id']);
            $table->index(['tax_id']);
            $table->index(['filing_type']);
            $table->index(['tax_year']);
            $table->index(['period']);
            $table->index(['status']);
            $table->index(['submission_date']);
            $table->index(['review_date']);
            $table->index(['approval_date']);
            $table->index(['rejection_date']);
            $table->index(['reviewed_by']);
            $table->index(['approved_by']);
            $table->index(['rejected_by']);
            $table->index(['appeal_status']);
            $table->index(['amended_from']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_filings');
    }
};
