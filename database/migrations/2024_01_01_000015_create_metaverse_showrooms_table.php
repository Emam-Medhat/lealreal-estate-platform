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
        Schema::create('metaverse_showrooms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('virtual_world_id')->constrained();
            $table->string('showroom_type');
            $table->string('location_coordinates');
            $table->json('dimensions')->nullable();
            $table->string('access_level');
            $table->integer('capacity');
            $table->string('theme')->nullable();
            $table->json('lighting_settings')->nullable();
            $table->json('audio_settings')->nullable();
            $table->json('interactive_elements')->nullable();
            $table->string('status')->default('active');
            $table->boolean('is_active')->default(true);
            $table->integer('current_visitors')->default(0);
            $table->integer('max_visitors');
            $table->json('features')->nullable();
            $table->json('amenities')->nullable();
            $table->json('multimedia_content')->nullable();
            $table->json('navigation_settings')->nullable();
            $table->json('ambient_settings')->nullable();
            $table->json('customization_options')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('visit_count')->default(0);
            $table->integer('event_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['virtual_world_id', 'status']);
            $table->index(['owner_id', 'status']);
            $table->index(['showroom_type', 'status']);
            $table->index(['access_level', 'status']);
            $table->index(['is_active', 'status']);
            $table->index(['created_at', 'status']);
            $table->index(['updated_at', 'status']);

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
        Schema::dropIfExists('metaverse_showrooms');
    }
};
