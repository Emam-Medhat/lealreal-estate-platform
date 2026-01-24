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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('device_id')->nullable();
            $table->string('session_id')->unique();
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->json('payload');
            $table->integer('last_activity')->index();
            $table->timestamp('expires_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
            
            // Foreign keys will be added after users and devices tables are created
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('device_id')->references('id')->on('user_devices')->onDelete('cascade');
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
