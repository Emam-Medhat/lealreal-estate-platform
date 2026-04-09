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
        if (!Schema::hasTable('neighborhood_reviews')) {
        Schema::create('neighborhood_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('neighborhood_id')->constrained()->onDelete('cascade');
            $table->string('title', 255);
            $table->text('content');
            $table->decimal('rating', 2, 1);
            $table->string('status', 20)->default('published');
            $table->string('reviewer_name', 255);
            $table->string('reviewer_email', 255)->nullable();
            $table->string('reviewer_phone', 50)->nullable();
            $table->string('reviewer_type', 50)->default('resident');
            $table->json('pros')->nullable();
            $table->json('cons')->nullable();
            $table->string('recommendation', 20)->nullable();
            $table->string('experience_period', 50)->nullable();
            $table->string('property_type', 50)->nullable();
            $table->json('property_details')->nullable();
            $table->json('community_aspects')->nullable();
            $table->json('improvement_suggestions')->nullable();
            $table->json('images')->nullable();
            $table->json('photos')->nullable();
            $table->json('videos')->nullable();
            $table->boolean('verified')->default(false);
            $table->boolean('featured')->default(false);
            $table->json('tags')->nullable();
            $table->json('metadata')->nullable();
            $table->integer('helpful_count')->default(0);
            $table->integer('report_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['neighborhood_id']);
            $table->index(['rating']);
            $table->index(['status']);
            $table->index(['reviewer_type']);
            $table->index(['recommendation']);
            $table->index(['verified']);
            $table->index(['featured']);
            $table->index(['helpful_count']);
            $table->index(['view_count']);

            // Regular index for search (removed full-text due to length limit)
            $table->index('title');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neighborhood_reviews');
    }
};
