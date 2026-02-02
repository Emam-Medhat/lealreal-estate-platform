<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tenants')) {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('national_id')->unique();
            $table->date('date_of_birth');
            $table->text('address');
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone');
            $table->string('employment_status');
            $table->string('employer_name')->nullable();
            $table->decimal('monthly_income', 10, 2);
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->json('references')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('active'); // active, inactive, blacklisted
            $table->boolean('verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Blacklist fields
            $table->boolean('blacklisted')->default(false);
            $table->string('blacklist_reason')->nullable();
            $table->text('blacklist_notes')->nullable();
            $table->timestamp('blacklisted_at')->nullable();
            $table->foreignId('blacklisted_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Screening fields
            $table->string('screening_status')->default('pending'); // pending, passed, failed
            $table->timestamp('screening_completed_at')->nullable();
            
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['status', 'verified']);
            $table->index(['blacklisted']);
            $table->index(['screening_status']);
            $table->index('email');
            $table->index('national_id');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
