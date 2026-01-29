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
        Schema::create('property_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 15, 2);
            $table->enum('price_type', ['sale', 'rent', 'lease', 'auction'])->default('sale');
            $table->enum('currency', ['USD', 'EUR', 'GBP', 'AED', 'SAR', 'EGP'])->default('USD');
            $table->decimal('original_price', 15, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->decimal('price_per_sqft', 10, 2)->nullable();
            $table->decimal('price_per_sqm', 10, 2)->nullable();
            $table->enum('price_status', ['active', 'inactive', 'negotiable', 'sold', 'rented'])->default('active');
            $table->date('effective_date')->default(now());
            $table->date('expiry_date')->nullable();
            $table->text('price_notes')->nullable();
            $table->json('price_history')->nullable(); // JSON array of price changes
            $table->boolean('is_featured')->default(false);
            $table->foreignId('set_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['property_id', 'price_status']);
            $table->index(['price_type', 'currency']);
            $table->index(['effective_date']);
            $table->index(['is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_prices');
    }
};
