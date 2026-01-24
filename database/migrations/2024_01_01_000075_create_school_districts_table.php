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
        Schema::create('school_districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained('metaverse_properties')->onDelete('cascade');
            $table->foreignId('analytics_id')->nullable()->constrained('geospatial_analytics')->onDelete('cascade');
            $table->string('district_name');
            $table->string('district_type');
            $table->decimal('elementary_rating', 5, 2)->nullable();
            $table->decimal('middle_rating', 5, 2)->nullable();
            $table->decimal('high_rating', 5, 2)->nullable();
            $table->decimal('overall_rating', 5, 2)->nullable();
            $table->integer('school_count')->nullable();
            $table->decimal('student_teacher_ratio', 5, 2)->nullable();
            $table->json('academic_performance')->nullable();
            $table->json('extracurricular_activities')->nullable();
            $table->json('school_types')->nullable();
            $table->json('transportation_options')->nullable();
            $table->json('improvement_suggestions')->nullable();
            $table->json('analysis_parameters')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['property_id', 'district_type']);
            $table->index('analytics_id');
            $table->index('status');
            $table->index('district_type');
            $table->index('overall_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_districts');
    }
};
