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
        Schema::create('property_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('tax_id')->constrained()->onDelete('cascade');
            $table->foreignId('tax_assessment_id')->nullable()->constrained('taxes')->onDelete('set null');
            $table->decimal('assessment_value', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('penalty_amount', 12, 2)->default(0);
            $table->decimal('interest_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('remaining_amount', 12, 2)->default(0);
            $table->integer('tax_year');
            $table->enum('status', ['pending', 'assessed', 'due', 'paid', 'overdue', 'cancelled', 'refunded'])->default('pending');
            $table->date('assessment_date')->nullable();
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->date('late_payment_date')->nullable();
            $table->integer('grace_period_days')->default(0);
            $table->decimal('penalty_rate', 5, 2)->default(0);
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->json('calculation_details')->nullable();
            $table->json('assessment_details')->nullable();
            $table->json('payment_schedule')->nullable();
            $table->json('exemptions_applied')->nullable();
            $table->json('deductions_applied')->nullable();
            $table->json('credits_applied')->nullable();
            $table->json('adjustments_applied')->nullable();
            $table->json('penalties_applied')->nullable();
            $table->json('interest_applied')->nullable();
            $table->json('payment_history')->nullable();
            $table->json('correspondence')->nullable();
            $table->json('documents')->nullable();
            $table->json('notes')->nullable();
            $table->json('notes_ar')->nullable();
            $table->boolean('appeal_filed')->default(false);
            $table->date('appeal_date')->nullable();
            $table->text('appeal_reason')->nullable();
            $table->text('appeal_reason_ar')->nullable();
            $table->enum('appeal_status', ['pending', 'approved', 'rejected', 'withdrawn'])->nullable();
            $table->date('appeal_decision_date')->nullable();
            $table->text('appeal_decision')->nullable();
            $table->text('appeal_decision_ar')->nullable();
            $table->boolean('installment_plan')->default(false);
            $table->json('installment_details')->nullable();
            $table->boolean('auto_payment')->default(false);
            $table->string('auto_payment_method')->nullable();
            $table->json('payment_reminders')->nullable();
            $table->json('compliance_checks')->nullable();
            $table->json('audit_trail')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['property_id']);
            $table->index(['tax_id']);
            $table->index(['tax_assessment_id']);
            $table->index(['tax_year']);
            $table->index(['status']);
            $table->index(['due_date']);
            $table->index(['paid_date']);
            $table->index(['assessment_date']);
            $table->index(['appeal_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_taxes');
    }
};
