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
        if (!Schema::hasTable('system_error_logs')) {
        Schema::create('system_error_logs', function (Blueprint $table) {
            $table->id();
            $table->string('message')->nullable();
            $table->string('code')->nullable();
            $table->string('file')->nullable();
            $table->integer('line')->nullable();
            $table->longText('trace')->nullable();
            $table->string('url')->nullable();
            $table->string('method')->nullable();
            $table->string('ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('request_data')->nullable();
            $table->boolean('is_resolved')->default(false);
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_error_logs');
    }
};
