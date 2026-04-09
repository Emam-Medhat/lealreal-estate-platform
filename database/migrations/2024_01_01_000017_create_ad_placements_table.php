<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('ad_placements')) {
        Schema::create('ad_placements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['banner', 'native', 'video', 'popup', 'mobile'])->default('banner');
            $table->integer('clicks_count')->default(0);
            $table->integer('conversions_count')->default(0);
            
            // Banner specific fields
            $table->enum('banner_size', ['leaderboard', 'medium_rectangle', 'wide_skyscraper', 'large_rectangle', 'mobile_banner'])->default('leaderboard');
            $table->string('position');
            $table->integer('width');
            $table->integer('height');
            $table->integer('max_ads')->default(1);
            $table->enum('pricing_model', ['cpm', 'cpc', 'cpa'])->default('cpm');
            $table->decimal('base_price', 10, 2);
            $table->decimal('min_bid', 10, 2);
            $table->json('target_pages')->nullable();
            $table->json('excluded_pages')->nullable();
            $table->json('device_types')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_rotate')->default(false);
            $table->integer('rotation_interval')->default(30); // seconds
            $table->boolean('show_on_mobile')->default(true);
            $table->boolean('show_on_desktop')->default(true);
            $table->boolean('show_on_tablet')->default(true);
            $table->text('custom_css')->nullable();
            $table->text('custom_js')->nullable();
            $table->boolean('tracking_enabled')->default(true);
            $table->boolean('frequency_capping')->default(false);
            $table->integer('max_impressions_per_user')->nullable();
            $table->integer('max_clicks_per_user')->nullable();
            $table->integer('time_between_impressions')->default(60); // seconds
            $table->integer('time_between_clicks')->default(300); // seconds
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['type', 'is_active']);
            $table->index(['pricing_model']);
            $table->index(['position']);
            $table->index(['is_active']);
            $table->index(['created_at']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('ad_placements');
    }
};
