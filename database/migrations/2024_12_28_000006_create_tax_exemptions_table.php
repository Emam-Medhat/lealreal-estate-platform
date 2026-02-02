<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tax_exemptions')) {
        Schema::create('tax_exemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_tax_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('exemption_type'); // senior_citizen, disability, veteran, primary_residence, agricultural, charitable
            $table->decimal('exemption_amount', 12, 2);
            $table->decimal('approved_amount', 12, 2)->nullable();
            $table->text('reason');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->date('application_date');
            $table->date('approved_date')->nullable();
            $table->date('rejected_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['property_tax_id', 'status']);
            $table->index('exemption_type');
            $table->index('application_date');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_exemptions');
    }
};
