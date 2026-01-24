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
        Schema::create('virtual_lands', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('virtual_world_id')->constrained()->onDelete('cascade');
            $table->string('land_type');
            $table->string('coordinates');
            $table->decimal('area', 10, 2);
            $table->string('area_unit', 20)->default('sqm');
            $table->json('dimensions')->nullable();
            $table->decimal('price', 15, 2);
            $table->string('currency', 10)->default('USD');
            $table->string('ownership_status')->default('available');
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('zoning_types')->nullable();
            $table->integer('max_building_height')->nullable();
            $table->decimal('min_lot_size', 10, 2)->nullable();
            $table->json('setback_requirements')->nullable();
            $table->json('parking_requirements')->nullable();
            $table->string('development_status')->default('undeveloped');
            $table->string('development_type')->nullable();
            $table->json('development_plan')->nullable();
            $table->decimal('estimated_development_cost', 15, 2)->nullable();
            $table->string('estimated_development_timeline')->nullable();
            $table->boolean('zoning_compliance')->default(false);
            $table->json('environmental_impact_assessment')->nullable();
            $table->json('infrastructure_requirements')->nullable();
            $table->timestamp('last_purchase_date')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->string('purchase_currency', 10)->nullable();
            $table->timestamp('last_transfer_date')->nullable();
            $table->integer('max_properties')->default(10);
            $table->boolean('is_prime_location')->default(false);
            $table->boolean('is_waterfront')->default(false);
            $table->string('terrain_type')->nullable();
            $table->string('soil_quality')->nullable();
            $table->decimal('elevation', 8, 2)->nullable();
            $table->decimal('distance_from_coast', 8, 2)->nullable();
            $table->decimal('water_body_proximity', 8, 2)->nullable();
            $table->string('flood_zone')->nullable();
            $table->json('utilities_available')->nullable();
            $table->json('access_roads')->nullable();
            $table->boolean('public_transport_access')->default(false);
            $table->json('nearby_amenities')->nullable();
            $table->decimal('market_value', 15, 2)->nullable();
            $table->decimal('assessment_value', 15, 2)->nullable();
            $table->decimal('tax_assessment', 15, 2)->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('inquiry_count')->default(0);
            $table->integer('offer_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['virtual_world_id', 'ownership_status']);
            $table->index(['owner_id', 'ownership_status']);
            $table->index(['land_type', 'ownership_status']);
            $table->index(['is_prime_location', 'ownership_status']);
            $table->index(['is_waterfront', 'ownership_status']);
            $table->index(['price', 'currency']);
            $table->index(['area', 'area_unit']);
            $table->index(['development_status', 'ownership_status']);
            $table->index('view_count');
            $table->index('inquiry_count');
            $table->index('offer_count');
            $table->index('created_at');
            $table->index('updated_at');

            // Regular index for search (SQLite doesn't support full-text index)
            $table->index('title');
            $table->index('description');
            $table->index('coordinates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_lands');
    }
};
