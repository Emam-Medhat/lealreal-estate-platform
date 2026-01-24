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
        Schema::create('property_marketing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('campaign_type'); // property_promotion, brand_awareness, lead_generation, etc.
            $table->string('status')->default('draft'); // draft, active, paused, completed
            $table->decimal('budget', 10, 2)->nullable();
            $table->string('currency', 3)->default('SAR');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('target_audience')->nullable();
            $table->json('marketing_channels')->nullable(); // social_media, email, search, display, etc.
            $table->json('content_strategy')->nullable();
            $table->json('creative_assets')->nullable();
            $table->json('performance_goals')->nullable();
            $table->json('tracking_settings')->nullable();
            $table->json('automation_settings')->nullable();
            $table->json('launch_settings')->nullable();
            
            // Performance metrics
            $table->bigInteger('total_impressions')->default(0);
            $table->bigInteger('total_clicks')->default(0);
            $table->bigInteger('total_conversions')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->decimal('cost_per_conversion', 10, 2)->nullable();
            $table->decimal('return_on_investment', 5, 2)->nullable();
            $table->decimal('total_spent', 10, 2)->default(0);
            
            // Timestamps
            $table->timestamp('launched_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('resumed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['property_id', 'status']);
            $table->index(['campaign_type', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_marketing');
    }
};
