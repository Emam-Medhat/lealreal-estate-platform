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
        if (!Schema::hasTable('tax_exemptions')) {
        Schema::create('tax_exemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_tax_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tax_id')->constrained()->onDelete('cascade');
            $table->string('exemption_number')->unique();
            $table->enum('exemption_type', ['owner_occupied', 'senior_citizen', 'disabled', 'veteran', 'non_profit', 'government', 'educational', 'religious', 'charitable', 'agricultural', 'industrial', 'residential', 'commercial', 'other'])->default('owner_occupied');
            $table->string('exemption_category');
            $table->text('exemption_reason')->nullable();
            $table->text('exemption_reason_ar')->nullable();
            $table->decimal('exemption_amount', 12, 2)->default(0);
            $table->decimal('exemption_percentage', 5, 2)->default(0);
            $table->decimal('original_tax_amount', 12, 2)->default(0);
            $table->decimal('reduced_tax_amount', 12, 2)->default(0);
            $table->integer('tax_year');
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'suspended', 'expired', 'cancelled'])->default('pending');
            $table->date('application_date');
            $table->date('review_date')->nullable();
            $table->date('approval_date')->nullable();
            $table->date('rejection_date')->nullable();
            $table->date('suspension_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('cancellation_date')->nullable();
            $table->integer('duration_years')->default(1);
            $table->boolean('renewable')->default(true);
            $table->date('next_renewal_date')->nullable();
            $table->json('eligibility_criteria')->nullable();
            $table->json('supporting_documents')->nullable();
            $table->json('verification_documents')->nullable();
            $table->json('proof_documents')->nullable();
            $table->json('income_documents')->nullable();
            $table->json('age_documents')->nullable();
            $table->json('disability_documents')->nullable();
            $table->json('veteran_documents')->nullable();
            $table->json('non_profit_documents')->nullable();
            $table->json('government_documents')->nullable();
            $table->json('educational_documents')->nullable();
            $table->json('religious_documents')->nullable();
            $table->json('charitable_documents')->nullable();
            $table->json('agricultural_documents')->nullable();
            $table->json('industrial_documents')->nullable();
            $table->json('property_documents')->nullable();
            $table->json('ownership_documents')->nullable();
            $table->json('residence_documents')->nullable();
            $table->json('business_documents')->nullable();
            $table->json('financial_documents')->nullable();
            $table->json('tax_documents')->nullable();
            $table->json('previous_exemptions')->nullable();
            $table->json('exemption_history')->nullable();
            $table->json('verification_details')->nullable();
            $table->json('review_notes')->nullable();
            $table->json('review_notes_ar')->nullable();
            $table->text('approval_notes')->nullable();
            $table->text('approval_notes_ar')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('rejection_reason_ar')->nullable();
            $table->text('suspension_reason')->nullable();
            $table->text('suspension_reason_ar')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('cancellation_reason_ar')->nullable();
            $table->json('conditions')->nullable();
            $table->json('restrictions')->nullable();
            $table->json('requirements')->nullable();
            $table->json('obligations')->nullable();
            $table->json('compliance_checks')->nullable();
            $table->json('audit_trail')->nullable();
            $table->json('correspondence')->nullable();
            $table->json('notifications')->nullable();
            $table->json('reminders')->nullable();
            $table->json('notes')->nullable();
            $table->json('notes_ar')->nullable();
            $table->boolean('electronically_signed')->default(false);
            $table->timestamp('signed_at')->nullable();
            $table->string('digital_signature')->nullable();
            $table->boolean('verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('appeal_filed')->default(false);
            $table->date('appeal_date')->nullable();
            $table->text('appeal_reason')->nullable();
            $table->text('appeal_reason_ar')->nullable();
            $table->enum('appeal_status', ['pending', 'approved', 'rejected', 'withdrawn'])->nullable();
            $table->date('appeal_decision_date')->nullable();
            $table->text('appeal_decision')->nullable();
            $table->text('appeal_decision_ar')->nullable();
            $table->json('appeal_documents')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('suspended_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['exemption_number']);
            $table->index(['property_tax_id']);
            $table->index(['user_id']);
            $table->index(['tax_id']);
            $table->index(['exemption_type']);
            $table->index(['exemption_category']);
            $table->index(['tax_year']);
            $table->index(['status']);
            $table->index(['application_date']);
            $table->index(['review_date']);
            $table->index(['approval_date']);
            $table->index(['rejection_date']);
            $table->index(['expiry_date']);
            $table->index(['next_renewal_date']);
            $table->index(['verified']);
            $table->index(['appeal_status']);
            $table->index(['reviewed_by']);
            $table->index(['approved_by']);
            $table->index(['rejected_by']);
            $table->index(['suspended_by']);
            $table->index(['cancelled_by']);
            $table->index(['verified_by']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_exemptions');
    }
};
