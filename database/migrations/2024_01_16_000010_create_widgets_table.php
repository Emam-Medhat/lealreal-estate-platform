<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('widgets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type'); // text, html, image, recent_posts, etc.
            $table->longText('content')->nullable();
            $table->json('config')->nullable(); // widget-specific configuration
            $table->string('location')->nullable(); // sidebar, footer, header
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['location', 'sort_order']);
            $table->index(['type', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('widgets');
    }
};
