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
        Schema::create('appraisal_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appraisal_id')->constrained()->onDelete('cascade');
            $table->foreignId('appraiser_id')->constrained()->onDelete('cascade');
            $table->string('report_number')->unique();
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('executive_summary')->nullable();
            $table->text('executive_summary_ar')->nullable();
            $table->decimal('market_value', 15, 2);
            $table->decimal('replacement_cost', 15, 2)->nullable();
            $table->decimal('investment_value', 15, 2)->nullable();
            $table->decimal('liquidation_value', 15, 2)->nullable();
            $table->enum('valuation_method', ['sales_comparison', 'cost_approach', 'income_approach', 'hybrid']);
            $table->json('comparable_properties')->nullable();
            $table->json('adjustments')->nullable();
            $table->json('market_analysis')->nullable();
            $table->json('property_analysis')->nullable();
            $table->json('location_analysis')->nullable();
            $table->json('condition_assessment')->nullable();
            $table->json('risk_factors')->nullable();
            $table->json('recommendations')->nullable();
            $table->date('valuation_date');
            $table->date('report_date');
            $table->enum('status', ['draft', 'review', 'approved', 'rejected'])->default('draft');
            $table->text('notes')->nullable();
            $table->string('report_pdf')->nullable();
            $table->json('photos')->nullable();
            $table->json('supporting_documents')->nullable();
            $table->timestamps();
            
            $table->index(['appraisal_id', 'status']);
            $table->index(['appraiser_id']);
            $table->index(['valuation_date']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appraisal_reports');
    }
};
