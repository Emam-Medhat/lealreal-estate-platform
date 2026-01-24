<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('event_type'); // login, logout, password_change, two_factor_enabled, etc.
            $table->text('description')->nullable();
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->json('metadata')->nullable(); // additional context data
            $table->string('severity')->default('info'); // info, warning, critical
            $table->boolean('resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'event_type', 'created_at']);
            $table->index(['severity', 'resolved', 'created_at']);
            $table->index(['ip_address', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
