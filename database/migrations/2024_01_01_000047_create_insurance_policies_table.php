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
        Schema::create('insurance_policies', function (Blueprint $table) {
            $table->id();
            $table->string('policy_number')->unique();
            $table->foreignId('insurance_provider_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->enum('policy_type', ['property', 'liability', 'comprehensive', 'fire', 'flood', 'earthquake', 'theft', 'tenant', 'landlord', 'builders_risk', 'other'])->default('property');
            $table->enum('status', ['draft', 'active', 'suspended', 'expired', 'cancelled', 'renewed'])->default('draft');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('premium_amount', 10, 2);
            $table->decimal('coverage_amount', 10, 2);
            $table->decimal('deductible', 10, 2)->default(0);
            $table->enum('payment_frequency', ['monthly', 'quarterly', 'semi_annually', 'annually'])->default('monthly');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'credit_card', 'online', 'auto_debit'])->default('bank_transfer');
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('cancellation_reason_ar')->nullable();
            $table->date('cancellation_date')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->boolean('auto_renewal')->default(false);
            $table->text('renewal_terms')->nullable();
            $table->text('renewal_terms_ar')->nullable();
            $table->json('special_conditions')->nullable();
            $table->json('exclusions')->nullable();
            $table->json('coverage_details')->nullable();
            $table->json('risk_factors')->nullable();
            $table->json('premium_breakdown')->nullable();
            $table->json('documents')->nullable();
            $table->json('photos')->nullable();
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['policy_number']);
            $table->index(['insurance_provider_id']);
            $table->index(['property_id']);
            $table->index(['status']);
            $table->index(['policy_type']);
            $table->index(['start_date']);
            $table->index(['end_date']);
            $table->index(['premium_amount']);
            $table->index(['auto_renewal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_policies');
    }
};
