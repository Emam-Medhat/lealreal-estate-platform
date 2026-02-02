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
        if (!Schema::hasTable('developer_project_units')) {
        Schema::create('developer_project_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('developer_projects')->onDelete('cascade');
            $table->foreignId('phase_id')->nullable()->constrained('developer_project_phases')->onDelete('set null');
            $table->string('unit_number')->unique();
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->enum('unit_type', ['apartment', 'villa', 'townhouse', 'penthouse', 'studio', 'duplex', 'office', 'retail', 'warehouse', 'land']);
            $table->enum('status', ['available', 'reserved', 'sold', 'under_contract', 'off_market', 'maintenance'])->default('available');
            $table->enum('view_type', ['street', 'garden', 'sea', 'lake', 'mountain', 'city', 'park', 'courtyard']);
            $table->decimal('area', 10, 2);
            $table->decimal('net_area', 10, 2)->nullable();
            $table->string('area_unit')->default('sqm');
            $table->integer('bedrooms')->default(0);
            $table->integer('bathrooms')->default(0);
            $table->integer('parking_spaces')->default(0);
            $table->integer('balconies')->default(0);
            $table->string('floor_number')->nullable();
            $table->string('building_number')->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('price_per_sqm', 10, 2)->nullable();
            $table->string('currency')->default('SAR');
            $table->boolean('is_negotiable')->default(false);
            $table->boolean('includes_vat')->default(false);
            $table->decimal('vat_rate', 5, 2)->default(0);
            $table->decimal('service_charges', 10, 2)->nullable();
            $table->decimal('maintenance_fees', 10, 2)->nullable();
            $table->string('payment_frequency')->nullable(); // monthly, quarterly, annually
            $table->json('payment_terms')->nullable();
            $table->json('payment_plan')->nullable();
            $table->json('financing_options')->nullable();
            $table->json('features')->nullable();
            $table->json('amenities')->nullable();
            $table->json('finishes')->nullable();
            $table->json('appliances')->nullable();
            $table->json('furniture')->nullable();
            $table->json('technical_specifications')->nullable();
            $table->json('floor_plan')->nullable();
            $table->json('media_gallery')->nullable();
            $table->string('brochure_pdf')->nullable();
            $table->string('video_tour')->nullable();
            $table->string('virtual_tour_url')->nullable();
            $table->json('layout_diagram')->nullable();
            $table->json('dimensions')->nullable();
            $table->json('orientation')->nullable();
            $table->json('energy_efficiency')->nullable();
            $table->json('smart_home_features')->nullable();
            $table->json('security_features')->nullable();
            $table->json('climate_control')->nullable();
            $table->json('storage_spaces')->nullable();
            $table->boolean('is_furnished')->default(false);
            $table->boolean('is_corner_unit')->default(false);
            $table->boolean('is_penthouse')->default(false);
            $table->boolean('is_ground_floor')->default(false);
            $table->boolean('has_private_entrance')->default(false);
            $table->boolean('has_private_elevator')->default(false);
            $table->boolean('has_private_pool')->default(false);
            $table->boolean('has_private_garden')->default(false);
            $table->boolean('has_terrace')->default(false);
            $table->boolean('is_accessible')->default(false);
            $table->boolean('is_pet_friendly')->default(true);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->integer('inquiry_count')->default(0);
            $table->integer('favorite_count')->default(0);
            $table->timestamp('listed_at')->nullable();
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->foreignId('reserved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('sold_to')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['project_id', 'status']);
            $table->index(['phase_id', 'status']);
            $table->index(['unit_type']);
            $table->index(['unit_number']);
            $table->index(['price']);
            $table->index(['bedrooms']);
            $table->index(['area']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_project_units');
    }
};
