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
        if (!Schema::hasTable('ai_lead_scores')) {
        Schema::create('ai_lead_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('scoring_model', 50);
            $table->decimal('total_score', 5, 2); // 0.00-100.00
            $table->decimal('demographic_score', 5, 2); // 0.00-100.00
            $table->decimal('behavioral_score', 5, 2); // 0.00-100.00
            $table->decimal('engagement_score', 5, 2); // 0.00-100.00
            $table->decimal('source_quality_score', 5, 2); // 0.00-100.00
            $table->decimal('timing_score', 5, 2); // 0.00-100.00
            $table->decimal('budget_score', 5, 2); // 0.00-100.00
            $table->decimal('property_match_score', 5, 2); // 0.00-100.00
            $table->json('scoring_factors')->nullable();
            $table->json('risk_assessment')->nullable();
            $table->decimal('conversion_probability', 3, 2); // 0.00-1.00
            $table->string('lead_quality_level', 20);
            $table->string('priority_level', 20);
            $table->json('recommended_actions')->nullable();
            $table->json('next_best_action')->nullable();
            $table->json('optimal_contact_time')->nullable();
            $table->string('ai_model_version', 20);
            $table->json('scoring_metadata')->nullable();
            $table->decimal('confidence_level', 3, 2); // 0.00-1.00
            $table->string('status', 20)->default('active');
            $table->timestamp('last_scored_at');
            $table->json('score_history')->nullable();
            $table->json('improvement_suggestions')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['lead_id', 'scoring_model']);
            $table->index('user_id');
            $table->index('total_score');
            $table->index('lead_quality_level');
            $table->index('priority_level');
            $table->index('status');
            $table->index('last_scored_at');
            $table->index('conversion_probability');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_lead_scores');
    }
};
