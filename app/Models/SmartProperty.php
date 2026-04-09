<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmartProperty extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'has_smart_thermostat',
        'has_smart_lighting',
        'has_smart_security',
        'has_smart_locks',
        'has_smart_sprinklers',
        'has_energy_monitoring',
        'has_solar_panels',
        'has_smart_appliances',
        'has_voice_control',
        'has_automated_blinds',
        'has_smart_hvac',
        'smart_features',
        'device_list',
        'automation_enabled',
        'energy_monitoring_enabled',
        'security_enabled',
        'climate_control_enabled',
        'water_management_enabled',
        'air_quality_monitoring_enabled',
        'smart_lock_enabled',
        'voice_control_enabled',
        'ai_optimization_enabled',
        'last_sync_at',
        'device_count',
        'active_alerts_count',
        'energy_efficiency_score',
        'security_score',
        'automation_rules_count',
        'monthly_energy_cost',
        'monthly_water_cost',
        'carbon_footprint',
        'smart_home_hub_type', // alexa, google_home, apple_homekit, custom
        'integration_status', // connected, disconnected, error
        'firmware_version',
        'last_maintenance_check',
        'next_maintenance_due',
        'warranty_expiry',
        'insurance_coverage',
        'emergency_contacts',
        'access_permissions',
        'privacy_settings',
        'data_retention_days',
        'backup_enabled',
        'remote_access_enabled',
        'geofencing_enabled',
        'schedule_automation_enabled',
        'energy_saving_mode',
        'security_mode', // home, away, vacation, night
        'current_status', // active, inactive, maintenance, error
        'health_score', // 0-100
        'uptime_percentage',
        'response_time_ms',
        'data_usage_mb',
        'battery_backup_hours',
        'power_consumption_watts',
        'temperature_sensors_count',
        'humidity_sensors_count',
        'motion_sensors_count',
        'door_sensors_count',
        'window_sensors_count',
        'smoke_detectors_count',
        'water_leak_detectors_count',
        'air_quality_sensors_count',
        'smart_bulbs_count',
        'smart_switches_count',
        'smart_thermostats_count',
        'smart_locks_count',
        'security_cameras_count',
        'smart_plugs_count',
        'irrigation_controllers_count',
        'pool_controllers_count',
        'solar_panels_count',
        'battery_storage_count',
        'electric_vehicle_chargers_count',
    ];

    protected $casts = [
        'automation_enabled' => 'boolean',
        'energy_monitoring_enabled' => 'boolean',
        'security_enabled' => 'boolean',
        'climate_control_enabled' => 'boolean',
        'water_management_enabled' => 'boolean',
        'air_quality_monitoring_enabled' => 'boolean',
        'smart_lock_enabled' => 'boolean',
        'voice_control_enabled' => 'boolean',
        'ai_optimization_enabled' => 'boolean',
        'last_sync_at' => 'datetime',
        'last_maintenance_check' => 'datetime',
        'next_maintenance_due' => 'datetime',
        'warranty_expiry' => 'datetime',
        'emergency_contacts' => 'json',
        'access_permissions' => 'json',
        'privacy_settings' => 'json',
        'backup_enabled' => 'boolean',
        'remote_access_enabled' => 'boolean',
        'geofencing_enabled' => 'boolean',
        'schedule_automation_enabled' => 'boolean',
        'energy_saving_mode' => 'boolean',
        'monthly_energy_cost' => 'decimal:2',
        'monthly_water_cost' => 'decimal:2',
        'carbon_footprint' => 'decimal:2',
        'energy_efficiency_score' => 'integer',
        'security_score' => 'integer',
        'health_score' => 'integer',
        'uptime_percentage' => 'decimal:2',
        'response_time_ms' => 'integer',
        'data_usage_mb' => 'decimal:2',
        'battery_backup_hours' => 'decimal:1',
        'power_consumption_watts' => 'decimal:2',
    ];

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(IoTDevice::class);
    }

    public function energyData(): HasMany
    {
        return $this->hasMany(EnergyMonitoringData::class);
    }

    public function securityLogs(): HasMany
    {
        return $this->hasMany(SecurityLog::class);
    }

    public function automationRules(): HasMany
    {
        return $this->hasMany(AutomationRule::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(SmartPropertyAlert::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('current_status', 'active');
    }

    public function scopeBySmartLevel($query, $level)
    {
        return $query->where('smart_level', $level);
    }

    public function scopeWithHighEnergyEfficiency($query)
    {
        return $query->where('energy_efficiency_score', '>=', 80);
    }

    public function scopeWithHighSecurity($query)
    {
        return $query->where('security_score', '>=', 80);
    }

    // Methods
    public function getDeviceStatusSummary(): array
    {
        $total = $this->devices()->count();
        $active = $this->devices()->where('status', 'active')->count();
        $offline = $this->devices()->where('status', 'offline')->count();
        $error = $this->devices()->where('status', 'error')->count();

        return [
            'total' => $total,
            'active' => $active,
            'offline' => $offline,
            'error' => $error,
            'active_percentage' => $total > 0 ? ($active / $total) * 100 : 0,
        ];
    }

    public function getCurrentEnergyUsage(): float
    {
        $latestData = $this->energyData()
            ->where('recorded_at', '>=', now()->subMinutes(5))
            ->latest('recorded_at')
            ->first();

        return $latestData ? $latestData->current_usage_kw : 0;
    }

    public function getMonthlyEnergyCost(): float
    {
        return $this->energyData()
            ->where('recorded_at', '>=', now()->startOfMonth())
            ->sum('cost_amount');
    }

    public function getSecurityStatus(): string
    {
        if ($this->security_score >= 90) return 'excellent';
        if ($this->security_score >= 70) return 'good';
        if ($this->security_score >= 50) return 'fair';
        return 'poor';
    }

    public function getEnergyEfficiencyStatus(): string
    {
        if ($this->energy_efficiency_score >= 90) return 'excellent';
        if ($this->energy_efficiency_score >= 70) return 'good';
        if ($this->energy_efficiency_score >= 50) return 'fair';
        return 'poor';
    }

    public function triggerAutomation(string $trigger, array $params = []): bool
    {
        $rules = $this->automationRules()
            ->where('trigger_type', $trigger)
            ->where('is_active', true)
            ->get();

        foreach ($rules as $rule) {
            $rule->execute($params);
        }

        return true;
    }

    public function generateHealthReport(): array
    {
        return [
            'overall_health' => $this->health_score,
            'device_health' => $this->getDeviceStatusSummary(),
            'energy_efficiency' => $this->getEnergyEfficiencyStatus(),
            'security_status' => $this->getSecurityStatus(),
            'active_alerts' => $this->alerts()->where('resolved_at', null)->count(),
            'last_sync' => $this->last_sync_at,
            'uptime' => $this->uptime_percentage,
            'maintenance_due' => $this->next_maintenance_due,
        ];
    }

    public function optimizeEnergyUsage(): array
    {
        if (!$this->ai_optimization_enabled) {
            return ['status' => 'error', 'message' => 'AI optimization not enabled'];
        }

        // AI optimization logic here
        $suggestions = [
            'adjust_thermostat' => [
                'current' => 22,
                'recommended' => 20,
                'savings' => 15.5
            ],
            'optimize_lighting' => [
                'current_usage' => 2.5,
                'optimized_usage' => 1.8,
                'savings' => 28.0
            ],
            'schedule_appliances' => [
                'off_peak_hours' => ['22:00-06:00'],
                'potential_savings' => 25.0
            ]
        ];

        return [
            'status' => 'success',
            'suggestions' => $suggestions,
            'estimated_monthly_savings' => 68.5,
            'implementation_priority' => 'high'
        ];
    }
}
