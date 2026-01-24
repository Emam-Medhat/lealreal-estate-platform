<?php

namespace App\Http\Controllers;

use App\Models\GreenBuilding;
use App\Models\SmartProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GreenBuildingController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_buildings' => GreenBuilding::count(),
            'certified_buildings' => GreenBuilding::where('status', 'certified')->count(),
            'average_rating' => GreenBuilding::selectRaw('AVG(energy_efficiency_rating + water_efficiency_rating + waste_reduction_rating + indoor_air_quality_rating + sustainable_materials_rating) / 5 as avg_rating')->value('avg_rating'),
            'buildings_by_level' => $this->getBuildingsByLevel(),
            'certification_trends' => $this->getCertificationTrends(),
        ];

        $recentBuildings = GreenBuilding::with(['property'])
            ->latest()
            ->take(10)
            ->get();

        $topPerformers = $this->getTopPerformers();
        $expiringSoon = $this->getExpiringSoon();

        return view('sustainability.green-building-dashboard', compact(
            'stats', 
            'recentBuildings', 
            'topPerformers', 
            'expiringSoon'
        ));
    }

    public function index(Request $request)
    {
        $query = GreenBuilding::with(['property']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('certification_level')) {
            $query->where('certification_level', $request->certification_level);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('certification_date_from')) {
            $query->whereDate('certification_date', '>=', $request->certification_date_from);
        }

        if ($request->filled('certification_date_to')) {
            $query->whereDate('certification_date', '<=', $request->certification_date_to);
        }

        if ($request->filled('overall_rating_min')) {
            $query->havingRaw('(energy_efficiency_rating + water_efficiency_rating + waste_reduction_rating + indoor_air_quality_rating + sustainable_materials_rating) / 5 >= ?', [$request->overall_rating_min]);
        }

        if ($request->filled('overall_rating_max')) {
            $query->havingRaw('(energy_efficiency_rating + water_efficiency_rating + waste_reduction_rating + indoor_air_quality_rating + sustainable_materials_rating) / 5 <= ?', [$request->overall_rating_max]);
        }

        $buildings = $query->latest()->paginate(12);

        $certificationLevels = ['certified', 'silver', 'gold', 'platinum'];
        $statuses = ['pending', 'certified', 'expired', 'suspended', 'revoked'];

        return view('sustainability.green-building-index', compact(
            'buildings', 
            'certificationLevels', 
            'statuses'
        ));
    }

    public function create()
    {
        $properties = SmartProperty::all();

        return view('sustainability.green-building-create', compact(
            'properties'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $buildingData = $request->validate([
                'property_id' => 'required|exists:smart_properties,id',
                'building_name' => 'required|string|max:255',
                'certification_level' => 'required|in:certified,silver,gold,platinum',
                'certification_body' => 'required|string|max:255',
                'certificate_number' => 'required|string|max:255|unique:green_buildings',
                'certification_date' => 'required|date',
                'expiry_date' => 'nullable|date|after:certification_date',
                'green_features' => 'nullable|array',
                'energy_efficiency_rating' => 'required|numeric|min:0|max:100',
                'water_efficiency_rating' => 'required|numeric|min:0|max:100',
                'waste_reduction_rating' => 'required|numeric|min:0|max:100',
                'indoor_air_quality_rating' => 'required|numeric|min:0|max:100',
                'sustainable_materials_rating' => 'required|numeric|min:0|max:100',
                'building_design_features' => 'nullable|array',
                'innovation_features' => 'nullable|array',
                'regional_priority' => 'nullable|array',
                'status' => 'required|in:pending,certified,expired,suspended,revoked',
            ]);

            $buildingData['created_by'] = auth()->id();
            $buildingData['green_features'] = $this->generateGreenFeatures($request);
            $buildingData['building_design_features'] = $this->generateBuildingDesignFeatures($request);
            $buildingData['innovation_features'] = $this->generateInnovationFeatures($request);
            $buildingData['regional_priority'] = $this->generateRegionalPriority($request);

            $building = GreenBuilding::create($buildingData);

            DB::commit();

            return redirect()
                ->route('green-building.show', $building)
                ->with('success', 'تم إضافة شهادة المبنى الأخضر بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة الشهادة: ' . $e->getMessage());
        }
    }

    public function show(GreenBuilding $building)
    {
        $building->load(['property']);
        $buildingDetails = $this->getBuildingDetails($building);
        $certificationAnalysis = $this->getCertificationAnalysis($building);
        $performanceMetrics = $this->getPerformanceMetrics($building);

        return view('sustainability.green-building-show', compact(
            'building', 
            'buildingDetails', 
            'certificationAnalysis', 
            'performanceMetrics'
        ));
    }

    public function edit(GreenBuilding $building)
    {
        $properties = SmartProperty::all();

        return view('sustainability.green-building-edit', compact(
            'building', 
            'properties'
        ));
    }

    public function update(Request $request, GreenBuilding $building)
    {
        DB::beginTransaction();
        try {
            $buildingData = $request->validate([
                'building_name' => 'required|string|max:255',
                'certification_level' => 'required|in:certified,silver,gold,platinum',
                'certification_body' => 'required|string|max:255',
                'certificate_number' => 'required|string|max:255|unique:green_buildings,certificate_number,' . $building->id,
                'certification_date' => 'required|date',
                'expiry_date' => 'nullable|date|after:certification_date',
                'green_features' => 'nullable|array',
                'energy_efficiency_rating' => 'required|numeric|min:0|max:100',
                'water_efficiency_rating' => 'required|numeric|min:0|max:100',
                'waste_reduction_rating' => 'required|numeric|min:0|max:100',
                'indoor_air_quality_rating' => 'required|numeric|min:0|max:100',
                'sustainable_materials_rating' => 'required|numeric|min:0|max:100',
                'building_design_features' => 'nullable|array',
                'innovation_features' => 'nullable|array',
                'regional_priority' => 'nullable|array',
                'status' => 'required|in:pending,certified,expired,suspended,revoked',
            ]);

            $buildingData['updated_by'] = auth()->id();
            $buildingData['green_features'] = $this->generateGreenFeatures($request);
            $buildingData['building_design_features'] = $this->generateBuildingDesignFeatures($request);
            $buildingData['innovation_features'] = $this->generateInnovationFeatures($request);
            $buildingData['regional_priority'] = $this->generateRegionalPriority($request);

            $building->update($buildingData);

            DB::commit();

            return redirect()
                ->route('green-building.show', $building)
                ->with('success', 'تم تحديث شهادة المبنى الأخضر بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الشهادة: ' . $e->getMessage());
        }
    }

    public function destroy(GreenBuilding $building)
    {
        try {
            $building->delete();

            return redirect()
                ->route('green-building.index')
                ->with('success', 'تم حذف شهادة المبنى الأخضر بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف الشهادة: ' . $e->getMessage());
        }
    }

    public function assessBuilding(Request $request)
    {
        $propertyId = $request->input('property_id');
        $assessmentData = $request->input('assessment_data', []);

        $assessment = $this->performBuildingAssessment($propertyId, $assessmentData);

        return response()->json([
            'success' => true,
            'assessment' => $assessment
        ]);
    }

    public function calculateRating(GreenBuilding $building)
    {
        $rating = $this->calculateBuildingRating($building);

        return response()->json([
            'success' => true,
            'rating' => $rating
        ]);
    }

    public function getCertificationPath(GreenBuilding $building)
    {
        $path = $this->generateCertificationPath($building);

        return response()->json([
            'success' => true,
            'certification_path' => $path
        ]);
    }

    public function generateCertificate(GreenBuilding $building)
    {
        try {
            $certificateData = $this->generateBuildingCertificate($building);
            
            return response()->json([
                'success' => true,
                'certificate' => $certificateData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generateGreenFeatures($request)
    {
        return [
            'energy_efficiency' => $request->input('energy_efficiency_features', []),
            'water_conservation' => $request->input('water_conservation_features', []),
            'waste_management' => $request->input('waste_management_features', []),
            'sustainable_materials' => $request->input('sustainable_materials_features', []),
            'indoor_environmental_quality' => $request->input('indoor_environmental_quality_features', []),
            'site_sustainability' => $request->input('site_sustainability_features', []),
            'innovation' => $request->input('innovation_features', []),
        ];
    }

    private function generateBuildingDesignFeatures($request)
    {
        return [
            'passive_design' => $request->input('passive_design', false),
            'natural_lighting' => $request->input('natural_lighting', false),
            'natural_ventilation' => $request->input('natural_ventilation', false),
            'thermal_comfort' => $request->input('thermal_comfort', false),
            'acoustic_comfort' => $request->input('acoustic_comfort', false),
            'daylighting' => $request->input('daylighting', false),
            'views' => $request->input('views', false),
            'site_integration' => $request->input('site_integration', false),
        ];
    }

    private function generateInnovationFeatures($request)
    {
        return [
            'innovative_technologies' => $request->input('innovative_technologies', []),
            'unique_solutions' => $request->input('unique_solutions', []),
            'research_collaboration' => $request->input('research_collaboration', []),
            'educational_components' => $request->input('educational_components', []),
            'community_benefits' => $request->input('community_benefits', []),
            'performance_monitoring' => $request->input('performance_monitoring', []),
        ];
    }

    private function generateRegionalPriority($request)
    {
        return [
            'local_materials' => $request->input('local_materials', false),
            'local_labor' => $request->input('local_labor', false),
            'climate_adaptation' => $request->input('climate_adaptation', false),
            'cultural_sensitivity' => $request->input('cultural_sensitivity', false),
            'community_engagement' => $request->input('community_engagement', false),
            'regional_certifications' => $request->input('regional_certifications', []),
        ];
    }

    private function getBuildingsByLevel()
    {
        return GreenBuilding::select('certification_level', DB::raw('COUNT(*) as count'))
            ->groupBy('certification_level')
            ->get();
    }

    private function getCertificationTrends()
    {
        return GreenBuilding::selectRaw('MONTH(certification_date) as month, COUNT(*) as certified_count')
            ->whereYear('certification_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getTopPerformers()
    {
        return GreenBuilding::with(['property'])
            ->select('property_id', DB::raw('AVG(energy_efficiency_rating + water_efficiency_rating + waste_reduction_rating + indoor_air_quality_rating + sustainable_materials_rating) / 5 as avg_rating'))
            ->groupBy('property_id')
            ->orderBy('avg_rating', 'desc')
            ->take(5)
            ->get();
    }

    private function getExpiringSoon()
    {
        return GreenBuilding::with(['property'])
            ->where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addDays(90))
            ->where('status', 'certified')
            ->orderBy('expiry_date', 'asc')
            ->get();
    }

    private function getBuildingDetails($building)
    {
        return [
            'overall_rating' => $building->getOverallRating(),
            'rating_grade' => $building->getRatingGrade(),
            'green_features_count' => $building->getGreenFeaturesCount(),
            'has_innovation_features' => $building->hasInnovationFeatures(),
            'days_until_expiry' => $building->getDaysUntilExpiry(),
            'is_expiring' => $building->isExpiring(),
            'is_expired' => $building->isExpired(),
        ];
    }

    private function getCertificationAnalysis($building)
    {
        return [
            'certification_level' => $building->certification_level,
            'certification_body' => $building->certification_body,
            'certificate_number' => $building->certificate_number,
            'certification_date' => $building->certification_date->toDateString(),
            'expiry_date' => $building->expiry_date?->toDateString(),
            'certification_age' => $building->certification_date->diffInDays(now()),
            'renewal_status' => $this->getRenewalStatus($building),
        ];
    }

    private function getPerformanceMetrics($building)
    {
        return [
            'energy_performance' => $building->energy_efficiency_rating,
            'water_performance' => $building->water_efficiency_rating,
            'waste_performance' => $building->waste_reduction_rating,
            'air_quality_performance' => $building->indoor_air_quality_rating,
            'materials_performance' => $building->sustainable_materials_rating,
            'overall_performance' => $building->getOverallRating(),
            'performance_trend' => $this->getPerformanceTrend($building),
            'benchmark_comparison' => $this->getBenchmarkComparison($building),
        ];
    }

    private function getRenewalStatus($building)
    {
        if (!$building->expiry_date) {
            return [
                'eligible' => false,
                'reason' => 'No expiry date set',
                'next_renewal' => null,
            ];
        }

        $daysUntilExpiry = $building->getDaysUntilExpiry();
        
        return [
            'eligible' => $daysUntilExpiry <= 90,
            'reason' => $daysUntilExpiry <= 90 ? 'Renewal window open' : 'Not yet eligible',
            'next_renewal' => $building->expiry_date->toDateString(),
            'days_until_expiry' => $daysUntilExpiry,
        ];
    }

    private function getPerformanceTrend($building)
    {
        // This would typically compare with historical data
        return 'stable';
    }

    private function getBenchmarkComparison($building)
    {
        $averageRating = GreenBuilding::selectRaw('AVG(energy_efficiency_rating + water_efficiency_rating + waste_reduction_rating + indoor_air_quality_rating + sustainable_materials_rating) / 5 as avg_rating')->value('avg_rating');
        
        return [
            'building_rating' => $building->getOverallRating(),
            'industry_average' => $averageRating,
            'percentile' => $this->calculatePercentile($building->getOverallRating()),
            'performance_rating' => $building->getOverallRating() > $averageRating ? 'Above Average' : 'Below Average',
        ];
    }

    private function calculatePercentile($rating)
    {
        $total = GreenBuilding::count();
        $better = GreenBuilding::selectRaw('COUNT(*) as count')
            ->havingRaw('(energy_efficiency_rating + water_efficiency_rating + waste_reduction_rating + indoor_air_quality_rating + sustainable_materials_rating) / 5 > ?', [$rating])
            ->value('count');
        
        return (($total - $better) / $total) * 100;
    }

    private function performBuildingAssessment($propertyId, $assessmentData)
    {
        $baseScore = 50;
        
        // Calculate scores based on various green building criteria
        if ($assessmentData['energy_efficiency'] ?? false) $baseScore += 10;
        if ($assessmentData['water_conservation'] ?? false) $baseScore += 10;
        if ($assessmentData['waste_management'] ?? false) $baseScore += 10;
        if ($assessmentData['sustainable_materials'] ?? false) $baseScore += 10;
        if ($assessmentData['indoor_air_quality'] ?? false) $baseScore += 10;

        return [
            'property_id' => $propertyId,
            'overall_rating' => min(100, $baseScore),
            'certification_level' => $this->determineCertificationLevel($baseScore),
            'assessment_date' => now()->toDateString(),
        ];
    }

    private function determineCertificationLevel($score)
    {
        if ($score >= 85) return 'platinum';
        if ($score >= 75) return 'gold';
        if ($score >= 65) return 'silver';
        return 'certified';
    }

    private function calculateBuildingRating($building)
    {
        return [
            'overall_rating' => $building->getOverallRating(),
            'rating_grade' => $building->getRatingGrade(),
            'individual_ratings' => [
                'energy' => $building->energy_efficiency_rating,
                'water' => $building->water_efficiency_rating,
                'waste' => $building->waste_reduction_rating,
                'air_quality' => $building->indoor_air_quality_rating,
                'materials' => $building->sustainable_materials_rating,
            ],
            'strengths' => $this->getRatingStrengths($building),
            'weaknesses' => $this->getRatingWeaknesses($building),
        ];
    }

    private function getRatingStrengths($building)
    {
        $ratings = [
            'energy' => $building->energy_efficiency_rating,
            'water' => $building->water_efficiency_rating,
            'waste' => $building->waste_reduction_rating,
            'air_quality' => $building->indoor_air_quality_rating,
            'materials' => $building->sustainable_materials_rating,
        ];

        $strengths = [];
        foreach ($ratings as $category => $rating) {
            if ($rating >= 80) {
                $strengths[] = $category;
            }
        }

        return $strengths;
    }

    private function getRatingWeaknesses($building)
    {
        $ratings = [
            'energy' => $building->energy_efficiency_rating,
            'water' => $building->water_efficiency_rating,
            'waste' => $building->waste_reduction_rating,
            'air_quality' => $building->indoor_air_quality_rating,
            'materials' => $building->sustainable_materials_rating,
        ];

        $weaknesses = [];
        foreach ($ratings as $category => $rating) {
            if ($rating < 60) {
                $weaknesses[] = $category;
            }
        }

        return $weaknesses;
    }

    private function generateCertificationPath($building)
    {
        $currentLevel = $building->certification_level;
        $nextLevel = $this->getNextCertificationLevel($currentLevel);
        
        return [
            'current_level' => $currentLevel,
            'next_level' => $nextLevel,
            'requirements_met' => $this->getRequirementsMet($building),
            'requirements_missing' => $this->getRequirementsMissing($building, $nextLevel),
            'estimated_cost' => $this->estimateCertificationCost($building, $nextLevel),
            'timeline' => $this->estimateCertificationTimeline($building, $nextLevel),
        ];
    }

    private function getNextCertificationLevel($currentLevel)
    {
        $levels = ['certified', 'silver', 'gold', 'platinum'];
        $currentIndex = array_search($currentLevel, $levels);
        
        return $levels[min($currentIndex + 1, count($levels) - 1)];
    }

    private function getRequirementsMet($building)
    {
        return [
            'energy_efficiency' => $building->energy_efficiency_rating >= 70,
            'water_efficiency' => $building->water_efficiency_rating >= 70,
            'waste_reduction' => $building->waste_reduction_rating >= 70,
            'indoor_air_quality' => $building->indoor_air_quality_rating >= 70,
            'sustainable_materials' => $building->sustainable_materials_rating >= 70,
        ];
    }

    private function getRequirementsMissing($building, $targetLevel)
    {
        $requirements = [];
        $targetScore = match($targetLevel) {
            'silver' => 75,
            'gold' => 85,
            'platinum' => 95,
            default => 65,
        };

        if ($building->energy_efficiency_rating < $targetScore) {
            $requirements[] = 'Improve energy efficiency to ' . $targetScore;
        }

        if ($building->water_efficiency_rating < $targetScore) {
            $requirements[] = 'Improve water efficiency to ' . $targetScore;
        }

        if ($building->waste_reduction_rating < $targetScore) {
            $requirements[] = 'Improve waste reduction to ' . $targetScore;
        }

        if ($building->indoor_air_quality_rating < $targetScore) {
            $requirements[] = 'Improve indoor air quality to ' . $targetScore;
        }

        if ($building->sustainable_materials_rating < $targetScore) {
            $requirements[] = 'Improve sustainable materials to ' . $targetScore;
        }

        return $requirements;
    }

    private function estimateCertificationCost($building, $targetLevel)
    {
        $baseCost = 5000;
        $levelMultiplier = match($targetLevel) {
            'silver' => 1.5,
            'gold' => 2,
            'platinum' => 2.5,
            default => 1,
        };

        return $baseCost * $levelMultiplier;
    }

    private function estimateCertificationTimeline($building, $targetLevel)
    {
        $currentRating = $building->getOverallRating();
        $targetRating = match($targetLevel) {
            'silver' => 75,
            'gold' => 85,
            'platinum' => 95,
            default => 65,
        };

        $improvementNeeded = $targetRating - $currentRating;
        
        if ($improvementNeeded <= 10) return '3-6 months';
        if ($improvementNeeded <= 20) return '6-12 months';
        return '1-2 years';
    }

    private function generateBuildingCertificate($building)
    {
        return [
            'certificate_id' => uniqid('green_cert_'),
            'building_name' => $building->building_name,
            'property_name' => $building->property->property_name,
            'certification_level' => $building->certification_level,
            'certification_body' => $building->certification_body,
            'certificate_number' => $building->certificate_number,
            'certification_date' => $building->certification_date->toDateString(),
            'expiry_date' => $building->expiry_date?->toDateString(),
            'overall_rating' => $building->getOverallRating(),
            'rating_grade' => $building->getRatingGrade(),
            'issued_by' => auth()->user()->name,
            'verification_code' => strtoupper(uniqid('GREEN_')),
            'generated_at' => now()->toDateTimeString(),
        ];
    }
}
