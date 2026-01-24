<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AgentTerritory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'boundary_type',
        'coordinates',
        'polygon_data',
        'center_latitude',
        'center_longitude',
        'radius_km',
        'city',
        'state_province',
        'country',
        'postal_codes',
        'neighborhoods',
        'districts',
        'zones',
        'property_types',
        'price_ranges',
        'market_segments',
        'population_density',
        'average_income',
        'property_value_index',
        'growth_rate',
        'competition_level',
        'market_potential',
        'lead_conversion_rate',
        'average_days_on_market',
        'inventory_level',
        'demand_level',
        'supply_level',
        'seasonality_factors',
        'demographics',
        'economic_indicators',
        'infrastructure',
        'transportation',
        'schools',
        'healthcare',
        'shopping',
        'recreation',
        'business_centers',
        'industrial_areas',
        'development_projects',
        'zoning_regulations',
        'tax_rates',
        'insurance_requirements',
        'environmental_factors',
        'climate',
        'natural_disasters',
        'accessibility',
        'parking',
        'public_transport',
        'major_roads',
        'highways',
        'airports',
        'train_stations',
        'bus_routes',
        'special_features',
        'landmarks',
        'historical_significance',
        'cultural_aspects',
        'language_preferences',
        'cultural_considerations',
        'business_opportunities',
        'investment_potential',
        'development_timeline',
        'future_projections',
        'market_trends',
        'risk_factors',
        'opportunity_factors',
        'target_demographics',
        'marketing_strategies',
        'recommended_approaches',
        'best_practices',
        'local_regulations',
        'licensing_requirements',
        'permit_requirements',
        'compliance_notes',
        'is_active',
        'is_exclusive',
        'overlap_allowed',
        'max_agents',
        'current_agents_count',
        'waiting_list',
        'assignment_rules',
        'performance_metrics',
        'quality_standards',
        'service_expectations',
        'response_time_requirements',
        'coverage_requirements',
        'documentation_requirements',
        'training_requirements',
        'support_resources',
        'tools_and_technology',
        'marketing_materials',
        'branding_guidelines',
        'commission_structure',
        'fee_schedule',
        'incentive_programs',
        'performance_bonuses',
        'recognition_programs',
        'notes',
        'internal_notes',
        'custom_fields',
    ];

    protected $casts = [
        'coordinates' => 'json',
        'polygon_data' => 'json',
        'center_latitude' => 'decimal:10,8',
        'center_longitude' => 'decimal:11,8',
        'radius_km' => 'decimal:8,2',
        'postal_codes' => 'json',
        'neighborhoods' => 'json',
        'districts' => 'json',
        'zones' => 'json',
        'property_types' => 'json',
        'price_ranges' => 'json',
        'market_segments' => 'json',
        'population_density' => 'decimal:10,2',
        'average_income' => 'decimal:15,2',
        'property_value_index' => 'decimal:8,2',
        'growth_rate' => 'decimal:5,2',
        'lead_conversion_rate' => 'decimal:5,2',
        'average_days_on_market' => 'integer',
        'demographics' => 'json',
        'economic_indicators' => 'json',
        'infrastructure' => 'json',
        'transportation' => 'json',
        'schools' => 'json',
        'healthcare' => 'json',
        'shopping' => 'json',
        'recreation' => 'json',
        'business_centers' => 'json',
        'industrial_areas' => 'json',
        'development_projects' => 'json',
        'zoning_regulations' => 'json',
        'tax_rates' => 'json',
        'seasonality_factors' => 'json',
        'environmental_factors' => 'json',
        'special_features' => 'json',
        'landmarks' => 'json',
        'business_opportunities' => 'json',
        'investment_potential' => 'json',
        'future_projections' => 'json',
        'market_trends' => 'json',
        'risk_factors' => 'json',
        'opportunity_factors' => 'json',
        'target_demographics' => 'json',
        'marketing_strategies' => 'json',
        'best_practices' => 'json',
        'local_regulations' => 'json',
        'training_requirements' => 'json',
        'support_resources' => 'json',
        'tools_and_technology' => 'json',
        'marketing_materials' => 'json',
        'commission_structure' => 'json',
        'fee_schedule' => 'json',
        'incentive_programs' => 'json',
        'performance_bonuses' => 'json',
        'recognition_programs' => 'json',
        'is_active' => 'boolean',
        'is_exclusive' => 'boolean',
        'overlap_allowed' => 'boolean',
        'max_agents' => 'integer',
        'current_agents_count' => 'integer',
        'waiting_list' => 'json',
        'assignment_rules' => 'json',
        'performance_metrics' => 'json',
        'quality_standards' => 'json',
        'service_expectations' => 'json',
        'custom_fields' => 'json',
    ];

    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(Agent::class, 'agent_territory_pivot')
                    ->withPivot('assigned_date', 'status', 'performance_score', 'exclusive_rights', 'notes')
                    ->withTimestamps();
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeExclusive($query)
    {
        return $query->where('is_exclusive', true);
    }

    public function scopeNonExclusive($query)
    {
        return $query->where('is_exclusive', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByBoundaryType($query, $boundaryType)
    {
        return $query->where('boundary_type', $boundaryType);
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopeByState($query, $state)
    {
        return $query->where('state_province', $state);
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    public function scopeByPropertyType($query, $propertyType)
    {
        return $query->whereJsonContains('property_types', $propertyType);
    }

    public function scopeByPriceRange($query, $minPrice, $maxPrice = null)
    {
        $priceRanges = $query->first()->price_ranges ?? [];
        
        foreach ($priceRanges as $range) {
            if (isset($range['min']) && isset($range['max'])) {
                if ($range['min'] <= $minPrice && $range['max'] >= ($maxPrice ?? $minPrice)) {
                    return $query->whereJsonContains('price_ranges', $range);
                }
            }
        }
        
        return $query;
    }

    public function scopeByMarketSegment($query, $segment)
    {
        return $query->whereJsonContains('market_segments', $segment);
    }

    public function scopeWithAvailability($query)
    {
        return $query->whereRaw('(current_agents_count < max_agents OR max_agents IS NULL)');
    }

    public function scopeFullyAssigned($query)
    {
        return $query->whereRaw('current_agents_count >= max_agents');
    }

    public function scopeByCompetitionLevel($query, $level)
    {
        return $query->where('competition_level', $level);
    }

    public function scopeByMarketPotential($query, $potential)
    {
        return $query->where('market_potential', $potential);
    }

    public function scopeByDemandLevel($query, $level)
    {
        return $query->where('demand_level', $level);
    }

    public function scopeBySupplyLevel($query, $level)
    {
        return $query->where('supply_level', $level);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', '%' . $term . '%')
              ->orWhere('description', 'like', '%' . $term . '%')
              ->orWhere('city', 'like', '%' . $term . '%')
              ->orWhere('state_province', 'like', '%' . $term . '%')
              ->orWhere('country', 'like', '%' . $term . '%')
              ->orWhere('neighborhoods', 'like', '%' . $term . '%')
              ->orWhere('districts', 'like', '%' . $term . '%')
              ->orWhere('notes', 'like', '%' . $term . '%');
        });
    }

    // Helper Methods
    public function getCoordinatesListAttribute(): array
    {
        return $this->coordinates ?? [];
    }

    public function getPolygonDataListAttribute(): array
    {
        return $this->polygon_data ?? [];
    }

    public function getPostalCodesListAttribute(): array
    {
        return $this->postal_codes ?? [];
    }

    public function getNeighborhoodsListAttribute(): array
    {
        return $this->neighborhoods ?? [];
    }

    public function getDistrictsListAttribute(): array
    {
        return $this->districts ?? [];
    }

    public function getZonesListAttribute(): array
    {
        return $this->zones ?? [];
    }

    public function getPropertyTypesListAttribute(): array
    {
        return $this->property_types ?? [];
    }

    public function getPriceRangesListAttribute(): array
    {
        return $this->price_ranges ?? [];
    }

    public function getMarketSegmentsListAttribute(): array
    {
        return $this->market_segments ?? [];
    }

    public function getDemographicsListAttribute(): array
    {
        return $this->demographics ?? [];
    }

    public function getEconomicIndicatorsListAttribute(): array
    {
        return $this->economic_indicators ?? [];
    }

    public function getInfrastructureListAttribute(): array
    {
        return $this->infrastructure ?? [];
    }

    public function getTransportationListAttribute(): array
    {
        return $this->transportation ?? [];
    }

    public function getSchoolsListAttribute(): array
    {
        return $this->schools ?? [];
    }

    public function getHealthcareListAttribute(): array
    {
        return $this->healthcare ?? [];
    }

    public function getShoppingListAttribute(): array
    {
        return $this->shopping ?? [];
    }

    public function getRecreationListAttribute(): array
    {
        return $this->recreation ?? [];
    }

    public function getBusinessCentersListAttribute(): array
    {
        return $this->business_centers ?? [];
    }

    public function getIndustrialAreasListAttribute(): array
    {
        return $this->industrial_areas ?? [];
    }

    public function getDevelopmentProjectsListAttribute(): array
    {
        return $this->development_projects ?? [];
    }

    public function getZoningRegulationsListAttribute(): array
    {
        return $this->zoning_regulations ?? [];
    }

    public function getTaxRatesListAttribute(): array
    {
        return $this->tax_rates ?? [];
    }

    public function getSeasonalityFactorsListAttribute(): array
    {
        return $this->seasonality_factors ?? [];
    }

    public function getEnvironmentalFactorsListAttribute(): array
    {
        return $this->environmental_factors ?? [];
    }

    public function getSpecialFeaturesListAttribute(): array
    {
        return $this->special_features ?? [];
    }

    public function getLandmarksListAttribute(): array
    {
        return $this->landmarks ?? [];
    }

    public function getBusinessOpportunitiesListAttribute(): array
    {
        return $this->business_opportunities ?? [];
    }

    public function getInvestmentPotentialListAttribute(): array
    {
        return $this->investment_potential ?? [];
    }

    public function getFutureProjectionsListAttribute(): array
    {
        return $this->future_projections ?? [];
    }

    public function getMarketTrendsListAttribute(): array
    {
        return $this->market_trends ?? [];
    }

    public function getRiskFactorsListAttribute(): array
    {
        return $this->risk_factors ?? [];
    }

    public function getOpportunityFactorsListAttribute(): array
    {
        return $this->opportunity_factors ?? [];
    }

    public function getTargetDemographicsListAttribute(): array
    {
        return $this->target_demographics ?? [];
    }

    public function getMarketingStrategiesListAttribute(): array
    {
        return $this->marketing_strategies ?? [];
    }

    public function getBestPracticesListAttribute(): array
    {
        return $this->best_practices ?? [];
    }

    public function getLocalRegulationsListAttribute(): array
    {
        return $this->local_regulations ?? [];
    }

    public function getTrainingRequirementsListAttribute(): array
    {
        return $this->training_requirements ?? [];
    }

    public function getSupportResourcesListAttribute(): array
    {
        return $this->support_resources ?? [];
    }

    public function getToolsAndTechnologyListAttribute(): array
    {
        return $this->tools_and_technology ?? [];
    }

    public function getMarketingMaterialsListAttribute(): array
    {
        return $this->marketing_materials ?? [];
    }

    public function getCommissionStructureListAttribute(): array
    {
        return $this->commission_structure ?? [];
    }

    public function getFeeScheduleListAttribute(): array
    {
        return $this->fee_schedule ?? [];
    }

    public function getIncentiveProgramsListAttribute(): array
    {
        return $this->incentive_programs ?? [];
    }

    public function getPerformanceBonusesListAttribute(): array
    {
        return $this->performance_bonuses ?? [];
    }

    public function getRecognitionProgramsListAttribute(): array
    {
        return $this->recognition_programs ?? [];
    }

    public function getCustomFieldsListAttribute(): array
    {
        return $this->custom_fields ?? [];
    }

    public function getFormattedAverageIncomeAttribute(): string
    {
        return number_format($this->average_income, 2) . ' SAR';
    }

    public function getFormattedGrowthRateAttribute(): string
    {
        return number_format($this->growth_rate, 2) . '%';
    }

    public function getFormattedLeadConversionRateAttribute(): string
    {
        return number_format($this->lead_conversion_rate, 2) . '%';
    }

    public function getFormattedRadiusAttribute(): string
    {
        return $this->radius_km ? $this->radius_km . ' km' : 'Not specified';
    }

    public function getFullLocationAttribute(): string
    {
        $parts = array_filter([
            $this->city,
            $this->state_province,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    public function getCenterCoordinatesAttribute(): array
    {
        return [
            'latitude' => $this->center_latitude,
            'longitude' => $this->center_longitude,
        ];
    }

    public function getFormattedCenterCoordinatesAttribute(): string
    {
        return $this->center_latitude . ', ' . $this->center_longitude;
    }

    public function getAvailabilityStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        } elseif ($this->is_fully_assigned) {
            return 'full';
        } elseif ($this->current_agents_count === 0) {
            return 'available';
        } else {
            return 'limited';
        }
    }

    public function getAvailabilityStatusColorAttribute(): string
    {
        switch ($this->availability_status) {
            case 'available':
                return 'green';
            case 'limited':
                return 'yellow';
            case 'full':
                return 'red';
            case 'inactive':
                return 'gray';
            default:
                return 'gray';
        }
    }

    public function getMarketHealthAttribute(): string
    {
        $demandScore = $this->getDemandScore();
        $supplyScore = $this->getSupplyScore();
        $growthScore = $this->getGrowthScore();

        $overallScore = ($demandScore + $supplyScore + $growthScore) / 3;

        if ($overallScore >= 80) {
            return 'excellent';
        } elseif ($overallScore >= 60) {
            return 'good';
        } elseif ($overallScore >= 40) {
            return 'moderate';
        } elseif ($overallScore >= 20) {
            return 'poor';
        } else {
            return 'critical';
        }
    }

    public function getMarketHealthColorAttribute(): string
    {
        switch ($this->market_health) {
            case 'excellent':
                return 'green';
            case 'good':
                return 'blue';
            case 'moderate':
                return 'yellow';
            case 'poor':
                return 'orange';
            case 'critical':
                return 'red';
            default:
                return 'gray';
        }
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isExclusive(): bool
    {
        return $this->is_exclusive;
    }

    public function isFullyAssigned(): bool
    {
        return $this->max_agents && $this->current_agents_count >= $this->max_agents;
    }

    public function hasAvailability(): bool
    {
        return $this->is_active && !$this->is_fully_assigned;
    }

    public function hasCoordinates(): bool
    {
        return !empty($this->center_latitude) && !empty($this->center_longitude);
    }

    public function hasPolygonData(): bool
    {
        return !empty($this->polygon_data);
    }

    public function hasPostalCodes(): bool
    {
        return !empty($this->postal_codes);
    }

    public function hasNeighborhoods(): bool
    {
        return !empty($this->neighborhoods);
    }

    public function hasDistricts(): bool
    {
        return !empty($this->districts);
    }

    public function hasPropertyTypes(): bool
    {
        return !empty($this->property_types);
    }

    public function hasPriceRanges(): bool
    {
        return !empty($this->price_ranges);
    }

    public function hasMarketSegments(): bool
    {
        return !empty($this->market_segments);
    }

    public function hasDemographics(): bool
    {
        return !empty($this->demographics);
    }

    public function hasEconomicIndicators(): bool
    {
        return !empty($this->economic_indicators);
    }

    public function hasInfrastructure(): bool
    {
        return !empty($this->infrastructure);
    }

    public function hasTransportation(): bool
    {
        return !empty($this->transportation);
    }

    public function hasSchools(): bool
    {
        return !empty($this->schools);
    }

    public function hasHealthcare(): bool
    {
        return !empty($this->healthcare);
    }

    public function hasShopping(): bool
    {
        return !empty($this->shopping);
    }

    public function hasRecreation(): bool
    {
        return !empty($this->recreation);
    }

    public function hasBusinessOpportunities(): bool
    {
        return !empty($this->business_opportunities);
    }

    public function hasInvestmentPotential(): bool
    {
        return !empty($this->investment_potential);
    }

    public function hasMarketTrends(): bool
    {
        return !empty($this->market_trends);
    }

    public function hasRiskFactors(): bool
    {
        return !empty($this->risk_factors);
    }

    public function hasOpportunityFactors(): bool
    {
        return !empty($this->opportunity_factors);
    }

    public function hasMarketingStrategies(): bool
    {
        return !empty($this->marketing_strategies);
    }

    public function hasBestPractices(): bool
    {
        return !empty($this->best_practices);
    }

    public function hasLocalRegulations(): bool
    {
        return !empty($this->local_regulations);
    }

    public function hasTrainingRequirements(): bool
    {
        return !empty($this->training_requirements);
    }

    public function hasSupportResources(): bool
    {
        return !empty($this->support_resources);
    }

    public function hasCommissionStructure(): bool
    {
        return !empty($this->commission_structure);
    }

    public function hasIncentivePrograms(): bool
    {
        return !empty($this->incentive_programs);
    }

    public function hasCustomFields(): bool
    {
        return !empty($this->custom_fields);
    }

    public function getDemandScore(): int
    {
        $score = 0;
        
        switch ($this->demand_level) {
            case 'very_high':
                $score = 100;
                break;
            case 'high':
                $score = 80;
                break;
            case 'moderate':
                $score = 60;
                break;
            case 'low':
                $score = 40;
                break;
            case 'very_low':
                $score = 20;
                break;
        }

        return $score;
    }

    public function getSupplyScore(): int
    {
        $score = 0;
        
        switch ($this->supply_level) {
            case 'very_low':
                $score = 100;
                break;
            case 'low':
                $score = 80;
                break;
            case 'moderate':
                $score = 60;
                break;
            case 'high':
                $score = 40;
                break;
            case 'very_high':
                $score = 20;
                break;
        }

        return $score;
    }

    public function getGrowthScore(): int
    {
        return min(max($this->growth_rate * 10, 0), 100);
    }

    public function getCompetitionScore(): int
    {
        $score = 0;
        
        switch ($this->competition_level) {
            case 'very_low':
                $score = 100;
                break;
            case 'low':
                $score = 80;
                break;
            case 'moderate':
                $score = 60;
                break;
            case 'high':
                $score = 40;
                break;
            case 'very_high':
                $score = 20;
                break;
        }

        return $score;
    }

    public function getMarketPotentialScore(): int
    {
        $score = 0;
        
        switch ($this->market_potential) {
            case 'excellent':
                $score = 100;
                break;
            case 'good':
                $score = 80;
                break;
            case 'moderate':
                $score = 60;
                break;
            case 'limited':
                $score = 40;
                break;
            case 'poor':
                $score = 20;
                break;
        }

        return $score;
    }

    public function getOverallMarketScore(): int
    {
        $weights = [
            'demand' => 0.3,
            'supply' => 0.2,
            'growth' => 0.2,
            'competition' => 0.15,
            'potential' => 0.15,
        ];

        $overallScore = 
            ($this->getDemandScore() * $weights['demand']) +
            ($this->getSupplyScore() * $weights['supply']) +
            ($this->getGrowthScore() * $weights['growth']) +
            ($this->getCompetitionScore() * $weights['competition']) +
            ($this->getMarketPotentialScore() * $weights['potential']);

        return round($overallScore);
    }

    public function getAvailableSlotsAttribute(): int
    {
        if (!$this->max_agents) {
            return 999; // Unlimited
        }

        return max(0, $this->max_agents - $this->current_agents_count);
    }

    public function getOccupancyRateAttribute(): float
    {
        if (!$this->max_agents) {
            return 0;
        }

        return ($this->current_agents_count / $this->max_agents) * 100;
    }

    public function getFormattedOccupancyRateAttribute(): string
    {
        return number_format($this->occupancy_rate, 1) . '%';
    }

    public function isPointInTerritory($latitude, $longitude): bool
    {
        if (!$this->has_coordinates()) {
            return false;
        }

        // Simple radius check for circular territories
        if ($this->boundary_type === 'radius' && $this->radius_km) {
            $distance = $this->calculateDistance($latitude, $longitude);
            return $distance <= $this->radius_km;
        }

        // Polygon check for complex boundaries
        if ($this->boundary_type === 'polygon' && $this->has_polygon_data) {
            return $this->isPointInPolygon($latitude, $longitude);
        }

        return false;
    }

    public function calculateDistance($latitude, $longitude): float
    {
        if (!$this->has_coordinates()) {
            return 0;
        }

        $earthRadius = 6371; // Earth's radius in kilometers

        $latDiff = deg2rad($latitude - $this->center_latitude);
        $lonDiff = deg2rad($longitude - $this->center_longitude);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos(deg2rad($this->center_latitude)) * cos(deg2rad($latitude)) *
             sin($lonDiff / 2) * sin($lonDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function isPointInPolygon($latitude, $longitude): bool
    {
        if (!$this->has_polygon_data) {
            return false;
        }

        $points = $this->polygon_data_list;
        $n = count($points);
        $inside = false;

        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $xi = $points[$i]['lat'];
            $yi = $points[$i]['lng'];
            $xj = $points[$j]['lat'];
            $yj = $points[$j]['lng'];

            $intersect = (($yi > $longitude) != ($yj > $longitude))
                && ($latitude < ($xj - $xi) * ($longitude - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

    public function addNeighborhood(string $neighborhood): void
    {
        $neighborhoods = $this->neighborhoods ?? [];
        
        if (!in_array($neighborhood, $neighborhoods)) {
            $neighborhoods[] = $neighborhood;
            $this->update(['neighborhoods' => $neighborhoods]);
        }
    }

    public function removeNeighborhood(string $neighborhood): void
    {
        $neighborhoods = $this->neighborhoods ?? [];
        
        if (($key = array_search($neighborhood, $neighborhoods)) !== false) {
            unset($neighborhoods[$key]);
            $this->update(['neighborhoods' => array_values($neighborhoods)]);
        }
    }

    public function addPropertyType(string $propertyType): void
    {
        $propertyTypes = $this->property_types ?? [];
        
        if (!in_array($propertyType, $propertyTypes)) {
            $propertyTypes[] = $propertyType;
            $this->update(['property_types' => $propertyTypes]);
        }
    }

    public function addMarketSegment(string $segment): void
    {
        $segments = $this->market_segments ?? [];
        
        if (!in_array($segment, $segments)) {
            $segments[] = $segment;
            $this->update(['market_segments' => $segments]);
        }
    }

    public function addBusinessOpportunity(array $opportunity): void
    {
        $opportunities = $this->business_opportunities ?? [];
        $opportunities[] = array_merge($opportunity, ['added_date' => now()->format('Y-m-d')]);
        $this->update(['business_opportunities' => $opportunities]);
    }

    public function addRiskFactor(array $risk): void
    {
        $risks = $this->risk_factors ?? [];
        $risks[] = array_merge($risk, ['identified_date' => now()->format('Y-m-d')]);
        $this->update(['risk_factors' => $risks]);
    }

    public function setCustomField(string $key, $value): void
    {
        $customFields = $this->custom_fields ?? [];
        $customFields[$key] = $value;
        $this->update(['custom_fields' => $customFields]);
    }

    public function getCustomField(string $key, $default = null)
    {
        $customFields = $this->custom_fields ?? [];
        return $customFields[$key] ?? $default;
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function makeExclusive(): void
    {
        $this->update(['is_exclusive' => true]);
    }

    public function makeNonExclusive(): void
    {
        $this->update(['is_exclusive' => false]);
    }

    public function updateAgentCount(): void
    {
        $count = $this->agents()->wherePivot('status', 'active')->count();
        $this->update(['current_agents_count' => $count]);
    }

    public function assignAgent($agentId, $exclusiveRights = false): bool
    {
        if (!$this->has_availability) {
            return false;
        }

        if ($this->is_exclusive && $this->current_agents_count > 0) {
            return false;
        }

        $this->agents()->attach($agentId, [
            'assigned_date' => now(),
            'status' => 'active',
            'exclusive_rights' => $exclusiveRights,
        ]);

        $this->updateAgentCount();

        return true;
    }

    public function unassignAgent($agentId): void
    {
        $this->agents()->detach($agentId);
        $this->updateAgentCount();
    }

    public function getTerritorySummaryAttribute(): array
    {
        return [
            'name' => $this->name,
            'location' => $this->full_location,
            'type' => $this->type,
            'agents_count' => $this->current_agents_count,
            'max_agents' => $this->max_agents,
            'availability' => $this->availability_status,
            'market_health' => $this->market_health,
            'market_score' => $this->overall_market_score,
            'property_types' => $this->property_types_list,
            'price_ranges' => $this->price_ranges_list,
            'exclusive' => $this->is_exclusive,
        ];
    }

    public function getMarketAnalysisAttribute(): array
    {
        return [
            'demand_level' => $this->demand_level,
            'supply_level' => $this->supply_level,
            'growth_rate' => $this->formatted_growth_rate,
            'competition_level' => $this->competition_level,
            'market_potential' => $this->market_potential,
            'lead_conversion_rate' => $this->formatted_lead_conversion_rate,
            'average_days_on_market' => $this->average_days_on_market,
            'inventory_level' => $this->inventory_level,
            'market_health' => $this->market_health,
            'overall_score' => $this->overall_market_score,
        ];
    }

    public function getDemographicProfileAttribute(): array
    {
        return [
            'population_density' => $this->population_density,
            'average_income' => $this->formatted_average_income,
            'property_value_index' => $this->property_value_index,
            'demographics' => $this->demographics_list,
            'target_demographics' => $this->target_demographics_list,
        ];
    }

    public function getInfrastructureProfileAttribute(): array
    {
        return [
            'infrastructure' => $this->infrastructure_list,
            'transportation' => $this->transportation_list,
            'schools' => $this->schools_list,
            'healthcare' => $this->healthcare_list,
            'shopping' => $this->shopping_list,
            'recreation' => $this->recreation_list,
            'business_centers' => $this->business_centers_list,
        ];
    }

    public function getOpportunitiesAndRisksAttribute(): array
    {
        return [
            'business_opportunities' => $this->business_opportunities_list,
            'investment_potential' => $this->investment_potential_list,
            'development_projects' => $this->development_projects_list,
            'market_trends' => $this->market_trends_list,
            'opportunity_factors' => $this->opportunity_factors_list,
            'risk_factors' => $this->risk_factors_list,
            'future_projections' => $this->future_projections_list,
        ];
    }
}
