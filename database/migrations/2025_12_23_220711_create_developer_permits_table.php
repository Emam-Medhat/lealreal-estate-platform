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
        if (!Schema::hasTable('developer_permits')) {
        Schema::create('developer_permits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('developer_projects')->onDelete('cascade');
            $table->foreignId('phase_id')->nullable()->constrained('developer_project_phases')->onDelete('cascade');
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->string('permit_number')->unique();
            $table->enum('permit_type', ['building', 'construction', 'occupancy', 'electrical', 'plumbing', 'mechanical', 'fire', 'environmental', 'demolition', 'excavation', 'safety', 'health']);
            $table->enum('status', ['applied', 'under_review', 'approved', 'issued', 'suspended', 'expired', 'revoked', 'rejected'])->default('applied');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->string('issuing_authority');
            $table->string('issuing_authority_ar')->nullable();
            $table->date('application_date');
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('renewal_date')->nullable();
            $table->decimal('permit_fee', 12, 2)->nullable();
            $table->string('currency')->default('SAR');
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->json('permit_conditions')->nullable();
            $table->json('compliance_requirements')->nullable();
            $table->json('inspection_requirements')->nullable();
            $table->json('scope_of_work')->nullable();
            $table->json('technical_specifications')->nullable();
            $table->json('safety_requirements')->nullable();
            $table->json('environmental_requirements')->nullable();
            $table->json('design_requirements')->nullable();
            $table->json('documentation_required')->nullable();
            $table->json('supporting_documents')->nullable();
            $table->json('approved_documents')->nullable();
            $table->json('inspection_reports')->nullable();
            $table->json('compliance_reports')->nullable();
            $table->json('violation_notices')->nullable();
            $table->json('correction_actions')->nullable();
            $table->json('penalties')->nullable();
            $table->json('appeals')->nullable();
            $table->json('amendments')->nullable();
            $table->json('renewals')->nullable();
            $table->string('permit_file')->nullable();
            $table->string('permit_pdf')->nullable();
            $table->json('digital_copy')->nullable();
            $table->string('verification_url')->nullable();
            $table->json('contact_information')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('require_renewal')->default(false);
            $table->boolean('auto_renewal')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['developer_id', 'status']);
            $table->index(['project_id']);
            $table->index(['phase_id']);
            $table->index(['permit_type']);
            $table->index(['expiry_date']);
            $table->index(['issue_date']);
            $table->index(['is_verified']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_permits');
    }
};
