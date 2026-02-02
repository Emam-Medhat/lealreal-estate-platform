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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_id')->unique();
            $table->string('method');
            $table->text('url');
            $table->string('ip_address');
            $table->text('user_agent')->nullable();
            $table->json('headers')->nullable();
            $table->json('payload')->nullable();
            $table->string('status')->default('processing');
            $table->timestamp('started_at');
            $table->foreignId('user_id')->nullable();
            $table->timestamps();
            
            $table->index('request_id');
            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
