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
        if (!Schema::hasTable('repair_estimates')) {
        Schema::create('repair_estimates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('inspection_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('defect_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('estimate_number')->unique();
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description');
            $table->text('description_ar')->nullable();
            $table->enum('status', ['draft', 'sent', 'approved', 'rejected', 'expired'])->default('draft');
            $table->decimal('labor_cost', 15, 2)->default(0);
            $table->decimal('material_cost', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->decimal('contingency_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->json('labor_items')->nullable();
            $table->json('material_items')->nullable();
            $table->json('equipment_items')->nullable();
            $table->json('permit_requirements')->nullable();
            $table->json('timeline_estimate')->nullable();
            $table->integer('estimated_days')->nullable();
            $table->date('estimate_date');
            $table->date('valid_until')->nullable();
            $table->date('start_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->text('terms_conditions_ar')->nullable();
            $table->string('contractor_name')->nullable();
            $table->string('contractor_phone')->nullable();
            $table->string('contractor_email')->nullable();
            $table->string('contractor_license')->nullable();
            $table->json('contractor_references')->nullable();
            $table->json('warranty_terms')->nullable();
            $table->string('estimate_pdf')->nullable();
            $table->json('supporting_documents')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index(['property_id', 'status']);
            $table->index(['inspection_id']);
            $table->index(['defect_id']);
            $table->index(['estimate_date']);
            $table->index(['valid_until']);
            $table->index(['priority']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_estimates');
    }
};
