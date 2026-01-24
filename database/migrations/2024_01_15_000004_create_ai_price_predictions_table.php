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
        Schema::create('ai_price_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->date('prediction_date');
            $table->decimal('current_price', 15, 2);
            $table->decimal('predicted_price_1m', 15, 2);
            $table->decimal('predicted_price_3m', 15, 2);
            $table->decimal('predicted_price_6m', 15, 2);
            $table->decimal('predicted_price_1y', 15, 2);
            $table->decimal('confidence_score', 3, 2); // 0.00-1.00
            $table->decimal('accuracy_score', 5, 2)->nullable(); // 0.00-100.00
            $table->json('market_factors')->nullable();
            $table->string('prediction_model', 50);
            $table->json('historical_data')->nullable();
            $table->json('comparable_analysis')->nullable();
            $table->json('trend_analysis')->nullable();
            $table->json('risk_assessment')->nullable();
            $table->json('investment_recommendation')->nullable();
            $table->string('ai_model_version', 20);
            $table->json('prediction_metadata')->nullable();
            $table->string('status', 20)->default('completed');
            $table->decimal('actual_price_1m', 15, 2)->nullable();
            $table->decimal('actual_price_3m', 15, 2)->nullable();
            $table->decimal('actual_price_6m', 15, 2)->nullable();
            $table->decimal('actual_price_1y', 15, 2)->nullable();
            $table->decimal('accuracy_1m', 5, 2)->nullable(); // 0.00-100.00
            $table->decimal('accuracy_3m', 5, 2)->nullable(); // 0.00-100.00
            $table->decimal('accuracy_6m', 5, 2)->nullable(); // 0.00-100.00
            $table->decimal('accuracy_1y', 5, 2)->nullable(); // 0.00-100.00
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['property_id', 'prediction_date']);
            $table->index('user_id');
            $table->index('status');
            $table->index('prediction_date');
            $table->index('confidence_score');
            $table->index('prediction_model');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_price_predictions');
    }
};
