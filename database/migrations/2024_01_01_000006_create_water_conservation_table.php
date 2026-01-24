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
        Schema::create('water_conservation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->decimal('water_consumption', 10, 2)->default(0);
            $table->string('consumption_unit', 50);
            $table->integer('water_efficiency_rating')->default(0);
            $table->boolean('rainwater_harvesting')->default(false);
            $table->decimal('rainwater_capacity', 8, 2)->nullable();
            $table->boolean('greywater_recycling')->default(false);
            $table->decimal('greywater_capacity', 8, 2)->nullable();
            $table->boolean('low_flow_fixtures')->default(false);
            $table->json('fixture_types')->nullable();
            $table->boolean('smart_irrigation')->default(false);
            $table->string('irrigation_type', 100)->nullable();
            $table->boolean('drip_irrigation')->default(false);
            $table->boolean('xeriscaping')->default(false);
            $table->boolean('native_plants')->default(false);
            $table->boolean('leak_detection_system')->default(false);
            $table->boolean('water_metering')->default(false);
            $table->boolean('water_pressure_optimization')->default(false);
            $table->boolean('hot_water_efficiency')->default(false);
            $table->string('hot_water_system_type', 100)->nullable();
            $table->boolean('pool_cover')->default(false);
            $table->boolean('pool_recycling_system')->default(false);
            $table->boolean('water_treatment_system')->default(false);
            $table->string('treatment_type', 100)->nullable();
            $table->json('conservation_goals')->nullable();
            $table->string('monitoring_frequency', 50)->nullable();
            $table->date('assessment_date');
            $table->date('next_assessment_date')->nullable();
            $table->foreignId('assessed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('potential_savings', 5, 2)->default(0);
            $table->json('recommendations')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['property_id']);
            $table->index(['water_efficiency_rating']);
            $table->index(['assessment_date']);
            $table->index(['assessed_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_conservation');
    }
};
