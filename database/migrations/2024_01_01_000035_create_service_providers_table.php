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
        Schema::create('service_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('contact_person');
            $table->string('contact_person_ar')->nullable();
            $table->string('phone');
            $table->string('email')->unique();
            $table->string('address');
            $table->string('address_ar')->nullable();
            $table->string('city');
            $table->string('city_ar')->nullable();
            $table->string('state');
            $table->string('state_ar')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country');
            $table->string('country_ar')->nullable();
            $table->string('website')->nullable();
            $table->string('specialization');
            $table->string('specialization_ar')->nullable();
            $table->json('services')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('total_requests')->default(0);
            $table->decimal('average_response_time', 5, 2)->nullable();
            $table->decimal('average_completion_time', 5, 2)->nullable();
            $table->decimal('success_rate', 5, 2)->default(0);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->string('license_number')->nullable();
            $table->date('license_expiry')->nullable();
            $table->string('insurance_number')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->json('working_hours')->nullable();
            $table->json('service_areas')->nullable();
            $table->json('certifications')->nullable();
            $table->json('attachments')->nullable();
            $table->string('logo')->nullable();
            $table->json('photos')->nullable();
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['status']);
            $table->index(['specialization']);
            $table->index(['is_verified']);
            $table->index(['is_featured']);
            $table->index(['rating']);
            $table->index(['city']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_providers');
    }
};
