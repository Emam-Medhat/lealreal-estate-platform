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
        Schema::create('smart_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->boolean('has_smart_thermostat')->default(false);
            $table->boolean('has_smart_lighting')->default(false);
            $table->boolean('has_smart_security')->default(false);
            $table->boolean('has_smart_locks')->default(false);
            $table->boolean('has_smart_sprinklers')->default(false);
            $table->boolean('has_energy_monitoring')->default(false);
            $table->boolean('has_solar_panels')->default(false);
            $table->boolean('has_smart_appliances')->default(false);
            $table->boolean('has_voice_control')->default(false);
            $table->boolean('has_automated_blinds')->default(false);
            $table->boolean('has_smart_hvac')->default(false);
            $table->text('smart_features')->nullable();
            $table->json('device_list')->nullable();
            $table->string('automation_system')->nullable();
            $table->string('security_system')->nullable();
            $table->string('energy_management_system')->nullable();
            $table->decimal('energy_efficiency_score', 5, 2)->nullable();
            $table->text('smart_home_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smart_properties');
    }
};
