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
        if (!Schema::hasTable('service_providers')) {
        Schema::create('service_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->enum('type', ['individual', 'company'])->default('individual');
            $table->enum('specialization', ['general', 'electrical', 'plumbing', 'hvac', 'structural', 'painting', 'landscaping', 'cleaning', 'security', 'other'])->default('general');
            $table->text('description')->nullable();
            $table->decimal('rating', 3, 2)->default(0); // 0.00 to 5.00
            $table->integer('total_jobs')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->string('license_number')->nullable();
            $table->date('license_expiry')->nullable();
            $table->string('insurance_number')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->json('working_hours')->nullable(); // JSON with working schedule
            $table->json('services_offered')->nullable(); // JSON array of services
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_active', 'specialization']);
            $table->index(['is_verified', 'rating']);
            $table->index(['type', 'is_active']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_providers');
    }
};
