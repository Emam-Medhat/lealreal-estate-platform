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
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->unique();
            $table->string('ip_address');
            $table->text('user_agent');
            $table->foreignId('device_id')->nullable()->constrained('user_devices')->onDelete('set null');
            $table->json('location')->nullable(); // country, city, coordinates
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_activity_at');
            $table->timestamp('expires_at')->nullable();
            $table->enum('login_method', ['password', 'social', 'two_factor', 'biometric', 'magic_link']);
            $table->boolean('two_factor_verified')->default(false);
            $table->boolean('biometric_verified')->default(false);
            $table->json('security_flags')->nullable(); // suspicious activity flags
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index('expires_at');
            $table->index('last_activity_at');
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
