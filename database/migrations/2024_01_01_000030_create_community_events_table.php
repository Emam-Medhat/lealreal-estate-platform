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
        Schema::create('community_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id')->constrained()->onDelete('cascade');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('event_type', 50)->default('other');
            $table->string('status', 20)->default('draft');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->string('location', 500)->nullable();
            $table->decimal('latitude', 8, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->string('organizer_name', 255);
            $table->string('organizer_email', 255)->nullable();
            $table->string('organizer_phone', 50)->nullable();
            $table->integer('max_participants')->nullable();
            $table->integer('current_participants')->default(0);
            $table->string('age_restriction', 50)->default('all');
            $table->json('price_info')->nullable();
            $table->json('schedule')->nullable();
            $table->json('requirements')->nullable();
            $table->json('facilities')->nullable();
            $table->json('contact_info')->nullable();
            $table->json('social_sharing')->nullable();
            $table->json('images')->nullable();
            $table->string('cover_image', 500)->nullable();
            $table->json('gallery')->nullable();
            $table->json('tags')->nullable();
            $table->json('metadata')->nullable();
            $table->integer('view_count')->default(0);
            $table->decimal('rating', 2, 1)->default(0);
            $table->integer('review_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['community_id']);
            $table->index(['event_type']);
            $table->index(['status']);
            $table->index(['start_date']);
            $table->index(['end_date']);
            $table->index(['rating']);
            $table->index(['view_count']);
            $table->index(['current_participants']);

            // Spatial index for coordinates (if supported)
            $table->index(['latitude', 'longitude']);

            // Regular index for search (removed full-text due to length limit)
            $table->index('title');
            $table->index('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_events');
    }
};
