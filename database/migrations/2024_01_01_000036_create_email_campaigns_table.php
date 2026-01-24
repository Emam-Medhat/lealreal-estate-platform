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
        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('name');
            $table->string('subject');
            $table->string('preheader')->nullable();
            $table->string('from_name');
            $table->string('from_email');
            $table->string('reply_to_email')->nullable();
            $table->string('campaign_type'); // newsletter, promotion, announcement, follow_up, etc.
            $table->string('status')->default('draft'); // draft, scheduled, active, sent, completed, paused
            $table->foreignId('template_id')->nullable()->constrained('email_templates')->onDelete('set null');
            $table->json('content')->nullable();
            $table->longText('html_content')->nullable();
            $table->longText('text_content')->nullable();
            $table->json('target_audience')->nullable();
            $table->json('segment_criteria')->nullable();
            $table->json('personalization_settings')->nullable();
            $table->json('schedule_settings')->nullable();
            $table->json('sending_settings')->nullable();
            $table->json('tracking_settings')->nullable();
            $table->json('automation_settings')->nullable();
            $table->json('test_settings')->nullable();
            
            // Performance metrics
            $table->bigInteger('total_recipients')->default(0);
            $table->bigInteger('sent_count')->default(0);
            $table->bigInteger('delivered_count')->default(0);
            $table->bigInteger('opened_count')->default(0);
            $table->bigInteger('clicked_count')->default(0);
            $table->bigInteger('unsubscribed_count')->default(0);
            $table->bigInteger('bounced_count')->default(0);
            $table->bigInteger('complained_count')->default(0);
            $table->decimal('open_rate', 5, 2)->default(0);
            $table->decimal('click_rate', 5, 2)->default(0);
            $table->decimal('bounce_rate', 5, 2)->default(0);
            $table->decimal('unsubscribe_rate', 5, 2)->default(0);
            $table->decimal('complaint_rate', 5, 2)->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->decimal('revenue_generated', 10, 2)->default(0);
            $table->decimal('cost_per_send', 10, 2)->default(0);
            $table->decimal('return_on_investment', 5, 2)->default(0);
            
            // Timestamps
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
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
            $table->index('scheduled_at');
            $table->index('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_campaigns');
    }
};
