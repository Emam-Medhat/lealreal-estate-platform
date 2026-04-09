<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('capital_gains_taxes')) {
        Schema::create('capital_gains_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('buyer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('purchase_price', 12, 2);
            $table->decimal('sale_price', 12, 2);
            $table->date('purchase_date');
            $table->date('sale_date');
            $table->decimal('improvement_costs', 12, 2)->default(0);
            $table->decimal('selling_costs', 12, 2)->default(0);
            $table->decimal('gain_amount', 12, 2);
            $table->decimal('tax_rate', 5, 2);
            $table->decimal('tax_amount', 12, 2);
            $table->string('status')->default('pending'); // pending, paid, exempt
            $table->date('paid_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['property_id', 'seller_id']);
            $table->index('status');
            $table->index(['purchase_date', 'sale_date']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('capital_gains_taxes');
    }
};
