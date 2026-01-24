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
        Schema::create('property_seo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('page_title');
            $table->text('meta_description');
            $table->json('meta_keywords')->nullable();
            $table->json('focus_keywords');
            $table->string('canonical_url')->nullable();
            $table->json('robots_meta')->nullable();
            $table->json('og_tags')->nullable();
            $table->json('twitter_cards')->nullable();
            $table->json('structured_data')->nullable();
            $table->json('content_optimization')->nullable();
            $table->json('technical_seo')->nullable();
            $table->json('tracking_analytics')->nullable();
            $table->json('local_seo')->nullable();
            $table->json('keyword_research')->nullable();
            $table->json('competitor_analysis')->nullable();
            $table->json('performance_tracking')->nullable();
            
            // SEO metrics
            $table->integer('seo_score')->default(0);
            $table->integer('total_keywords')->default(0);
            
            // Foreign keys
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['property_id', 'seo_score']);
            $table->index('seo_score');
            $table->index(['created_at', 'updated_at']);
            $table->index('page_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_seo');
    }
};
