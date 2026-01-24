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
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('rental_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('lease_number')->unique();
            $table->enum('status', ['draft', 'active', 'expired', 'terminated', 'suspended', 'renewed', 'pending'])->default('draft');
            $table->enum('lease_type', ['residential', 'commercial', 'industrial', 'retail', 'office', 'storage', 'parking', 'mixed'])->default('residential');
            $table->string('unit_number');
            $table->decimal('rent_amount', 10, 2);
            $table->decimal('security_deposit', 10, 2)->nullable();
            $table->decimal('pet_deposit', 10, 2)->default(0);
            $table->decimal('key_deposit', 10, 2)->default(0);
            $table->decimal('other_deposits', 10, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('lease_term_months');
            $table->enum('payment_frequency', ['monthly', 'quarterly', 'semi_annually', 'annually', 'bi_monthly'])->default('monthly');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'credit_card', 'online', 'auto_debit'])->default('bank_transfer');
            $table->date('payment_due_day')->nullable();
            $table->decimal('late_fee', 10, 2)->default(0);
            $table->decimal('late_fee_percentage', 5, 2)->default(0);
            $table->integer('grace_period_days')->default(0);
            $table->boolean('utilities_included')->default(false);
            $table->json('included_utilities')->nullable();
            $table->decimal('utilities_cost', 10, 2)->default(0);
            $table->json('amenities')->nullable();
            $table->json('restrictions')->nullable();
            $table->json('pet_policy')->nullable();
            $table->json('smoking_policy')->nullable();
            $table->json('maintenance_responsibilities')->nullable();
            $table->json('property_rules')->nullable();
            $table->json('common_areas')->nullable();
            $table->json('parking_spaces')->nullable();
            $table->json('storage_units')->nullable();
            $table->json('furniture_items')->nullable();
            $table->json('appliances')->nullable();
            $table->json('fixtures')->nullable();
            $table->json('condition_report')->nullable();
            $table->json('move_in_condition')->nullable();
            $table->json('move_out_condition')->nullable();
            $table->json('photos_move_in')->nullable();
            $table->json('photos_move_out')->nullable();
            $table->json('documents')->nullable();
            $table->json('attachments')->nullable();
            $table->text('special_terms')->nullable();
            $table->text('special_terms_ar')->nullable();
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable();
            $table->date('signed_date')->nullable();
            $table->date('effective_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->text('termination_reason')->nullable();
            $table->text('termination_reason_ar')->nullable();
            $table->enum('termination_type', ['mutual', 'landlord', 'tenant', 'breach', 'expiry', 'other'])->nullable();
            $table->decimal('termination_fee', 10, 2)->default(0);
            $table->json('termination_conditions')->nullable();
            $table->boolean('auto_renewal')->default(false);
            $table->integer('renewal_notice_days')->default(30);
            $table->decimal('renewal_rent_increase', 5, 2)->default(0);
            $table->json('renewal_terms')->nullable();
            $table->boolean('subletting_allowed')->default(false);
            $table->json('subletting_terms')->nullable();
            $table->boolean('assignment_allowed')->default(false);
            $table->json('assignment_terms')->nullable();
            $table->json('guarantors')->nullable();
            $table->json('co_tenants')->nullable();
            $table->json('emergency_contacts')->nullable();
            $table->json('insurance_requirements')->nullable();
            $table->json('compliance_requirements')->nullable();
            $table->json('regulatory_references')->nullable();
            $table->boolean('is_template')->default(false);
            $table->string('template_name')->nullable();
            $table->json('template_variables')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['property_id']);
            $table->index(['rental_id']);
            $table->index(['tenant_id']);
            $table->index(['lease_number']);
            $table->index(['status']);
            $table->index(['lease_type']);
            $table->index(['start_date']);
            $table->index(['end_date']);
            $table->index(['rent_amount']);
            $table->index(['is_template']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leases');
    }
};
