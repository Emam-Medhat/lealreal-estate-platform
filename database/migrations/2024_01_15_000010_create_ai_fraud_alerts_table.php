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
        if (!Schema::hasTable('ai_fraud_alerts')) {
        Schema::create('ai_fraud_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('alert_type', 50);
            $table->string('risk_level', 20);
            $table->decimal('confidence_score', 3, 2); // 0.00-1.00
            $table->text('description');
            $table->json('evidence')->nullable();
            $table->string('detection_method', 50);
            $table->json('fraud_indicators')->nullable();
            $table->json('risk_factors')->nullable();
            $table->json('affected_parties')->nullable();
            $table->json('financial_impact')->nullable();
            $table->json('timeline')->nullable();
            $table->string('investigation_status', 30)->default('pending');
            $table->foreignId('investigator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('investigation_notes')->nullable();
            $table->string('resolution_status', 30)->default('pending');
            $table->json('resolution_details')->nullable();
            $table->json('preventive_measures')->nullable();
            $table->string('ai_model_version', 20);
            $table->json('detection_metadata')->nullable();
            $table->decimal('processing_time', 8, 3); // in seconds
            $table->boolean('false_positive')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->boolean('escalated')->default(false);
            $table->timestamp('escalated_at')->nullable();
            $table->foreignId('escalated_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['property_id', 'alert_type']);
            $table->index('user_id');
            $table->index('alert_type');
            $table->index('risk_level');
            $table->index('investigation_status');
            $table->index('resolution_status');
            $table->index('investigator_id');
            $table->index('verified_by');
            $table->index('escalated');
            $table->index('false_positive');
            $table->index('confidence_score');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_fraud_alerts');
    }
};
