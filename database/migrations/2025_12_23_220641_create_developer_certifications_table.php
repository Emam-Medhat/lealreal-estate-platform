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
        if (!Schema::hasTable('developer_certifications')) {
        Schema::create('developer_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->string('certificate_number')->unique();
            $table->string('issuing_organization');
            $table->string('issuing_organization_ar')->nullable();
            $table->enum('certification_type', ['quality', 'environmental', 'safety', 'technical', 'management', 'financial', 'legal']);
            $table->enum('status', ['pending', 'active', 'expired', 'suspended', 'revoked'])->default('pending');
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->date('renewal_date')->nullable();
            $table->string('certificate_file');
            $table->string('certificate_pdf')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->json('scope')->nullable(); // areas covered by certification
            $table->json('standards')->nullable(); // standards met
            $table->json('requirements')->nullable(); // requirements fulfilled
            $table->json('benefits')->nullable(); // benefits of certification
            $table->json('verification_details')->nullable();
            $table->string('verification_url')->nullable();
            $table->json('supporting_documents')->nullable();
            $table->json('audit_reports')->nullable();
            $table->json('renewal_requirements')->nullable();
            $table->json('maintenance_requirements')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('auto_renewal')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['developer_id', 'status']);
            $table->index(['certification_type']);
            $table->index(['expiry_date']);
            $table->index(['is_verified']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_certifications');
    }
};
