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
        if (!Schema::hasTable('energy_efficiency')) {
        Schema::create('energy_efficiency', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->decimal('efficiency_rating', 5, 2)->default(0);
            $table->decimal('energy_consumption', 10, 2)->default(0);
            $table->string('energy_source', 100);
            $table->decimal('renewable_energy_percentage', 5, 2)->default(0);
            $table->integer('insulation_rating')->default(0);
            $table->integer('hvac_efficiency')->default(0);
            $table->integer('lighting_efficiency')->default(0);
            $table->integer('appliance_efficiency')->default(0);
            $table->boolean('solar_panels')->default(false);
            $table->decimal('solar_capacity', 8, 2)->nullable();
            $table->boolean('smart_thermostat')->default(false);
            $table->boolean('energy_monitoring')->default(false);
            $table->boolean('led_lighting')->default(false);
            $table->boolean('double_glazing')->default(false);
            $table->boolean('energy_star_appliances')->default(false);
            $table->date('assessment_date');
            $table->date('next_assessment_date')->nullable();
            $table->foreignId('assessed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('recommendations')->nullable();
            $table->decimal('potential_savings', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['property_id']);
            $table->index(['efficiency_rating']);
            $table->index(['assessment_date']);
            $table->index(['assessed_by']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('energy_efficiency');
    }
};
