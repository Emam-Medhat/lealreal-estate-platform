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
        if (!Schema::hasTable('developer_contractors')) {
        Schema::create('developer_contractors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->string('company_name_ar')->nullable();
            $table->string('license_number')->unique();
            $table->string('commercial_register')->unique();
            $table->enum('contractor_type', ['general', 'specialized', 'subcontractor', 'consultant', 'supplier']);
            $table->enum('specialization', ['civil', 'electrical', 'mechanical', 'plumbing', 'fire_protection', 'hvac', 'finishing', 'landscaping', 'structural', 'architectural']);
            $table->enum('status', ['pending', 'active', 'suspended', 'inactive', 'blacklisted'])->default('pending');
            $table->enum('classification', ['class_a', 'class_b', 'class_c', 'class_d', 'class_e']);
            $table->string('contact_person');
            $table->string('phone');
            $table->string('email')->unique();
            $table->json('address');
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->year('established_year')->nullable();
            $table->integer('employees_count')->default(0);
            $table->integer('engineers_count')->default(0);
            $table->integer('technicians_count')->default(0);
            $table->decimal('min_project_value', 15, 2)->nullable();
            $table->decimal('max_project_value', 15, 2)->nullable();
            $table->string('currency')->default('SAR');
            $table->json('services_offered')->nullable();
            $table->json('expertise_areas')->nullable();
            $table->json('previous_projects')->nullable();
            $table->json('current_projects')->nullable();
            $table->json('certifications')->nullable();
            $table->json('licenses')->nullable();
            $table->json('insurance_policies')->nullable();
            $table->json('bank_guarantees')->nullable();
            $table->json('financial_statements')->nullable();
            $table->json('technical_capabilities')->nullable();
            $table->json('equipment_fleet')->nullable();
            $table->json('quality_standards')->nullable();
            $table->json('safety_records')->nullable();
            $table->json('environmental_compliance')->nullable();
            $table->json('performance_history')->nullable();
            $table->json('client_references')->nullable();
            $table->json('partners')->nullable();
            $table->json('suppliers')->nullable();
            $table->json('awards')->nullable();
            $table->json('media_gallery')->nullable();
            $table->string('company_logo')->nullable();
            $table->json('contact_information')->nullable();
            $table->json('working_hours')->nullable();
            $table->json('payment_terms')->nullable();
            $table->json('warranty_policies')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            $table->integer('completed_projects')->default(0);
            $table->integer('ongoing_projects')->default(0);
            $table->decimal('total_value_completed', 15, 2)->default(0);
            $table->decimal('total_value_ongoing', 15, 2)->default(0);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_preferred')->default(false);
            $table->boolean('is_blacklisted')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['developer_id', 'status']);
            $table->index(['contractor_type']);
            $table->index(['specialization']);
            $table->index(['classification']);
            $table->index(['rating']);
            $table->index(['is_verified']);
            $table->index(['is_preferred']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_contractors');
    }
};
