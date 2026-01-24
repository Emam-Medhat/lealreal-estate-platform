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
        Schema::create('sales_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->decimal('total_commission', 15, 2)->default(0);
            $table->integer('properties_sold')->default(0);
            $table->decimal('average_sale_price', 15, 2)->default(0);
            $table->decimal('average_days_on_market', 8, 2)->default(0);
            $table->json('sales_by_agent')->nullable();
            $table->json('sales_by_property_type')->nullable();
            $table->json('sales_by_location')->nullable();
            $table->json('monthly_sales')->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamps();

            $table->index(['period_start', 'period_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_reports');
    }
};
