<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('agent_performance')) {
        Schema::create('agent_performance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->integer('properties_sold')->default(0);
            $table->integer('properties_rented')->default(0);
            $table->decimal('total_sales_value', 15, 2)->default(0);
            $table->decimal('total_commission', 15, 2)->default(0);
            $table->integer('leads_generated')->default(0);
            $table->integer('appointments_set')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->integer('average_days_on_market')->default(0);
            $table->decimal('customer_satisfaction', 3, 2)->default(0);
            $table->year('year');
            $table->tinyInteger('month');
            $table->timestamps();
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_performance');
    }
};
