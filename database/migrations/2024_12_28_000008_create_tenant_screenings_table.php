<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tenant_screenings')) {
        Schema::create('tenant_screenings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('screening_number')->unique();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->datetime('screening_date')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->enum('credit_check', ['pending', 'clear', 'flagged', 'failed'])->default('pending');
            $table->enum('criminal_check', ['pending', 'clear', 'flagged', 'convicted'])->default('pending');
            $table->enum('employment_verification', ['pending', 'verified', 'unverified', 'partially_verified'])->default('pending');
            $table->enum('rental_history', ['pending', 'positive', 'negative', 'neutral'])->default('pending');
            $table->enum('background_check', ['pending', 'clear', 'flagged', 'failed'])->default('pending');
            $table->integer('credit_score')->nullable();
            $table->enum('criminal_status', ['clear', 'misdemeanor', 'felony', 'pending'])->nullable();
            $table->enum('employment_status', ['verified', 'unverified', 'self_employed', 'unemployed'])->nullable();
            $table->enum('rental_status', ['excellent', 'good', 'poor', 'eviction', 'pending'])->nullable();
            $table->enum('background_status', ['clear', 'flagged', 'failed', 'pending'])->nullable();
            $table->integer('overall_score')->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high'])->nullable();
            $table->enum('recommendation', ['approved', 'conditional', 'rejected', 'pending'])->default('pending');
            $table->text('screening_notes')->nullable();
            $table->boolean('documents_verified')->default(false);
            $table->boolean('references_checked')->default(false);
            $table->boolean('income_verified')->default(false);
            $table->boolean('identity_verified')->default(false);
            $table->string('screening_agency')->nullable();
            $table->string('report_reference')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['status', 'screening_date']);
            $table->index(['recommendation']);
            $table->index(['risk_level']);
            $table->index(['completed_at']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_screenings');
    }
};
