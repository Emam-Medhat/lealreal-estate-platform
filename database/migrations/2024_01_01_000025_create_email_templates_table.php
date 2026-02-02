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
        if (!Schema::hasTable('email_templates')) {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->text('preheader')->nullable();
            $table->string('template_type'); // newsletter, promotion, announcement, follow_up, welcome, etc.
            $table->string('category')->nullable(); // marketing, transactional, informational, etc.
            $table->longText('html_content');
            $table->longText('text_content')->nullable();
            $table->json('variables')->nullable(); // Available template variables
            $table->json('design_settings')->nullable(); // Colors, fonts, layout options
            $table->json('header_settings')->nullable();
            $table->json('footer_settings')->nullable();
            $table->json('social_links')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0);
            $table->text('description')->nullable();
            
            // Foreign keys
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['template_type', 'is_active']);
            $table->index(['category', 'is_active']);
            $table->index('is_default');
            $table->index('usage_count');
            $table->index(['created_at', 'updated_at']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
