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
            $table->index(['user_id', 'action', 'created_at'], 'idx_ual_user_action_created');
        });

        Schema::table('user_favorites', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'idx_uf_user_created');
        });

        Schema::table('user_notifications', function (Blueprint $table) {
            $table->index(['user_id', 'is_read', 'created_at'], 'idx_un_user_read_created');
        });

        Schema::table('user_comparisons', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'idx_uc_user_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_activity_logs', function (Blueprint $table) {
            $table->dropIndex('idx_ual_user_action_created');
        });

        Schema::table('user_favorites', function (Blueprint $table) {
            $table->dropIndex('idx_uf_user_created');
        });

        Schema::table('user_notifications', function (Blueprint $table) {
            $table->dropIndex('idx_un_user_read_created');
        });

        Schema::table('user_comparisons', function (Blueprint $table) {
            $table->dropIndex('idx_uc_user_created');
        });
    }
};
