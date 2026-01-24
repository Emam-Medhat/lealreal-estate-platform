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
        Schema::create('retargeting_audiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('audience_type'); // website_visitors, property_viewers, cart_abandoners, etc.
            $table->string('platform'); // google_ads, facebook, instagram, linkedin, twitter, tiktok, pinterest
            $table->string('retargeting_type'); // pixel_based, list_based, dynamic, hybrid
            $table->string('status')->default('draft'); // draft, active, paused, archived
            $table->json('targeting_criteria');
            $table->json('audience_rules');
            $table->json('time_settings');
            $table->json('budget_settings');
            $table->json('creative_settings');
            $table->json('pixel_tracking');
            $table->json('audience_segments');
            $table->json('performance_goals');
            $table->json('integration_settings');
            
            // Performance metrics
            $table->bigInteger('audience_size')->default(0);
            $table->integer('total_campaigns')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            
            // Timestamps
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('resumed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['property_id', 'status']);
            $table->index(['platform', 'status']);
            $table->index(['audience_type', 'status']);
            $table->index(['retargeting_type', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('activated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retargeting_audiences');
    }
};
