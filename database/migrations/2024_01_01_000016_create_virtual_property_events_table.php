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
        if (!Schema::hasTable('virtual_property_events')) {
        Schema::create('virtual_property_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('event_type');
            $table->foreignId('metaverse_property_id')->constrained();
            $table->foreignId('metaverse_showroom_id')->nullable()->constrained()->onDelete('set null');
            $table->string('location_coordinates');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->string('timezone')->default('UTC');
            $table->integer('max_participants');
            $table->decimal('price', 15, 2)->nullable();
            $table->string('currency', 10)->default('USD');
            $table->string('access_level')->default('public');
            $table->json('schedule_settings')->nullable();
            $table->json('interactive_elements')->nullable();
            $table->json('multimedia_content')->nullable();
            $table->json('requirements')->nullable();
            $table->json('agenda')->nullable();
            $table->json('speakers')->nullable();
            $table->json('sponsors')->nullable();
            $table->json('tags')->nullable();
            $table->string('status')->default('scheduled');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('participant_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->integer('share_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->foreignId('host_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['metaverse_property_id', 'status']);
            $table->index(['metaverse_showroom_id', 'status']);
            $table->index(['event_type', 'status']);
            $table->index(['start_time', 'status']);
            $table->index(['access_level', 'status']);
            $table->index(['is_featured', 'status']);
            $table->index(['price', 'currency', 'status']);
            $table->index(['participant_count', 'status']);
            $table->index(['view_count', 'status']);
            $table->index(['like_count', 'status']);
            $table->index(['rating_average', 'status']);
            $table->index(['created_at', 'status']);
            $table->index(['updated_at', 'status']);

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
        Schema::dropIfExists('virtual_property_events');
    }
};
