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
        if (!Schema::hasTable('certifications')) {
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('inspection_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('certificate_number')->unique();
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->enum('certificate_type', ['occupancy', 'safety', 'environmental', 'energy', 'accessibility', 'fire_safety', 'structural', 'electrical', 'plumbing', 'quality', 'other']);
            $table->enum('status', ['pending', 'issued', 'expired', 'suspended', 'revoked'])->default('pending');
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('issuing_authority');
            $table->string('issuing_authority_ar')->nullable();
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->date('renewal_date')->nullable();
            $table->json('requirements_met')->nullable();
            $table->json('standards_complied')->nullable();
            $table->json('conditions')->nullable();
            $table->json('restrictions')->nullable();
            $table->json('inspections_required')->nullable();
            $table->json('maintenance_requirements')->nullable();
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable();
            $table->string('certificate_pdf')->nullable();
            $table->json('supporting_documents')->nullable();
            $table->json('photos')->nullable();
            $table->string('inspector_name')->nullable();
            $table->string('inspector_signature')->nullable();
            $table->string('inspector_license')->nullable();
            $table->foreignId('issued_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('renewal_certificate_number')->nullable();
            $table->json('renewal_notes')->nullable();
            $table->timestamps();
            
            $table->index(['property_id', 'certificate_type']);
            $table->index(['inspection_id']);
            $table->index(['status']);
            $table->index(['issue_date']);
            $table->index(['expiry_date']);
            $table->index(['is_active']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certifications');
    }
};
