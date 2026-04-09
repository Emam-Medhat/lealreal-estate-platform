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
        if (!Schema::hasTable('rentals')) {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('lease_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('rental_number')->unique();
            $table->enum('status', ['available', 'occupied', 'maintenance', 'unavailable', 'reserved', 'pending'])->default('available');
            $table->enum('rental_type', ['residential', 'commercial', 'industrial', 'retail', 'office', 'storage', 'parking', 'other'])->default('residential');
            $table->decimal('rent_amount', 10, 2);
            $table->decimal('security_deposit', 10, 2)->nullable();
            $table->decimal('late_fee', 10, 2)->default(0);
            $table->decimal('late_fee_percentage', 5, 2)->default(0);
            $table->date('available_from');
            $table->date('available_to')->nullable();
            $table->date('rent_start_date')->nullable();
            $table->date('rent_end_date')->nullable();
            $table->enum('payment_frequency', ['monthly', 'quarterly', 'semi_annually', 'annually', 'bi_monthly'])->default('monthly');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'credit_card', 'online', 'auto_debit'])->default('bank_transfer');
            $table->boolean('utilities_included')->default(false);
            $table->json('included_utilities')->nullable();
            $table->decimal('utilities_cost', 10, 2)->default(0);
            $table->json('amenities')->nullable();
            $table->json('restrictions')->nullable();
            $table->json('pet_policy')->nullable();
            $table->json('smoking_policy')->nullable();
            $table->integer('min_lease_term')->default(1);
            $table->integer('max_lease_term')->nullable();
            $table->boolean('furnished')->default(false);
            $table->json('furniture_items')->nullable();
            $table->json('appliances')->nullable();
            $table->json('photos')->nullable();
            $table->json('documents')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->json('features')->nullable();
            $table->json('nearby_amenities')->nullable();
            $table->json('transportation')->nullable();
            $table->string('listing_title')->nullable();
            $table->string('listing_description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('views_count')->default(0);
            $table->integer('inquiries_count')->default(0);
            $table->json('application_requirements')->nullable();
            $table->json('tenant_preferences')->nullable();
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['property_id']);
            $table->index(['tenant_id']);
            $table->index(['lease_id']);
            $table->index(['status']);
            $table->index(['rental_type']);
            $table->index(['available_from']);
            $table->index(['rent_amount']);
            $table->index(['is_featured']);
            $table->index(['is_active']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
