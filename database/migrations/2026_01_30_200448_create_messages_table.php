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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->string('type')->default('text');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->boolean('is_deleted_by_sender')->default(false);
            $table->boolean('is_deleted_by_receiver')->default(false);
            $table->timestamp('deleted_by_sender_at')->nullable();
            $table->timestamp('deleted_by_receiver_at')->nullable();
            $table->foreignId('reply_to_id')->nullable()->constrained('messages')->onDelete('set null');
            $table->foreignId('forwarded_from_id')->nullable()->constrained('messages')->onDelete('set null');
            $table->string('priority')->default('normal');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['conversation_id', 'created_at']);
            $table->index(['sender_id', 'receiver_id']);
            $table->index('is_read');
            $table->index('type');
            $table->index('priority');
            $table->index('reply_to_id');
            $table->index('forwarded_from_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
