<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_behaviors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_session_id')->nullable();
            $table->string('behavior_type', 50);
            $table->string('action', 100);
            $table->text('page_url')->nullable();
            $table->json('properties')->nullable();
            $table->decimal('duration', 8, 2)->nullable();
            $table->string('device_type', 20)->nullable();
            $table->string('browser', 20)->nullable();
            $table->string('os', 20)->nullable();
            $table->string('location')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->index(['user_id', 'behavior_type']);
            $table->index('user_session_id');
            $table->index('behavior_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_behaviors');
    }
};
