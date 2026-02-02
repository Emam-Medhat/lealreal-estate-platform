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
        if (!Schema::hasTable('metaverse_coordinates')) {
        Schema::create('metaverse_coordinates', function (Blueprint $table) {
            $table->id();
            $table->string('coordinate_type');
            $table->string('coordinate_value');
            $table->decimal('x_coordinate', 15, 8)->nullable();
            $table->decimal('y_coordinate', 15, 8)->nullable();
            $table->decimal('z_coordinate', 15, 8)->nullable();
            $table->string('world_name');
            $table->string('zone_name')->nullable();
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);
            $table->foreignId('virtual_world_id')->nullable()->constrained();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['coordinate_type', 'is_active']);
            $table->index(['virtual_world_id', 'is_active']);
            $table->index(['world_name', 'is_active']);
            $table->index(['zone_name', 'is_active']);
            $table->index(['is_active', 'created_at']);
            $table->index(['is_public', 'created_at']);

            // Regular index for search (removed full-text due to length limit)
            // $table->index('coordinate_value');
            // $table->index('description');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metaverse_coordinates');
    }
};
