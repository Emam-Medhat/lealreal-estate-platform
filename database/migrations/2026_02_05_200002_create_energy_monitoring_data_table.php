<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('energy_monitoring_data')) {
        Schema::create('energy_monitoring_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('smart_property_id')->constrained()->onDelete('cascade');
            $table->foreignId('device_id')->nullable();
            
            // Timestamp
            $table->timestamp('recorded_at');
            
            // Data Type & Source
            $table->enum('data_type', ['electricity', 'water', 'gas', 'solar', 'battery'])->default('electricity');
            $table->string('data_source')->default('smart_meter');
            $table->string('meter_id')->nullable();
            
            // Usage Metrics
            $table->decimal('current_usage_kw', 8, 4); // Current power consumption
            $table->decimal('daily_usage_kwh', 8, 4); // Total daily consumption
            $table->decimal('monthly_usage_kwh', 8, 4); // Total monthly consumption
            $table->decimal('peak_usage_kw', 8, 4); // Peak usage for the period
            $table->decimal('off_peak_usage_kw', 8, 4); // Off-peak usage
            
            // Cost Information
            $table->decimal('cost_amount', 8, 2); // Cost for this period
            $table->decimal('cost_per_kwh', 6, 4); // Cost per unit
            $table->decimal('tariff_rate', 6, 4); // Current tariff rate
            $table->string('utility_provider')->nullable();
            $table->string('rate_plan')->nullable();
            $table->enum('contract_type', ['fixed', 'variable', 'time_of_use'])->default('variable');
            
            // Environmental Impact
            $table->decimal('carbon_footprint_kg', 8, 4); // CO2 emissions
            $table->decimal('renewable_percentage', 5, 2)->default(0); // Renewable energy percentage
            $table->decimal('carbon_intensity', 6, 4); // CO2 per kWh
            
            // Environmental Conditions
            $table->decimal('temperature_celsius', 5, 2); // Ambient temperature
            $table->decimal('humidity_percent', 5, 2); // Ambient humidity
            $table->enum('weather_condition', ['sunny', 'cloudy', 'rainy', 'snowy', 'windy'])->nullable();
            
            // Occupancy & Usage Context
            $table->integer('occupancy_count')->default(0);
            $table->enum('time_of_day', ['peak', 'off_peak', 'shoulder'])->nullable();
            $table->enum('season', ['summer', 'winter', 'spring', 'fall'])->nullable();
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->nullable();
            $table->boolean('is_holiday')->default(false);
            $table->boolean('is_weekend')->default(false);
            
            // Renewable Energy & Storage
            $table->decimal('solar_generation_kw', 8, 4)->default(0);
            $table->decimal('battery_charge_percent', 5, 2)->nullable();
            $table->decimal('battery_discharge_kw', 8, 4)->default(0);
            $table->decimal('grid_import_kw', 8, 4)->default(0);
            $table->decimal('grid_export_kw', 8, 4)->default(0);
            $table->decimal('backup_power_usage_kw', 8, 4)->default(0);
            
            // Efficiency & Performance
            $table->integer('energy_efficiency_score')->default(50); // 0-100 efficiency rating
            $table->decimal('baseline_usage_kw', 8, 4); // Expected baseline usage
            $table->decimal('variance_percent', 6, 2)->default(0); // Variance from baseline
            $table->decimal('load_factor', 5, 3); // Load factor percentage
            $table->decimal('power_factor', 5, 3); // Power factor
            
            // Power Quality
            $table->decimal('voltage', 6, 2)->nullable(); // Voltage level
            $table->decimal('current_amperes', 6, 2)->nullable(); // Current draw
            $table->decimal('frequency_hz', 6, 2)->nullable(); // Grid frequency
            $table->decimal('harmonic_distortion', 6, 3)->nullable(); // Power quality metric
            $table->integer('quality_score')->default(50); // Power quality score
            
            // Reliability
            $table->integer('outage_minutes')->default(0); // Power outage duration
            
            // Demand Response
            $table->boolean('demand_response_participation')->default(false);
            $table->decimal('peak_shaving_kw', 8, 4)->default(0);
            $table->decimal('load_shifting_kwh', 8, 4)->default(0);
            
            // Storage & Renewable Efficiency
            $table->decimal('storage_efficiency', 5, 3)->nullable();
            $table->decimal('solar_efficiency', 5, 3)->nullable();
            
            // AI & Analytics
            $table->decimal('predicted_usage_kw', 8, 4)->nullable(); // AI predicted usage
            $table->boolean('anomaly_detected')->default(false);
            $table->decimal('anomaly_score', 5, 3)->default(0); // Anomaly confidence score
            $table->json('optimization_suggestions')->nullable();
            $table->decimal('savings_potential', 8, 2)->default(0);
            $table->decimal('forecast_accuracy', 5, 2)->nullable();
            $table->string('model_version')->nullable();
            
            // Data Quality
            $table->integer('data_quality_score')->default(100);
            $table->integer('missing_data_points')->default(0);
            $table->boolean('estimated_usage_flag')->default(false);
            $table->string('interpolation_method')->nullable();
            
            // Cost Breakdown
            $table->decimal('demand_charges', 8, 2)->default(0);
            $table->decimal('connection_charges', 8, 2)->default(0);
            $table->decimal('tax_amount', 8, 2)->default(0);
            $table->decimal('rebates_amount', 8, 2)->default(0);
            $table->decimal('incentives_amount', 8, 2)->default(0);
            $table->decimal('penalty_amount', 8, 2)->default(0);
            $table->decimal('credits_amount', 8, 2)->default(0);
            $table->decimal('net_metering_balance', 8, 4)->default(0);
            
            // Environmental Credits
            $table->decimal('renewable_credits', 8, 4)->default(0);
            $table->decimal('carbon_credits', 8, 4)->default(0);
            
            // Performance Metrics
            $table->json('appliance_usage_breakdown')->nullable();
            $table->json('zone_usage_breakdown')->nullable();
            $table->json('time_of_usage_breakdown')->nullable();
            $table->json('cost_breakdown')->nullable();
            $table->json('carbon_breakdown')->nullable();
            
            // Benchmarking
            $table->decimal('regional_average', 8, 4)->nullable();
            $table->decimal('national_average', 8, 4)->nullable();
            $table->decimal('peer_comparison', 8, 4)->nullable();
            
            // Normalization
            $table->decimal('normalization_factor', 6, 3)->default(1.0);
            $table->decimal('weather_normalization', 6, 3)->default(1.0);
            $table->decimal('occupancy_normalization', 6, 3)->default(1.0);
            $table->decimal('size_normalization', 6, 3)->default(1.0);
            
            // Intensity Metrics
            $table->decimal('usage_intensity', 8, 4)->nullable(); // Usage per square meter
            $table->decimal('cost_intensity', 8, 2)->nullable(); // Cost per square meter
            $table->decimal('carbon_intensity_per_area', 8, 4)->nullable(); // Emissions per square meter
            
            // Sustainability & Certification
            $table->integer('efficiency_rating')->default(50);
            $table->integer('sustainability_score')->default(50);
            $table->integer('green_certification_points')->default(0);
            $table->decimal('leed_certification_impact', 6, 2)->default(0);
            $table->integer('energy_star_rating')->default(0);
            $table->boolean('passive_house_standard')->default(false);
            $table->boolean('net_zero_status')->default(false);
            $table->boolean('carbon_neutral_status')->default(false);
            
            // Targets & Progress
            $table->decimal('renewable_energy_target', 5, 2)->default(0);
            $table->decimal('energy_reduction_target', 5, 2)->default(0);
            $table->decimal('carbon_reduction_target', 5, 2)->default(0);
            $table->json('progress_to_targets')->nullable();
            $table->json('achievement_badges')->nullable();
            
            // Compliance & Reporting
            $table->string('reporting_period')->default('daily');
            $table->string('aggregation_method')->default('average');
            $table->boolean('compliance_status')->default(true);
            $table->string('verification_status')->default('pending');
            $table->json('audit_trail')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['smart_property_id']);
            $table->index(['device_id']);
            $table->index(['data_type']);
            $table->index(['recorded_at']);
            $table->index(['anomaly_detected']);
            $table->index(['energy_efficiency_score']);
            $table->index(['time_of_day']);
            $table->index(['is_holiday']);
            $table->index(['is_weekend']);
            
            // Composite indexes for common queries
            $table->index(['smart_property_id', 'recorded_at'], 'emd_prop_recorded_idx');
            $table->index(['smart_property_id', 'data_type', 'recorded_at'], 'emd_prop_type_recorded_idx');
            $table->index(['device_id', 'recorded_at'], 'emd_device_recorded_idx');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('energy_monitoring_data');
    }
};
