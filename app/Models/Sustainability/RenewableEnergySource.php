<?php

namespace App\Models\Sustainability;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RenewableEnergySource extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_sustainability_id',
        'source_type',
        'capacity',
        'unit',
        'manufacturer',
        'model',
        'installation_date',
        'expected_lifespan',
        'efficiency_rating',
        'installation_cost',
        'maintenance_cost_per_year',
        'annual_production',
        'grid_connected',
        'battery_storage',
        'battery_capacity',
        'monitoring_system',
        'warranty_period',
        'certifications',
        'location',
        'orientation',
        'tilt_angle',
        'shading_factors',
        'performance_data',
        'documents',
        'status',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'capacity' => 'decimal:2',
        'efficiency_rating' => 'decimal:2',
        'installation_cost' => 'decimal:2',
        'maintenance_cost_per_year' => 'decimal:2',
        'annual_production' => 'decimal:2',
        'battery_capacity' => 'decimal:2',
        'installation_date' => 'date',
        'expected_lifespan' => 'integer',
        'warranty_period' => 'integer',
        'tilt_angle' => 'decimal:2',
        'certifications' => 'array',
        'shading_factors' => 'array',
        'performance_data' => 'array',
        'documents' => 'array',
        'grid_connected' => 'boolean',
        'battery_storage' => 'boolean',
        'monitoring_system' => 'boolean',
    ];

    // Relationships
    public function propertySustainability(): BelongsTo
    {
        return $this->belongsTo(PropertySustainability::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(RenewableEnergyMaintenance::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('source_type', $type);
    }

    public function scopeWithBattery($query)
    {
        return $query->where('battery_storage', true);
    }

    public function scopeGridConnected($query)
    {
        return $query->where('grid_connected', true);
    }

    public function scopeWithMonitoring($query)
    {
        return $query->where('monitoring_system', true);
    }

    // Attributes
    public function getSourceTypeTextAttribute(): string
    {
        return match($this->source_type) {
            'solar' => 'طاقة شمسية',
            'wind' => 'طاقة رياح',
            'geothermal' => 'طاقة حرارية أرضية',
            'biomass' => 'طاقة حيوية',
            'hydro' => 'طاقة مائية',
            'hybrid' => 'نظام هجين',
            default => 'غير معروف',
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'maintenance' => 'تحت الصيانة',
            'decommissioned' => 'مفكك',
            default => 'غير معروف',
        };
    }

    public function getUnitTextAttribute(): string
    {
        return match($this->unit) {
            'kW' => 'كيلوواط',
            'MW' => 'ميجاواط',
            'W' => 'واط',
            default => 'غير معروف',
        };
    }

    public function getAgeAttribute(): int
    {
        return $this->installation_date ? $this->installation_date->age : 0;
    }

    public function getRemainingLifespanAttribute(): int
    {
        return max(0, $this->expected_lifespan - $this->age);
    }

    public function getIsWarrantyValidAttribute(): bool
    {
        return $this->warranty_period > 0 && $this->age < $this->warranty_period;
    }

    public function getWarrantyExpiryDateAttribute(): ?\Carbon\Carbon
    {
        return $this->installation_date ? $this->installation_date->addYears($this->warranty_period) : null;
    }

    public function getAnnualRevenueAttribute(): float
    {
        // Simplified revenue calculation (assuming $0.12 per kWh)
        return $this->annual_production * 0.12;
    }

    public function getAnnualSavingsAttribute(): float
    {
        // Simplified savings calculation (assuming $0.15 per kWh grid cost)
        return $this->annual_production * 0.15;
    }

    public function getPaybackPeriodAttribute(): ?float
    {
        $netAnnualBenefit = $this->annual_revenue + $this->annual_savings - $this->maintenance_cost_per_year;
        return $netAnnualBenefit > 0 ? $this->installation_cost / $netAnnualBenefit : null;
    }

    public function getCapacityInKwAttribute(): float
    {
        return match($this->unit) {
            'MW' => $this->capacity * 1000,
            'W' => $this->capacity / 1000,
            default => $this->capacity,
        };
    }

    public function getPerformanceRatioAttribute(): float
    {
        if (!$this->annual_production || !$this->capacity_in_kw) return 0;
        
        // Expected annual production (simplified: 1500 kWh per kW per year)
        $expectedProduction = $this->capacity_in_kw * 1500;
        
        return min(100, ($this->annual_production / $expectedProduction) * 100);
    }

    // Methods
    public function calculateProduction(\Carbon\Carbon $startDate, \Carbon\Carbon $endDate): array
    {
        $days = $startDate->diffInDays($endDate);
        $dailyProduction = $this->annual_production / 365;
        
        // Apply seasonal and weather factors (simplified)
        $seasonalFactor = $this->getSeasonalFactor($startDate);
        $weatherFactor = 0.9; // Assumed average weather factor
        
        $adjustedDailyProduction = $dailyProduction * $seasonalFactor * $weatherFactor;
        $totalProduction = $adjustedDailyProduction * $days;
        
        return [
            'period_days' => $days,
            'daily_production' => round($adjustedDailyProduction, 2),
            'total_production' => round($totalProduction, 2),
            'seasonal_factor' => $seasonalFactor,
            'weather_factor' => $weatherFactor,
        ];
    }

    private function getSeasonalFactor(\Carbon\Carbon $date): float
    {
        $month = $date->month;
        
        // Simplified seasonal factors for solar
        if ($month >= 3 && $month <= 5) return 1.1; // Spring
        if ($month >= 6 && $month <= 8) return 1.2; // Summer
        if ($month >= 9 && $month <= 11) return 0.9; // Autumn
        return 0.7; // Winter
    }

    public function getMaintenanceSchedule(): array
    {
        $schedule = [];
        
        // Monthly checks
        $schedule['monthly'] = [
            'visual_inspection',
            'performance_monitoring',
            'cleaning_check',
        ];
        
        // Quarterly maintenance
        $schedule['quarterly'] = [
            'detailed_inspection',
            'connection_checks',
            'performance_analysis',
        ];
        
        // Annual maintenance
        $schedule['annual'] = [
            'comprehensive_maintenance',
            'efficiency_testing',
            'safety_inspection',
            'warranty_validation',
        ];
        
        return $schedule;
    }

    public function getNextMaintenanceDate(): \Carbon\Carbon
    {
        $lastMaintenance = $this->maintenanceRecords()
            ->where('maintenance_type', 'routine')
            ->latest('maintenance_date')
            ->first();
        
        if ($lastMaintenance) {
            return $lastMaintenance->maintenance_date->addMonths(3);
        }
        
        return $this->installation_date->addMonths(3);
    }

    public function getMaintenanceStatus(): array
    {
        $nextMaintenance = $this->getNextMaintenanceDate();
        $daysUntil = now()->diffInDays($nextMaintenance, false);
        
        return [
            'next_maintenance_date' => $nextMaintenance,
            'days_until_maintenance' => $daysUntil,
            'overdue' => $daysUntil < 0,
            'due_soon' => $daysUntil >= 0 && $daysUntil <= 30,
            'status' => $daysUntil < 0 ? 'متأخر' : ($daysUntil <= 30 ? 'قريب' : 'مجدول'),
        ];
    }

    public function getPerformanceMetrics(): array
    {
        return [
            'capacity_utilization' => $this->performance_ratio,
            'efficiency_rating' => $this->efficiency_rating,
            'annual_production' => $this->annual_production,
            'capacity_factor' => $this->calculateCapacityFactor(),
            'availability' => $this->calculateAvailability(),
            'degradation_rate' => $this->calculateDegradationRate(),
        ];
    }

    private function calculateCapacityFactor(): float
    {
        if (!$this->annual_production || !$this->capacity_in_kw) return 0;
        
        // Capacity factor = (Annual Production) / (Capacity × 8760 hours)
        return round(($this->annual_production / ($this->capacity_in_kw * 8760)) * 100, 2);
    }

    private function calculateAvailability(): float
    {
        // Simplified availability calculation
        $downtimeHours = $this->maintenanceRecords()
            ->where('maintenance_type', 'repair')
            ->sum('duration_hours');
        
        $totalHours = $this->age * 8760;
        $uptimeHours = max(0, $totalHours - $downtimeHours);
        
        return $totalHours > 0 ? round(($uptimeHours / $totalHours) * 100, 2) : 100;
    }

    private function calculateDegradationRate(): float
    {
        // Simplified degradation calculation
        $baseRate = match($this->source_type) {
            'solar' => 0.5, // 0.5% per year
            'wind' => 1.0, // 1.0% per year
            'geothermal' => 0.2, // 0.2% per year
            default => 0.5,
        };
        
        // Adjust for quality and maintenance
        $qualityFactor = $this->efficiency_rating / 100;
        $maintenanceFactor = $this->calculateAvailability() / 100;
        
        return round($baseRate * (2 - $qualityFactor) * (2 - $maintenanceFactor), 2);
    }

    public function getEnvironmentalImpact(): array
    {
        $annualProduction = $this->annual_production;
        
        return [
            'co2_savings_tons' => round($annualProduction * 0.0005, 2), // 0.5 kg CO2 per kWh
            'trees_equivalent' => round($annualProduction * 0.0005 / 21, 0), // 21 kg CO2 per tree per year
            'homes_powered' => round($annualProduction / 10000, 1), // 10,000 kWh per home per year
            'cars_off_road' => round($annualProduction * 0.0005 / 4600, 1), // 4600 kg CO2 per car per year
        ];
    }

    public function getFinancialAnalysis(): array
    {
        $totalInvestment = $this->installation_cost;
        $annualRevenue = $this->annual_revenue;
        $annualSavings = $this->annual_savings;
        $annualCost = $this->maintenance_cost_per_year;
        
        $netAnnualCashFlow = $annualRevenue + $annualSavings - $annualCost;
        $paybackPeriod = $this->payback_period;
        
        return [
            'total_investment' => $totalInvestment,
            'annual_revenue' => $annualRevenue,
            'annual_savings' => $annualSavings,
            'annual_costs' => $annualCost,
            'net_annual_cash_flow' => $netAnnualCashFlow,
            'payback_period_years' => $paybackPeriod,
            'roi_10_years' => $paybackPeriod ? (($netAnnualCashFlow * 10 - $totalInvestment) / $totalInvestment) * 100 : 0,
            'npv_20_years' => $this->calculateNPV($totalInvestment, $netAnnualCashFlow, 20),
            'irr' => $this->calculateIRR($totalInvestment, $netAnnualCashFlow),
        ];
    }

    private function calculateNPV(float $investment, float $annualCashFlow, int $years = 20, float $discountRate = 0.05): float
    {
        $npv = -$investment;
        
        for ($year = 1; $year <= $years; $year++) {
            $npv += $annualCashFlow / pow(1 + $discountRate, $year);
        }
        
        return round($npv, 2);
    }

    private function calculateIRR(float $investment, float $annualCashFlow): float
    {
        // Simplified IRR calculation
        $paybackPeriod = $this->payback_period;
        
        if (!$paybackPeriod) return 0;
        
        // Rough approximation: IRR ≈ (1 / Payback Period) - 1
        return round(((1 / $paybackPeriod) - 1) * 100, 2);
    }

    public function getTechnicalSpecifications(): array
    {
        return [
            'source_type' => $this->source_type_text,
            'manufacturer' => $this->manufacturer,
            'model' => $this->model,
            'capacity' => $this->capacity . ' ' . $this->unit_text,
            'efficiency_rating' => $this->efficiency_rating . '%',
            'installation_date' => $this->installation_date,
            'expected_lifespan' => $this->expected_lifespan . ' سنة',
            'warranty_period' => $this->warranty_period . ' سنة',
            'grid_connected' => $this->grid_connected ? 'نعم' : 'لا',
            'battery_storage' => $this->battery_storage ? 'نعم' : 'لا',
            'monitoring_system' => $this->monitoring_system ? 'نعم' : 'لا',
            'location' => $this->location,
            'orientation' => $this->orientation,
            'tilt_angle' => $this->tilt_angle . '°',
        ];
    }

    public function getOptimizationRecommendations(): array
    {
        $recommendations = [];
        
        if ($this->performance_ratio < 80) {
            $recommendations[] = [
                'category' => 'الأداء',
                'issue' => 'انخفاض نسبة الأداء',
                'recommendation' => 'فحص وتنظيف الألواح، فحص الاتصالات الكهربائية',
                'potential_improvement' => '10-20%',
                'estimated_cost' => 'منخفض',
            ];
        }
        
        if ($this->efficiency_rating < 85) {
            $recommendations[] = [
                'category' => 'الكفاءة',
                'issue' => 'انخفاض كفاءة النظام',
                'recommendation' => 'ترقية المكونات، تحسين التبريد',
                'potential_improvement' => '5-15%',
                'estimated_cost' => 'متوسط',
            ];
        }
        
        if (!$this->monitoring_system) {
            $recommendations[] = [
                'category' => 'المراقبة',
                'issue' => 'عدم وجود نظام مراقبة',
                'recommendation' => 'تركيب نظام مراقبة في الوقت الفعلي',
                'potential_improvement' => '5-10%',
                'estimated_cost' => 'منخفض',
            ];
        }
        
        if (!$this->battery_storage && $this->grid_connected) {
            $recommendations[] = [
                'category' => 'تخزين الطاقة',
                'issue' => 'عدم وجود تخزين للطاقة',
                'recommendation' => 'تركيب بطاريات لتخزين الطاقة الزائدة',
                'potential_improvement' => '15-25%',
                'estimated_cost' => 'مرتفع',
            ];
        }
        
        return $recommendations;
    }

    // Events
    protected static function booted()
    {
        static::created(function ($source) {
            // Update property sustainability renewable energy percentage
            $source->updatePropertyRenewablePercentage();
        });

        static::updated(function ($source) {
            // Update property sustainability renewable energy percentage if capacity or status changed
            if ($source->wasChanged(['capacity', 'status'])) {
                $source->updatePropertyRenewablePercentage();
            }
        });

        static::deleted(function ($source) {
            // Update property sustainability renewable energy percentage
            $source->updatePropertyRenewablePercentage();
        });
    }

    private function updatePropertyRenewablePercentage(): void
    {
        $propertySustainability = $this->propertySustainability;
        $totalCapacity = $propertySustainability->renewableEnergySources()
            ->where('status', 'active')
            ->sum('capacity');
        
        // Simple calculation - this could be more sophisticated
        $renewablePercentage = min(100, $totalCapacity * 10); // Simplified calculation
        
        $propertySustainability->update(['renewable_energy_percentage' => round($renewablePercentage, 1)]);
    }
}
