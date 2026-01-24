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
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('rate_name');
            $table->string('rate_name_ar')->nullable();
            $table->text('rate_description')->nullable();
            $table->text('rate_description_ar')->nullable();
            $table->enum('tax_type', ['property_tax', 'capital_gains', 'vat', 'stamp_duty', 'registration_fee', 'municipality_fee', 'other'])->default('property_tax');
            $table->decimal('rate_percentage', 5, 2)->default(0);
            $table->decimal('rate_amount', 12, 2)->default(0);
            $table->enum('rate_basis', ['percentage', 'fixed_amount', 'tiered', 'progressive'])->default('percentage');
            $table->json('rate_tiers')->nullable();
            $table->json('rate_brackets')->nullable();
            $table->decimal('min_value', 12, 2)->nullable();
            $table->decimal('max_value', 12, 2)->nullable();
            $table->decimal('min_amount', 12, 2)->default(0);
            $table->decimal('max_amount', 12, 2)->nullable();
            $table->enum('applicability', ['all_properties', 'residential', 'commercial', 'industrial', 'agricultural', 'vacant_land', 'specific_areas'])->default('all_properties');
            $table->json('applicable_areas')->nullable();
            $table->json('applicable_property_types')->nullable();
            $table->json('applicable_zones')->nullable();
            $table->json('exemptions')->nullable();
            $table->json('special_conditions')->nullable();
            $table->enum('frequency', ['one_time', 'annual', 'semi_annual', 'quarterly', 'monthly'])->default('annual');
            $table->date('effective_date');
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['draft', 'active', 'suspended', 'expired', 'cancelled'])->default('draft');
            $table->date('activation_date')->nullable();
            $table->date('suspension_date')->nullable();
            $table->date('cancellation_date')->nullable();
            $table->json('rate_rules')->nullable();
            $table->json('calculation_formula')->nullable();
            $table->json('rate_parameters')->nullable();
            $table->json('rate_metadata')->nullable();
            $table->json('rate_history')->nullable();
            $table->json('rate_adjustments')->nullable();
            $table->json('rate_reviews')->nullable();
            $table->json('rate_approvals')->nullable();
            $table->json('supporting_documents')->nullable();
            $table->json('legal_references')->nullable();
            $table->json('regulatory_references')->nullable();
            $table->json('policy_references')->nullable();
            $table->json('notes')->nullable();
            $table->json('notes_ar')->nullable();
            $table->text('activation_notes')->nullable();
            $table->text('activation_notes_ar')->nullable();
            $table->text('suspension_reason')->nullable();
            $table->text('suspension_reason_ar')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('cancellation_reason_ar')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('auto_apply')->default(false);
            $table->boolean('allow_override')->default(false);
            $table->boolean('electronically_signed')->default(false);
            $table->timestamp('signed_at')->nullable();
            $table->string('digital_signature')->nullable();
            $table->boolean('notification_sent')->default(false);
            $table->timestamp('notification_sent_at')->nullable();
            $table->json('payment_notifications')->nullable();
            $table->json('payment_reminders')->nullable();
            $table->json('audit_trail')->nullable();
            $table->json('compliance_checks')->nullable();
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
            $table->foreignId('activated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('suspended_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['rate_name']);
            $table->index(['tax_type']);
            $table->index(['rate_percentage']);
            $table->index(['rate_basis']);
            $table->index(['applicability']);
            $table->index(['frequency']);
            $table->index(['effective_date']);
            $table->index(['expiry_date']);
            $table->index(['status']);
            $table->index(['activation_date']);
            $table->index(['suspension_date']);
            $table->index(['cancellation_date']);
            $table->index(['is_default']);
            $table->index(['is_mandatory']);
            $table->index(['auto_apply']);
            $table->index(['allow_override']);
            $table->index(['electronically_signed']);
            $table->index(['notification_sent']);
            $table->index(['appeal_status']);
            $table->index(['audit_status']);
            $table->index(['activated_by']);
            $table->index(['suspended_by']);
            $table->index(['cancelled_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
    }
};
