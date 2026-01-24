<?php

namespace App\Http\Controllers\Sustainability;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Sustainability\PropertySustainability;
use App\Models\Sustainability\EcoScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EcoScoreController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_sustainability');
    }

    public function index()
    {
        $ecoScores = EcoScore::with(['propertySustainability.property'])
            ->when(Auth::user()->hasRole('agent'), function($query) {
                $query->whereHas('propertySustainability.property', function($q) {
                    $q->where('agent_id', Auth::id());
                });
            })
            ->latest('calculated_at')
            ->paginate(15);

        $stats = [
            'total_scores' => EcoScore::count(),
            'average_score' => EcoScore::avg('overall_score'),
            'excellent_properties' => EcoScore::where('overall_score', '>=', 90)->count(),
            'good_properties' => EcoScore::whereBetween('overall_score', [80, 89])->count(),
            'needs_improvement' => EcoScore::where('overall_score', '<', 70)->count(),
        ];

        return view('sustainability.eco-scores.index', compact('ecoScores', 'stats'));
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

        return view('sustainability.eco-scores.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_sustainability_id' => 'required|exists:property_sustainability,id',
            'overall_score' => 'required|numeric|min:0|max:100',
            'energy_score' => 'required|numeric|min:0|max:100',
            'water_score' => 'required|numeric|min:0|max:100',
            'waste_score' => 'required|numeric|min:0|max:100',
            'materials_score' => 'required|numeric|min:0|max:100',
            'transport_score' => 'nullable|numeric|min:0|max:100',
            'biodiversity_score' => 'nullable|numeric|min:0|max:100',
            'air_quality_score' => 'nullable|numeric|min:0|max:100',
            'noise_pollution_score' => 'nullable|numeric|min:0|max:100',
            'community_impact_score' => 'nullable|numeric|min:0|max:100',
            'innovation_score' => 'nullable|numeric|min:0|max:100',
            'calculation_method' => 'required|string|in:standard,advanced,custom',
            'calculation_version' => 'required|string|max:50',
            'data_sources' => 'nullable|array',
            'data_sources.*' => 'string',
            'factors_considered' => 'nullable|array',
            'factors_considered.*' => 'string',
            'weightings_used' => 'nullable|array',
            'weightings_used.*' => 'numeric',
            'benchmark_comparison' => 'nullable|array',
            'improvement_areas' => 'nullable|array',
            'improvement_areas.*' => 'string',
            'strength_areas' => 'nullable|array',
            'strength_areas.*' => 'string',
            'recommendations' => 'nullable|array',
            'recommendations.*' => 'string',
            'target_score' => 'nullable|numeric|min:0|max:100',
            'achievement_date' => 'nullable|date|after:today',
            'certification_eligibility' => 'nullable|array',
            'certification_eligibility.*' => 'string',
            'notes' => 'nullable|string',
        ]);

        // Auto-calculate if not provided
        if (!isset($validated['overall_score']) || $validated['calculation_method'] === 'standard') {
            $calculatedScores = $this->calculateEcoScore($validated['property_sustainability_id']);
            $validated = array_merge($validated, $calculatedScores);
        }

        $ecoScore = EcoScore::create([
            'property_sustainability_id' => $validated['property_sustainability_id'],
            'overall_score' => $validated['overall_score'],
            'energy_score' => $validated['energy_score'],
            'water_score' => $validated['water_score'],
            'waste_score' => $validated['waste_score'],
            'materials_score' => $validated['materials_score'],
            'transport_score' => $validated['transport_score'] ?? 0,
            'biodiversity_score' => $validated['biodiversity_score'] ?? 0,
            'air_quality_score' => $validated['air_quality_score'] ?? 0,
            'noise_pollution_score' => $validated['noise_pollution_score'] ?? 0,
            'community_impact_score' => $validated['community_impact_score'] ?? 0,
            'innovation_score' => $validated['innovation_score'] ?? 0,
            'calculation_method' => $validated['calculation_method'],
            'calculation_version' => $validated['calculation_version'],
            'data_sources' => $validated['data_sources'] ?? [],
            'factors_considered' => $validated['factors_considered'] ?? [],
            'weightings_used' => $validated['weightings_used'] ?? [],
            'benchmark_comparison' => $validated['benchmark_comparison'] ?? [],
            'improvement_areas' => $this->identifyImprovementAreas($validated),
            'strength_areas' => $this->identifyStrengthAreas($validated),
            'recommendations' => $this->generateRecommendations($validated),
            'target_score' => $validated['target_score'] ?? 85,
            'achievement_date' => $validated['achievement_date'] ?? now()->addMonths(6),
            'certification_eligibility' => $this->checkCertificationEligibility($validated['overall_score']),
            'calculated_at' => now(),
            'calculated_by' => Auth::id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update property sustainability eco score
        $propertySustainability = PropertySustainability::find($validated['property_sustainability_id']);
        $propertySustainability->update(['eco_score' => $validated['overall_score']]);

        return redirect()
            ->route('eco-scores.show', $ecoScore)
            ->with('success', 'تم حساب التقييم البيئي بنجاح');
    }

    public function show(EcoScore $ecoScore)
    {
        $ecoScore->load(['propertySustainability.property', 'propertySustainability.carbonFootprints', 'propertySustainability.greenCertifications']);
        
        // Get historical scores for trend analysis
        $historicalScores = EcoScore::where('property_sustainability_id', $ecoScore->property_sustainability_id)
            ->where('id', '!=', $ecoScore->id)
            ->orderBy('calculated_at', 'desc')
            ->take(12)
            ->get();

        // Benchmark against similar properties
        $benchmark = $this->getBenchmarkData($ecoScore);

        // Generate score breakdown
        $scoreBreakdown = $this->generateScoreBreakdown($ecoScore);

        return view('sustainability.eco-scores.show', compact('ecoScore', 'historicalScores', 'benchmark', 'scoreBreakdown'));
    }

    public function edit(EcoScore $ecoScore)
    {
        $ecoScore->load('propertySustainability.property');
        return view('sustainability.eco-scores.edit', compact('ecoScore'));
    }

    public function update(Request $request, EcoScore $ecoScore)
    {
        $validated = $request->validate([
            'overall_score' => 'required|numeric|min:0|max:100',
            'energy_score' => 'required|numeric|min:0|max:100',
            'water_score' => 'required|numeric|min:0|max:100',
            'waste_score' => 'required|numeric|min:0|max:100',
            'materials_score' => 'required|numeric|min:0|max:100',
            'transport_score' => 'nullable|numeric|min:0|max:100',
            'biodiversity_score' => 'nullable|numeric|min:0|max:100',
            'air_quality_score' => 'nullable|numeric|min:0|max:100',
            'noise_pollution_score' => 'nullable|numeric|min:0|max:100',
            'community_impact_score' => 'nullable|numeric|min:0|max:100',
            'innovation_score' => 'nullable|numeric|min:0|max:100',
            'target_score' => 'nullable|numeric|min:0|max:100',
            'achievement_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string',
        ]);

        $ecoScore->update([
            'overall_score' => $validated['overall_score'],
            'energy_score' => $validated['energy_score'],
            'water_score' => $validated['water_score'],
            'waste_score' => $validated['waste_score'],
            'materials_score' => $validated['materials_score'],
            'transport_score' => $validated['transport_score'] ?? $ecoScore->transport_score,
            'biodiversity_score' => $validated['biodiversity_score'] ?? $ecoScore->biodiversity_score,
            'air_quality_score' => $validated['air_quality_score'] ?? $ecoScore->air_quality_score,
            'noise_pollution_score' => $validated['noise_pollution_score'] ?? $ecoScore->noise_pollution_score,
            'community_impact_score' => $validated['community_impact_score'] ?? $ecoScore->community_impact_score,
            'innovation_score' => $validated['innovation_score'] ?? $ecoScore->innovation_score,
            'improvement_areas' => $this->identifyImprovementAreas($validated),
            'strength_areas' => $this->identifyStrengthAreas($validated),
            'recommendations' => $this->generateRecommendations($validated),
            'target_score' => $validated['target_score'] ?? $ecoScore->target_score,
            'achievement_date' => $validated['achievement_date'] ?? $ecoScore->achievement_date,
            'certification_eligibility' => $this->checkCertificationEligibility($validated['overall_score']),
            'notes' => $validated['notes'] ?? $ecoScore->notes,
        ]);

        // Update property sustainability
        $propertySustainability = PropertySustainability::find($ecoScore->property_sustainability_id);
        $propertySustainability->update(['eco_score' => $validated['overall_score']]);

        return redirect()
            ->route('eco-scores.show', $ecoScore)
            ->with('success', 'تم تحديث التقييم البيئي بنجاح');
    }

    public function destroy(EcoScore $ecoScore)
    {
        $ecoScore->delete();

        return redirect()
            ->route('eco-scores.index')
            ->with('success', 'تم حذف التقييم البيئي بنجاح');
    }

    public function calculator()
    {
        return view('sustainability.eco-scores.calculator');
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'energy_efficiency' => 'required|integer|min:1|max:100',
            'water_efficiency' => 'required|integer|min:1|max:100',
            'waste_management' => 'required|integer|min:1|max:100',
            'sustainable_materials' => 'required|integer|min:1|max:100',
            'green_space_ratio' => 'required|numeric|min:0|max:1',
            'renewable_energy' => 'required|integer|min:0|max:100',
            'carbon_footprint' => 'required|numeric|min:0',
            'transport_access' => 'nullable|integer|min:1|max:100',
            'biodiversity' => 'nullable|integer|min:1|max:100',
        ]);

        // Quick calculation for demo purposes
        $scores = $this->quickEcoScoreCalculation($validated);

        return response()->json([
            'overall_score' => round($scores['overall'], 1),
            'energy_score' => round($scores['energy'], 1),
            'water_score' => round($scores['water'], 1),
            'waste_score' => round($scores['waste'], 1),
            'materials_score' => round($scores['materials'], 1),
            'recommendations' => $this->getQuickRecommendations($scores),
        ]);
    }

    public function analytics()
    {
        $scoreTrends = EcoScore::selectRaw('DATE_FORMAT(calculated_at, "%Y-%m") as month, AVG(overall_score) as avg_score, COUNT(*) as count')
            ->where('calculated_at', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $scoreDistribution = EcoScore::selectRaw('
            CASE 
                WHEN overall_score >= 90 THEN "ممتاز (90-100)"
                WHEN overall_score >= 80 THEN "جيد جداً (80-89)"
                WHEN overall_score >= 70 THEN "جيد (70-79)"
                WHEN overall_score >= 60 THEN "متوسط (60-69)"
                ELSE "ضعيف (أقل من 60)"
            END as score_range, 
            COUNT(*) as count
        ')
            ->groupBy('score_range')
            ->orderBy('overall_score', 'desc')
            ->get();

        $categoryAverages = EcoScore::selectRaw('
            AVG(energy_score) as avg_energy,
            AVG(water_score) as avg_water,
            AVG(waste_score) as avg_waste,
            AVG(materials_score) as avg_materials,
            AVG(transport_score) as avg_transport,
            AVG(biodiversity_score) as avg_biodiversity
        ')
            ->first();

        $topPerformers = EcoScore::with(['propertySustainability.property'])
            ->orderBy('overall_score', 'desc')
            ->take(10)
            ->get();

        $mostImproved = $this->getMostImprovedProperties();

        return view('sustainability.eco-scores.analytics', compact(
            'scoreTrends',
            'scoreDistribution',
            'categoryAverages',
            'topPerformers',
            'mostImproved'
        ));
    }

    public function improvementPlan(EcoScore $ecoScore)
    {
        $improvementPlan = $this->generateDetailedImprovementPlan($ecoScore);
        
        return view('sustainability.eco-scores.improvement-plan', compact('ecoScore', 'improvementPlan'));
    }

    public function benchmark(EcoScore $ecoScore)
    {
        $benchmarkData = $this->getDetailedBenchmarkData($ecoScore);
        
        return view('sustainability.eco-scores.benchmark', compact('ecoScore', 'benchmarkData'));
    }

    private function calculateEcoScore($propertySustainabilityId)
    {
        $propertySustainability = PropertySustainability::find($propertySustainabilityId);
        
        $scores = [
            'energy_score' => $propertySustainability->energy_efficiency_rating ?? 50,
            'water_score' => $propertySustainability->water_efficiency_rating ?? 50,
            'waste_score' => $propertySustainability->waste_management_score ?? 50,
            'materials_score' => $propertySustainability->sustainable_materials_percentage ?? 50,
            'transport_score' => 70, // Default score
            'biodiversity_score' => $propertySustainability->green_space_ratio * 100,
            'air_quality_score' => 75, // Default score
            'noise_pollution_score' => 70, // Default score
            'community_impact_score' => 65, // Default score
            'innovation_score' => 60, // Default score
        ];

        // Calculate overall score with weights
        $weights = [
            'energy' => 0.25,
            'water' => 0.20,
            'waste' => 0.15,
            'materials' => 0.15,
            'transport' => 0.10,
            'biodiversity' => 0.05,
            'air_quality' => 0.03,
            'noise_pollution' => 0.02,
            'community_impact' => 0.03,
            'innovation' => 0.02,
        ];

        $overallScore = (
            $scores['energy_score'] * $weights['energy'] +
            $scores['water_score'] * $weights['water'] +
            $scores['waste_score'] * $weights['waste'] +
            $scores['materials_score'] * $weights['materials'] +
            $scores['transport_score'] * $weights['transport'] +
            $scores['biodiversity_score'] * $weights['biodiversity'] +
            $scores['air_quality_score'] * $weights['air_quality'] +
            $scores['noise_pollution_score'] * $weights['noise_pollution'] +
            $scores['community_impact_score'] * $weights['community_impact'] +
            $scores['innovation_score'] * $weights['innovation']
        );

        $scores['overall_score'] = round($overallScore, 1);

        return $scores;
    }

    private function identifyImprovementAreas($scores)
    {
        $areas = [];
        
        if ($scores['energy_score'] < 70) $areas[] = 'كفاءة الطاقة';
        if ($scores['water_score'] < 70) $areas[] = 'كفاءة المياه';
        if ($scores['waste_score'] < 70) $areas[] = 'إدارة النفايات';
        if ($scores['materials_score'] < 70) $areas[] = 'المواد المستدامة';
        if ($scores['transport_score'] < 70) $areas[] = 'النقل';
        if ($scores['biodiversity_score'] < 70) $areas[] = 'التنوع البيولوجي';
        
        return $areas;
    }

    private function identifyStrengthAreas($scores)
    {
        $areas = [];
        
        if ($scores['energy_score'] >= 80) $areas[] = 'كفاءة الطاقة';
        if ($scores['water_score'] >= 80) $areas[] = 'كفاءة المياه';
        if ($scores['waste_score'] >= 80) $areas[] = 'إدارة النفايات';
        if ($scores['materials_score'] >= 80) $areas[] = 'المواد المستدامة';
        if ($scores['transport_score'] >= 80) $areas[] = 'النقل';
        if ($scores['biodiversity_score'] >= 80) $areas[] = 'التنوع البيولوجي';
        
        return $areas;
    }

    private function generateRecommendations($scores)
    {
        $recommendations = [];
        
        if ($scores['energy_score'] < 70) {
            $recommendations[] = 'تحسين كفاءة الطاقة من خلال تركيب ألواح شمسية وعزل أفضل';
        }
        
        if ($scores['water_score'] < 70) {
            $recommendations[] = 'تحسين كفاءة المياه من خلال تجميع مياه الأمطار وإعادة التدوير';
        }
        
        if ($scores['waste_score'] < 70) {
            $recommendations[] = 'تحسين إدارة النفايات من خلال زيادة إعادة التدوير';
        }
        
        if ($scores['materials_score'] < 70) {
            $recommendations[] = 'استخدام مواد مستدامة ومعتمدة';
        }
        
        if ($scores['overall_score'] < 70) {
            $recommendations[] = 'الحاجة إلى تحسين شامل في أداء الاستدامة';
        }
        
        return $recommendations;
    }

    private function checkCertificationEligibility($overallScore)
    {
        $eligibility = [];
        
        if ($overallScore >= 90) {
            $eligibility[] = 'LEED Platinum';
            $eligibility[] = 'BREEAM Outstanding';
        } elseif ($overallScore >= 80) {
            $eligibility[] = 'LEED Gold';
            $eligibility[] = 'BREEAM Excellent';
        } elseif ($overallScore >= 70) {
            $eligibility[] = 'LEED Silver';
            $eligibility[] = 'BREEAM Very Good';
        } elseif ($overallScore >= 60) {
            $eligibility[] = 'LEED Certified';
            $eligibility[] = 'BREEAM Good';
        }
        
        return $eligibility;
    }

    private function getBenchmarkData($ecoScore)
    {
        $propertyType = $ecoScore->propertySustainability->property->type;
        
        return EcoScore::join('property_sustainability', 'eco_scores.property_sustainability_id', '=', 'property_sustainability.id')
            ->join('properties', 'property_sustainability.property_id', '=', 'properties.id')
            ->where('properties.type', $propertyType)
            ->where('eco_scores.id', '!=', $ecoScore->id)
            ->selectRaw('AVG(eco_scores.overall_score) as avg_score, MIN(eco_scores.overall_score) as min_score, MAX(eco_scores.overall_score) as max_score, COUNT(*) as count')
            ->first();
    }

    private function generateScoreBreakdown($ecoScore)
    {
        return [
            'energy' => [
                'score' => $ecoScore->energy_score,
                'weight' => 25,
                'contribution' => $ecoScore->energy_score * 0.25,
                'status' => $this->getScoreStatus($ecoScore->energy_score),
            ],
            'water' => [
                'score' => $ecoScore->water_score,
                'weight' => 20,
                'contribution' => $ecoScore->water_score * 0.20,
                'status' => $this->getScoreStatus($ecoScore->water_score),
            ],
            'waste' => [
                'score' => $ecoScore->waste_score,
                'weight' => 15,
                'contribution' => $ecoScore->waste_score * 0.15,
                'status' => $this->getScoreStatus($ecoScore->waste_score),
            ],
            'materials' => [
                'score' => $ecoScore->materials_score,
                'weight' => 15,
                'contribution' => $ecoScore->materials_score * 0.15,
                'status' => $this->getScoreStatus($ecoScore->materials_score),
            ],
        ];
    }

    private function getScoreStatus($score)
    {
        if ($score >= 90) return 'ممتاز';
        if ($score >= 80) return 'جيد جداً';
        if ($score >= 70) return 'جيد';
        if ($score >= 60) return 'متوسط';
        return 'ضعيف';
    }

    private function quickEcoScoreCalculation($data)
    {
        $weights = [
            'energy' => 0.25,
            'water' => 0.20,
            'waste' => 0.15,
            'materials' => 0.15,
            'green_space' => 0.10,
            'renewable' => 0.15,
        ];

        $energyScore = $data['energy_efficiency'] * $weights['energy'];
        $waterScore = $data['water_efficiency'] * $weights['water'];
        $wasteScore = $data['waste_management'] * $weights['waste'];
        $materialsScore = $data['sustainable_materials'] * $weights['materials'];
        $greenSpaceScore = $data['green_space_ratio'] * 100 * $weights['green_space'];
        $renewableScore = $data['renewable_energy'] * $weights['renewable'];

        $overall = $energyScore + $waterScore + $wasteScore + $materialsScore + $greenSpaceScore + $renewableScore;

        return [
            'overall' => $overall,
            'energy' => $data['energy_efficiency'],
            'water' => $data['water_efficiency'],
            'waste' => $data['waste_management'],
            'materials' => $data['sustainable_materials'],
        ];
    }

    private function getQuickRecommendations($scores)
    {
        $recommendations = [];

        if ($scores['overall'] < 70) {
            $recommendations[] = 'تحسين شامل في أداء الاستدامة مطلوب';
        }

        if ($scores['energy'] < 70) {
            $recommendations[] = 'تحسين كفاءة الطاقة';
        }

        if ($scores['water'] < 70) {
            $recommendations[] = 'تحسين كفاءة المياه';
        }

        return $recommendations;
    }

    private function getMostImprovedProperties()
    {
        return EcoScore::selectRaw('property_sustainability_id, MIN(overall_score) as min_score, MAX(overall_score) as max_score, MAX(overall_score) - MIN(overall_score) as improvement')
            ->groupBy('property_sustainability_id')
            ->having('improvement', '>', 10)
            ->orderBy('improvement', 'desc')
            ->take(10)
            ->with('propertySustainability.property')
            ->get();
    }

    private function generateDetailedImprovementPlan($ecoScore)
    {
        $plan = [];

        if ($ecoScore->energy_score < 70) {
            $plan[] = [
                'category' => 'الطاقة',
                'current_score' => $ecoScore->energy_score,
                'target_score' => min(85, $ecoScore->energy_score + 20),
                'actions' => ['تركيب ألواح شمسية', 'تحسين العزل', 'استخدام أجهزة موفرة'],
                'estimated_cost' => 'متوسط إلى مرتفع',
                'timeline' => '3-6 أشهر',
            ];
        }

        if ($ecoScore->water_score < 70) {
            $plan[] = [
                'category' => 'المياه',
                'current_score' => $ecoScore->water_score,
                'target_score' => min(85, $ecoScore->water_score + 20),
                'actions' => ['تجميع مياه الأمطار', 'إعادة التدوير', 'أجهزة منخفضة التدفق'],
                'estimated_cost' => 'منخفض إلى متوسط',
                'timeline' => '1-3 أشهر',
            ];
        }

        return $plan;
    }

    private function getDetailedBenchmarkData($ecoScore)
    {
        $propertyType = $ecoScore->propertySustainability->property->type;
        
        return EcoScore::join('property_sustainability', 'eco_scores.property_sustainability_id', '=', 'property_sustainability.id')
            ->join('properties', 'property_sustainability.property_id', '=', 'properties.id')
            ->where('properties.type', $propertyType)
            ->selectRaw('
                AVG(eco_scores.overall_score) as avg_overall,
                AVG(eco_scores.energy_score) as avg_energy,
                AVG(eco_scores.water_score) as avg_water,
                AVG(eco_scores.waste_score) as avg_waste,
                AVG(eco_scores.materials_score) as avg_materials,
                COUNT(*) as total_properties,
                PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY eco_scores.overall_score) as median_score
            ')
            ->first();
    }
}
