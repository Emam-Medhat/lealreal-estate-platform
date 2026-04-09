<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('survey_responses')) {
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->json('responses');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['survey_id', 'user_id']);
            $table->index('survey_id');
            $table->index('user_id');
            $table->index('ip_address');
            $table->index('completed_at');
            $table->unique(['survey_id', 'user_id'], 'survey_responses_unique');
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('survey_responses');
    }
};
