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
        Schema::create('compliance_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('inspection_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('check_number')->unique();
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->enum('check_type', ['building_code', 'safety', 'accessibility', 'environmental', 'zoning', 'fire_safety', 'electrical', 'plumbing', 'structural', 'other']);
            $table->enum('status', ['pending', 'in_progress', 'passed', 'failed', 'requires_action'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->json('checklist_items')->nullable();
            $table->json('requirements')->nullable();
            $table->json('standards')->nullable();
            $table->json('regulations')->nullable();
            $table->json('findings')->nullable();
            $table->json('violations')->nullable();
            $table->json('recommendations')->nullable();
            $table->json('corrective_actions')->nullable();
            $table->date('check_date');
            $table->date('next_check_date')->nullable();
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable();
            $table->json('photos')->nullable();
            $table->json('documents')->nullable();
            $table->json('certificates')->nullable();
            $table->string('inspector_name')->nullable();
            $table->string('inspector_signature')->nullable();
            $table->string('inspector_license')->nullable();
            $table->foreignId('performed_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->string('report_pdf')->nullable();
            $table->boolean('is_compliant')->nullable();
            $table->integer('score')->nullable();
            $table->timestamps();
            
            $table->index(['property_id', 'check_type']);
            $table->index(['inspection_id']);
            $table->index(['status']);
            $table->index(['check_date']);
            $table->index(['priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_checks');
    }
};
