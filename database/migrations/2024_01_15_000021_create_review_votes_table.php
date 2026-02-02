<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('review_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('response_id')->nullable()->constrained('review_responses')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('vote_type');
            $table->timestamps();

            $table->index(['review_id', 'user_id']);
            $table->index(['response_id', 'user_id']);
            $table->index('vote_type');
            $table->unique(['review_id', 'user_id'], 'review_votes_review_unique');
            $table->unique(['response_id', 'user_id'], 'review_votes_response_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('review_votes');
    }
};
