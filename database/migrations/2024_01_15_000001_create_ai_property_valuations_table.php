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
        Schema::create('ai_property_valuations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->date('valuation_date');
            $table->decimal('estimated_value', 15, 2);
            $table->tinyInteger('confidence_score')->unsigned(); // 0-100
            $table->string('valuation_method', 50);
            $table->json('market_analysis')->nullable();
            $table->json('comparable_properties')->nullable();
            $table->json('adjustment_factors')->nullable();
            $table->text('final_recommendation')->nullable();
            $table->string('ai_model_version', 20);
            $table->json('valuation_metadata')->nullable();
            $table->string('status', 20)->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['property_id', 'valuation_date']);
            $table->index('user_id');
            $table->index('status');
            $table->index('valuation_date');
            $table->index('confidence_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_property_valuations');
    }
};
