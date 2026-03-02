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
            // Index for whereDate('created_at') and action-specific counts
            $table->index(['user_id', 'created_at'], 'idx_user_activity_user_created');
            $table->index(['user_id', 'action'], 'idx_user_activity_user_action');
        });

        Schema::table('user_favorites', function (Blueprint $table) {
            // Index for counting user favorites and today's favorites
            $table->index(['user_id', 'created_at'], 'idx_user_favorites_user_created');
        });

        Schema::table('user_notifications', function (Blueprint $table) {
            // Index for counting unread notifications
            $table->index(['user_id', 'is_read', 'created_at'], 'idx_user_notifications_unread');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_activity_logs', function (Blueprint $table) {
            $table->dropIndex('idx_user_activity_user_created');
            $table->dropIndex('idx_user_activity_user_action');
        });

        Schema::table('user_favorites', function (Blueprint $table) {
            $table->dropIndex('idx_user_favorites_user_created');
        });

        Schema::table('user_notifications', function (Blueprint $table) {
            $table->dropIndex('idx_user_notifications_unread');
        });
    }
};
