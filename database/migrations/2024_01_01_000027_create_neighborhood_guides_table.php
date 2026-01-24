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
        Schema::create('neighborhood_guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('neighborhood_id')->constrained()->onDelete('cascade');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('guide_type', 50)->default('general');
            $table->string('status', 20)->default('draft');
            $table->json('content')->nullable();
            $table->json('media')->nullable();
            $table->json('contact_info')->nullable();
            $table->json('useful_links')->nullable();
            $table->json('emergency_contacts')->nullable();
            $table->json('transportation_info')->nullable();
            $table->json('local_services')->nullable();
            $table->json('cost_of_living')->nullable();
            $table->json('weather_info')->nullable();
            $table->json('cultural_info')->nullable();
            $table->json('metadata')->nullable();
            $table->integer('view_count')->default(0);
            $table->decimal('rating', 2, 1)->default(0);
            $table->integer('review_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['neighborhood_id']);
            $table->index(['guide_type']);
            $table->index(['status']);
            $table->index(['rating']);
            $table->index(['view_count']);

            // Regular index for search (removed full-text due to length limit)
            $table->index('title');
            $table->index('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neighborhood_guides');
    }
};
