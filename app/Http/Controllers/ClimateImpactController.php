<?php

namespace App\Http\Controllers;

use App\Models\ClimateImpact;
use App\Models\SmartProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClimateImpactController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_impacts' => ClimateImpact::count(),
            'assessed_properties' => ClimateImpact::distinct('property_id')->count(),
            'average_carbon_footprint' => ClimateImpact::avg('carbon_footprint'),
            'total_energy_consumption' => ClimateImpact::sum('energy_consumption'),
            'impacts_by_level' => $this->getImpactsByLevel(),
            'impact_trends' => $this->getImpactTrends(),
        ];

        $recentImpacts = ClimateImpact::with(['property'])
            ->latest()
            ->take(10)
            ->get();

        $highRiskProperties = $this->getHighRiskProperties();
        $mitigationProgress = $this->getMitigationProgress();

        return view('sustainability.climate-impact-dashboard', compact(
            'stats', 
            'recentImpacts', 
            'highRiskProperties', 
            'mitigationProgress'
        ));
    }

    public function index(Request $request)
    {
        $query = ClimateImpact::with(['property']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('impact_level')) {
            $query->where('impact_level', $request->impact_level);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('assessment_date_from')) {
            $query->whereDate('assessment_date', '>=', $request->assessment_date_from);
        }

        if ($request->filled('assessment_date_to')) {
            $query->whereDate('assessment_date', '<=', $request->assessment_date_to);
        }

        if ($request->filled('carbon_footprint_min')) {
            $query->where('carbon_footprint', '>=', $request->carbon_footprint_min);
        }

        if ($request->filled('carbon_footprint_max')) {
            $query->where('carbon_footprint', '<=', $request->carbon_footprint_max);
        }

        $impacts = $query->latest()->paginate(12);

        $impactLevels = ['low', 'moderate', 'high', 'severe'];
        $statuses = ['assessed', 'monitoring', 'mitigating', 'compliant'];

        return view('sustainability.climate-impact-index', compact(
            'impacts', 
            'impactLevels', 
            'statuses'
        ));
    }

    public function create()
    {
        $properties = SmartProperty::all();

        return view('sustainability.climate-impact-create', compact(
            'properties'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $impactData = $request->validate([
                'property_id' => 'required|exists:smart_properties,id',
                'carbon_footprint' => 'required|numeric|min:0',
                'energy_consumption' => 'required|numeric|min:0',
                'water_usage' => 'required|numeric|min:0',
                'waste_generation' => 'required|numeric|min:0',
                'biodiversity_impact' => 'required|numeric|min:0|max:10',
                'air_quality_impact' => 'required|numeric|min:0|max:10',
                'water_quality_impact' => 'required|numeric|min:0|max:10',
                'soil_impact' => 'required|numeric|min:0|max:10',
                'impact_factors' => 'nullable|array',
                'mitigation_measures' => 'nullable|array',
                'impact_level' => 'required|in:low,moderate,high,severe',
                'climate_risk_assessment' => 'nullable|array',
                'adaptation_strategies' => 'nullable|array',
                'assessment_date' => 'required|date',
                'next_assessment_date' => 'nullable|date|after:assessment_date',
                'status' => 'required|in:assessed,monitoring,mitigating,compliant',
            ]);

            $impactData['created_by'] = auth()->id();
            $impactData['impact_factors'] = $this->generateImpactFactors($request);
            $impactData['mitigation_measures'] = $this->generateMitigationMeasures($request);
            $impactData['climate_risk_assessment'] = $this->generateClimateRiskAssessment($request);
            $impactData['adaptation_strategies'] = $this->generateAdaptationStrategies($request);

            $impact = ClimateImpact::create($impactData);

            DB::commit();

            return redirect()
                ->route('climate-impact.show', $impact)
                ->with('success', 'تم إضافة تقييم التأثير المناخي بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة التقييم: ' . $e->getMessage());
        }
    }

    public function show(ClimateImpact $impact)
    {
        $impact->load(['property']);
        $impactDetails = $this->getImpactDetails($impact);
        $riskAnalysis = $this->getRiskAnalysis($impact);
        $mitigationPlan = $this->getMitigationPlan($impact);

        return view('sustainability.climate-impact-show', compact(
            'impact', 
            'impactDetails', 
            'riskAnalysis', 
            'mitigationPlan'
        ));
    }

    public function edit(ClimateImpact $impact)
    {
        $properties = SmartProperty::all();

        return view('sustainability.climate-impact-edit', compact(
            'impact', 
            'properties'
        ));
    }

    public function update(Request $request, ClimateImpact $impact)
    {
        DB::beginTransaction();
        try {
            $impactData = $request->validate([
                'carbon_footprint' => 'required|numeric|min:0',
                'energy_consumption' => 'required|numeric|min:0',
                'water_usage' => 'required|numeric|min:0',
                'waste_generation' => 'required|numeric|min:0',
                'biodiversity_impact' => 'required|numeric|min:0|max:10',
                'air_quality_impact' => 'required|numeric|min:0|max:10',
                'water_quality_impact' => 'required|numeric|min:0|max:10',
                'soil_impact' => 'required|numeric|min:0|max:10',
                'impact_factors' => 'nullable|array',
                'mitigation_measures' => 'nullable|array',
                'impact_level' => 'required|in:low,moderate,high,severe',
                'climate_risk_assessment' => 'nullable|array',
                'adaptation_strategies' => 'nullable|array',
                'assessment_date' => 'required|date',
                'next_assessment_date' => 'nullable|date|after:assessment_date',
                'status' => 'required|in:assessed,monitoring,mitigating,compliant',
            ]);

            $impactData['updated_by'] = auth()->id();
            $impactData['impact_factors'] = $this->generateImpactFactors($request);
            $impactData['mitigation_measures'] = $this->generateMitigationMeasures($request);
            $impactData['climate_risk_assessment'] = $this->generateClimateRiskAssessment($request);
            $impactData['adaptation_strategies'] = $this->generateAdaptationStrategies($request);

            $impact->update($impactData);

            DB::commit();

            return redirect()
                ->route('climate-impact.show', $impact)
                ->with('success', 'تم تحديث تقييم التأثير المناخي بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث التقييم: ' . $e->getMessage());
        }
    }

    public function destroy(ClimateImpact $impact)
    {
        try {
            $impact->delete();

            return redirect()
                ->route('climate-impact.index')
                ->with('success', 'تم حذف تقييم التأثير المناخي بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف التقييم: ' . $e->getMessage());
        }
    }

    public function assessImpact(Request $request)
    {
        $propertyId = $request->input('property_id');
        $assessmentData = $request->input('assessment_data', []);

        $assessment = $this->performClimateImpactAssessment($propertyId, $assessmentData);

        return response()->json([
            'success' => true,
            'assessment' => $assessment
        ]);
    }

    public function calculateCarbonIntensity(ClimateImpact $impact)
    {
        $intensity = $this->calculateCarbonIntensity($impact);

        return response()->json([
            'success' => true,
            'carbon_intensity' => $intensity
        ]);
    }

    public function getMitigationRecommendations(ClimateImpact $impact)
    {
        $recommendations = $this->generateMitigationRecommendations($impact);

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations
        ]);
    }

    public function generateReport(ClimateImpact $impact)
    {
        try {
            $reportData = $this->generateClimateReport($impact);
            
            return response()->json([
                'success' => true,
                'report' => $reportData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generateImpactFactors($request)
    {
        return [
            'energy_sources' => $request->input('energy_sources', []),
            'transportation_patterns' => $request->input('transportation_patterns', []),
            'waste_management' => $request->input('waste_management', []),
            'water_management' => $request->input('water_management', []),
            'land_use' => $request->input('land_use', []),
            'material_sourcing' => $request->input('material_sourcing', []),
            'operational_practices' => $request->input('operational_practices', []),
        ];
    }

    private function generateMitigationMeasures($request)
    {
        return [
            'energy_efficiency' => $request->input('energy_efficiency_measures', []),
            'renewable_energy' => $request->input('renewable_energy_measures', []),
            'carbon_offsetting' => $request->input('carbon_offsetting_measures', []),
            'waste_reduction' => $request->input('waste_reduction_measures', []),
            'water_conservation' => $request->input('water_conservation_measures', []),
            'sustainable_transport' => $request->input('sustainable_transport_measures', []),
            'biodiversity_protection' => $request->input('biodiversity_protection_measures', []),
        ];
    }

    private function generateClimateRiskAssessment($request)
    {
        return [
            'temperature_risks' => $request->input('temperature_risks', []),
            'precipitation_risks' => $request->input('precipitation_risks', []),
            'sea_level_risks' => $request->input('sea_level_risks', []),
            'extreme_weather_risks' => $request->input('extreme_weather_risks', []),
            'vulnerability_assessment' => $request->input('vulnerability_assessment', []),
            'risk_mitigation' => $request->input('risk_mitigation', []),
            'emergency_preparedness' => $request->input('emergency_preparedness', []),
        ];
    }

    private function generateAdaptationStrategies($request)
    {
        return [
            'infrastructure_adaptation' => $request->input('infrastructure_adaptation', []),
            'operational_adaptation' => $request->input('operational_adaptation', []),
            'policy_adaptation' => $request->input('policy_adaptation', []),
            'community_adaptation' => $request->input('community_adaptation', []),
            'technological_adaptation' => $request->input('technological_adaptation', []),
            'financial_adaptation' => $request->input('financial_adaptation', []),
        ];
    }

    private function getImpactsByLevel()
    {
        return ClimateImpact::select('impact_level', DB::raw('COUNT(*) as count'))
            ->groupBy('impact_level')
            ->get();
    }

    private function getImpactTrends()
    {
        return ClimateImpact::selectRaw('MONTH(assessment_date) as month, AVG(carbon_footprint) as avg_carbon')
            ->whereYear('assessment_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getHighRiskProperties()
    {
        return ClimateImpact::with(['property'])
            ->where('impact_level', 'high')
            ->orWhere('impact_level', 'severe')
            ->orderBy('carbon_footprint', 'desc')
            ->take(10)
            ->get();
    }

    private function getMitigationProgress()
    {
        return ClimateImpact::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();
    }

    private function getImpactDetails($impact)
    {
        return [
            'overall_impact_score' => $impact->getOverallImpactScore(),
            'impact_grade' => $impact->getImpactGrade(),
            'carbon_intensity' => $impact->getCarbonIntensity(),
            'mitigation_priority' => $impact->getMitigationPriority(),
            'has_high_risk_factors' => $impact->hasHighRiskFactors(),
            'days_since_assessment' => $impact->assessment_date->diffInDays(now()),
        ];
    }

    private function getRiskAnalysis($impact)
    {
        $riskAssessment = $impact->climate_risk_assessment ?? [];
        
        return [
            'overall_risk_level' => $this->calculateOverallRiskLevel($riskAssessment),
            'primary_risks' => $this->identifyPrimaryRisks($riskAssessment),
            'risk_factors' => $riskAssessment,
            'vulnerability_score' => $this->calculateVulnerabilityScore($impact),
            'adaptation_readiness' => $this->assessAdaptationReadiness($impact),
        ];
    }

    private function getMitigationPlan($impact)
    {
        return [
            'current_measures' => $impact->mitigation_measures,
            'effectiveness_rating' => $this->assessMitigationEffectiveness($impact),
            'implementation_status' => $this->getImplementationStatus($impact),
            'cost_benefit_analysis' => $this->analyzeCostBenefit($impact),
            'timeline' => $this->estimateImplementationTimeline($impact),
            'success_metrics' => $this->defineSuccessMetrics($impact),
        ];
    }

    private function calculateOverallRiskLevel($riskAssessment)
    {
        $riskFactors = [
            'temperature_risks' => $riskAssessment['temperature_risks'] ?? [],
            'precipitation_risks' => $riskAssessment['precipitation_risks'] ?? [],
            'sea_level_risks' => $riskAssessment['sea_level_risks'] ?? [],
            'extreme_weather_risks' => $riskAssessment['extreme_weather_risks'] ?? [],
        ];

        $totalRisks = 0;
        foreach ($riskFactors as $riskType => $risks) {
            $totalRisks += count($risks);
        }

        if ($totalRisks >= 10) return 'very_high';
        if ($totalRisks >= 7) return 'high';
        if ($totalRisks >= 4) return 'moderate';
        return 'low';
    }

    private function identifyPrimaryRisks($riskAssessment)
    {
        $risks = [];
        
        if (!empty($riskAssessment['temperature_risks'])) {
            $risks[] = 'Temperature-related risks';
        }
        
        if (!empty($riskAssessment['precipitation_risks'])) {
            $risks[] = 'Precipitation-related risks';
        }
        
        if (!empty($riskAssessment['sea_level_risks'])) {
            $risks[] = 'Sea level rise risks';
        }
        
        if (!empty($riskAssessment['extreme_weather_risks'])) {
            $risks[] = 'Extreme weather risks';
        }

        return $risks;
    }

    private function calculateVulnerabilityScore($impact)
    {
        $baseScore = 50;
        
        // Calculate vulnerability based on impact levels
        if ($impact->biodiversity_impact > 5) $baseScore += 10;
        if ($impact->air_quality_impact > 5) $baseScore += 10;
        if ($impact->water_quality_impact > 5) $baseScore += 10;
        if ($impact->soil_impact > 5) $baseScore += 10;
        
        return min(100, $baseScore);
    }

    private function assessAdaptationReadiness($impact)
    {
        $strategies = $impact->adaptation_strategies ?? [];
        $strategyCount = count($strategies);
        
        if ($strategyCount >= 6) return 'high';
        if ($strategyCount >= 4) return 'moderate';
        if ($strategyCount >= 2) return 'low';
        return 'very_low';
    }

    private function assessMitigationEffectiveness($impact)
    {
        $measures = $impact->mitigation_measures ?? [];
        $measureCount = count($measures);
        
        if ($measureCount >= 8) return 'highly_effective';
        if ($measureCount >= 6) return 'effective';
        if ($measureCount >= 4) return 'moderately_effective';
        return 'limited_effectiveness';
    }

    private function getImplementationStatus($impact)
    {
        return [
            'planned_measures' => count($impact->mitigation_measures['planned'] ?? []),
            'implemented_measures' => count($impact->mitigation_measures['implemented'] ?? []),
            'completion_percentage' => $this->calculateCompletionPercentage($impact),
            'next_steps' => $this->identifyNextSteps($impact),
        ];
    }

    private function analyzeCostBenefit($impact)
    {
        return [
            'implementation_cost' => $this->estimateImplementationCost($impact),
            'annual_savings' => $this->estimateAnnualSavings($impact),
            'payback_period' => $this->calculatePaybackPeriod($impact),
            'roi_percentage' => $this->calculateROI($impact),
            'benefit_categories' => $this->categorizeBenefits($impact),
        ];
    }

    private function estimateImplementationTimeline($impact)
    {
        $complexity = $this->assessImplementationComplexity($impact);
        
        if ($complexity === 'low') return '3-6 months';
        if ($complexity === 'moderate') return '6-12 months';
        if ($complexity === 'high') return '1-2 years';
        return '2+ years';
    }

    private function defineSuccessMetrics($impact)
    {
        return [
            'carbon_reduction_target' => $impact->carbon_footprint * 0.3, // 30% reduction
            'energy_efficiency_target' => $impact->energy_consumption * 0.25, // 25% reduction
            'water_conservation_target' => $impact->water_usage * 0.2, // 20% reduction
            'waste_reduction_target' => $impact->waste_generation * 0.4, // 40% reduction
            'biodiversity_improvement_target' => 'increase by 20%',
            'compliance_deadline' => $impact->next_assessment_date?->toDateString(),
        ];
    }

    private function calculateCompletionPercentage($impact)
    {
        $planned = count($impact->mitigation_measures['planned'] ?? []);
        $implemented = count($impact->mitigation_measures['implemented'] ?? []);
        
        return $planned > 0 ? ($implemented / $planned) * 100 : 0;
    }

    private function identifyNextSteps($impact)
    {
        $steps = [];
        $measures = $impact->mitigation_measures ?? [];
        
        if (empty($measures['energy_efficiency'])) {
            $steps[] = 'Implement energy efficiency measures';
        }
        
        if (empty($measures['renewable_energy'])) {
            $steps[] = 'Install renewable energy systems';
        }
        
        if (empty($measures['carbon_offsetting'])) {
            $steps[] = 'Establish carbon offsetting program';
        }
        
        return $steps;
    }

    private function assessImplementationComplexity($impact)
    {
        $complexityScore = 0;
        $measures = $impact->mitigation_measures ?? [];
        
        foreach ($measures as $category => $categoryMeasures) {
            $complexityScore += count($categoryMeasures);
        }
        
        if ($complexityScore <= 5) return 'low';
        if ($complexityScore <= 10) return 'moderate';
        return 'high';
    }

    private function estimateImplementationCost($impact)
    {
        $baseCost = 10000;
        $complexityMultiplier = $this->assessImplementationComplexity($impact) === 'high' ? 2 : 1;
        
        return $baseCost * $complexityMultiplier;
    }

    private function estimateAnnualSavings($impact)
    {
        $energySavings = $impact->energy_consumption * 0.15; // 15% energy savings
        $waterSavings = $impact->water_usage * 0.1; // 10% water savings
        $wasteSavings = $impact->waste_generation * 0.2; // 20% waste reduction savings
        
        return ($energySavings * 0.15) + ($waterSavings * 0.002) + ($wasteSavings * 0.05);
    }

    private function calculatePaybackPeriod($impact)
    {
        $annualSavings = $this->estimateAnnualSavings($impact);
        $implementationCost = $this->estimateImplementationCost($impact);
        
        return $annualSavings > 0 ? $implementationCost / $annualSavings : 0;
    }

    private function calculateROI($impact)
    {
        $annualSavings = $this->estimateAnnualSavings($impact);
        $implementationCost = $this->estimateImplementationCost($impact);
        
        return $implementationCost > 0 ? ($annualSavings / $implementationCost) * 100 : 0;
    }

    private function categorizeBenefits($impact)
    {
        return [
            'environmental_benefits' => [
                'carbon_reduction' => $impact->carbon_footprint * 0.3,
                'energy_conservation' => $impact->energy_consumption * 0.15,
                'water_conservation' => $impact->water_usage * 0.1,
            ],
            'economic_benefits' => [
                'cost_savings' => $this->estimateAnnualSavings($impact),
                'property_value_increase' => '5-10%',
                'compliance_avoidance' => 'reduced regulatory costs',
            ],
            'social_benefits' => [
                'improved_health' => 'better air and water quality',
                'community_resilience' => 'enhanced climate adaptation',
                'job_creation' => 'green jobs in implementation',
            ],
        ];
    }

    private function performClimateImpactAssessment($propertyId, $assessmentData)
    {
        $baseImpact = 5; // Base impact score
        
        // Calculate impact based on various factors
        if ($assessmentData['high_energy_consumption'] ?? false) $baseImpact += 2;
        if ($assessmentData['high_water_usage'] ?? false) $baseImpact += 1;
        if ($assessmentData['high_waste_generation'] ?? false) $baseImpact += 1;
        if ($assessmentData['poor_biodiversity'] ?? false) $baseImpact += 1;
        if ($assessmentData['air_quality_issues'] ?? false) $baseImpact += 1;
        if ($assessmentData['water_quality_issues'] ?? false) $baseImpact += 1;
        if ($assessmentData['soil_degradation'] ?? false) $baseImpact += 1;

        return [
            'property_id' => $propertyId,
            'overall_impact_score' => min(10, $baseImpact),
            'impact_level' => $this->determineImpactLevel($baseImpact),
            'assessment_date' => now()->toDateString(),
        ];
    }

    private function determineImpactLevel($score)
    {
        if ($score >= 8) return 'severe';
        if ($score >= 6) return 'high';
        if ($score >= 4) return 'moderate';
        return 'low';
    }

    private function calculateCarbonIntensityForReport($impact)
    {
        return $impact->getCarbonIntensity();
    }

    private function generateMitigationRecommendations($impact)
    {
        $recommendations = [];
        
        if ($impact->carbon_footprint > 100) {
            $recommendations[] = 'Implement carbon reduction measures';
            $recommendations[] = 'Invest in renewable energy';
        }
        
        if ($impact->energy_consumption > 1000) {
            $recommendations[] = 'Improve energy efficiency';
            $recommendations[] = 'Upgrade to energy-efficient systems';
        }
        
        if ($impact->water_usage > 500) {
            $recommendations[] = 'Implement water conservation measures';
            $recommendations[] = 'Install water recycling systems';
        }
        
        if ($impact->waste_generation > 100) {
            $recommendations[] = 'Implement waste reduction programs';
            $recommendations[] = 'Establish recycling initiatives';
        }
        
        if ($impact->biodiversity_impact > 5) {
            $recommendations[] = 'Implement biodiversity protection measures';
            $recommendations[] = 'Create green spaces and habitats';
        }

        return $recommendations;
    }

    private function generateClimateReport($impact)
    {
        return [
            'report_id' => uniqid('climate_report_'),
            'property_name' => $impact->property->property_name,
            'assessment_date' => $impact->assessment_date->toDateString(),
            'overall_impact_score' => $impact->getOverallImpactScore(),
            'impact_level' => $impact->impact_level,
            'carbon_footprint' => $impact->carbon_footprint,
            'energy_consumption' => $impact->energy_consumption,
            'water_usage' => $impact->water_usage,
            'waste_generation' => $impact->waste_generation,
            'biodiversity_impact' => $impact->biodiversity_impact,
            'mitigation_measures' => $impact->mitigation_measures,
            'adaptation_strategies' => $impact->adaptation_strategies,
            'generated_at' => now()->toDateTimeString(),
        ];
    }
}
