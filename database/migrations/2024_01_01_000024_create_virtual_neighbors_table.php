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
        Schema::create('virtual_neighbors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('virtual_land_id')->constrained();
            $table->foreignId('neighbor_land_id')->constrained('virtual_lands');
            $table->string('relationship_type');
            $table->decimal('distance', 10, 2);
            $table->string('distance_unit', 10)->default('meters');
            $table->string('direction')->nullable();
            $table->json('shared_boundaries')->nullable();
            $table->json('access_points')->nullable();
            $table->json('shared_utilities')->nullable();
            $table->json('joint_development_opportunities')->nullable();
            $table->json('zoning_compatibility')->nullable();
            $table->json('neighborhood_rating')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['virtual_land_id', 'is_active']);
            $table->index(['neighbor_land_id', 'is_active']);
            $table->index(['relationship_type', 'is_active']);
            $table->index(['distance', 'is_active']);
            $table->index(['direction', 'is_active']);
            $table->index(['is_active', 'created_at']);

            // Full-text search index

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_neighbors');
    }
};
