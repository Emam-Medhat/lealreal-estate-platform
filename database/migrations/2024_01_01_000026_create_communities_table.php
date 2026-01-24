<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('communities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('neighborhood_id')->constrained()->onDelete('cascade');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('type', 50)->default('residential');
            $table->string('status', 20)->default('active');
            $table->date('founded_date')->nullable();
            $table->integer('member_count')->default(0);
            $table->integer('property_count')->default(0);
            $table->string('activity_level', 20)->default('medium');
            $table->json('contact_info')->nullable();
            $table->json('social_media')->nullable();
            $table->json('rules')->nullable();
            $table->json('amenities')->nullable();
            $table->json('services')->nullable();
            $table->integer('events_count')->default(0);
            $table->integer('posts_count')->default(0);
            $table->integer('news_count')->default(0);
            $table->decimal('rating', 2, 1)->default(0);
            $table->integer('review_count')->default(0);
            $table->string('verification_status', 20)->default('unverified');
            $table->boolean('featured')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['neighborhood_id']);
            $table->index(['status']);
            $table->index(['type']);
            $table->index(['activity_level']);
            $table->index(['verification_status']);
            $table->index(['featured']);
            $table->index(['rating']);
            $table->index(['member_count']);
            $table->index(['property_count']);

            // Regular index for search (removed full-text due to length limit)
            $table->index('name');
            $table->index('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communities');
    }
};
