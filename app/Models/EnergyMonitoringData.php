<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnergyMonitoringData extends Model
{
    use HasFactory;

    protected $fillable = [
        'smart_property_id',
        'device_id',
        'recorded_at',
        'data_type', // electricity, water, gas, solar, battery
        'current_usage_kw', // Current power consumption
        'daily_usage_kwh', // Total daily consumption
        'monthly_usage_kwh', // Total monthly consumption
        'peak_usage_kw', // Peak usage for the period
        'off_peak_usage_kw', // Off-peak usage
        'cost_amount', // Cost for this period
        'cost_per_kwh', // Cost per unit
        'tariff_rate', // Current tariff rate
        'carbon_footprint_kg', // CO2 emissions
        'temperature_celsius', // Ambient temperature
        'humidity_percent', // Ambient humidity
        'weather_condition', // sunny, cloudy, rainy, etc.
        'occupancy_count', // Number of occupants
        'time_of_day', // peak, off_peak, shoulder
        'season', // summer, winter, spring, fall
        'day_of_week', // monday, tuesday, etc.
        'is_holiday', // Holiday flag
        'is_weekend', // Weekend flag
        'solar_generation_kw', // Solar power generated
        'battery_charge_percent', // Battery charge level
        'battery_discharge_kw', // Battery discharge rate
        'grid_import_kw', // Power imported from grid
        'grid_export_kw', // Power exported to grid
        'backup_power_usage_kw', // Backup power usage
        'energy_efficiency_score', // 0-100 efficiency rating
        'predicted_usage_kw', // AI predicted usage
        'anomaly_detected', // Usage anomaly flag
        'anomaly_score', // Anomaly confidence score
        'optimization_suggestions', // AI optimization suggestions
        'savings_potential', // Potential savings amount
        'baseline_usage_kw', // Expected baseline usage
        'variance_percent', // Variance from baseline
        'load_factor', // Load factor percentage
        'power_factor', // Power factor
        'voltage', // Voltage level
        'current_amperes', // Current draw
        'frequency_hz', // Grid frequency
        'harmonic_distortion', // Power quality metric
        'outage_minutes', // Power outage duration
        'quality_score', // Power quality score
        'renewable_percentage', // Renewable energy percentage
        'demand_response_participation', // DR program participation
        'peak_shaving_kw', // Peak shaving amount
        'load_shifting_kwh', // Load shifting amount
        'storage_efficiency', // Battery storage efficiency
        'solar_efficiency', // Solar panel efficiency
        'weather_impact_factor', // Weather impact on usage
        'occupancy_pattern', // Occupancy pattern type
        'appliance_usage_breakdown', // Usage by appliance
        'zone_usage_breakdown', // Usage by property zone
        'time_of_usage_breakdown', // Usage by time period
        'cost_breakdown', // Cost by category
        'carbon_breakdown', // Emissions by source
        'forecast_accuracy', // Forecast accuracy percentage
        'model_version', // AI model version used
        'data_quality_score', // Data quality rating
        'missing_data_points', // Count of missing readings
        'estimated_usage_flag', // Flag for estimated vs actual
        'interpolation_method', // Method used for interpolation
        'data_source', // Source of data (smart_meter, device, etc.)
        'meter_id', // Physical meter identifier
        'utility_provider', // Energy utility company
        'rate_plan', // Current rate plan
        'contract_type', // Fixed, variable, time-of-use
        'demand_charges', // Demand charge amount
        'connection_charges', // Connection fee amount
        'tax_amount', // Energy tax amount
        'rebates_amount', // Rebates received
        'incentives_amount', // Incentive payments
        'penalty_amount', // Penalty charges
        'credits_amount', // Energy credits
        'net_metering_balance', // Net metering balance
        'energy_storage_status', // Battery system status
        'renewable_credits', // Renewable energy credits
        'carbon_credits', // Carbon offset credits
        'efficiency_improvements', // Efficiency improvement measures
        'retrofit_impact', // Impact of retrofits
        'behavioral_changes', // Behavioral change impacts
        'maintenance_impact', // Maintenance effect on efficiency
        'equipment_age_factor', // Age-related efficiency factor
        'degradation_rate', // Equipment degradation rate
        'performance_ratio', // System performance ratio
        'availability_factor', // System availability
        'capacity_factor', // Capacity utilization
        'dispatch_efficiency', // Dispatch efficiency
        'control_efficiency', // Control system efficiency
        'measurement_uncertainty', // Measurement error margin
        'calibration_status', // Calibration status
        'verification_status', // Data verification status
        'audit_trail', // Audit trail information
        'compliance_status', // Regulatory compliance
        'reporting_period', // Reporting period type
        'aggregation_method', // Data aggregation method
        'normalization_factor', // Usage normalization
        'benchmark_comparison', // Industry benchmark comparison
        'peer_comparison', // Similar property comparison
        'regional_average', // Regional usage average
        'national_average', // National usage average
        'seasonal_adjustment', // Seasonal adjustment factor
        'weather_normalization', // Weather normalization
        'occupancy_normalization', // Occupancy normalization
        'size_normalization', // Property size normalization
        'usage_intensity', // Usage per square meter
        'cost_intensity', // Cost per square meter
        'carbon_intensity', // Emissions per square meter
        'efficiency_rating', // Overall efficiency rating
        'sustainability_score', // Sustainability score
        'green_certification_points', // Green certification points
        'leed_certification_impact', // LEED certification impact
        'energy_star_rating', // Energy Star rating
        'passive_house_standard', // Passive house compliance
        'net_zero_status', // Net zero energy status
        'carbon_neutral_status', // Carbon neutral status
        'renewable_energy_target', // Renewable energy target
        'energy_reduction_target', // Energy reduction target
        'carbon_reduction_target', // Carbon reduction target
        'progress_to_targets', // Progress towards targets
        'achievement_badges', // Efficiency achievement badges
        'recognition_status', // Recognition status
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'is_holiday' => 'boolean',
        'is_weekend' => 'boolean',
        'anomaly_detected' => 'boolean',
        'estimated_usage_flag' => 'boolean',
        'current_usage_kw' => 'decimal:4',
        'daily_usage_kwh' => 'decimal:4',
        'monthly_usage_kwh' => 'decimal:4',
        'peak_usage_kw' => 'decimal:4',
        'off_peak_usage_kw' => 'decimal:4',
        'cost_amount' => 'decimal:2',
        'cost_per_kwh' => 'decimal:4',
        'carbon_footprint_kg' => 'decimal:4',
        'temperature_celsius' => 'decimal:2',
        'humidity_percent' => 'decimal:2',
        'solar_generation_kw' => 'decimal:4',
        'battery_charge_percent' => 'decimal:2',
        'battery_discharge_kw' => 'decimal:4',
        'grid_import_kw' => 'decimal:4',
        'grid_export_kw' => 'decimal:4',
        'backup_power_usage_kw' => 'decimal:4',
        'energy_efficiency_score' => 'integer',
        'anomaly_score' => 'decimal:3',
        'savings_potential' => 'decimal:2',
        'baseline_usage_kw' => 'decimal:4',
        'variance_percent' => 'decimal:2',
        'load_factor' => 'decimal:3',
        'power_factor' => 'decimal:3',
        'voltage' => 'decimal:2',
        'current_amperes' => 'decimal:2',
        'frequency_hz' => 'decimal:2',
        'harmonic_distortion' => 'decimal:3',
        'outage_minutes' => 'integer',
        'quality_score' => 'integer',
        'renewable_percentage' => 'decimal:2',
        'peak_shaving_kw' => 'decimal:4',
        'load_shifting_kwh' => 'decimal:4',
        'storage_efficiency' => 'decimal:3',
        'solar_efficiency' => 'decimal:3',
        'weather_impact_factor' => 'decimal:3',
        'optimization_suggestions' => 'json',
        'appliance_usage_breakdown' => 'json',
        'zone_usage_breakdown' => 'json',
        'time_of_usage_breakdown' => 'json',
        'cost_breakdown' => 'json',
        'carbon_breakdown' => 'json',
        'forecast_accuracy' => 'decimal:2',
        'data_quality_score' => 'integer',
        'missing_data_points' => 'integer',
        'demand_charges' => 'decimal:2',
        'connection_charges' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'rebates_amount' => 'decimal:2',
        'incentives_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'credits_amount' => 'decimal:2',
        'net_metering_balance' => 'decimal:4',
        'renewable_credits' => 'decimal:4',
        'carbon_credits' => 'decimal:4',
        'efficiency_improvements' => 'json',
        'retrofit_impact' => 'json',
        'behavioral_changes' => 'json',
        'maintenance_impact' => 'json',
        'equipment_age_factor' => 'decimal:3',
        'degradation_rate' => 'decimal:4',
        'performance_ratio' => 'decimal:3',
        'availability_factor' => 'decimal:3',
        'capacity_factor' => 'decimal:3',
        'dispatch_efficiency' => 'decimal:3',
        'control_efficiency' => 'decimal:3',
        'measurement_uncertainty' => 'decimal:3',
        'normalization_factor' => 'decimal:3',
        'usage_intensity' => 'decimal:4',
        'cost_intensity' => 'decimal:2',
        'carbon_intensity' => 'decimal:4',
        'efficiency_rating' => 'integer',
        'sustainability_score' => 'integer',
        'green_certification_points' => 'integer',
        'leed_certification_impact' => 'decimal:2',
        'energy_star_rating' => 'integer',
        'passive_house_standard' => 'boolean',
        'net_zero_status' => 'boolean',
        'carbon_neutral_status' => 'boolean',
        'renewable_energy_target' => 'decimal:2',
        'energy_reduction_target' => 'decimal:2',
        'carbon_reduction_target' => 'decimal:2',
        'progress_to_targets' => 'json',
        'achievement_badges' => 'json',
    ];

    // Relationships
    public function smartProperty(): BelongsTo
    {
        return $this->belongsTo(SmartProperty::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(IoTDevice::class);
    }

    // Scopes
    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('smart_property_id', $propertyId);
    }

    public function scopeByDevice($query, $deviceId)
    {
        return $query->where('device_id', $deviceId);
    }

    public function scopeByDataType($query, $dataType)
    {
        return $query->where('data_type', $dataType);
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('recorded_at', [$startDate, $endDate]);
    }

    public function scopeWithAnomalies($query)
    {
        return $query->where('anomaly_detected', true);
    }

    public function scopeHighUsage($query, $threshold = 10.0)
    {
        return $query->where('current_usage_kw', '>', $threshold);
    }

    public function scopePeakHours($query)
    {
        return $query->where('time_of_day', 'peak');
    }

    // Methods
    public function getEfficiencyGrade(): string
    {
        $score = $this->energy_efficiency_score;
        
        if ($score >= 90) return 'A+';
        if ($score >= 80) return 'A';
        if ($score >= 70) return 'B';
        if ($score >= 60) return 'C';
        if ($score >= 50) return 'D';
        return 'F';
    }

    public function getCostPerSqm(): ?float
    {
        $property = $this->smartProperty;
        if (!$property || !$property->property) return null;
        
        $area = $property->property->area;
        return $area > 0 ? $this->cost_amount / $area : null;
    }

    public function getCarbonIntensity(): ?float
    {
        return $this->daily_usage_kwh > 0 ? 
            $this->carbon_footprint_kg / $this->daily_usage_kwh : null;
    }

    public function getRenewablePercentage(): float
    {
        $totalGeneration = $this->solar_generation_kw + $this->battery_discharge_kw;
        $totalUsage = $this->current_usage_kw;
        
        return $totalUsage > 0 ? ($totalGeneration / $totalUsage) * 100 : 0;
    }

    public function isPeakHour(): bool
    {
        $hour = $this->recorded_at->hour;
        return ($hour >= 17 && $hour <= 21) || ($hour >= 6 && $hour <= 10);
    }

    public function getLoadCategory(): string
    {
        $usage = $this->current_usage_kw;
        
        if ($usage < 1.0) return 'light';
        if ($usage < 3.0) return 'moderate';
        if ($usage < 5.0) return 'heavy';
        return 'extreme';
    }

    public function calculateSavingsOpportunity(): array
    {
        $baseline = $this->baseline_usage_kw;
        $actual = $this->current_usage_kw;
        $rate = $this->cost_per_kwh;
        
        $excessUsage = max(0, $actual - $baseline);
        $potentialSavings = $excessUsage * $rate;
        
        return [
            'excess_usage_kw' => $excessUsage,
            'potential_savings' => $potentialSavings,
            'reduction_needed_percent' => $baseline > 0 ? (($actual - $baseline) / $baseline) * 100 : 0,
            'optimization_priority' => $potentialSavings > 10 ? 'high' : 
                                     ($potentialSavings > 5 ? 'medium' : 'low'),
        ];
    }

    public function generateInsights(): array
    {
        $insights = [];
        
        // Usage pattern insights
        if ($this->anomaly_detected) {
            $insights[] = [
                'type' => 'anomaly',
                'severity' => 'high',
                'message' => 'Unusual energy consumption pattern detected',
                'recommendation' => 'Investigate potential equipment malfunction or unusual activity',
            ];
        }
        
        // Efficiency insights
        if ($this->energy_efficiency_score < 60) {
            $insights[] = [
                'type' => 'efficiency',
                'severity' => 'medium',
                'message' => 'Energy efficiency is below optimal levels',
                'recommendation' => 'Consider upgrading equipment or optimizing usage patterns',
            ];
        }
        
        // Cost insights
        if ($this->cost_amount > $this->savings_potential * 2) {
            $insights[] = [
                'type' => 'cost',
                'severity' => 'medium',
                'message' => 'Energy costs are significantly higher than potential savings',
                'recommendation' => 'Review tariff plans and consider time-of-use optimization',
            ];
        }
        
        // Renewable insights
        if ($this->getRenewablePercentage() < 20) {
            $insights[] = [
                'type' => 'renewable',
                'severity' => 'low',
                'message' => 'Low renewable energy utilization',
                'recommendation' => 'Consider increasing solar capacity or battery storage',
            ];
        }
        
        return $insights;
    }

    public function getBenchmarkComparison(): array
    {
        return [
            'regional_average' => $this->regional_average ?? 0,
            'national_average' => $this->national_average ?? 0,
            'peer_comparison' => $this->peer_comparison ?? 0,
            'performance_vs_regional' => $this->regional_average ? 
                (($this->daily_usage_kwh - $this->regional_average) / $this->regional_average) * 100 : 0,
            'performance_vs_national' => $this->national_average ? 
                (($this->daily_usage_kwh - $this->national_average) / $this->national_average) * 100 : 0,
            'ranking_percentile' => $this->calculateRankingPercentile(),
        ];
    }

    private function calculateRankingPercentile(): int
    {
        // Simulate percentile calculation
        $efficiency = $this->energy_efficiency_score;
        
        if ($efficiency >= 95) return 95;
        if ($efficiency >= 90) return 85;
        if ($efficiency >= 80) return 70;
        if ($efficiency >= 70) return 50;
        if ($efficiency >= 60) return 30;
        return 15;
    }

    public function exportForAnalysis(): array
    {
        return [
            'timestamp' => $this->recorded_at->toISOString(),
            'property_id' => $this->smart_property_id,
            'device_id' => $this->device_id,
            'usage_kw' => $this->current_usage_kw,
            'cost' => $this->cost_amount,
            'carbon_kg' => $this->carbon_footprint_kg,
            'efficiency_score' => $this->energy_efficiency_score,
            'renewable_percent' => $this->getRenewablePercentage(),
            'anomaly_detected' => $this->anomaly_detected,
            'weather_condition' => $this->weather_condition,
            'temperature' => $this->temperature_celsius,
            'occupancy' => $this->occupancy_count,
            'time_of_day' => $this->time_of_day,
            'day_type' => $this->is_weekend ? 'weekend' : 'weekday',
            'insights' => $this->generateInsights(),
            'savings_opportunity' => $this->calculateSavingsOpportunity(),
        ];
    }
}
