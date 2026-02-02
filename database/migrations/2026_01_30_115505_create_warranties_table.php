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
            $table->string('warranty_number')->unique();
            $table->string('warranty_code')->unique()->nullable();
            $table->string('warranty_type'); // product, labor, combined, extended
            $table->string('title');
            $table->text('description');
            $table->text('coverage_details');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_provider_id')->nullable()->constrained()->onDelete('set null');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration_months');
            $table->decimal('coverage_amount', 15, 2);
            $table->decimal('deductible_amount', 15, 2)->nullable();
            $table->text('terms_conditions')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('notes')->nullable();
            
            // Status and timestamps
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Extension fields
            $table->integer('extension_months')->nullable();
            $table->decimal('extension_cost', 15, 2)->nullable();
            $table->timestamp('extended_at')->nullable();
            $table->foreignId('extended_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('extension_notes')->nullable();
            
            // Expiry fields
            $table->timestamp('expired_at')->nullable();
            $table->text('expiry_reason')->nullable();
            $table->foreignId('expired_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Reactivation fields
            $table->timestamp('reactivated_at')->nullable();
            $table->foreignId('reactivated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['status', 'end_date']);
            $table->index(['property_id']);
            $table->index(['service_provider_id']);
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
