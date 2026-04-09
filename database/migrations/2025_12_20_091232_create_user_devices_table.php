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
        if (!Schema::hasTable('user_devices')) {
        Schema::create('user_devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('device_name');
            $table->string('device_type'); // mobile, desktop, tablet
            $table->string('platform'); // iOS, Android, Windows, macOS, Linux
            $table->string('browser')->nullable();
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->boolean('is_trusted')->default(false);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'device_type']);
            
            // Foreign key will be added after users table is created
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};
