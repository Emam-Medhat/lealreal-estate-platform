<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the existing incorrect subscriptions table
        Schema::dropIfExists('subscriptions');
        
        // Create the correct subscriptions table
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('subscription_plans')->onDelete('cascade');
            $table->enum('status', ['pending', 'active', 'expired', 'cancelled', 'suspended'])->default('pending');
            $table->datetime('starts_at')->nullable();
            $table->datetime('ends_at')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('billing_cycle', 20); // monthly, yearly, etc.
            $table->boolean('auto_renew')->default(true);
            $table->string('payment_method')->nullable(); // stripe, paypal, bank_transfer
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->datetime('activated_at')->nullable();
            $table->datetime('cancelled_at')->nullable();
            $table->datetime('upgraded_at')->nullable();
            $table->datetime('last_renewed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['plan_id', 'status']);
            $table->index(['status', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
