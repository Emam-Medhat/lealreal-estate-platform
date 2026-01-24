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
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->index();
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->string('method', 10);
            $table->string('url');
            $table->text('full_url');
            $table->json('query_parameters')->nullable();
            $table->json('request_data')->nullable();
            $table->integer('response_status')->nullable();
            $table->integer('response_size')->nullable();
            $table->float('duration')->nullable();
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->string('location_country', 100)->nullable();
            $table->string('location_city', 100)->nullable();
            $table->boolean('is_mobile')->default(false);
            $table->boolean('is_tablet')->default(false);
            $table->boolean('is_desktop')->default(false);
            $table->text('referrer')->nullable();
            $table->string('landing_page')->nullable();
            $table->string('exit_page')->nullable();
            $table->integer('page_views')->default(1);
            $table->integer('time_on_page')->default(0);
            $table->float('bounce_rate')->nullable();
            $table->float('conversion_rate')->nullable();
            $table->string('activity_type', 50)->default('page_view');
            $table->string('activity_category', 50)->default('general');
            $table->text('activity_description')->nullable();
            $table->json('tags')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_bot')->default(false);
            $table->string('bot_name')->nullable();
            $table->boolean('is_authenticated')->default(false);
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_premium')->default(false);
            $table->string('subscription_tier')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'created_at']);
            $table->index(['session_id', 'created_at']);
            $table->index(['activity_type', 'created_at']);
            $table->index(['activity_category', 'created_at']);
            $table->index(['ip_address', 'created_at']);
            $table->index(['is_bot', 'created_at']);
            $table->index(['is_authenticated', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
