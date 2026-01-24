<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('seo_meta', function (Blueprint $table) {
            $table->id();
            $table->string('model_type'); // blog_post, page, news, etc.
            $table->unsignedBigInteger('model_id');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('og_image')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('twitter_card')->nullable();
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();
            $table->string('canonical_url')->nullable();
            $table->json('structured_data')->nullable();
            $table->boolean('noindex')->default(false);
            $table->boolean('nofollow')->default(false);
            $table->boolean('noarchive')->default(false);
            $table->string('robots')->default('index, follow');
            $table->timestamps();

            // Indexes
            $table->index(['model_type', 'model_id']);
            $table->index('meta_title');
            $table->index('robots');
        });
    }

    public function down()
    {
        Schema::dropIfExists('seo_meta');
    }
};
