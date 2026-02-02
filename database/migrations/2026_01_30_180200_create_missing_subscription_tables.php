<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create subscription_invoices table
        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->date('billing_date');
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['subscription_id', 'status']);
            $table->index(['status', 'due_date']);
        });

        // Create subscription_upgrades table
        Schema::create('subscription_upgrades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_plan_id')->constrained('subscription_plans')->onDelete('cascade');
            $table->foreignId('to_plan_id')->constrained('subscription_plans')->onDelete('cascade');
            $table->decimal('price_difference', 10, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->datetime('processed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['subscription_id', 'status']);
        });

        // Create subscription_usage table
        Schema::create('subscription_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->string('metric'); // api_calls, storage, etc.
            $table->integer('usage')->default(0);
            $table->integer('limit')->default(0);
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamps();
            
            $table->unique(['subscription_id', 'metric', 'period_start'], 'usage_unique');
            $table->index(['subscription_id', 'metric']);
        });

        // Create subscription_renewals table
        Schema::create('subscription_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'processed', 'failed'])->default('pending');
            $table->datetime('scheduled_at');
            $table->datetime('processed_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index(['subscription_id', 'status']);
            $table->index(['scheduled_at']);
        });

        // Create subscription_cancellations table
        Schema::create('subscription_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->enum('reason', ['not_used', 'too_expensive', 'found_alternative', 'technical_issues', 'other'])->nullable();
            $table->text('feedback')->nullable();
            $table->date('requested_at');
            $table->date('effective_at');
            $table->boolean('refund_requested')->default(false);
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'processed'])->default('pending');
            $table->timestamps();
            
            $table->index(['subscription_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_cancellations');
        Schema::dropIfExists('subscription_renewals');
        Schema::dropIfExists('subscription_usage');
        Schema::dropIfExists('subscription_upgrades');
        Schema::dropIfExists('subscription_invoices');
    }
};
