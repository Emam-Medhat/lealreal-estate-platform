<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * ai_usage_logs
     *
     * Records every call to an AI API (OpenAI, Google Vision, etc.) for:
     *  - Cost tracking (sum cost_usd monthly)
     *  - Abuse detection (per-user/IP analysis)
     *  - Performance auditing
     */
    public function up(): void
    {
        if (Schema::hasTable('ai_usage_logs')) {
            return;
        }

        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();

            // Which AI feature triggered this call
            $table->string('feature', 50)->index();
            // e.g. gpt-4o, text-embedding-3-small, google_vision
            $table->string('model', 80)->nullable();

            // Token accounting
            $table->unsignedInteger('input_tokens')->default(0);
            $table->unsignedInteger('output_tokens')->default(0);

            // Computed USD cost
            $table->decimal('cost_usd', 10, 6)->default(0);

            // Who triggered it
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable()->index();

            // Extra metadata (raw request snippet, session ID, …)
            $table->json('meta')->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Performance indexes
            $table->index(['created_at']);
            $table->index(['feature', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_usage_logs');
    }
};
