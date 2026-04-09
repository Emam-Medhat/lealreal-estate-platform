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
        if (!Schema::hasTable('chat_rooms')) {
            Schema::create('chat_rooms', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->text('description')->nullable();
                $table->enum('type', ['general', 'property', 'support', 'private'])->default('general');
                $table->foreignId('property_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->boolean('is_active')->default(true);
                $table->boolean('is_private')->default(false);
                $table->integer('max_participants')->default(50);
                $table->timestamp('last_activity_at')->nullable();
                $table->timestamps();
                
                // Indexes
                $table->index(['type', 'is_active']);
                $table->index('property_id');
                $table->index('created_by');
                $table->index('last_activity_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_rooms');
    }
};
