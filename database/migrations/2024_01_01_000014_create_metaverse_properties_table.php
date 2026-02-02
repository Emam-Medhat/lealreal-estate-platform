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
        Schema::create('metaverse_properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('virtual_world_id')->constrained()->onDelete('cascade');
            $table->string('property_type');
            $table->string('location_coordinates');
            $table->json('dimensions')->nullable();
            $table->decimal('price', 15, 2);
            $table->string('currency', 10)->default('USD');
            $table->boolean('is_for_sale')->default(false);
            $table->boolean('is_for_rent')->default(false);
            $table->decimal('rent_price', 15, 2)->nullable();
            $table->string('rent_currency', 10)->nullable();
            $table->string('rent_period')->nullable();
            $table->string('status')->default('active');
            $table->string('visibility')->default('public');
            $table->string('access_level')->default('public');
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('virtual_property_design_id')->nullable();
            $table->foreignId('nft_id')->nullable();
            $table->boolean('is_nft')->default(false);
            $table->json('features')->nullable();
            $table->json('amenities')->nullable();
            $table->json('utilities')->nullable();
            $table->json('zoning_info')->nullable();
            $table->json('building_restrictions')->nullable();
            $table->json('environmental_settings')->nullable();
            $table->json('security_settings')->nullable();
            $table->json('accessibility_features')->nullable();
            $table->json('multimedia_settings')->nullable();
            $table->json('interaction_settings')->nullable();
            $table->json('customization_options')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->integer('share_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->timestamp('last_visit_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['virtual_world_id', 'status']);
            $table->index(['owner_id', 'status']);
            $table->index(['property_type', 'status']);
            $table->index(['is_for_sale', 'status']);
            $table->index(['is_nft', 'status']);
            $table->index(['visibility', 'status']);
            $table->index(['access_level', 'status']);
            $table->index(['price', 'currency']);
            $table->index(['rating_average', 'rating_count']);
            $table->index('view_count');
            $table->index('like_count');
            $table->index('created_at');
            $table->index('updated_at');

            // Regular index for search (SQLite doesn't support index)
            $table->index('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metaverse_properties');
    }
};
