<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('smart_properties')) {
        Schema::create('smart_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            
            // Smart Level & Features
            $table->enum('smart_level', ['basic', 'advanced', 'premium'])->default('basic');
            $table->boolean('automation_enabled')->default(true);
            $table->boolean('energy_monitoring_enabled')->default(true);
            $table->boolean('security_enabled')->default(false);
            $table->boolean('climate_control_enabled')->default(false);
            $table->boolean('water_management_enabled')->default(false);
            $table->boolean('air_quality_monitoring_enabled')->default(false);
            $table->boolean('smart_lock_enabled')->default(false);
            $table->boolean('voice_control_enabled')->default(false);
            $table->boolean('ai_optimization_enabled')->default(false);
            
            // System Status
            $table->timestamp('last_sync_at')->nullable();
            $table->integer('device_count')->default(0);
            $table->integer('active_alerts_count')->default(0);
            $table->enum('current_status', ['active', 'inactive', 'maintenance', 'error'])->default('active');
            
            // Performance Metrics
            $table->integer('energy_efficiency_score')->default(50);
            $table->integer('security_score')->default(50);
            $table->integer('health_score')->default(50);
            $table->decimal('uptime_percentage', 5, 2)->default(99.00);
            $table->integer('response_time_ms')->default(100);
            
            // Energy & Cost
            $table->decimal('monthly_energy_cost', 8, 2)->default(0);
            $table->decimal('monthly_water_cost', 8, 2)->default(0);
            $table->decimal('carbon_footprint', 8, 2)->default(0);
            $table->decimal('power_consumption_watts', 8, 2)->default(0);
            
            // Smart Home Integration
            $table->enum('smart_home_hub_type', ['alexa', 'google_home', 'apple_homekit', 'custom'])->nullable();
            $table->enum('integration_status', ['connected', 'disconnected', 'error'])->default('disconnected');
            $table->string('firmware_version')->nullable();
            
            // Maintenance & Support
            $table->date('last_maintenance_check')->nullable();
            $table->date('next_maintenance_due')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->boolean('insurance_coverage')->default(false);
            
            // Security & Privacy
            $table->json('emergency_contacts')->nullable();
            $table->json('access_permissions')->nullable();
            $table->json('privacy_settings')->nullable();
            $table->integer('data_retention_days')->default(90);
            $table->boolean('backup_enabled')->default(false);
            $table->boolean('remote_access_enabled')->default(true);
            $table->boolean('geofencing_enabled')->default(false);
            
            // Automation
            $table->boolean('schedule_automation_enabled')->default(false);
            $table->enum('energy_saving_mode', ['off', 'eco', 'max'])->default('off');
            $table->enum('security_mode', ['home', 'away', 'vacation', 'night'])->default('home');
            
            // Device Counts
            $table->integer('temperature_sensors_count')->default(0);
            $table->integer('humidity_sensors_count')->default(0);
            $table->integer('motion_sensors_count')->default(0);
            $table->integer('door_sensors_count')->default(0);
            $table->integer('window_sensors_count')->default(0);
            $table->integer('smoke_detectors_count')->default(0);
            $table->integer('water_leak_detectors_count')->default(0);
            $table->integer('air_quality_sensors_count')->default(0);
            $table->integer('smart_bulbs_count')->default(0);
            $table->integer('smart_switches_count')->default(0);
            $table->integer('smart_thermostats_count')->default(0);
            $table->integer('smart_locks_count')->default(0);
            $table->integer('security_cameras_count')->default(0);
            $table->integer('smart_plugs_count')->default(0);
            
            // Advanced Features
            $table->integer('automation_rules_count')->default(0);
            $table->decimal('battery_backup_hours', 5, 2)->default(0);
            $table->decimal('data_usage_mb', 8, 2)->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['property_id']);
            $table->index(['smart_level']);
            $table->index(['current_status']);
            $table->index(['automation_enabled']);
            $table->index(['energy_monitoring_enabled']);
            $table->index(['security_enabled']);
            $table->index(['ai_optimization_enabled']);
            $table->index(['health_score']);
            $table->index(['last_sync_at']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('smart_properties');
    }
};
