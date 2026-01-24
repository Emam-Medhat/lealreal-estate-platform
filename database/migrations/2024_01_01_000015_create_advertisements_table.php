<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('campaign_id')->nullable();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['banner', 'native', 'video', 'popup']);
            $table->string('image_url')->nullable();
            $table->string('video_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->string('target_url');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->decimal('daily_budget', 10, 2);
            $table->decimal('total_budget', 10, 2)->nullable();
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->decimal('daily_spent', 10, 2)->default(0);
            $table->enum('status', ['draft', 'active', 'paused', 'inactive', 'expired'])->default('draft');
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->datetime('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('rejected_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable();
            $table->integer('impressions_count')->default(0);
            $table->integer('clicks_count')->default(0);
            $table->integer('conversions_count')->default(0);
            
            // Banner specific fields
            $table->enum('banner_size', ['leaderboard', 'medium_rectangle', 'large_rectangle', 'wide_skyscraper', 'custom'])->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->enum('animation_type', ['none', 'fade', 'slide', 'zoom'])->default('none');
            
            // Video specific fields
            $table->integer('video_duration')->nullable();
            $table->boolean('autoplay')->default(false);
            $table->boolean('muted')->default(true);
            $table->boolean('controls')->default(true);
            $table->boolean('loop')->default(false);
            $table->integer('playback_position')->nullable();
            $table->integer('skip_after')->nullable();
            
            // Tracking settings
            $table->boolean('click_tracking')->default(true);
            $table->boolean('impression_tracking')->default(true);
            
            // Promotion type for property ads
            $table->enum('promotion_type', ['featured', 'premium', 'spotlight'])->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['campaign_id', 'status']);
            $table->index(['type', 'status']);
            $table->index(['approval_status', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index(['created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('advertisements');
    }
};
