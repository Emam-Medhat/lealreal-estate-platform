<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('review_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('response_id')->nullable()->constrained('review_responses')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reason');
            $table->text('description')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->string('action_taken')->nullable();
            $table->timestamps();

            $table->index(['review_id', 'user_id']);
            $table->index(['response_id', 'user_id']);
            $table->index('status');
            $table->index('reason');
            $table->index('reviewed_by');
            $table->unique(['review_id', 'user_id'], 'review_flags_review_unique');
            $table->unique(['response_id', 'user_id'], 'review_flags_response_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('review_flags');
    }
};
