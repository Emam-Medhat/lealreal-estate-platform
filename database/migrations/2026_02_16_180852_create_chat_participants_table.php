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
        if (!Schema::hasTable('chat_participants')) {
            Schema::create('chat_participants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('chat_room_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('role', 50)->default('member');
                $table->timestamp('joined_at');
                $table->timestamp('last_read_at')->nullable();
                $table->timestamps();
                
                // Indexes
                $table->index(['chat_room_id', 'user_id']);
                $table->index('role');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_participants');
    }
};
