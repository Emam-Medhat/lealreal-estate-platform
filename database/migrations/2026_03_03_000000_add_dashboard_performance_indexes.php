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
            $table->index(['user_id', 'action', 'created_at'], 'idx_user_activity_action_date');
        });

        Schema::table('user_favorites', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'idx_user_favorites_user_date');
        });

        Schema::table('user_notifications', function (Blueprint $table) {
            $table->index(['user_id', 'is_read', 'created_at'], 'idx_user_notifications_unread_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_activity_logs', function (Blueprint $table) {
            $table->dropIndex('idx_user_activity_action_date');
        });

        Schema::table('user_favorites', function (Blueprint $table) {
            $table->dropIndex('idx_user_favorites_user_date');
        });

        Schema::table('user_notifications', function (Blueprint $table) {
            $table->dropIndex('idx_user_notifications_unread_date');
        });
    }
};
