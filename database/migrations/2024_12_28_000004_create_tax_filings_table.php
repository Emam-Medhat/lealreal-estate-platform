<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tax_filings')) {
        Schema::create('tax_filings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_tax_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('filing_type'); // annual, quarterly, amended
            $table->integer('tax_year');
            $table->string('status')->default('draft'); // draft, submitted, approved, rejected
            $table->date('submission_date')->nullable();
            $table->date('review_date')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_notes')->nullable();
            $table->decimal('approved_amount', 12, 2)->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['property_tax_id', 'tax_year']);
            $table->index('status');
            $table->index('submission_date');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_filings');
    }
};
