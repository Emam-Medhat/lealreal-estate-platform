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
        if (!Schema::hasTable('listing_promotions')) {
        Schema::create('listing_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('promotion_type'); // featured, premium, urgent, open_house, etc.
            $table->string('status')->default('draft'); // draft, promoted, paused, expired
            $table->string('priority_level')->default('medium'); // low, medium, high, urgent
            $table->decimal('budget', 10, 2)->nullable();
            $table->string('currency', 3)->default('SAR');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('target_regions')->nullable();
            $table->json('target_audience')->nullable();
            $table->json('promotion_channels')->nullable(); // website, social_media, email, search, etc.
            $table->json('ad_copy')->nullable();
            $table->json('creative_assets')->nullable();
            $table->json('bidding_strategy')->nullable();
            $table->json('placement_settings')->nullable();
            $table->json('optimization_goals')->nullable();
            $table->json('tracking_settings')->nullable();
            $table->json('boost_settings')->nullable();
            
            // Performance metrics
            $table->bigInteger('total_impressions')->default(0);
            $table->bigInteger('total_clicks')->default(0);
            $table->bigInteger('total_views')->default(0);
            $table->bigInteger('total_inquiries')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->decimal('click_through_rate', 5, 2)->default(0);
            $table->decimal('cost_per_click', 10, 2)->nullable();
            $table->decimal('cost_per_inquiry', 10, 2)->nullable();
            $table->decimal('return_on_investment', 5, 2)->nullable();
            $table->decimal('total_spent', 10, 2)->default(0);
            
            // Timestamps
            $table->timestamp('promoted_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('resumed_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['property_id', 'status']);
            $table->index(['promotion_type', 'status']);
            $table->index(['priority_level', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('start_date');
            $table->index('end_date');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_promotions');
    }
};
