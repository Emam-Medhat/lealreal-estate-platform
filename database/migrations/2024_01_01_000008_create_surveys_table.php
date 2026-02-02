<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('surveys')) {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('target_audience');
            $table->timestamp('starts_at');
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('allow_multiple_responses')->default(false);
            $table->boolean('show_results')->default(false);
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->integer('response_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('created_by');
            $table->index('status');
            $table->index('target_audience');
            $table->index('starts_at');
            $table->index('expires_at');
            $table->index('published_at');
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('surveys');
    }
};
