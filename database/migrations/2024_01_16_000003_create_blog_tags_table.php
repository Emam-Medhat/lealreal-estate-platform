<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('blog_tags')) {
        Schema::create('blog_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->nullable();
            $table->integer('usage_count')->default(0);
            $table->timestamps();
        });
        }

        if (!Schema::hasTable('blog_post_tag')) {
        Schema::create('blog_post_tag', function (Blueprint $table) {
            $table->foreignId('blog_post_id')->constrained()->onDelete('cascade');
            $table->foreignId('blog_tag_id')->constrained()->onDelete('cascade');
            $table->primary(['blog_post_id', 'blog_tag_id']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('blog_post_tag');
        Schema::dropIfExists('blog_tags');
    }
};
