<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytic_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_session_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_name', 100);
            $table->text('page_url')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->json('properties')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->index(['event_name', 'created_at']);
            $table->index('user_session_id');
            $table->index('page_url');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytic_events');
    }
};
