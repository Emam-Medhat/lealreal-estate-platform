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
        Schema::create('developer_investors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('developer_projects')->onDelete('cascade');
            $table->string('investor_name');
            $table->string('investor_name_ar')->nullable();
            $table->enum('investor_type', ['individual', 'company', 'fund', 'bank', 'government', 'institution']);
            $table->enum('investment_type', ['equity', 'debt', 'mezzanine', 'preferred_equity', 'joint_venture', 'partnership']);
            $table->enum('status', ['prospect', 'committed', 'active', 'withdrawn', 'completed'])->default('prospect');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->json('address')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->decimal('investment_amount', 15, 2);
            $table->string('currency')->default('SAR');
            $table->decimal('equity_percentage', 5, 2)->nullable();
            $table->date('investment_date')->nullable();
            $table->date('commitment_date')->nullable();
            $table->date('maturity_date')->nullable();
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->string('interest_type')->nullable(); // fixed, variable, hybrid
            $table->enum('payment_frequency', ['monthly', 'quarterly', 'semi_annual', 'annual', 'maturity'])->nullable();
            $table->json('investment_terms')->nullable();
            $table->json('return_expectations')->nullable();
            $table->json('risk_profile')->nullable();
            $table->json('investment_criteria')->nullable();
            $table->json('due_diligence_documents')->nullable();
            $table->json('legal_documents')->nullable();
            $table->json('financial_documents')->nullable();
            $table->json('agreement_documents')->nullable();
            $table->json('guarantees')->nullable();
            $table->json('collateral')->nullable();
            $table->json('security_instruments')->nullable();
            $table->json('reporting_requirements')->nullable();
            $table->json('governance_rights')->nullable();
            $table->json('voting_rights')->nullable();
            $table->json('board_representation')->nullable();
            $table->json('management_rights')->nullable();
            $table->json('exit_strategy')->nullable();
            $table->json('exit_conditions')->nullable();
            $table->json('performance_metrics')->nullable();
            $table->json('reporting_schedule')->nullable();
            $table->json('communication_protocol')->nullable();
            $table->json('contact_information')->nullable();
            $table->boolean('is_lead_investor')->default(false);
            $table->boolean('is_strategic_partner')->default(false);
            $table->boolean('requires_reporting')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamp('committed_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['developer_id', 'status']);
            $table->index(['project_id']);
            $table->index(['investor_type']);
            $table->index(['investment_type']);
            $table->index(['investment_amount']);
            $table->index(['commitment_date']);
            $table->index(['is_lead_investor']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_investors');
    }
};
