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
        if (!Schema::hasTable('appointments')) {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            
            // Basic appointment info
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('appointment_type')->default('meeting');
            $table->string('status')->default('scheduled');
            $table->string('priority')->default('medium');
            
            // Date and time
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->integer('duration')->default(60); // in minutes
            $table->string('timezone')->default('UTC');
            
            // Location
            $table->string('location_type')->default('in_person'); // in_person, virtual, phone
            $table->string('location')->nullable();
            $table->text('address')->nullable();
            $table->string('meeting_link')->nullable();
            $table->string('meeting_password')->nullable();
            $table->string('meeting_platform')->nullable(); // zoom, teams, etc.
            $table->string('phone_number')->nullable();
            
            // Participants (for compatibility with controller)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('participant_id')->nullable()->constrained('users')->onDelete('cascade');
            
            // Original fields from model
            $table->foreignId('agent_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('lead_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('cascade');
            
            // Notes and agenda
            $table->text('notes')->nullable();
            $table->text('agenda')->nullable();
            $table->text('preparation_notes')->nullable();
            $table->text('follow_up_notes')->nullable();
            $table->text('outcome')->nullable();
            $table->text('next_steps')->nullable();
            
            // Attendees
            $table->json('attendees')->nullable();
            $table->json('required_attendees')->nullable();
            $table->json('optional_attendees')->nullable();
            
            // Confirmation and reminders
            $table->string('confirmation_status')->default('pending');
            $table->dateTime('confirmed_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('reminders')->nullable();
            
            // Rescheduling
            $table->integer('rescheduled_count')->default(0);
            $table->dateTime('original_start_datetime')->nullable();
            
            // Cancellation
            $table->text('cancellation_reason')->nullable();
            $table->dateTime('canceled_at')->nullable();
            $table->foreignId('canceled_by')->nullable()->constrained('users')->onDelete('set null');
            
            // No show
            $table->text('no_show_reason')->nullable();
            $table->dateTime('no_show_at')->nullable();
            
            // Feedback and rating
            $table->integer('rating')->nullable();
            $table->text('feedback')->nullable();
            $table->text('client_feedback')->nullable();
            $table->text('agent_feedback')->nullable();
            
            // Tags and metadata
            $table->json('tags')->nullable();
            $table->json('custom_fields')->nullable();
            $table->json('metadata')->nullable();
            
            // Tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Additional fields from model
            $table->string('calendar_event_id')->nullable();
            $table->string('video_conference_link')->nullable();
            $table->json('documents_required')->nullable();
            $table->json('pre_meeting_checklist')->nullable();
            $table->json('post_meeting_checklist')->nullable();
            $table->json('action_items')->nullable();
            $table->json('decisions_made')->nullable();
            $table->json('commitments')->nullable();
            $table->foreignId('next_appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->foreignId('previous_appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            
            // Property viewing specific fields
            $table->text('property_viewing_notes')->nullable();
            $table->string('client_interest_level')->nullable();
            $table->text('budget_discussion')->nullable();
            $table->text('timeline_discussion')->nullable();
            $table->json('objections_handled')->nullable();
            $table->json('concerns_raised')->nullable();
            
            // Sales tracking
            $table->dateTime('next_follow_up_date')->nullable();
            $table->integer('probability_of_sale')->nullable();
            $table->date('estimated_close_date')->nullable();
            $table->json('competition_mentioned')->nullable();
            $table->boolean('decision_makers_present')->default(false);
            $table->json('buying_signals')->nullable();
            $table->json('red_flags')->nullable();
            $table->json('opportunities')->nullable();
            $table->json('strengths')->nullable();
            $table->json('weaknesses')->nullable();
            $table->json('threats')->nullable();
            
            // Notes
            $table->text('internal_notes')->nullable();
            $table->text('private_notes')->nullable();
            $table->text('client_notes')->nullable();
            $table->text('agent_notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['user_id', 'participant_id']);
            $table->index(['agent_id', 'lead_id']);
            $table->index(['status', 'start_datetime']);
            $table->index(['appointment_type', 'priority']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
