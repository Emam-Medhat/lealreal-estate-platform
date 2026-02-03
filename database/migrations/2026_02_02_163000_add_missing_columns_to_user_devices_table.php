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
        if (Schema::hasTable('user_devices')) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('user_devices', 'browser_version')) {
                Schema::table('user_devices', function (Blueprint $table) {
                    $table->string('browser_version')->nullable()->after('browser');
                });
            }

            if (!Schema::hasColumn('user_devices', 'is_active')) {
                Schema::table('user_devices', function (Blueprint $table) {
                    $table->boolean('is_active')->default(true)->after('is_trusted');
                });
            }

            if (!Schema::hasColumn('user_devices', 'last_seen_at')) {
                Schema::table('user_devices', function (Blueprint $table) {
                    $table->timestamp('last_seen_at')->nullable()->after('last_used_at');
                });
            }

            // Add foreign key if it doesn't exist
            if (!Schema::hasColumn('user_devices', 'user_id') || Schema::hasColumn('user_devices', 'user_id')) {
                Schema::table('user_devices', function (Blueprint $table) {
                    // Only add foreign key if it doesn't exist
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_devices')) {
            Schema::table('user_devices', function (Blueprint $table) {
                // Drop columns if they exist
                if (Schema::hasColumn('user_devices', 'browser_version')) {
                    $table->dropColumn('browser_version');
                }
                if (Schema::hasColumn('user_devices', 'is_active')) {
                    $table->dropColumn('is_active');
                }
                if (Schema::hasColumn('user_devices', 'last_seen_at')) {
                    $table->dropColumn('last_seen_at');
                }
            });
        }
    }
};
