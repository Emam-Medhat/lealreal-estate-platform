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
        if (!Schema::hasTable('drone_footages')) {
        Schema::create('drone_footages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('footage_type'); // aerial_tour, neighborhood_overview, property_highlight, etc.
            $table->string('status')->default('draft'); // draft, processing, published, archived
            $table->string('video_file');
            $table->string('thumbnail')->nullable();
            $table->string('quality'); // 720p, 1080p, 4k, 8k
            $table->integer('duration'); // in seconds
            $table->json('tags')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('allow_comments')->default(true);
            $table->boolean('allow_downloads')->default(false);
            $table->boolean('password_protected')->default(false);
            $table->string('password')->nullable();
            $table->json('flight_info')->nullable();
            $table->json('editing_info')->nullable();
            $table->json('music_info')->nullable();
            $table->json('call_to_action')->nullable();
            $table->json('seo_settings')->nullable();
            $table->json('distribution_settings')->nullable();
            $table->string('subtitles')->nullable();
            $table->text('transcript')->nullable();
            $table->json('additional_media')->nullable();
            $table->json('behind_the_scenes')->nullable();
            
            // Performance metrics
            $table->bigInteger('views')->default(0);
            $table->bigInteger('unique_viewers')->default(0);
            $table->integer('average_watch_time')->default(0); // in seconds
            $table->decimal('completion_rate', 5, 2)->default(0);
            $table->decimal('engagement_rate', 5, 2)->default(0);
            $table->bigInteger('shares')->default(0);
            $table->bigInteger('likes')->default(0);
            $table->bigInteger('comments')->default(0);
            $table->bigInteger('downloads')->default(0);
            
            // Timestamps
            $table->timestamp('published_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['property_id', 'status']);
            $table->index(['footage_type', 'status']);
            $table->index(['quality', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('featured');
            $table->index('published_at');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drone_footages');
    }
};
