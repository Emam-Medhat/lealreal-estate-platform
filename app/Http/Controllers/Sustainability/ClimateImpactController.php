<?php

namespace App\Http\Controllers\Sustainability;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Sustainability\PropertySustainability;
use App\Models\Sustainability\ClimateImpact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClimateImpactController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_sustainability');
    }

    public function index()
    {
        $climateImpacts = ClimateImpact::with(['propertySustainability.property'])
            ->when(Auth::user()->hasRole('agent'), function($query) {
                $query->whereHas('propertySustainability.property', function($q) {
                    $q->where('agent_id', Auth::id());
                });
            })
            ->latest('assessment_date')
            ->paginate(15);

        $stats = [
            'total_assessments' => ClimateImpact::count(),
            'average_risk_score' => ClimateImpact::avg('climate_risk_score'),
            'high_risk_properties' => ClimateImpact::where('climate_risk_score', '>=', 70)->count(),
            'properties_with_adaptation' => ClimateImpact::where('adaptation_measures_implemented', true)->count(),
        ];

        return view('sustainability.climate-impact.index', compact('climateImpacts', 'stats'));
    }

    public function create()
    {
        $properties = PropertySustainability::with('property')
            ->when(Auth::user()->hasRole('agent'), function($query) {
                $query->whereHas('property', function($q) {
                    $q->where('agent_id', Auth::id());
                });
            })
            ->get();

        $riskCategories = [
            'heat_waves' => 'موجات الحرارة',
            'flooding' => 'الفيضانات',
            'sea_level_rise' => 'ارتفاع مستوى سطح البحر',
            'drought' => 'الجفاف',
            'storms' => 'العواصف',
            'wildfires' => 'حرائق الغابات',
            'air_quality' => 'جودة الهواء',
            'extreme_precipitation' => 'هطول الأمطار الغزيرة',
        ];

        return view('sustainability.climate-impact.create', compact('properties', 'riskCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_sustainability_id' => 'required|exists:property_sustainability,id',
            'climate_risk_score' => 'required|numeric|min:0|max:100',
            'heat_wave_risk' => 'required|numeric|min:0|max:100',
            'flooding_risk' => 'required|numeric|min:0|max:100',
            'sea_level_rise_risk' => 'required|numeric|min:0|max:100',
            'drought_risk' => 'required|numeric|min:0|max:100',
            'storm_risk' => 'required|numeric|min:0|max:100',
            'wildfire_risk' => 'required|numeric|min:0|max:100',
            'air_quality_risk' => 'required|numeric|min:0|max:100',
            'extreme_precipitation_risk' => 'required|numeric|min:0|max:100',
            'vulnerability_score' => 'required|numeric|min:0|max:100',
            'adaptation_capacity' => 'required|numeric|min:0|max:100',
            'resilience_score' => 'required|numeric|min:0|max:100',
            'location_coordinates' => 'required|string|max:255',
            'elevation' => 'required|numeric',
            'distance_from_coast' => 'nullable|numeric',
            'proximity_to_water_bodies' => 'required|boolean',
            'water_body_type' => 'nullable|string|max:255',
            'flood_zone' => 'required|string|max:255',
            'soil_type' => 'required|string|max:255',
            'vegetation_cover' => 'required|numeric|min:0|max:100',
            'building_foundation_type' => 'required|string|max:255',
            'building_materials' => 'required|array',
            'building_materials.*' => 'string',
            'roof_condition' => 'required|string|max:255',
            'drainage_system' => 'required|boolean',
            'drainage_capacity' => 'nullable|string|max:255',
            'cooling_system_type' => 'required|string|max:255',
            'cooling_efficiency' => 'required|numeric|min:0|max:100',
            'ventilation_system' => 'required|boolean',
            'insulation_level' => 'required|string|max:255',
            'shade_provision' => 'required|boolean',
            'green_roof' => 'required|boolean',
            'permeable_surfaces' => 'required|numeric|min:0|max:100',
            'rainwater_harvesting' => 'required|boolean',
            'flood_barriers' => 'required|boolean',
            'barrier_type' => 'nullable|string|max:255',
            'fire_resistance_rating' => 'required|integer|min:1|max:10',
            'emergency_power_system' => 'required|boolean',
            'emergency_water_storage' => 'required|boolean',
            'emergency_shelter_capacity' => 'nullable|integer|min:0',
            'early_warning_system' => 'required|boolean',
            'evacuation_plan' => 'required|boolean',
            'climate_resilience_features' => 'required|array',
            'climate_resilience_features.*' => 'string',
            'adaptation_measures_implemented' => 'required|boolean',
            'implemented_measures' => 'nullable|array',
            'implemented_measures.*' => 'string',
            'planned_adaptation_measures' => 'nullable|array',
            'planned_adaptation_measures.*' => 'string',
            'adaptation_cost_estimate' => 'nullable|numeric|min:0',
            'adaptation_timeline' => 'nullable|string|max:255',
            'insurance_coverage' => 'required|boolean',
            'insurance_provider' => 'nullable|string|max:255',
            'coverage_details' => 'nullable|string',
            'climate_data_sources' => 'required|array',
            'climate_data_sources.*' => 'string',
            'assessment_methodology' => 'required|string|max:1000',
            'assessment_date' => 'required|date|before_or_equal:today',
            'next_assessment_date' => 'required|date|after:assessment_date',
            'assessor_name' => 'required|string|max:255',
            'assessor_qualifications' => 'nullable|string|max:255',
            'projected_climate_impacts' => 'nullable|array',
            'projected_climate_impacts.*' => 'string',
            'time_horizon_years' => 'required|integer|min:1|max:100',
            'scenario_used' => 'required|string|max:255',
            'confidence_level' => 'required|numeric|min:0|max:100',
            'uncertainty_factors' => 'nullable|array',
            'uncertainty_factors.*' => 'string',
            'mitigation_strategies' => 'required|array',
            'mitigation_strategies.*' => 'string',
            'monitoring_plan' => 'required|boolean',
            'monitoring_frequency' => 'nullable|string|max:255',
            'key_performance_indicators' => 'nullable|array',
            'key_performance_indicators.*' => 'string',
            'stakeholder_engagement' => 'required|boolean',
            'community_resilience_contribution' => 'nullable|string|max:1000',
            'biodiversity_impact' => 'required|string|max:1000',
            'ecosystem_services_impact' => 'nullable|string|max:1000',
            'climate_justice_considerations' => 'nullable|string|max:1000',
            'notes' => 'nullable|string',
        ]);

        // Calculate derived scores if not provided
        if (!isset($validated['climate_risk_score'])) {
            $validated['climate_risk_score'] = $this->calculateClimateRiskScore($validated);
        }

        if (!isset($validated['resilience_score'])) {
            $validated['resilience_score'] = $this->calculateResilienceScore($validated);
        }

        $climateImpact = ClimateImpact::create([
            'property_sustainability_id' => $validated['property_sustainability_id'],
            'climate_risk_score' => $validated['climate_risk_score'],
            'heat_wave_risk' => $validated['heat_wave_risk'],
            'flooding_risk' => $validated['flooding_risk'],
            'sea_level_rise_risk' => $validated['sea_level_rise_risk'],
            'drought_risk' => $validated['drought_risk'],
            'storm_risk' => $validated['storm_risk'],
            'wildfire_risk' => $validated['wildfire_risk'],
            'air_quality_risk' => $validated['air_quality_risk'],
            'extreme_precipitation_risk' => $validated['extreme_precipitation_risk'],
            'vulnerability_score' => $validated['vulnerability_score'],
            'adaptation_capacity' => $validated['adaptation_capacity'],
            'resilience_score' => $validated['resilience_score'],
            'location_coordinates' => $validated['location_coordinates'],
            'elevation' => $validated['elevation'],
            'distance_from_coast' => $validated['distance_from_coast'],
            'proximity_to_water_bodies' => $validated['proximity_to_water_bodies'],
            'water_body_type' => $validated['water_body_type'],
            'flood_zone' => $validated['flood_zone'],
            'soil_type' => $validated['soil_type'],
            'vegetation_cover' => $validated['vegetation_cover'],
            'building_foundation_type' => $validated['building_foundation_type'],
            'building_materials' => $validated['building_materials'],
            'roof_condition' => $validated['roof_condition'],
            'drainage_system' => $validated['drainage_system'],
            'drainage_capacity' => $validated['drainage_capacity'],
            'cooling_system_type' => $validated['cooling_system_type'],
            'cooling_efficiency' => $validated['cooling_efficiency'],
            'ventilation_system' => $validated['ventilation_system'],
            'insulation_level' => $validated['insulation_level'],
            'shade_provision' => $validated['shade_provision'],
            'green_roof' => $validated['green_roof'],
            'permeable_surfaces' => $validated['permeable_surfaces'],
            'rainwater_harvesting' => $validated['rainwater_harvesting'],
            'flood_barriers' => $validated['flood_barriers'],
            'barrier_type' => $validated['barrier_type'],
            'fire_resistance_rating' => $validated['fire_resistance_rating'],
            'emergency_power_system' => $validated['emergency_power_system'],
            'emergency_water_storage' => $validated['emergency_water_storage'],
            'emergency_shelter_capacity' => $validated['emergency_shelter_capacity'],
            'early_warning_system' => $validated['early_warning_system'],
            'evacuation_plan' => $validated['evacuation_plan'],
            'climate_resilience_features' => $validated['climate_resilience_features'],
            'adaptation_measures_implemented' => $validated['adaptation_measures_implemented'],
            'implemented_measures' => $validated['implemented_measures'] ?? [],
            'planned_adaptation_measures' => $validated['planned_adaptation_measures'] ?? [],
            'adaptation_cost_estimate' => $validated['adaptation_cost_estimate'] ?? 0,
            'adaptation_timeline' => $validated['adaptation_timeline'],
            'insurance_coverage' => $validated['insurance_coverage'],
            'insurance_provider' => $validated['insurance_provider'],
            'coverage_details' => $validated['coverage_details'],
            'climate_data_sources' => $validated['climate_data_sources'],
            'assessment_methodology' => $validated['assessment_methodology'],
            'assessment_date' => $validated['assessment_date'],
            'next_assessment_date' => $validated['next_assessment_date'],
            'assessor_name' => $validated['assessor_name'],
            'assessor_qualifications' => $validated['assessor_qualifications'],
            'projected_climate_impacts' => $validated['projected_climate_impacts'] ?? [],
            'time_horizon_years' => $validated['time_horizon_years'],
            'scenario_used' => $validated['scenario_used'],
            'confidence_level' => $validated['confidence_level'],
            'uncertainty_factors' => $validated['uncertainty_factors'] ?? [],
            'mitigation_strategies' => $this->generateMitigationStrategies($validated),
            'monitoring_plan' => $validated['monitoring_plan'],
            'monitoring_frequency' => $validated['monitoring_frequency'],
            'key_performance_indicators' => $validated['key_performance_indicators'] ?? [],
            'stakeholder_engagement' => $validated['stakeholder_engagement'],
            'community_resilience_contribution' => $validated['community_resilience_contribution'],
            'biodiversity_impact' => $validated['biodiversity_impact'],
            'ecosystem_services_impact' => $validated['ecosystem_services_impact'],
            'climate_justice_considerations' => $validated['climate_justice_considerations'],
            'created_by' => Auth::id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('climate-impact.show', $climateImpact)
            ->with('success', 'تم تقييم التأثير المناخي بنجاح');
    }

    public function show(ClimateImpact $climateImpact)
    {
        $climateImpact->load(['propertySustainability.property']);
        
        // Get risk analysis
        $riskAnalysis = $this->analyzeRisks($climateImpact);
        
        // Get adaptation recommendations
        $adaptationRecommendations = $this->generateAdaptationRecommendations($climateImpact);

        return view('sustainability.climate-impact.show', compact('climateImpact', 'riskAnalysis', 'adaptationRecommendations'));
    }

    public function edit(ClimateImpact $climateImpact)
    {
        $climateImpact->load('propertySustainability.property');
        
        $riskCategories = [
            'heat_waves' => 'موجات الحرارة',
            'flooding' => 'الفيضانات',
            'sea_level_rise' => 'ارتفاع مستوى سطح البحر',
            'drought' => 'الجفاف',
            'storms' => 'العواصف',
            'wildfires' => 'حرائق الغابات',
            'air_quality' => 'جودة الهواء',
            'extreme_precipitation' => 'هطول الأمطار الغزيرة',
        ];

        return view('sustainability.climate-impact.edit', compact('climateImpact', 'riskCategories'));
    }

    public function update(Request $request, ClimateImpact $climateImpact)
    {
        $validated = $request->validate([
            'climate_risk_score' => 'required|numeric|min:0|max:100',
            'heat_wave_risk' => 'required|numeric|min:0|max:100',
            'flooding_risk' => 'required|numeric|min:0|max:100',
            'sea_level_rise_risk' => 'required|numeric|min:0|max:100',
            'drought_risk' => 'required|numeric|min:0|max:100',
            'storm_risk' => 'required|numeric|min:0|max:100',
            'wildfire_risk' => 'required|numeric|min:0|max:100',
            'air_quality_risk' => 'required|numeric|min:0|max:100',
            'extreme_precipitation_risk' => 'required|numeric|min:0|max:100',
            'vulnerability_score' => 'required|numeric|min:0|max:100',
            'adaptation_capacity' => 'required|numeric|min:0|max:100',
            'resilience_score' => 'required|numeric|min:0|max:100',
            'adaptation_measures_implemented' => 'required|boolean',
            'planned_adaptation_measures' => 'nullable|array',
            'planned_adaptation_measures.*' => 'string',
            'adaptation_cost_estimate' => 'nullable|numeric|min:0',
            'adaptation_timeline' => 'nullable|string|max:255',
            'insurance_coverage' => 'required|boolean',
            'next_assessment_date' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);

        $climateImpact->update([
            'climate_risk_score' => $validated['climate_risk_score'],
            'heat_wave_risk' => $validated['heat_wave_risk'],
            'flooding_risk' => $validated['flooding_risk'],
            'sea_level_rise_risk' => $validated['sea_level_rise_risk'],
            'drought_risk' => $validated['drought_risk'],
            'storm_risk' => $validated['storm_risk'],
            'wildfire_risk' => $validated['wildfire_risk'],
            'air_quality_risk' => $validated['air_quality_risk'],
            'extreme_precipitation_risk' => $validated['extreme_precipitation_risk'],
            'vulnerability_score' => $validated['vulnerability_score'],
            'adaptation_capacity' => $validated['adaptation_capacity'],
            'resilience_score' => $validated['resilience_score'],
            'adaptation_measures_implemented' => $validated['adaptation_measures_implemented'],
            'planned_adaptation_measures' => $validated['planned_adaptation_measures'] ?? [],
            'adaptation_cost_estimate' => $validated['adaptation_cost_estimate'] ?? 0,
            'adaptation_timeline' => $validated['adaptation_timeline'],
            'insurance_coverage' => $validated['insurance_coverage'],
            'next_assessment_date' => $validated['next_assessment_date'],
            'mitigation_strategies' => $this->generateMitigationStrategies($validated),
            'notes' => $validated['notes'] ?? $climateImpact->notes,
        ]);

        return redirect()
            ->route('climate-impact.show', $climateImpact)
            ->with('success', 'تم تحديث تقييم التأثير المناخي بنجاح');
    }

    public function destroy(ClimateImpact $climateImpact)
    {
        $climateImpact->delete();

        return redirect()
            ->route('climate-impact.index')
            ->with('success', 'تم حذف تقييم التأثير المناخي بنجاح');
    }

    public function riskAssessment()
    {
        return view('sustainability.climate-impact.risk-assessment');
    }

    public function calculateRisk(Request $request)
    {
        $validated = $request->validate([
            'location_type' => 'required|string|in:coastal,inland,mountain,urban,rural',
            'elevation' => 'required|numeric',
            'distance_from_water' => 'nullable|numeric',
            'building_age' => 'required|integer|min:0|max:' . date('Y'),
            'building_type' => 'required|string',
            'flood_zone' => 'required|string',
            'vegetation_cover' => 'required|numeric|min:0|max:100',
            'cooling_system' => 'required|boolean',
            'drainage_system' => 'required|boolean',
            'emergency_systems' => 'required|boolean',
        ]);

        // Quick risk calculation
        $riskScores = $this->quickRiskCalculation($validated);

        return response()->json([
            'overall_risk' => round($riskScores['overall'], 1),
            'heat_wave_risk' => round($riskScores['heat_wave'], 1),
            'flooding_risk' => round($riskScores['flooding'], 1),
            'storm_risk' => round($riskScores['storm'], 1),
            'recommendations' => $this->getQuickRiskRecommendations($riskScores),
        ]);
    }

    public function analytics()
    {
        $riskTrends = ClimateImpact::selectRaw('DATE_FORMAT(assessment_date, "%Y-%m") as month, AVG(climate_risk_score) as avg_risk, COUNT(*) as count')
            ->where('assessment_date', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $riskDistribution = ClimateImpact::selectRaw('
            CASE 
                WHEN climate_risk_score >= 80 THEN "مرتفع جداً (80-100)"
                WHEN climate_risk_score >= 60 THEN "مرتفع (60-79)"
                WHEN climate_risk_score >= 40 THEN "متوسط (40-59)"
                WHEN climate_risk_score >= 20 THEN "منخفض (20-39)"
                ELSE "منخفض جداً (0-19)"
            END as risk_range, 
            COUNT(*) as count
        ')
            ->groupBy('risk_range')
            ->orderBy('climate_risk_score', 'desc')
            ->get();

        $adaptationStats = ClimateImpact::selectRaw('
            AVG(adaptation_capacity) as avg_adaptation,
            AVG(resilience_score) as avg_resilience,
            SUM(CASE WHEN adaptation_measures_implemented = 1 THEN 1 ELSE 0 END) as with_adaptation,
            SUM(CASE WHEN insurance_coverage = 1 THEN 1 ELSE 0 END) as insured,
            COUNT(*) as total
        ')
            ->first();

        $highRiskProperties = ClimateImpact::with(['propertySustainability.property'])
            ->where('climate_risk_score', '>=', 70)
            ->orderBy('climate_risk_score', 'desc')
            ->take(10)
            ->get();

        return view('sustainability.climate-impact.analytics', compact(
            'riskTrends',
            'riskDistribution',
            'adaptationStats',
            'highRiskProperties'
        ));
    }

    public function adaptationPlan(ClimateImpact $climateImpact)
    {
        $adaptationPlan = $this->generateDetailedAdaptationPlan($climateImpact);
        
        return view('sustainability.climate-impact.adaptation-plan', compact('climateImpact', 'adaptationPlan'));
    }

    private function calculateClimateRiskScore($data)
    {
        $weights = [
            'heat_waves' => 0.15,
            'flooding' => 0.20,
            'sea_level_rise' => 0.15,
            'drought' => 0.10,
            'storms' => 0.15,
            'wildfires' => 0.10,
            'air_quality' => 0.05,
            'extreme_precipitation' => 0.10,
        ];

        $totalRisk = (
            $data['heat_wave_risk'] * $weights['heat_waves'] +
            $data['flooding_risk'] * $weights['flooding'] +
            $data['sea_level_rise_risk'] * $weights['sea_level_rise'] +
            $data['drought_risk'] * $weights['drought'] +
            $data['storm_risk'] * $weights['storms'] +
            $data['wildfire_risk'] * $weights['wildfires'] +
            $data['air_quality_risk'] * $weights['air_quality'] +
            $data['extreme_precipitation_risk'] * $weights['extreme_precipitation']
        );

        return round($totalRisk, 1);
    }

    private function calculateResilienceScore($data)
    {
        $baseScore = 50;
        
        // Positive factors
        if ($data['adaptation_measures_implemented']) $baseScore += 15;
        if ($data['insurance_coverage']) $baseScore += 10;
        if ($data['early_warning_system']) $baseScore += 10;
        if ($data['emergency_power_system']) $baseScore += 8;
        if ($data['drainage_system']) $baseScore += 7;
        if ($data['green_roof']) $baseScore += 5;
        if ($data['rainwater_harvesting']) $baseScore += 5;
        
        // Adjust based on adaptation capacity
        $baseScore += ($data['adaptation_capacity'] - 50) * 0.3;
        
        return max(0, min(100, $baseScore));
    }

    private function generateMitigationStrategies($data)
    {
        $strategies = [];
        
        if ($data['heat_wave_risk'] > 60) {
            $strategies[] = 'تحسين أنظمة التبريد والعزل الحراري';
        }
        
        if ($data['flooding_risk'] > 60) {
            $strategies[] = 'تركيب حواجز فيضانات وتحسين أنظمة الصرف';
        }
        
        if ($data['storm_risk'] > 60) {
            $strategies[] = 'تعزيز هيكل المبنى وتأمين الأشياء الخارجية';
        }
        
        if ($data['wildfire_risk'] > 60) {
            $strategies[] = 'إنشاء منطقة عازلة حول المبنى واستخدام مواد مقاومة للحريق';
        }
        
        return $strategies;
    }

    private function analyzeRisks($climateImpact)
    {
        return [
            'highest_risks' => $this->getHighestRisks($climateImpact),
            'critical_vulnerabilities' => $this->getCriticalVulnerabilities($climateImpact),
            'time_sensitive_risks' => $this->getTimeSensitiveRisks($climateImpact),
            'cascading_effects' => $this->analyzeCascadingEffects($climateImpact),
        ];
    }

    private function generateAdaptationRecommendations($climateImpact)
    {
        $recommendations = [];
        
        if ($climateImpact->climate_risk_score > 70) {
            $recommendations[] = [
                'priority' => 'عاجل',
                'action' => 'تنفيذ خطة تكيف شاملة',
                'timeline' => '0-6 أشهر',
                'cost_estimate' => 'مرتفع',
            ];
        }
        
        if ($climateImpact->heat_wave_risk > 70) {
            $recommendations[] = [
                'priority' => 'مرتفع',
                'action' => 'تركيب أنظمة تبريد فعالة',
                'timeline' => '3-6 أشهر',
                'cost_estimate' => 'متوسط',
            ];
        }
        
        if ($climateImpact->flooding_risk > 70) {
            $recommendations[] = [
                'priority' => 'مرتفع',
                'action' => 'تحسين أنظمة الصرف وحماية المبنى',
                'timeline' => '6-12 شهر',
                'cost_estimate' => 'مرتفع',
            ];
        }
        
        return $recommendations;
    }

    private function quickRiskCalculation($data)
    {
        $baseRisk = 30;
        
        // Location-based risks
        if ($data['location_type'] === 'coastal') $baseRisk += 20;
        if ($data['location_type'] === 'inland') $baseRisk += 10;
        if ($data['location_type'] === 'mountain') $baseRisk += 15;
        
        // Elevation risk
        if ($data['elevation'] < 10) $baseRisk += 15;
        
        // Building factors
        if ($data['building_age'] > 30) $baseRisk += 10;
        
        // System factors
        if (!$data['cooling_system']) $baseRisk += 15;
        if (!$data['drainage_system']) $baseRisk += 20;
        if (!$data['emergency_systems']) $baseRisk += 10;
        
        return [
            'overall' => min(100, $baseRisk),
            'heat_wave' => $data['cooling_system'] ? 40 : 70,
            'flooding' => $data['drainage_system'] ? 35 : 75,
            'storm' => $data['building_age'] > 30 ? 60 : 40,
        ];
    }

    private function getQuickRiskRecommendations($riskScores)
    {
        $recommendations = [];
        
        if ($riskScores['overall'] > 70) {
            $recommendations[] = 'مخاطر مناخية عالية - تتطلب إجراءات عاجلة';
        }
        
        if ($riskScores['heat_wave'] > 60) {
            $recommendations[] = 'تحسين أنظمة التبريد ضروري';
        }
        
        if ($riskScores['flooding'] > 60) {
            $recommendations[] = 'تحسين أنظمة الصرف مطلوب';
        }
        
        return $recommendations;
    }

    private function getHighestRisks($climateImpact)
    {
        $risks = [
            'موجات الحرارة' => $climateImpact->heat_wave_risk,
            'الفيضانات' => $climateImpact->flooding_risk,
            'ارتفاع مستوى سطح البحر' => $climateImpact->sea_level_rise_risk,
            'الجفاف' => $climateImpact->drought_risk,
            'العواصف' => $climateImpact->storm_risk,
            'حرائق الغابات' => $climateImpact->wildfire_risk,
        ];
        
        arsort($risks);
        return array_slice($risks, 0, 3, true);
    }

    private function getCriticalVulnerabilities($climateImpact)
    {
        $vulnerabilities = [];
        
        if ($climateImpact->vulnerability_score > 70) {
            $vulnerabilities[] = 'ضعف عام في قدرة التكيف';
        }
        
        if ($climateImpact->adaptation_capacity < 50) {
            $vulnerabilities[] = 'قدرة تكيف منخفضة';
        }
        
        if (!$climateImpact->insurance_coverage) {
            $vulnerabilities[] = 'عدم وجود تغطية تأمينية';
        }
        
        return $vulnerabilities;
    }

    private function getTimeSensitiveRisks($climateImpact)
    {
        return [
            'immediate' => 'مخاطر تتطلب إجراءات فورية',
            'short_term' => 'مخاطر خلال 1-3 سنوات',
            'long_term' => 'مخاطر طويلة الأمد',
        ];
    }

    private function analyzeCascadingEffects($climateImpact)
    {
        return [
            'infrastructure' => 'تأثير على البنية التحتية',
            'health' => 'تأثير على الصحة العامة',
            'economy' => 'تأثير اقتصادي',
            'social' => 'تأثير اجتماعي',
        ];
    }

    private function generateDetailedAdaptationPlan($climateImpact)
    {
        $plan = [];
        
        if ($climateImpact->climate_risk_score > 70) {
            $plan[] = [
                'phase' => 'المرحلة الأولى (عاجلة)',
                'actions' => ['تقييم المخاطر التفصيلي', 'وضع خطة طوارئ', 'تحسين أنظمة الإنذار المبكر'],
                'timeline' => '0-3 أشهر',
                'budget' => 'منخفض إلى متوسط',
                'priority' => 'عاجل',
            ];
        }
        
        if ($climateImpact->adaptation_capacity < 60) {
            $plan[] = [
                'phase' => 'المرحلة الثانية (تحسين)',
                'actions' => ['تركيب أنظمة مقاومة', 'تدريب السكان', 'تحسين البنية التحتية'],
                'timeline' => '3-12 شهر',
                'budget' => 'متوسط إلى مرتفع',
                'priority' => 'مرتفع',
            ];
        }
        
        return $plan;
    }
}
