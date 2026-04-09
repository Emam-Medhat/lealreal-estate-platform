<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('content_revisions')) {
        Schema::create('content_revisions', function (Blueprint $table) {
            $table->id();
            $table->string('model_type'); // blog_post, page, etc.
            $table->unsignedBigInteger('model_id');
            $table->longText('content');
            $table->json('changes')->nullable(); // what changed
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['model_type', 'model_id']);
            $table->index(['author_id']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('content_revisions');
    }
};
