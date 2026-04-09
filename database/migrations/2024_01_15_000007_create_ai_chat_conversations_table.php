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
        if (!Schema::hasTable('ai_chat_conversations')) {
        Schema::create('ai_chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_id', 100)->unique();
            $table->string('conversation_type', 50);
            $table->json('messages')->nullable();
            $table->json('intent_analysis')->nullable();
            $table->json('sentiment_analysis')->nullable();
            $table->json('entity_extraction')->nullable();
            $table->json('context_data')->nullable();
            $table->json('user_preferences')->nullable();
            $table->json('conversation_summary')->nullable();
            $table->decimal('satisfaction_score', 3, 2)->nullable(); // 0.00-5.00
            $table->string('resolution_status', 30)->default('pending');
            $table->json('ai_responses')->nullable();
            $table->boolean('human_intervention')->default(false);
            $table->tinyInteger('escalation_level')->unsigned()->default(0); // 0-4
            $table->string('ai_model_version', 20);
            $table->json('chat_metadata')->nullable();
            $table->decimal('processing_time', 8, 3)->default(0); // in seconds
            $table->decimal('confidence_level', 3, 2); // 0.00-1.00
            $table->string('status', 20)->default('active');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration')->default(0); // in seconds
            $table->integer('message_count')->default(0);
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'session_id']);
            $table->index('property_id');
            $table->index('conversation_type');
            $table->index('status');
            $table->index('resolution_status');
            $table->index('started_at');
            $table->index('human_intervention');
            $table->index('escalation_level');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_chat_conversations');
    }
};
