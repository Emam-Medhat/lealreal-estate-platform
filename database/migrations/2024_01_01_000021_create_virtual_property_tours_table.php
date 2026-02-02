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
        if (!Schema::hasTable('virtual_property_tours')) {
        Schema::create('virtual_property_tours', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('tour_type');
            $table->foreignId('metaverse_property_id')->nullable()->constrained();
            $table->foreignId('metaverse_showroom_id')->nullable()->constrained();
            $table->foreignId('guide_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('duration_minutes');
            $table->integer('max_participants');
            $table->decimal('price', 15, 2)->nullable();
            $table->string('currency', 10)->default('USD');
            $table->json('languages')->nullable();
            $table->string('difficulty_level')->default('beginner');
            $table->json('accessibility_features')->nullable();
            $table->json('equipment_required')->nullable();
            $table->json('tour_points')->nullable();
            $table->json('schedule_settings')->nullable();
            $table->json('interactive_elements')->nullable();
            $table->json('multimedia_content')->nullable();
            $table->json('navigation_settings')->nullable();
            $table->json('customization_options')->nullable();
            $table->string('status')->default('active');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('view_count')->default(0);
            $table->integer('participant_count')->default(0);
            $table->integer('session_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['metaverse_property_id', 'status']);
            $table->index(['metaverse_showroom_id', 'status']);
            $table->index(['guide_id', 'status']);
            $table->index(['tour_type', 'status']);
            $table->index(['difficulty_level', 'status']);
            $table->index(['is_active', 'status']);
            $table->index(['is_featured', 'status']);
            $table->index(['price', 'currency', 'status']);
            $table->index(['duration_minutes', 'status']);
            $table->index(['max_participants', 'status']);
            $table->index(['view_count', 'status']);
            $table->index(['participant_count', 'status']);
            $table->index(['session_count', 'status']);
            $table->index(['rating_average', 'status']);
            $table->index(['rating_count', 'status']);
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
        Schema::dropIfExists('virtual_property_tours');
    }
};
