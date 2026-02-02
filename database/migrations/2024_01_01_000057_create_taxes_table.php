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
        if (!Schema::hasTable('taxes')) {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->enum('tax_type', ['property', 'income', 'capital_gains', 'vat', 'stamp_duty', 'municipality', 'other'])->default('property');
            $table->decimal('rate', 8, 4)->default(0);
            $table->boolean('is_active')->default(true);
            $table->date('effective_date');
            $table->date('expiry_date')->nullable();
            $table->json('applicable_to')->nullable();
            $table->json('exemptions')->nullable();
            $table->json('calculation_rules')->nullable();
            $table->json('payment_terms')->nullable();
            $table->json('filing_requirements')->nullable();
            $table->json('penalty_rules')->nullable();
            $table->json('reporting_requirements')->nullable();
            $table->json('compliance_rules')->nullable();
            $table->json('jurisdiction')->nullable();
            $table->json('tax_codes')->nullable();
            $table->json('forms')->nullable();
            $table->json('deadlines')->nullable();
            $table->json('thresholds')->nullable();
            $table->json('brackets')->nullable();
            $table->json('deductions')->nullable();
            $table->json('credits')->nullable();
            $table->json('adjustments')->nullable();
            $table->json('notes')->nullable();
            $table->json('notes_ar')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['tax_type']);
            $table->index(['is_active']);
            $table->index(['effective_date']);
            $table->index(['expiry_date']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
