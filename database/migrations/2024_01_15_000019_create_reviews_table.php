<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('reviews')) {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('agent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('review_type', ['property', 'agent', 'service']);
            $table->integer('rating'); // 1-5 stars
            $table->string('title')->nullable();
            $table->text('content');
            $table->json('pros')->nullable();
            $table->json('cons')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'hidden'])->default('pending');
            $table->integer('helpful_count')->default(0);
            $table->text('response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
};
