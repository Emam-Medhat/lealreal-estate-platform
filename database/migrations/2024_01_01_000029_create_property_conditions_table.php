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
        if (!Schema::hasTable('property_conditions')) {
        Schema::create('property_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('inspection_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('overall_condition', ['excellent', 'good', 'fair', 'poor', 'very_poor']);
            $table->integer('condition_score')->default(0); // 0-100
            $table->json('exterior_condition')->nullable();
            $table->json('interior_condition')->nullable();
            $table->json('structural_condition')->nullable();
            $table->json('mechanical_systems')->nullable();
            $table->json('electrical_systems')->nullable();
            $table->json('plumbing_systems')->nullable();
            $table->json('hvac_systems')->nullable();
            $table->json('roof_condition')->nullable();
            $table->json('foundation_condition')->nullable();
            $table->json('windows_doors')->nullable();
            $table->json('flooring_condition')->nullable();
            $table->json('walls_ceiling')->nullable();
            $table->json('kitchen_condition')->nullable();
            $table->json('bathroom_condition')->nullable();
            $table->json('safety_features')->nullable();
            $table->json('accessibility_features')->nullable();
            $table->json('maintenance_needs')->nullable();
            $table->json('improvements_needed')->nullable();
            $table->decimal('estimated_repair_cost', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable();
            $table->json('photos')->nullable();
            $table->date('assessment_date');
            $table->date('next_assessment_date')->nullable();
            $table->timestamps();
            
            $table->index(['property_id', 'assessment_date']);
            $table->index(['overall_condition']);
            $table->index(['condition_score']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_conditions');
    }
};
