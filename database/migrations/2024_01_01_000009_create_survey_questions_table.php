<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('survey_questions')) {
        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->string('question_type');
            $table->json('options')->nullable();
            $table->boolean('is_required')->default(false);
            $table->integer('order');
            $table->timestamps();
            $table->softDeletes();

            $table->index('survey_id');
            $table->index('question_type');
            $table->index('order');
            $table->unique(['survey_id', 'order'], 'survey_questions_order_unique');
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('survey_questions');
    }
};
