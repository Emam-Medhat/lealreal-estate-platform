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
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('device_name');
            $table->enum('device_type', ['desktop', 'mobile', 'tablet', 'tv', 'other']);
            $table->string('platform')->nullable(); // Windows, macOS, Linux, iOS, Android
            $table->string('browser')->nullable(); // Chrome, Firefox, Safari, Edge
            $table->string('browser_version')->nullable();
            $table->string('ip_address');
            $table->text('user_agent');
            $table->string('fingerprint')->nullable();
            $table->boolean('is_trusted')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at');
            $table->timestamp('expires_at')->nullable();
            $table->json('location')->nullable(); // country, city, coordinates
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'fingerprint']);
            $table->index(['user_id', 'is_active']);
            $table->index(['user_id', 'is_trusted']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};
