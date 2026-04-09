<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('property_reviews')) {
        Schema::create('property_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('rating'); // 1-5 stars
            $table->text('title')->nullable();
            $table->text('review');
            $table->json('pros')->nullable();
            $table->json('cons')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->unique(['property_id', 'user_id']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('property_reviews');
    }
};
