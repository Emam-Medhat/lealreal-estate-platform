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
        Schema::create('insurance_providers', function (Blueprint $table) {
            $table->id();
            $table->string('provider_code')->unique();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->enum('provider_type', ['company', 'broker', 'agent', 'underwriter', 'reinsurer'])->default('company');
            $table->enum('status', ['active', 'inactive', 'suspended', 'blacklisted'])->default('active');
            $table->string('license_number')->unique();
            $table->date('license_expiry');
            $table->string('registration_number')->unique();
            $table->string('tax_id')->unique();
            $table->string('phone');
            $table->string('phone_secondary')->nullable();
            $table->string('email');
            $table->string('email_support')->nullable();
            $table->string('website')->nullable();
            $table->string('address');
            $table->string('address_ar')->nullable();
            $table->string('city');
            $table->string('city_ar')->nullable();
            $table->string('state');
            $table->string('state_ar')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country');
            $table->string('country_ar')->nullable();
            $table->string('contact_person');
            $table->string('contact_person_title')->nullable();
            $table->string('contact_person_phone');
            $table->string('contact_person_email');
            $table->json('services_offered')->nullable();
            $table->json('coverage_types')->nullable();
            $table->json('specializations')->nullable();
            $table->json('regions_served')->nullable();
            $table->decimal('min_premium', 10, 2)->nullable();
            $table->decimal('max_coverage', 10, 2)->nullable();
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->enum('payment_terms', ['monthly', 'quarterly', 'semi_annually', 'annually'])->default('monthly');
            $table->integer('claims_processing_days')->default(30);
            $table->decimal('customer_satisfaction', 3, 2)->nullable();
            $table->decimal('financial_rating', 3, 2)->nullable();
            $table->string('rating_agency')->nullable();
            $table->date('rating_date')->nullable();
            $table->json('accreditations')->nullable();
            $table->json('certifications')->nullable();
            $table->json('awards')->nullable();
            $table->integer('years_in_business')->nullable();
            $table->integer('policies_issued')->default(0);
            $table->decimal('total_premiums', 15, 2)->default(0);
            $table->decimal('total_claims_paid', 15, 2)->default(0);
            $table->decimal('claims_ratio', 5, 2)->nullable();
            $table->json('key_personnel')->nullable();
            $table->json('branch_offices')->nullable();
            $table->json('partners')->nullable();
            $table->json('technology_platforms')->nullable();
            $table->json('api_integrations')->nullable();
            $table->json('documents')->nullable();
            $table->json('photos')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('recommended')->default(false);
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['provider_code']);
            $table->index(['name']);
            $table->index(['status']);
            $table->index(['provider_type']);
            $table->index(['license_number']);
            $table->index(['verified']);
            $table->index(['featured']);
            $table->index(['recommended']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_providers');
    }
};
