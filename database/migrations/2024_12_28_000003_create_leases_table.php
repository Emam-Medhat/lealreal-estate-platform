<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->string('lease_number')->unique();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('rent_amount', 10, 2);
            $table->decimal('security_deposit', 10, 2);
            $table->string('rent_frequency')->default('monthly'); // monthly, quarterly, annually
            $table->integer('payment_due_day')->default(1);
            $table->decimal('late_fee', 10, 2)->nullable();
            $table->string('late_fee_type')->nullable(); // fixed, percentage
            $table->string('status')->default('active'); // active, expired, terminated, suspended
            $table->text('terms_and_conditions');
            $table->text('special_terms')->nullable();
            $table->json('utilities_included')->nullable();
            $table->json('amenities_included')->nullable();
            $table->string('maintenance_responsibility');
            $table->boolean('renewal_option')->default(false);
            $table->text('renewal_terms')->nullable();
            $table->integer('termination_notice_days')->default(30);
            
            // Termination fields
            $table->string('termination_reason')->nullable();
            $table->date('termination_date')->nullable();
            $table->text('termination_notes')->nullable();
            
            // Suspension fields
            $table->string('suspension_reason')->nullable();
            $table->text('suspension_notes')->nullable();
            
            // Status change tracking
            $table->timestamp('activated_at')->nullable();
            $table->foreignId('activated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('terminated_at')->nullable();
            $table->foreignId('terminated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('suspended_at')->nullable();
            $table->foreignId('suspended_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resumed_at')->nullable();
            $table->foreignId('resumed_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['property_id', 'status']);
            $table->index(['tenant_id', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index('lease_number');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leases');
    }
};
