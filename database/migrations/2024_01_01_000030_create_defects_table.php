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
        Schema::create('defects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('inspection_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description');
            $table->text('description_ar')->nullable();
            $table->enum('severity', ['minor', 'moderate', 'major', 'critical']);
            $table->enum('category', ['structural', 'electrical', 'plumbing', 'hvac', 'roofing', 'foundation', 'exterior', 'interior', 'safety', 'accessibility', 'other']);
            $table->enum('urgency', ['low', 'medium', 'high', 'urgent']);
            $table->enum('status', ['identified', 'reported', 'in_progress', 'resolved', 'deferred']);
            $table->string('location')->nullable();
            $table->string('location_ar')->nullable();
            $table->json('photos')->nullable();
            $table->decimal('estimated_repair_cost', 15, 2)->nullable();
            $table->text('repair_recommendation')->nullable();
            $table->text('repair_recommendation_ar')->nullable();
            $table->json('affected_areas')->nullable();
            $table->json('potential_consequences')->nullable();
            $table->json('required_materials')->nullable();
            $table->json('required_permits')->nullable();
            $table->date('identified_date');
            $table->date('target_resolution_date')->nullable();
            $table->date('actual_resolution_date')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->text('resolution_notes_ar')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('resolution_photos')->nullable();
            $table->decimal('actual_repair_cost', 15, 2)->nullable();
            $table->string('contractor_used')->nullable();
            $table->text('warranty_info')->nullable();
            $table->timestamps();
            
            $table->index(['property_id', 'severity']);
            $table->index(['inspection_id']);
            $table->index(['status']);
            $table->index(['urgency']);
            $table->index(['category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defects');
    }
};
