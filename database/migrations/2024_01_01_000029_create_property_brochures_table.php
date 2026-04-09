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
        if (!Schema::hasTable('property_brochures')) {
        Schema::create('property_brochures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('template'); // modern, classic, luxury, minimal, corporate
            $table->string('format'); // a4, a5, letter, legal, square
            $table->string('orientation'); // portrait, landscape
            $table->string('status')->default('draft'); // draft, processing, published, archived
            $table->string('cover_image')->nullable();
            $table->string('logo')->nullable();
            $table->json('gallery_images')->nullable();
            $table->json('features')->nullable();
            $table->json('amenities')->nullable();
            $table->json('contact_info')->nullable();
            $table->json('pricing_info')->nullable();
            $table->json('custom_colors')->nullable();
            $table->string('font_family')->nullable();
            $table->boolean('include_floor_plans')->default(false);
            $table->boolean('include_location_map')->default(false);
            $table->boolean('include_qr_code')->default(false);
            $table->string('pdf_file')->nullable();
            
            // Performance metrics
            $table->bigInteger('download_count')->default(0);
            $table->bigInteger('view_count')->default(0);
            
            // Timestamps
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['property_id', 'status']);
            $table->index(['template', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('generated_at');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_brochures');
    }
};
