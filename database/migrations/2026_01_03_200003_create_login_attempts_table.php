<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('login_attempts')) {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email_or_phone');
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->boolean('successful')->default(false);
            $table->string('failure_reason')->nullable(); // invalid_credentials, account_locked, etc.
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('attempted_at');
            $table->timestamps();
            
            $table->index(['email_or_phone', 'attempted_at']);
            $table->index(['ip_address', 'attempted_at']);
            $table->index(['successful', 'attempted_at']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};
