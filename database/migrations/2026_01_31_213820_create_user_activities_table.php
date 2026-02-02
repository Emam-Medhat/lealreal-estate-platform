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
        if (!Schema::hasTable('user_activities')) {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->string('activity_type');
            $table->string('activity_category');
            $table->text('activity_description');
            $table->float('duration')->nullable();
            $table->string('device_type');
            $table->string('browser');
            $table->string('platform');
            $table->boolean('is_mobile')->default(false);
            $table->boolean('is_tablet')->default(false);
            $table->boolean('is_desktop')->default(false);
            $table->boolean('is_bot')->default(false);
            $table->string('bot_name')->nullable();
            $table->string('location_country')->nullable();
            $table->string('location_city')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->string('session_id');
            $table->string('method');
            $table->string('url');
            $table->text('full_url');
            $table->json('query_parameters')->nullable();
            $table->json('request_data')->nullable();
            $table->integer('response_status');
            $table->string('ip_address');
            $table->text('user_agent');
            $table->text('referrer')->nullable();
            $table->boolean('is_authenticated')->default(false);
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_premium')->default(false);
            $table->string('subscription_tier')->nullable();
            $table->timestamp('last_activity_at');
            $table->timestamps();
            
            $table->index('activity_type');
            $table->index('activity_category');
            $table->index('user_id');
            $table->index('session_id');
            $table->index('ip_address');
            $table->index('is_authenticated');
            $table->index('last_activity_at');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
