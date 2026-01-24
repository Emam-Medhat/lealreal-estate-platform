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
        Schema::create('developer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->unique()->constrained()->onDelete('cascade');
            $table->string('company_name_ar')->nullable();
            $table->text('about_us')->nullable();
            $table->text('about_us_ar')->nullable();
            $table->text('vision')->nullable();
            $table->text('vision_ar')->nullable();
            $table->text('mission')->nullable();
            $table->text('mission_ar')->nullable();
            $table->json('values')->nullable();
            $table->year('established_year')->nullable();
            $table->integer('employees_count')->default(0);
            $table->integer('engineers_count')->default(0);
            $table->json('headquarters_address');
            $table->json('branches')->nullable();
            $table->json('services')->nullable();
            $table->json('expertise_areas')->nullable();
            $table->json('awards')->nullable();
            $table->json('partners')->nullable();
            $table->json('banking_partners')->nullable();
            $table->json('insurance_partners')->nullable();
            $table->json('legal_partners')->nullable();
            $table->json('media_gallery')->nullable();
            $table->json('company_documents')->nullable();
            $table->json('financial_statements')->nullable();
            $table->json('project_portfolio')->nullable();
            $table->json('certifications_details')->nullable();
            $table->json('quality_standards')->nullable();
            $table->json('sustainability_initiatives')->nullable();
            $table->json('technology_stack')->nullable();
            $table->json('design_philosophy')->nullable();
            $table->json('construction_methods')->nullable();
            $table->json('materials_preference')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('video_presentation')->nullable();
            $table->json('social_media_links')->nullable();
            $table->json('contact_information');
            $table->json('working_hours')->nullable();
            $table->json('languages_supported')->nullable();
            $table->json('payment_methods')->nullable();
            $table->json('warranty_policies')->nullable();
            $table->json('after_sales_service')->nullable();
            $table->json('maintenance_services')->nullable();
            $table->boolean('show_contact_form')->default(true);
            $table->boolean('enable_chat_support')->default(false);
            $table->boolean('allow_online_booking')->default(false);
            $table->json('seo_settings')->nullable();
            $table->json('privacy_settings')->nullable();
            $table->json('notification_preferences')->nullable();
            $table->timestamps();
            
            $table->index(['developer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_profiles');
    }
};
