<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('review_sentiment_analyses')) {
        Schema::create('review_sentiment_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id');
            $table->string('sentiment');
            $table->decimal('confidence', 3, 3);
            $table->decimal('positive_score', 3, 3);
            $table->decimal('negative_score', 3, 3);
            $table->decimal('neutral_score', 3, 3);
            $table->json('keywords')->nullable();
            $table->json('emotions')->nullable();
            $table->timestamp('analyzed_at')->nullable();
            $table->timestamps();

            $table->index('review_id');
            $table->index('sentiment');
            $table->index('confidence');
            $table->unique('review_id', 'review_sentiment_analyses_unique');
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('review_sentiment_analyses');
    }
};
