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
        Schema::create('social_media_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->string('platform'); // facebook, twitter, instagram, linkedin, youtube, tiktok
            $table->string('post_type'); // image, video, carousel, story, reel, live_stream
            $table->string('status')->default('draft'); // draft, scheduled, published, archived, failed
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->json('hashtags')->nullable();
            $table->json('mentions')->nullable();
            $table->json('call_to_action')->nullable();
            $table->json('target_audience')->nullable();
            $table->decimal('budget', 10, 2)->nullable();
            $table->boolean('boost_post')->default(false);
            $table->json('media_files')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('video_url')->nullable();
            $table->string('link_url')->nullable();
            $table->string('location_tag')->nullable();
            $table->string('language')->default('ar');
            $table->json('engagement_settings')->nullable();
            $table->json('promotion_settings')->nullable();
            $table->json('analytics_settings')->nullable();
            
            // Performance metrics
            $table->bigInteger('total_engagement')->default(0);
            $table->bigInteger('reach')->default(0);
            $table->bigInteger('impressions')->default(0);
            $table->bigInteger('likes')->default(0);
            $table->bigInteger('comments')->default(0);
            $table->bigInteger('shares')->default(0);
            $table->bigInteger('saves')->default(0);
            $table->bigInteger('clicks')->default(0);
            $table->bigInteger('video_views')->default(0);
            $table->decimal('video_completion_rate', 5, 2)->default(0);
            $table->bigInteger('carousel_swipes')->default(0);
            $table->bigInteger('story_views')->default(0);
            $table->bigInteger('story_replies')->default(0);
            $table->bigInteger('story_shares')->default(0);
            $table->bigInteger('story_exits')->default(0);
            $table->json('boost_performance')->nullable();
            
            // Foreign keys
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['property_id', 'status']);
            $table->index(['platform', 'status']);
            $table->index(['post_type', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('scheduled_at');
            $table->index('published_at');
            $table->index('boost_post');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_media_posts');
    }
};
