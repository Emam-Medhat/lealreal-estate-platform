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
        Schema::create('virtual_property_designs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('virtual_world_id')->nullable()->constrained();
            $table->foreignId('virtual_land_id')->nullable()->constrained();
            $table->string('design_type');
            $table->string('architectural_style');
            $table->json('building_specifications')->nullable();
            $table->json('materials_used')->nullable();
            $table->json('color_scheme')->nullable();
            $table->json('lighting_design')->nullable();
            $table->json('interior_design')->nullable();
            $table->json('landscape_design')->nullable();
            $table->json('amenities')->nullable();
            $table->json('special_features')->nullable();
            $table->json('blueprint_data')->nullable();
            $table->json('model_data')->nullable();
            $table->json('texture_data')->nullable();
            $table->json('animation_data')->nullable();
            $table->json('interaction_points')->nullable();
            $table->json('navigation_paths')->nullable();
            $table->json('performance_settings')->nullable();
            $table->json('compatibility_settings')->nullable();
            $table->integer('estimated_build_time')->nullable();
            $table->decimal('estimated_cost', 15, 2)->nullable();
            $table->string('currency', 10)->default('USD');
            $table->string('difficulty_level')->default('beginner');
            $table->json('required_skills')->nullable();
            $table->json('tools_needed')->nullable();
            $table->string('status')->default('draft');
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_design_id')->nullable()->constrained('virtual_property_designs')->onDelete('set null');
            $table->integer('download_count')->default(0);
            $table->integer('usage_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_public')->default(true);
            $table->string('license_type')->default('free');
            $table->json('usage_rights')->nullable();
            $table->boolean('commercial_use_allowed')->default(false);
            $table->boolean('modification_allowed')->default(true);
            $table->boolean('attribution_required')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['virtual_world_id', 'status']);
            $table->index(['virtual_land_id', 'status']);
            $table->index(['design_type', 'status']);
            $table->index(['architectural_style', 'status']);
            $table->index(['creator_id', 'status']);
            $table->index(['difficulty_level', 'status']);
            $table->index(['license_type', 'status']);
            $table->index(['is_public', 'status']);
            $table->index(['is_featured', 'status']);
            $table->index(['download_count', 'status']);
            $table->index(['usage_count', 'status']);
            $table->index(['rating_average', 'status']);
            $table->index(['rating_count', 'status']);
            $table->index(['estimated_cost', 'currency', 'status']);
            $table->index(['created_at', 'status']);
            $table->index(['updated_at', 'status']);

            // Regular index for search (removed full-text due to length limit)
            $table->index('title');
            $table->index('description');
            $table->index('architectural_style');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_property_designs');
    }
};
