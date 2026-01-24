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
        Schema::create('developer_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('description_ar')->nullable();
            $table->enum('project_type', ['residential', 'commercial', 'mixed', 'industrial', 'retail', 'office', 'hospitality']);
            $table->enum('status', ['planning', 'under_construction', 'completed', 'on_hold', 'cancelled'])->default('planning');
            $table->enum('visibility', ['public', 'private', 'featured'])->default('public');
            $table->json('location');
            $table->decimal('total_area', 12, 2);
            $table->string('area_unit')->default('sqm');
            $table->decimal('total_investment', 15, 2);
            $table->string('currency')->default('SAR');
            $table->date('start_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->date('expected_completion_date')->nullable();
            $table->integer('total_units')->default(0);
            $table->integer('sold_units')->default(0);
            $table->integer('available_units')->default(0);
            $table->json('unit_types')->nullable();
            $table->decimal('min_unit_price', 12, 2)->nullable();
            $table->decimal('max_unit_price', 12, 2)->nullable();
            $table->json('amenities')->nullable();
            $table->json('facilities')->nullable();
            $table->json('nearby_landmarks')->nullable();
            $table->json('infrastructure')->nullable();
            $table->json('payment_plans')->nullable();
            $table->json('financing_options')->nullable();
            $table->json('construction_company')->nullable();
            $table->json('architecture_firm')->nullable();
            $table->json('engineering_consultant')->nullable();
            $table->json('interior_design')->nullable();
            $table->json('landscape_design')->nullable();
            $table->json('security_systems')->nullable();
            $table->json('smart_home_features')->nullable();
            $table->json('sustainability_features')->nullable();
            $table->json('certifications')->nullable();
            $table->json('awards')->nullable();
            $table->json('media_gallery')->nullable();
            $table->string('brochure_pdf')->nullable();
            $table->string('floor_plan_pdf')->nullable();
            $table->string('video_tour')->nullable();
            $table->string('virtual_tour_url')->nullable();
            $table->json('social_media_links')->nullable();
            $table->json('contact_information')->nullable();
            $table->json('showroom_details')->nullable();
            $table->json('sales_office')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('allow_online_booking')->default(true);
            $table->boolean('enable_virtual_tour')->default(false);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->integer('inquiry_count')->default(0);
            $table->integer('favorite_count')->default(0);
            $table->json('seo_settings')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['developer_id', 'status']);
            $table->index(['project_type']);
            $table->index(['visibility']);
            $table->index(['is_featured']);
            $table->index(['rating']);
            $table->index(['completion_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_projects');
    }
};
