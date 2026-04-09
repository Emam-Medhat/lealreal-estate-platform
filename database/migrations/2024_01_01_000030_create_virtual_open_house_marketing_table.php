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
        if (!Schema::hasTable('virtual_open_house_marketing')) {
        Schema::create('virtual_open_house_marketing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('platform'); // zoom, teams, google_meet, skype, custom
            $table->string('event_type'); // live_tour, recorded_tour, qna_session, webinar, presentation
            $table->string('status')->default('scheduled'); // scheduled, active, completed, cancelled
            $table->timestamp('scheduled_at');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration'); // in minutes
            $table->integer('max_attendees')->nullable();
            $table->boolean('registration_required')->default(true);
            $table->timestamp('registration_deadline')->nullable();
            $table->string('meeting_link')->nullable();
            $table->string('meeting_id')->nullable();
            $table->string('password')->nullable();
            $table->json('host_info')->nullable();
            $table->json('promotion_channels')->nullable();
            $table->json('email_template')->nullable();
            $table->json('social_media_posts')->nullable();
            $table->json('reminder_settings')->nullable();
            $table->json('recording_settings')->nullable();
            $table->json('follow_up_settings')->nullable();
            $table->string('custom_banner')->nullable();
            $table->json('featured_images')->nullable();
            $table->string('virtual_tour_link')->nullable();
            $table->string('property_video_url')->nullable();
            $table->json('floor_plans')->nullable();
            
            // Performance metrics
            $table->bigInteger('total_attendees')->default(0);
            $table->bigInteger('total_views')->default(0);
            $table->bigInteger('total_registrations')->default(0);
            $table->bigInteger('total_interactions')->default(0);
            $table->integer('average_attendance_time')->default(0); // in seconds
            $table->integer('peak_attendance_time')->default(0); // in minutes from start
            $table->bigInteger('questions_asked')->default(0);
            $table->bigInteger('chat_messages')->default(0);
            $table->bigInteger('poll_participation')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->bigInteger('lead_generation')->default(0);
            $table->bigInteger('property_inquiries')->default(0);
            $table->bigInteger('tour_requests')->default(0);
            
            // Foreign keys
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['property_id', 'status']);
            $table->index(['platform', 'status']);
            $table->index(['event_type', 'status']);
            $table->index(['status', 'scheduled_at']);
            $table->index('started_at');
            $table->index('ended_at');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_open_house_marketing');
    }
};
