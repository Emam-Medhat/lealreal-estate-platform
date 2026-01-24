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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('tenant_number')->unique();
            $table->string('first_name');
            $table->string('first_name_ar')->nullable();
            $table->string('last_name');
            $table->string('last_name_ar')->nullable();
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('phone_secondary')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('birth_date')->nullable();
            $table->string('nationality');
            $table->string('national_id')->unique();
            $table->string('passport_number')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('visa_number')->nullable();
            $table->date('visa_expiry')->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->integer('family_members')->default(0);
            $table->integer('children_count')->default(0);
            $table->json('emergency_contacts')->nullable();
            $table->string('current_address');
            $table->string('current_address_ar')->nullable();
            $table->string('permanent_address')->nullable();
            $table->string('permanent_address_ar')->nullable();
            $table->string('city');
            $table->string('city_ar')->nullable();
            $table->string('state');
            $table->string('state_ar')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country');
            $table->string('country_ar')->nullable();
            $table->enum('employment_status', ['employed', 'self_employed', 'unemployed', 'student', 'retired', 'other'])->nullable();
            $table->string('employer_name')->nullable();
            $table->string('job_title')->nullable();
            $table->string('job_title_ar')->nullable();
            $table->string('work_phone')->nullable();
            $table->string('work_address')->nullable();
            $table->decimal('monthly_income', 10, 2)->nullable();
            $table->string('income_source')->nullable();
            $table->json('income_documents')->nullable();
            $table->enum('housing_status', ['renting', 'owning', 'living_with_family', 'other'])->nullable();
            $table->integer('previous_rental_duration')->nullable();
            $table->json('previous_landlords')->nullable();
            $table->json('rental_history')->nullable();
            $table->enum('screening_status', ['pending', 'in_progress', 'passed', 'failed', 'not_required'])->default('not_required');
            $table->timestamp('screening_completed_at')->nullable();
            $table->decimal('credit_score', 5, 2)->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high', 'very_high'])->nullable();
            $table->json('background_check')->nullable();
            $table->json('criminal_check')->nullable();
            $table->json('employment_verification')->nullable();
            $table->json('rental_verification')->nullable();
            $table->json('reference_checks')->nullable();
            $table->boolean('verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->boolean('blacklisted')->default(false);
            $table->text('blacklist_reason')->nullable();
            $table->timestamp('blacklisted_at')->nullable();
            $table->boolean('active')->default(true);
            $table->json('preferences')->nullable();
            $table->json('requirements')->nullable();
            $table->json('special_needs')->nullable();
            $table->json('pets')->nullable();
            $table->json('vehicles')->nullable();
            $table->json('documents')->nullable();
            $table->json('photos')->nullable();
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable();
            $table->foreignId('current_lease_id')->nullable()->constrained('rentals')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['user_id']);
            $table->index(['tenant_number']);
            $table->index(['email']);
            $table->index(['phone']);
            $table->index(['national_id']);
            $table->index(['screening_status']);
            $table->index(['verified']);
            $table->index(['blacklisted']);
            $table->index(['active']);
            $table->index(['current_lease_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
