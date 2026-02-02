<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sentiment_scores')) {
        Schema::create('sentiment_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source_type', 50);
            $table->bigInteger('source_id')->nullable();
            $table->text('content');
            $table->decimal('sentiment_score', 5, 2);
            $table->string('sentiment_label', 20);
            $table->decimal('confidence_score', 5, 2);
            $table->json('emotions')->nullable();
            $table->json('keywords')->nullable();
            $table->string('language', 10)->default('ar');
            $table->timestamp('analyzed_at')->nullable();
            $table->timestamps();
            
            $table->index(['source_type', 'source_id']);
            $table->index('sentiment_label');
            $table->index('analyzed_at');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sentiment_scores');
    }
};
