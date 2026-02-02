<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('water_conservation_measures')) {
        Schema::create('water_conservation_measures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('smart_properties')->onDelete('cascade');
            $table->decimal('water_consumption_baseline', 10, 2)->default(0);
            $table->decimal('current_consumption', 10, 2)->default(0);
            $table->decimal('water_saved', 10, 2)->default(0);
            $table->decimal('conservation_percentage', 5, 2)->default(0);
            $table->json('conservation_measures')->nullable();
            $table->json('water_usage_breakdown')->nullable();
            $table->enum('conservation_level', ['poor', 'fair', 'good', 'excellent', 'outstanding'])->default('poor');
            $table->json('implemented_fixtures')->nullable();
            $table->decimal('cost_savings', 10, 2)->default(0);
            $table->json('leak_detection_data')->nullable();
            $table->date('assessment_date');
            $table->date('next_assessment_date')->nullable();
            $table->enum('status', ['active', 'monitoring', 'improving', 'certified'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['property_id', 'assessment_date']);
            $table->index('conservation_percentage');
            $table->index('conservation_level');
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('water_conservation_measures');
    }
};
