<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('iot_devices')) {
        Schema::create('iot_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('smart_property_id')->constrained()->onDelete('cascade');
            
            // Device Information
            $table->string('device_type'); // thermostat, lock, camera, sensor, light, etc.
            $table->string('brand');
            $table->string('model');
            $table->string('serial_number');
            $table->string('mac_address');
            $table->string('ip_address')->nullable();
            $table->string('firmware_version')->nullable();
            $table->string('hardware_version')->nullable();
            
            // Installation & Warranty
            $table->date('installation_date');
            $table->date('warranty_expiry')->nullable();
            
            // Status & Connectivity
            $table->enum('status', ['active', 'inactive', 'offline', 'error', 'maintenance'])->default('active');
            $table->integer('battery_level')->nullable(); // 0-100
            $table->integer('signal_strength')->nullable(); // dBm
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('last_data_received_at')->nullable();
            
            // Location
            $table->string('location_within_property'); // living_room, bedroom_1, etc.
            $table->string('room_name')->nullable();
            $table->string('floor')->nullable();
            $table->string('zone')->nullable();
            
            // Criticality & Priority
            $table->boolean('is_critical')->default(false);
            $table->enum('access_level', ['public', 'private', 'restricted'])->default('private');
            
            // Features & Capabilities
            $table->boolean('auto_update_enabled')->default(true);
            $table->boolean('notifications_enabled')->default(true);
            $table->boolean('encryption_enabled')->default(true);
            $table->enum('security_level', ['basic', 'standard', 'high'])->default('standard');
            
            // Communication
            $table->enum('communication_protocol', ['wifi', 'zigbee', 'z-wave', 'bluetooth', 'lora', 'cellular'])->default('wifi');
            
            // Performance
            $table->integer('sampling_rate_seconds')->default(60);
            $table->decimal('power_consumption_watts', 8, 2)->default(0);
            $table->decimal('standby_power_watts', 8, 2)->default(0);
            $table->decimal('operating_voltage', 6, 2)->nullable();
            
            // Data & Storage
            $table->integer('data_retention_days')->default(30);
            $table->decimal('data_transferred_mb', 10, 2)->default(0);
            
            // Maintenance & Reliability
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->decimal('maintenance_cost_total', 8, 2)->default(0);
            $table->integer('downtime_minutes')->default(0);
            $table->decimal('uptime_percentage', 5, 2)->default(99.00);
            $table->integer('response_time_ms')->default(100);
            
            // Usage Statistics
            $table->integer('error_count')->default(0);
            $table->integer('warning_count')->default(0);
            $table->integer('critical_alerts_count')->default(0);
            $table->integer('user_interaction_count')->default(0);
            $table->integer('automated_actions_count')->default(0);
            
            // Energy & Cost
            $table->decimal('energy_saved_kwh', 8, 2)->default(0);
            $table->decimal('cost_saved_currency', 8, 2)->default(0);
            $table->decimal('cost_per_month', 8, 2)->default(0);
            $table->decimal('carbon_footprint_kg', 8, 2)->default(0);
            
            // Performance Scores
            $table->integer('efficiency_score')->default(50);
            $table->integer('reliability_score')->default(50);
            $table->integer('user_satisfaction_score')->default(50);
            $table->integer('health_score')->default(50);
            
            // Predictive Analytics
            $table->date('predicted_failure_date')->nullable();
            $table->date('recommended_replacement_date')->nullable();
            $table->date('end_of_life_date')->nullable();
            
            // Financial
            $table->decimal('replacement_cost', 8, 2)->default(0);
            $table->integer('depreciation_years')->default(5);
            $table->decimal('resale_value', 8, 2)->default(0);
            $table->boolean('insurance_covered')->default(false);
            
            // Compliance & Safety
            $table->json('compliance_certifications')->nullable();
            $table->json('safety_ratings')->nullable();
            $table->json('environmental_ratings')->nullable();
            
            // Configuration
            $table->json('user_permissions')->nullable();
            $table->json('automation_rules')->nullable();
            $table->json('schedule_settings')->nullable();
            $table->json('threshold_settings')->nullable();
            $table->json('alert_settings')->nullable();
            $table->json('maintenance_schedule')->nullable();
            $table->json('calibration_settings')->nullable();
            $table->json('configuration_json')->nullable();
            $table->json('custom_attributes')->nullable();
            
            // Support & Documentation
            $table->text('installation_notes')->nullable();
            $table->string('manufacturer_warranty_url')->nullable();
            $table->string('user_manual_url')->nullable();
            $table->string('support_contact')->nullable();
            
            // Backup & Redundancy
            $table->boolean('backup_power_required')->default(false);
            $table->boolean('redundancy_required')->default(false);
            $table->decimal('battery_backup_hours', 5, 2)->default(0);
            
            // Update History
            $table->integer('battery_replacement_count')->default(0);
            $table->integer('firmware_update_count')->default(0);
            $table->integer('configuration_change_count')->default(0);
            
            // API & Integration
            $table->string('api_endpoint')->nullable();
            $table->string('webhook_url')->nullable();
            $table->string('oauth_token')->nullable();
            $table->string('refresh_token')->nullable();
            $table->enum('integration_status', ['connected', 'disconnected', 'error'])->default('disconnected');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['smart_property_id']);
            $table->index(['device_type']);
            $table->index(['brand']);
            $table->index(['status']);
            $table->index(['is_critical']);
            $table->index(['last_seen_at']);
            $table->index(['health_score']);
            $table->index(['efficiency_score']);
            // Unique indexes moved to separate migration to avoid conflicts
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_devices');
    }
};
