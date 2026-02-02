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
        if (!Schema::hasTable('influencer_campaigns')) {
        Schema::create('influencer_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('campaign_type'); // property_promotion, neighborhood_showcase, lifestyle_content, etc.
            $table->string('status')->default('pending'); // pending, active, completed, paused, cancelled
            $table->json('campaign_objectives');
            $table->json('target_audience');
            $table->json('platforms'); // instagram, youtube, tiktok, twitter, facebook, linkedin, snapchat
            $table->json('content_requirements');
            $table->json('influencer_requirements');
            $table->json('budget_details');
            $table->json('timeline');
            $table->json('deliverables');
            $table->json('legal_requirements');
            $table->json('campaign_assets')->nullable();
            $table->json('measurement_kpis');
            
            // Performance metrics
            $table->integer('total_influencers')->default(0);
            $table->decimal('total_budget', 10, 2)->default(0);
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->integer('total_content_pieces')->default(0);
            $table->bigInteger('total_reach')->default(0);
            $table->bigInteger('total_engagement')->default(0);
            $table->bigInteger('total_conversions')->default(0);
            $table->decimal('average_engagement_rate', 5, 2)->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->decimal('return_on_investment', 5, 2)->default(0);
            
            // Timestamps
            $table->timestamp('launched_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('resumed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['property_id', 'status']);
            $table->index(['campaign_type', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('launched_at');
            $table->index('completed_at');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('influencer_campaigns');
    }
};
