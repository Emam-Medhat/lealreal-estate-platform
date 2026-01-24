<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('set null');
            $table->string('application_number')->unique();
            $table->string('applicant_name');
            $table->string('applicant_email');
            $table->string('applicant_phone');
            $table->text('applicant_address');
            $table->decimal('applicant_income', 10, 2)->nullable();
            $table->string('applicant_employment')->nullable();
            $table->json('applicant_references')->nullable();
            $table->date('move_in_date');
            $table->integer('lease_duration')->default(12);
            $table->decimal('offered_rent', 10, 2);
            $table->text('special_requests')->nullable();
            $table->enum('status', ['pending', 'reviewing', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('screening_result', ['pending', 'passed', 'failed', 'conditional'])->default('pending');
            $table->datetime('screening_date')->nullable();
            $table->datetime('review_date')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('approval_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('rejection_date')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->json('documents')->nullable();
            $table->foreignId('lease_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['property_id', 'status']);
            $table->index(['tenant_id', 'status']);
            $table->index(['status', 'priority']);
            $table->index(['screening_result']);
            $table->index(['application_number']);
            $table->index(['applicant_email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_applications');
    }
};
