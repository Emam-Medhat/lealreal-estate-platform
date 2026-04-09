<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'notification_preferences')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('notification_preferences')->nullable()->after('remember_token');
            });
        }

        if (!Schema::hasTable('intelligence_alerts')) {
            Schema::create('intelligence_alerts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('type', 64);
                $table->unsignedBigInteger('property_id')->nullable();
                $table->json('data')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'type']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('intelligence_alerts')) {
            Schema::dropIfExists('intelligence_alerts');
        }
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'notification_preferences')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('notification_preferences');
            });
        }
    }
};
