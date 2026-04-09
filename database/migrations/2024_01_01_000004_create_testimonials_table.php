<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('testimonials')) {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->string('client_name');
            $table->string('client_position')->nullable();
            $table->string('client_company')->nullable();
            $table->string('client_image')->nullable();
            $table->string('project_type')->nullable();
            $table->string('project_location')->nullable();
            $table->integer('rating')->nullable();
            $table->string('video_url')->nullable();
            $table->boolean('featured')->default(false);
            $table->string('status')->default('pending');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('featured_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('status');
            $table->index('featured');
            $table->index('project_type');
            $table->index('rating');
            $table->index('published_at');
            $table->index('created_at');
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('testimonials');
    }
};
