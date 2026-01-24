<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vat_records', function (Blueprint $table) {
            $table->id();
            $table->string('period'); // Q1-2024, Q2-2024, etc.
            $table->decimal('taxable_sales', 12, 2);
            $table->decimal('taxable_purchases', 12, 2);
            $table->decimal('vat_rate', 5, 2);
            $table->decimal('vat_collected', 12, 2);
            $table->decimal('vat_paid', 12, 2);
            $table->decimal('vat_payable', 12, 2);
            $table->string('status')->default('pending'); // pending, payable, refundable, submitted, paid
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('reference_number')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['period', 'status']);
            $table->index('user_id');
            $table->index('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vat_records');
    }
};
