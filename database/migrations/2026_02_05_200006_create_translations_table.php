<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('language', 2);
            $table->text('value');
            $table->string('group', 50)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['key', 'language'], 'translations_unique');
            $table->index(['language']);
            $table->index(['group']);
            $table->index(['is_verified']);
            $table->index(['is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
