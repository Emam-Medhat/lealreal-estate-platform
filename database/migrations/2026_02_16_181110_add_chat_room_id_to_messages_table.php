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
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->foreignId('chat_room_id')->nullable()->after('conversation_id')->constrained()->onDelete('cascade');
                $table->index('chat_room_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->dropIndex(['chat_room_id']);
                $table->dropColumn('chat_room_id');
            });
        }
    }
};
