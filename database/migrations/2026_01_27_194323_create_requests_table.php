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
            $table->string('request_id')->unique(); // Unique request identifier
            $table->string('method'); // GET, POST, PUT, DELETE, etc.
            $table->string('url'); // Request URL
            $table->string('ip_address'); // Client IP
            $table->string('user_agent')->nullable(); // Browser info
            $table->json('headers')->nullable(); // Request headers
            $table->json('payload')->nullable(); // Request data
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->integer('response_code')->nullable(); // HTTP response code
            $table->float('response_time')->nullable(); // Response time in milliseconds
            $table->text('error_message')->nullable(); // Error details if failed
            $table->timestamp('started_at')->nullable(); // When request started processing
            $table->timestamp('completed_at')->nullable(); // When request completed
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // User who made request
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['status', 'created_at']);
            $table->index('request_id');
            $table->index('ip_address');
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
