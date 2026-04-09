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
        if (!Schema::hasTable('agent_performance_metrics')) {
        Schema::create('agent_performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('agents')->onDelete('cascade');
            $table->string('metric_type'); // total_sales, commission_earned, etc.
            $table->decimal('value', 12, 2);
            $table->string('period'); // daily, weekly, monthly, yearly
            $table->date('period_start');
            $table->date('period_end');
            $table->json('breakdown')->nullable(); // detailed breakdown
            $table->timestamps();
            
            $table->unique(['agent_id', 'metric_type', 'period', 'period_start'], 'agent_perf_metrics_unique');
            $table->index(['agent_id', 'metric_type']);
            $table->index(['period', 'period_start']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_performance_metrics');
    }
};
