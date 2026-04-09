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
        if (!Schema::hasTable('company_profiles')) {
            if (!Schema::hasTable('company_profiles')) {
        Schema::create('company_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->text('description')->nullable();
                $table->date('founded_date')->nullable();
                $table->integer('employee_count')->nullable();
                $table->decimal('annual_revenue', 15, 2)->nullable();
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('country')->nullable();
                $table->string('postal_code')->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('website')->nullable();
                $table->string('logo')->nullable();
                $table->string('cover_image')->nullable();
                $table->json('services')->nullable();
                $table->json('specializations')->nullable();
                $table->json('certifications')->nullable();
                $table->json('awards')->nullable();
                $table->json('social_links')->nullable();
                $table->string('contact_person')->nullable();
                $table->string('contact_title')->nullable();
                $table->json('business_hours')->nullable();
                $table->json('languages')->nullable();
                $table->json('service_areas')->nullable();
                $table->string('company_size')->nullable();
                $table->string('industry')->nullable();
                $table->json('target_markets')->nullable();
                $table->text('mission_statement')->nullable();
                $table->text('vision_statement')->nullable();
                $table->text('company_values')->nullable();
                $table->text('history')->nullable();
                $table->text('achievements')->nullable();
                $table->json('partners')->nullable();
                $table->json('clients')->nullable();
                $table->json('testimonials')->nullable();
                $table->json('faq')->nullable();
                $table->json('press_mentions')->nullable();
                $table->json('gallery_images')->nullable();
                $table->string('video_url')->nullable();
                $table->string('virtual_tour_url')->nullable();
                $table->string('brokerage_license')->nullable();
                $table->text('insurance_info')->nullable();
                $table->text('compliance_info')->nullable();
                $table->string('seo_title')->nullable();
                $table->text('seo_description')->nullable();
                $table->string('seo_keywords')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};
