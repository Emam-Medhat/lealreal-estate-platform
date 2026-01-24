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
        Schema::create('developer_portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->enum('portfolio_type', ['residential', 'commercial', 'mixed', 'industrial', 'hospitality', 'retail', 'infrastructure']);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->json('projects')->nullable(); // list of project IDs
            $table->json('featured_projects')->nullable(); // featured project IDs
            $table->json('achievements')->nullable();
            $table->json('statistics')->nullable(); // total units, total area, etc.
            $table->json('key_metrics')->nullable(); // delivery rate, quality metrics
            $table->json('client_testimonials')->nullable();
            $table->json('awards_recognitions')->nullable();
            $table->json('media_coverage')->nullable();
            $table->json('case_studies')->nullable();
            $table->json('success_stories')->nullable();
            $table->json('partnerships')->nullable();
            $table->json('innovation_highlights')->nullable();
            $table->json('sustainability_impact')->nullable();
            $table->json('community_contributions')->nullable();
            $table->json('design_philosophy')->nullable();
            $table->json('construction_approach')->nullable();
            $table->json('quality_commitments')->nullable();
            $table->json('timeline_achievements')->nullable();
            $table->json('financial_highlights')->nullable();
            $table->json('market_presence')->nullable();
            $table->json('geographic_coverage')->nullable();
            $table->json('expertise_areas')->nullable();
            $table->json('specializations')->nullable();
            $table->json('target_markets')->nullable();
            $table->json('competitive_advantages')->nullable();
            $table->json('future_projects')->nullable();
            $table->json('expansion_plans')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();
            $table->string('video_presentation')->nullable();
            $table->string('brochure_pdf')->nullable();
            $table->string('portfolio_pdf')->nullable();
            $table->json('download_links')->nullable();
            $table->json('social_sharing')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_public')->default(true);
            $table->boolean('allow_download')->default(false);
            $table->boolean('enable_sharing')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->integer('share_count')->default(0);
            $table->json('seo_settings')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['developer_id', 'status']);
            $table->index(['portfolio_type']);
            $table->index(['is_featured']);
            $table->index(['published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_portfolios');
    }
};
