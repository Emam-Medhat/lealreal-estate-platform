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
        if (!Schema::hasTable('tax_documents')) {
        Schema::create('tax_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_tax_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('tax_filing_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('tax_assessment_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('tax_exemption_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('document_number')->unique();
            $table->enum('document_type', ['tax_return', 'assessment_notice', 'payment_receipt', 'exemption_certificate', 'tax_clearance', 'appeal_document', 'supporting_document', 'legal_document', 'financial_document', 'property_document', 'other'])->default('supporting_document');
            $table->string('document_category');
            $table->string('document_title');
            $table->string('document_title_ar')->nullable();
            $table->text('document_description')->nullable();
            $table->text('document_description_ar')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_extension');
            $table->integer('file_size');
            $table->string('mime_type');
            $table->string('file_hash')->nullable();
            $table->enum('status', ['draft', 'uploaded', 'under_review', 'approved', 'rejected', 'expired', 'archived'])->default('uploaded');
            $table->date('document_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('upload_date');
            $table->date('review_date')->nullable();
            $table->date('approval_date')->nullable();
            $table->date('rejection_date')->nullable();
            $table->date('expiry_notification_sent')->nullable();
            $table->json('document_metadata')->nullable();
            $table->json('document_tags')->nullable();
            $table->json('document_keywords')->nullable();
            $table->json('document_classification')->nullable();
            $table->json('document_priority')->nullable();
            $table->json('document_security')->nullable();
            $table->json('document_access')->nullable();
            $table->json('document_permissions')->nullable();
            $table->json('document_sharing')->nullable();
            $table->json('document_version')->nullable();
            $table->json('document_history')->nullable();
            $table->json('document_workflow')->nullable();
            $table->json('document_review')->nullable();
            $table->json('document_approval')->nullable();
            $table->json('document_rejection')->nullable();
            $table->json('document_notes')->nullable();
            $table->json('document_notes_ar')->nullable();
            $table->text('review_comments')->nullable();
            $table->text('review_comments_ar')->nullable();
            $table->text('approval_comments')->nullable();
            $table->text('approval_comments_ar')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('rejection_reason_ar')->nullable();
            $table->json('document_requirements')->nullable();
            $table->json('document_validations')->nullable();
            $table->json('document_compliance')->nullable();
            $table->json('document_regulations')->nullable();
            $table->json('document_standards')->nullable();
            $table->json('document_policies')->nullable();
            $table->json('document_procedures')->nullable();
            $table->json('document_guidelines')->nullable();
            $table->json('document_templates')->nullable();
            $table->json('document_formats')->nullable();
            $table->json('document_signatures')->nullable();
            $table->json('document_certifications')->nullable();
            $table->json('document_verifications')->nullable();
            $table->json('document_authentications')->nullable();
            $table->json('document_audits')->nullable();
            $table->json('document_logs')->nullable();
            $table->json('document_trail')->nullable();
            $table->json('document_notifications')->nullable();
            $table->json('document_reminders')->nullable();
            $table->json('document_alerts')->nullable();
            $table->boolean('electronically_signed')->default(false);
            $table->timestamp('signed_at')->nullable();
            $table->string('digital_signature')->nullable();
            $table->boolean('certified')->default(false);
            $table->timestamp('certified_at')->nullable();
            $table->boolean('verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->boolean('archived')->default(false);
            $table->timestamp('archived_at')->nullable();
            $table->boolean('public_access')->default(false);
            $table->date('public_access_date')->nullable();
            $table->json('access_permissions')->nullable();
            $table->json('confidentiality')->nullable();
            $table->json('retention_policy')->nullable();
            $table->date('retention_expiry_date')->nullable();
            $table->boolean('auto_delete')->default(false);
            $table->date('auto_delete_date')->nullable();
            $table->json('backup_info')->nullable();
            $table->json('recovery_info')->nullable();
            $table->json('integration_info')->nullable();
            $table->json('api_info')->nullable();
            $table->json('sync_info')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('signed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('certified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('archived_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['document_number']);
            $table->index(['property_tax_id']);
            $table->index(['tax_filing_id']);
            $table->index(['tax_assessment_id']);
            $table->index(['tax_exemption_id']);
            $table->index(['user_id']);
            $table->index(['document_type']);
            $table->index(['document_category']);
            $table->index(['status']);
            $table->index(['document_date']);
            $table->index(['expiry_date']);
            $table->index(['upload_date']);
            $table->index(['review_date']);
            $table->index(['approval_date']);
            $table->index(['rejection_date']);
            $table->index(['uploaded_by']);
            $table->index(['reviewed_by']);
            $table->index(['approved_by']);
            $table->index(['rejected_by']);
            $table->index(['signed_by']);
            $table->index(['certified_by']);
            $table->index(['verified_by']);
            $table->index(['archived_by']);
            $table->index(['archived']);
            $table->index(['certified']);
            $table->index(['verified']);
            $table->index(['electronically_signed']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_documents');
    }
};
