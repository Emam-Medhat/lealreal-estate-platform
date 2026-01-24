<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_predictions', function (Blueprint $table) {
            $table->id();
            $table->string('prediction_type', 50);
            $table->string('time_horizon', 20);
            $table->string('model_type', 50);
            $table->decimal('predicted_value', 12, 2);
            $table->decimal('confidence_score', 5, 2);
            $table->json('features')->nullable();
            $table->json('model_data')->nullable();
            $table->decimal('actual_value', 12, 2)->nullable();
            $table->decimal('accuracy', 5, 2)->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamps();
            
            $table->index(['prediction_type', 'status']);
            $table->index('model_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_predictions');
    }
};
