<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->string('template')->default('default');
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->boolean('show_in_menu')->default(false);
            $table->string('menu_title')->nullable();
            $table->json('seo_data')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'sort_order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('pages');
    }
};
