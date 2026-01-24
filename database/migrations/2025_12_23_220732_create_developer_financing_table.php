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
        Schema::create('developer_financing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('developer_projects')->onDelete('cascade');
            $table->foreignId('investor_id')->nullable()->constrained('developer_investors')->onDelete('set null');
            $table->string('financing_title');
            $table->string('financing_title_ar')->nullable();
            $table->enum('financing_type', ['construction_loan', 'mortgage', 'equity', 'mezzanine', 'bridge_loan', 'syndicated_loan', 'islamic_financing', ' sukuk']);
            $table->enum('status', ['applied', 'under_review', 'approved', 'disbursed', 'active', 'matured', 'defaulted', 'closed'])->default('applied');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->string('lender_name');
            $table->string('lender_name_ar')->nullable();
            $table->enum('lender_type', ['bank', 'financial_institution', 'investment_fund', 'private_equity', 'government', 'individual']);
            $table->decimal('loan_amount', 15, 2);
            $table->decimal('approved_amount', 15, 2)->nullable();
            $table->decimal('disbursed_amount', 15, 2)->default(0);
            $table->string('currency')->default('SAR');
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->string('interest_type')->nullable(); // fixed, variable, hybrid
            $table->enum('rate_basis', ['annual', 'semi_annual', 'quarterly', 'monthly'])->nullable();
            $table->string('rate_reference')->nullable(); // SIBOR, LIBOR, etc.
            $table->decimal('arrangement_fee', 10, 2)->nullable();
            $table->decimal('processing_fee', 10, 2)->nullable();
            $table->decimal('legal_fee', 10, 2)->nullable();
            $table->decimal('other_fees', 10, 2)->nullable();
            $table->date('application_date');
            $table->date('approval_date')->nullable();
            $table->date('disbursement_date')->nullable();
            $table->date('first_payment_date')->nullable();
            $table->date('maturity_date')->nullable();
            $table->integer('loan_term_months')->nullable();
            $table->enum('payment_frequency', ['monthly', 'quarterly', 'semi_annual', 'annual', 'bullet']);
            $table->enum('amortization_type', ['linear', 'annuity', 'bullet', 'interest_only', 'custom']);
            $table->json('collateral')->nullable();
            $table->json('guarantees')->nullable();
            $table->json('security_instruments')->nullable();
            $table->json('loan_covenants')->nullable();
            $table->json('financial_covenants')->nullable();
            $table->json('operating_covenants')->nullable();
            $table->json('reporting_requirements')->nullable();
            $table->json('disbursement_schedule')->nullable();
            $table->json('repayment_schedule')->nullable();
            $table->json('use_of_proceeds')->nullable();
            $table->json('project_cash_flow')->nullable();
            $table->json('financial_projections')->nullable();
            $table->json('risk_assessment')->nullable();
            $table->json('mitigation_measures')->nullable();
            $table->json('supporting_documents')->nullable();
            $table->json('legal_documents')->nullable();
            $table->json('financial_documents')->nullable();
            $table->json('technical_documents')->nullable();
            $table->json('insurance_requirements')->nullable();
            $table->json('monitoring_requirements')->nullable();
            $table->json('contact_information')->nullable();
            $table->boolean('is_revolving')->default(false);
            $table->boolean('has_prepayment_option')->default(false);
            $table->boolean('requires_syndication')->default(false);
            $table->boolean('is_sharia_compliant')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('disbursed_at')->nullable();
            $table->timestamp('matured_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['developer_id', 'status']);
            $table->index(['project_id']);
            $table->index(['investor_id']);
            $table->index(['financing_type']);
            $table->index(['lender_type']);
            $table->index(['approval_date']);
            $table->index(['maturity_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_financing');
    }
};
