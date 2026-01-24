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
        Schema::create('performance_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->string('agent_id');
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->decimal('total_commission', 15, 2)->default(0);
            $table->integer('properties_listed')->default(0);
            $table->integer('properties_sold')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->decimal('average_sale_price', 15, 2)->default(0);
            $table->decimal('customer_satisfaction', 5, 2)->nullable();
            $table->integer('leads_generated')->default(0);
            $table->integer('appointments_scheduled')->default(0);
            $table->json('monthly_performance')->nullable();
            $table->json('performance_metrics')->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamps();

            $table->index(['agent_id', 'period_start', 'period_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_reports');
    }
};
