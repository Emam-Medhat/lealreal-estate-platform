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
        Schema::create('resident_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id')->constrained()->onDelete('cascade');
            $table->string('title', 255);
            $table->text('content');
            $table->string('post_type', 50)->default('discussion');
            $table->string('status', 20)->default('draft');
            $table->string('author_name', 255);
            $table->string('author_email', 255)->nullable();
            $table->string('author_phone', 50)->nullable();
            $table->string('author_role', 100)->nullable();
            $table->datetime('published_at')->nullable();
            $table->datetime('expires_at')->nullable();
            $table->string('priority', 20)->default('medium');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('allow_comments')->default(true);
            $table->boolean('send_notifications')->default(false);
            $table->json('target_audience')->nullable();
            $table->json('tags')->nullable();
            $table->string('category', 100)->nullable();
            $table->json('images')->nullable();
            $table->string('cover_image', 500)->nullable();
            $table->json('gallery')->nullable();
            $table->json('videos')->nullable();
            $table->json('attachments')->nullable();
            $table->json('related_links')->nullable();
            $table->json('contact_info')->nullable();
            $table->json('social_sharing')->nullable();
            $table->json('metadata')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->integer('comment_count')->default(0);
            $table->integer('share_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['community_id']);
            $table->index(['post_type']);
            $table->index(['status']);
            $table->index(['priority']);
            $table->index(['is_pinned']);
            $table->index(['is_featured']);
            $table->index(['published_at']);
            $table->index(['expires_at']);
            $table->index(['view_count']);
            $table->index(['like_count']);
            $table->index(['comment_count']);

            // Regular index for search (removed full-text due to length limit)
            $table->index('title');
            $table->index('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resident_posts');
    }
};
