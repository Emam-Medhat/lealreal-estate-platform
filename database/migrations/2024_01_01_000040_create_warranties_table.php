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
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            $table->string('warranty_code')->unique();
            $table->string('warranty_number')->unique();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_provider_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('warranty_type', ['product', 'service', 'workmanship', 'extended', 'manufacturer', 'structural', 'other'])->default('service');
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description');
            $table->text('description_ar')->nullable();
            $table->text('coverage_details');
            $table->text('coverage_details_ar')->nullable();
            $table->json('covered_items')->nullable();
            $table->json('exclusions')->nullable();
            $table->json('conditions')->nullable();
            $table->json('limitations')->nullable();
            $table->enum('status', ['active', 'expired', 'suspended', 'revoked', 'claimed'])->default('active');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('expiry_date');
            $table->integer('duration_months')->nullable();
            $table->boolean('is_transferable')->default(false);
            $table->decimal('coverage_amount', 10, 2)->nullable();
            $table->decimal('deductible', 10, 2)->default(0);
            $table->string('provider_name');
            $table->string('provider_phone')->nullable();
            $table->string('provider_email')->nullable();
            $table->string('provider_address')->nullable();
            $table->string('claim_phone')->nullable();
            $table->string('claim_email')->nullable();
            $table->string('claim_website')->nullable();
            $table->json('claim_procedure')->nullable();
            $table->json('required_documents')->nullable();
            $table->json('maintenance_requirements')->nullable();
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->json('maintenance_records')->nullable();
            $table->json('attachments')->nullable();
            $table->json('photos')->nullable();
            $table->string('certificate_file')->nullable();
            $table->string('policy_file')->nullable();
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['warranty_code']);
            $table->index(['warranty_number']);
            $table->index(['property_id']);
            $table->index(['service_provider_id']);
            $table->index(['warranty_type']);
            $table->index(['status']);
            $table->index(['start_date']);
            $table->index(['end_date']);
            $table->index(['expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranties');
    }
};
