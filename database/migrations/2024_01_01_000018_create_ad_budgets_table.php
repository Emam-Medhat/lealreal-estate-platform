<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('ad_budgets')) {
        Schema::create('ad_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('ad_campaigns')->onDelete('cascade');
            $table->decimal('total_budget', 12, 2);
            $table->decimal('daily_budget', 10, 2);
            $table->decimal('remaining_budget', 12, 2);
            $table->decimal('daily_remaining', 10, 2);
            $table->decimal('spent_amount', 12, 2)->default(0);
            $table->decimal('daily_spent', 10, 2)->default(0);
            $table->enum('budget_type', ['standard', 'accelerated', 'limited'])->default('standard');
            $table->enum('delivery_method', ['standard', 'accelerated'])->default('standard');
            $table->boolean('auto_renew')->default(false);
            $table->decimal('renewal_amount', 12, 2)->nullable();
            $table->enum('renewal_trigger', ['exhausted', 'below_threshold'])->nullable();
            $table->decimal('spending_limit', 12, 2)->nullable();
            $table->enum('limit_type', ['daily', 'weekly', 'monthly', 'total'])->nullable();
            $table->decimal('alert_threshold', 5, 2)->nullable(); // percentage
            $table->enum('status', ['active', 'paused', 'exhausted', 'cancelled'])->default('active');
            $table->datetime('paused_at')->nullable();
            $table->datetime('exhausted_at')->nullable();
            $table->datetime('renewed_at')->nullable();
            $table->datetime('last_reset_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['campaign_id', 'status']);
            $table->index(['status']);
            $table->index(['created_at']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('ad_budgets');
    }
};
