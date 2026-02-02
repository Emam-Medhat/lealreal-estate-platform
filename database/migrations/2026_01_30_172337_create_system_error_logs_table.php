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
            $table->string('error_message');
            $table->text('stack_trace')->nullable();
            $table->string('url')->nullable();
            $table->string('method')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_resolved')->default(false);
            $table->string('severity')->default('error'); // error, warning, info
            $table->json('context')->nullable(); // Additional context data
            $table->timestamps();

            $table->index(['is_resolved', 'created_at']);
            $table->index('severity');
            $table->index('user_id');
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
