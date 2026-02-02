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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->string('subject')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('last_message_id')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->text('last_message_preview')->nullable();
            $table->timestamp('sender_deleted_at')->nullable();
            $table->timestamp('receiver_deleted_at')->nullable();
            $table->boolean('is_archived_by_sender')->default(false);
            $table->boolean('is_archived_by_receiver')->default(false);
            $table->boolean('is_starred_by_sender')->default(false);
            $table->boolean('is_starred_by_receiver')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['sender_id', 'receiver_id']);
            $table->index('status');
            $table->index('last_message_at');
            $table->index(['sender_id', 'sender_deleted_at']);
            $table->index(['receiver_id', 'receiver_deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
