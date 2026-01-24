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
        Schema::create('tax_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('tax_id')->constrained()->onDelete('cascade');
            $table->string('assessment_number')->unique();
            $table->enum('assessment_type', ['initial', 'annual', 'reassessment', 'correction', 'appeal', 'special'])->default('annual');
            $table->integer('assessment_year');
            $table->date('assessment_date');
            $table->date('effective_date');
            $table->date('expiry_date')->nullable();
            $table->decimal('market_value', 12, 2)->default(0);
            $table->decimal('assessed_value', 12, 2)->default(0);
            $table->decimal('taxable_value', 12, 2)->default(0);
            $table->decimal('previous_assessed_value', 12, 2)->default(0);
            $table->decimal('value_increase', 12, 2)->default(0);
            $table->decimal('value_increase_percentage', 5, 2)->default(0);
            $table->decimal('tax_rate', 8, 4)->default(0);
            $table->decimal('annual_tax', 12, 2)->default(0);
            $table->decimal('previous_annual_tax', 12, 2)->default(0);
            $table->decimal('tax_increase', 12, 2)->default(0);
            $table->decimal('tax_increase_percentage', 5, 2)->default(0);
            $table->enum('status', ['draft', 'under_review', 'approved', 'rejected', 'appealed', 'corrected', 'cancelled'])->default('draft');
            $table->date('review_date')->nullable();
            $table->date('approval_date')->nullable();
            $table->date('rejection_date')->nullable();
            $table->date('appeal_date')->nullable();
            $table->date('correction_date')->nullable();
            $table->date('cancellation_date')->nullable();
            $table->json('property_details')->nullable();
            $table->json('valuation_details')->nullable();
            $table->json('assessment_details')->nullable();
            $table->json('calculation_details')->nullable();
            $table->json('comparable_properties')->nullable();
            $table->json('valuation_methods')->nullable();
            $table->json('adjustments')->nullable();
            $table->json('depreciation')->nullable();
            $table->json('improvements')->nullable();
            $table->json('land_value')->nullable();
            $table->json('building_value')->nullable();
            $table->json('other_improvements')->nullable();
            $table->json('zoning_information')->nullable();
            $table->json('location_factors')->nullable();
            $table->json('market_conditions')->nullable();
            $table->json('economic_factors')->nullable();
            $table->json('property_characteristics')->nullable();
            $table->json('assessment_criteria')->nullable();
            $table->json('assessment_guidelines')->nullable();
            $table->json('supporting_documents')->nullable();
            $table->json('photographs')->nullable();
            $table->json('maps')->nullable();
            $table->json('survey_documents')->nullable();
            $table->json('legal_documents')->nullable();
            $table->json('building_permits')->nullable();
            $table->json('zoning_documents')->nullable();
            $table->json('inspection_reports')->nullable();
            $table->json('appraisal_reports')->nullable();
            $table->json('market_analysis')->nullable();
            $table->json('review_notes')->nullable();
            $table->json('review_notes_ar')->nullable();
            $table->text('approval_notes')->nullable();
            $table->text('approval_notes_ar')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('rejection_reason_ar')->nullable();
            $table->text('appeal_reason')->nullable();
            $table->text('appeal_reason_ar')->nullable();
            $table->text('correction_reason')->nullable();
            $table->text('correction_reason_ar')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('cancellation_reason_ar')->nullable();
            $table->json('conditions')->nullable();
            $table->json('restrictions')->nullable();
            $table->json('assumptions')->nullable();
            $table->json('limitations')->nullable();
            $table->json('disclaimers')->nullable();
            $table->json('certifications')->nullable();
            $table->json('qualifications')->nullable();
            $table->json('methodology')->nullable();
            $table->json('standards_used')->nullable();
            $table->json('data_sources')->nullable();
            $table->json('verification_methods')->nullable();
            $table->json('quality_assurance')->nullable();
            $table->json('audit_trail')->nullable();
            $table->json('correspondence')->nullable();
            $table->json('notifications')->nullable();
            $table->json('notes')->nullable();
            $table->json('notes_ar')->nullable();
            $table->boolean('electronically_signed')->default(false);
            $table->timestamp('signed_at')->nullable();
            $table->string('digital_signature')->nullable();
            $table->boolean('certified')->default(false);
            $table->timestamp('certified_at')->nullable();
            $table->foreignId('certified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->boolean('public_access')->default(false);
            $table->date('public_access_date')->nullable();
            $table->json('access_permissions')->nullable();
            $table->json('confidentiality')->nullable();
            $table->boolean('appeal_filed')->default(false);
            $table->date('appeal_deadline')->nullable();
            $table->enum('appeal_status', ['pending', 'under_review', 'approved', 'rejected', 'withdrawn'])->nullable();
            $table->date('appeal_decision_date')->nullable();
            $table->text('appeal_decision')->nullable();
            $table->text('appeal_decision_ar')->nullable();
            $table->json('appeal_documents')->nullable();
            $table->json('appeal_details')->nullable();
            $table->boolean('correction_applied')->default(false);
            $table->json('correction_details')->nullable();
            $table->decimal('corrected_value', 12, 2)->nullable();
            $table->decimal('corrected_tax', 12, 2)->nullable();
            $table->foreignId('assessor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('appealed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('corrected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['assessment_number']);
            $table->index(['property_id']);
            $table->index(['tax_id']);
            $table->index(['assessment_type']);
            $table->index(['assessment_year']);
            $table->index(['assessment_date']);
            $table->index(['effective_date']);
            $table->index(['expiry_date']);
            $table->index(['status']);
            $table->index(['review_date']);
            $table->index(['approval_date']);
            $table->index(['rejection_date']);
            $table->index(['appeal_date']);
            $table->index(['correction_date']);
            $table->index(['cancellation_date']);
            $table->index(['assessor_id']);
            $table->index(['reviewed_by']);
            $table->index(['approved_by']);
            $table->index(['rejected_by']);
            $table->index(['appealed_by']);
            $table->index(['corrected_by']);
            $table->index(['cancelled_by']);
            $table->index(['certified_by']);
            $table->index(['appeal_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_assessments');
    }
};
