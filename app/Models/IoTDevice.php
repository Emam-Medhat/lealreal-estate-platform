<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IoTDevice extends Model
{
    use HasFactory;

    protected $table = 'iot_devices';

    protected $fillable = [
        'smart_property_id',
        'device_type', // thermostat, lock, camera, sensor, light, switch, plug, etc.
        'brand',
        'model',
        'serial_number',
        'mac_address',
        'ip_address',
        'firmware_version',
        'hardware_version',
        'installation_date',
        'warranty_expiry',
        'status', // active, inactive, offline, error, maintenance
        'battery_level', // for battery-powered devices
        'signal_strength', // WiFi/Zigbee/Z-Wave signal
        'last_seen_at',
        'last_data_received_at',
        'location_within_property', // living_room, bedroom_1, kitchen, etc.
        'room_name',
        'floor',
        'zone',
        'is_critical', // critical for security/safety
        'auto_update_enabled',
        'notifications_enabled',
        'data_retention_days',
        'sampling_rate_seconds',
        'power_consumption_watts',
        'standby_power_watts',
        'operating_voltage',
        'communication_protocol', // wifi, zigbee, z-wave, bluetooth, lora, cellular
        'encryption_enabled',
        'security_level', // basic, standard, high
        'access_level', // public, private, restricted
        'user_permissions',
        'automation_rules',
        'schedule_settings',
        'threshold_settings',
        'alert_settings',
        'maintenance_schedule',
        'calibration_settings',
        'diagnostic_data',
        'error_log',
        'performance_metrics',
        'usage_statistics',
        'energy_consumption_history',
        'cost_per_month',
        'carbon_footprint_kg',
        'manufacturer_warranty_url',
        'user_manual_url',
        'support_contact',
        'replacement_cost',
        'depreciation_years',
        'resale_value',
        'insurance_covered',
        'backup_power_required',
        'redundancy_required',
        'compliance_certifications',
        'safety_ratings',
        'environmental_ratings',
        'installation_notes',
        'configuration_json',
        'custom_attributes',
        'integration_status',
        'api_endpoint',
        'webhook_url',
        'oauth_token',
        'refresh_token',
        'last_maintenance_date',
        'next_maintenance_date',
        'maintenance_cost_total',
        'downtime_minutes',
        'uptime_percentage',
        'response_time_ms',
        'data_transferred_mb',
        'error_count',
        'warning_count',
        'critical_alerts_count',
        'battery_replacement_count',
        'firmware_update_count',
        'configuration_change_count',
        'user_interaction_count',
        'automated_actions_count',
        'energy_saved_kwh',
        'cost_saved_currency',
        'efficiency_score',
        'reliability_score',
        'user_satisfaction_score',
        'health_score',
        'predicted_failure_date',
        'recommended_replacement_date',
        'end_of_life_date',
    ];

    protected $casts = [
        'installation_date' => 'datetime',
        'warranty_expiry' => 'datetime',
        'last_seen_at' => 'datetime',
        'last_data_received_at' => 'datetime',
        'last_maintenance_date' => 'datetime',
        'next_maintenance_date' => 'datetime',
        'predicted_failure_date' => 'datetime',
        'recommended_replacement_date' => 'datetime',
        'end_of_life_date' => 'datetime',
        'auto_update_enabled' => 'boolean',
        'notifications_enabled' => 'boolean',
        'encryption_enabled' => 'boolean',
        'backup_power_required' => 'boolean',
        'redundancy_required' => 'boolean',
        'insurance_covered' => 'boolean',
        'user_permissions' => 'json',
        'automation_rules' => 'json',
        'schedule_settings' => 'json',
        'threshold_settings' => 'json',
        'alert_settings' => 'json',
        'maintenance_schedule' => 'json',
        'calibration_settings' => 'json',
        'diagnostic_data' => 'json',
        'error_log' => 'json',
        'performance_metrics' => 'json',
        'usage_statistics' => 'json',
        'energy_consumption_history' => 'json',
        'configuration_json' => 'json',
        'custom_attributes' => 'json',
        'compliance_certifications' => 'json',
        'safety_ratings' => 'json',
        'environmental_ratings' => 'json',
        'installation_notes' => 'text',
        'battery_level' => 'integer',
        'signal_strength' => 'integer',
        'sampling_rate_seconds' => 'integer',
        'power_consumption_watts' => 'decimal:2',
        'standby_power_watts' => 'decimal:2',
        'operating_voltage' => 'decimal:2',
        'cost_per_month' => 'decimal:2',
        'carbon_footprint_kg' => 'decimal:2',
        'replacement_cost' => 'decimal:2',
        'depreciation_years' => 'integer',
        'resale_value' => 'decimal:2',
        'maintenance_cost_total' => 'decimal:2',
        'downtime_minutes' => 'integer',
        'uptime_percentage' => 'decimal:2',
        'response_time_ms' => 'integer',
        'data_transferred_mb' => 'decimal:2',
        'error_count' => 'integer',
        'warning_count' => 'integer',
        'critical_alerts_count' => 'integer',
        'battery_replacement_count' => 'integer',
        'firmware_update_count' => 'integer',
        'configuration_change_count' => 'integer',
        'user_interaction_count' => 'integer',
        'automated_actions_count' => 'integer',
        'energy_saved_kwh' => 'decimal:2',
        'cost_saved_currency' => 'decimal:2',
        'efficiency_score' => 'integer',
        'reliability_score' => 'integer',
        'user_satisfaction_score' => 'integer',
        'health_score' => 'integer',
        'data_retention_days' => 'integer',
    ];

    // Relationships
    public function smartProperty(): BelongsTo
    {
        return $this->belongsTo(SmartProperty::class);
    }

    public function sensorData(): HasMany
    {
        return $this->hasMany(SensorData::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(DeviceAlert::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(DeviceMaintenanceRecord::class);
    }

    public function firmwareUpdates(): HasMany
    {
        return $this->hasMany(FirmwareUpdate::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('device_type', $type);
    }

    public function scopeCritical($query)
    {
        return $query->where('is_critical', true);
    }

    public function scopeOffline($query)
    {
        return $query->where('status', 'offline');
    }

    public function scopeLowBattery($query)
    {
        return $query->where('battery_level', '<=', 20);
    }

    public function scopeNeedsMaintenance($query)
    {
        return $query->where('next_maintenance_date', '<=', now()->addDays(7));
    }

    // Methods
    public function isOnline(): bool
    {
        return $this->status === 'active' && 
               $this->last_seen_at && 
               $this->last_seen_at->gt(now()->subMinutes(5));
    }

    public function getBatteryStatus(): string
    {
        if (!$this->battery_level) return 'N/A';
        
        if ($this->battery_level >= 80) return 'excellent';
        if ($this->battery_level >= 60) return 'good';
        if ($this->battery_level >= 40) return 'fair';
        if ($this->battery_level >= 20) return 'poor';
        return 'critical';
    }

    public function getSignalStatus(): string
    {
        if (!$this->signal_strength) return 'N/A';
        
        if ($this->signal_strength >= -50) return 'excellent';
        if ($this->signal_strength >= -60) return 'good';
        if ($this->signal_strength >= -70) return 'fair';
        return 'poor';
    }

    public function getHealthStatus(): string
    {
        if ($this->health_score >= 90) return 'excellent';
        if ($this->health_score >= 70) return 'good';
        if ($this->health_score >= 50) return 'fair';
        return 'poor';
    }

    public function getCurrentData(): array
    {
        $latestData = $this->sensorData()
            ->latest('recorded_at')
            ->first();

        return $latestData ? $latestData->data : [];
    }

    public function getActiveAlerts(): array
    {
        return $this->alerts()
            ->where('resolved_at', null)
            ->orderBy('severity', 'desc')
            ->get()
            ->toArray();
    }

    public function getMaintenanceStatus(): string
    {
        if (!$this->next_maintenance_date) return 'not_scheduled';
        
        $daysUntil = now()->diffInDays($this->next_maintenance_date, false);
        
        if ($daysUntil < 0) return 'overdue';
        if ($daysUntil <= 7) return 'due_soon';
        if ($daysUntil <= 30) return 'scheduled';
        return 'upcoming';
    }

    public function getEfficiencyMetrics(): array
    {
        return [
            'efficiency_score' => $this->efficiency_score,
            'reliability_score' => $this->reliability_score,
            'uptime_percentage' => $this->uptime_percentage,
            'response_time_ms' => $this->response_time_ms,
            'error_rate' => $this->error_count > 0 ? ($this->error_count / max($this->user_interaction_count, 1)) * 100 : 0,
            'energy_consumption' => $this->power_consumption_watts,
            'cost_per_month' => $this->cost_per_month,
            'carbon_footprint' => $this->carbon_footprint_kg,
        ];
    }

    public function predictMaintenance(): array
    {
        $daysUntilMaintenance = $this->next_maintenance_date ? 
            now()->diffInDays($this->next_maintenance_date, false) : null;

        $riskFactors = [];
        
        if ($this->health_score < 70) $riskFactors[] = 'low_health_score';
        if ($this->uptime_percentage < 95) $riskFactors[] = 'low_uptime';
        if ($this->error_count > 10) $riskFactors[] = 'high_error_rate';
        if ($this->battery_level && $this->battery_level < 30) $riskFactors[] = 'low_battery';
        
        $riskLevel = count($riskFactors);
        
        return [
            'days_until_maintenance' => $daysUntilMaintenance,
            'maintenance_status' => $this->getMaintenanceStatus(),
            'risk_factors' => $riskFactors,
            'risk_level' => $riskLevel,
            'recommended_action' => $riskLevel >= 2 ? 'schedule_immediate' : 
                                  ($riskLevel >= 1 ? 'schedule_soon' : 'monitor'),
            'predicted_failure_date' => $this->predicted_failure_date,
        ];
    }

    public function generateDiagnosticReport(): array
    {
        return [
            'device_info' => [
                'type' => $this->device_type,
                'brand' => $this->brand,
                'model' => $this->model,
                'serial_number' => $this->serial_number,
                'firmware_version' => $this->firmware_version,
            ],
            'status' => [
                'current' => $this->status,
                'online' => $this->isOnline(),
                'battery' => $this->getBatteryStatus(),
                'signal' => $this->getSignalStatus(),
                'health' => $this->getHealthStatus(),
            ],
            'performance' => $this->getEfficiencyMetrics(),
            'maintenance' => $this->predictMaintenance(),
            'alerts' => $this->getActiveAlerts(),
            'last_activity' => $this->last_data_received_at,
            'uptime' => $this->uptime_percentage,
            'cost_analysis' => [
                'monthly_cost' => $this->cost_per_month,
                'replacement_cost' => $this->replacement_cost,
                'maintenance_cost' => $this->maintenance_cost_total,
                'energy_cost_saved' => $this->cost_saved_currency,
            ],
        ];
    }

    public function executeCommand(string $command, array $parameters = []): array
    {
        // Device command execution logic
        $commands = [
            'turn_on' => ['action' => 'power_on', 'params' => []],
            'turn_off' => ['action' => 'power_off', 'params' => []],
            'set_temperature' => ['action' => 'set_temp', 'params' => ['temperature']],
            'lock' => ['action' => 'secure', 'params' => []],
            'unlock' => ['action' => 'unsecure', 'params' => []],
            'take_snapshot' => ['action' => 'capture', 'params' => []],
            'calibrate' => ['action' => 'calibrate', 'params' => []],
        ];

        if (!isset($commands[$command])) {
            return [
                'success' => false,
                'message' => 'Command not supported',
                'command' => $command
            ];
        }

        // Simulate command execution
        $commandData = $commands[$command];
        
        // Log the command
        $this->user_interaction_count++;
        $this->save();

        return [
            'success' => true,
            'message' => 'Command executed successfully',
            'command' => $command,
            'action' => $commandData['action'],
            'params' => array_merge($commandData['params'], $parameters),
            'executed_at' => now()->toISOString(),
        ];
    }
}
