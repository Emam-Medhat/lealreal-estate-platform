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
        Schema::table('user_activity_logs', function (Blueprint $table) {
            // Index for: ActivityStats today (where user_id, action, created_at)
            $table->index(['user_id', 'action', 'created_at'], 'idx_activity_user_action_date');

            // Index for: Recent Activity (where user_id order by created_at desc)
            $table->index(['user_id', 'created_at'], 'idx_activity_user_date');
        });

        Schema::table('user_notifications', function (Blueprint $table) {
            // Index for: Unread notifications count and list
            $table->index(['user_id', 'is_read', 'created_at'], 'idx_notifications_user_unread');
        });

        Schema::table('user_favorites', function (Blueprint $table) {
            // Index for: Saved today count and Recent saved list
            $table->index(['user_id', 'created_at'], 'idx_favorites_user_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_activity_logs', function (Blueprint $table) {
            $table->dropIndex('idx_activity_user_action_date');
            $table->dropIndex('idx_activity_user_date');
        });

        Schema::table('user_notifications', function (Blueprint $table) {
            $table->dropIndex('idx_notifications_user_unread');
        });

        Schema::table('user_favorites', function (Blueprint $table) {
            $table->dropIndex('idx_favorites_user_date');
        });
    }
};
