<?php

namespace App\Http\Controllers;

use App\Models\EcoScore;
use App\Models\SmartProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EcoScoreController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_scores' => EcoScore::count(),
            'assessed_properties' => EcoScore::distinct('property_id')->count(),
            'average_overall_score' => EcoScore::avg('overall_score'),
            'certified_properties' => EcoScore::where('status', 'certified')->count(),
            'scores_by_level' => $this->getScoresByLevel(),
            'score_trends' => $this->getScoreTrends(),
        ];

        $recentScores = EcoScore::with(['property'])
            ->latest()
            ->take(10)
            ->get();

        $topPerformers = $this->getTopPerformers();
        $improvementAreas = $this->getCommonImprovementAreas();

        return view('sustainability.eco-score-dashboard', compact(
            'stats', 
            'recentScores', 
            'topPerformers', 
            'improvementAreas'
        ));
    }

    public function index(Request $request)
    {
        $query = EcoScore::with(['property']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('eco_level')) {
            $query->where('eco_level', $request->eco_level);
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

        if ($request->filled('overall_score_min')) {
            $query->where('overall_score', '>=', $request->overall_score_min);
        }

        if ($request->filled('overall_score_max')) {
            $query->where('overall_score', '<=', $request->overall_score_max);
        }

        $scores = $query->latest()->paginate(12);

        $ecoLevels = ['bronze', 'silver', 'gold', 'platinum', 'diamond'];
        $statuses = ['pending', 'assessed', 'improving', 'certified'];

        return view('sustainability.eco-score-index', compact(
            'scores', 
            'ecoLevels', 
            'statuses'
        ));
    }

    public function create()
    {
        $properties = SmartProperty::all();

        return view('sustainability.eco-score-create', compact(
            'properties'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $scoreData = $request->validate([
                'property_id' => 'required|exists:smart_properties,id',
                'overall_score' => 'required|numeric|min:0|max:100',
                'energy_score' => 'required|numeric|min:0|max:100',
                'water_score' => 'required|numeric|min:0|max:100',
                'waste_score' => 'required|numeric|min:0|max:100',
                'materials_score' => 'required|numeric|min:0|max:100',
                'transport_score' => 'required|numeric|min:0|max:100',
                'biodiversity_score' => 'required|numeric|min:0|max:100',
                'score_breakdown' => 'nullable|array',
                'improvement_suggestions' => 'nullable|array',
                'eco_level' => 'required|in:bronze,silver,gold,platinum,diamond',
                'certification_requirements' => 'nullable|array',
                'assessment_date' => 'required|date',
                'next_assessment_date' => 'nullable|date|after:assessment_date',
                'status' => 'required|in:pending,assessed,improving,certified',
            ]);

            $scoreData['created_by'] = auth()->id();
            $scoreData['score_breakdown'] = $this->generateScoreBreakdown($request);
            $scoreData['improvement_suggestions'] = $this->generateImprovementSuggestions($request);
            $scoreData['certification_requirements'] = $this->generateCertificationRequirements($request);

            $ecoScore = EcoScore::create($scoreData);

            DB::commit();

            return redirect()
                ->route('eco-score.show', $ecoScore)
                ->with('success', 'تم إضافة التقييم البيئي بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة التقييم: ' . $e->getMessage());
        }
    }

    public function show(EcoScore $ecoScore)
    {
        $ecoScore->load(['property']);
        $scoreDetails = $this->getScoreDetails($ecoScore);
        $improvementAnalysis = $this->getImprovementAnalysis($ecoScore);
        $certificationPath = $this->getCertificationPath($ecoScore);

        return view('sustainability.eco-score-show', compact(
            'ecoScore', 
            'scoreDetails', 
            'improvementAnalysis', 
            'certificationPath'
        ));
    }

    public function edit(EcoScore $ecoScore)
    {
        $properties = SmartProperty::all();

        return view('sustainability.eco-score-edit', compact(
            'ecoScore', 
            'properties'
        ));
    }

    public function update(Request $request, EcoScore $ecoScore)
    {
        DB::beginTransaction();
        try {
            $scoreData = $request->validate([
                'overall_score' => 'required|numeric|min:0|max:100',
                'energy_score' => 'required|numeric|min:0|max:100',
                'water_score' => 'required|numeric|min:0|max:100',
                'waste_score' => 'required|numeric|min:0|max:100',
                'materials_score' => 'required|numeric|min:0|max:100',
                'transport_score' => 'required|numeric|min:0|max:100',
                'biodiversity_score' => 'required|numeric|min:0|max:100',
                'score_breakdown' => 'nullable|array',
                'improvement_suggestions' => 'nullable|array',
                'eco_level' => 'required|in:bronze,silver,gold,platinum,diamond',
                'certification_requirements' => 'nullable|array',
                'assessment_date' => 'required|date',
                'next_assessment_date' => 'nullable|date|after:assessment_date',
                'status' => 'required|in:pending,assessed,improving,certified',
            ]);

            $scoreData['updated_by'] = auth()->id();
            $scoreData['score_breakdown'] = $this->generateScoreBreakdown($request);
            $scoreData['improvement_suggestions'] = $this->generateImprovementSuggestions($request);
            $scoreData['certification_requirements'] = $this->generateCertificationRequirements($request);

            $ecoScore->update($scoreData);

            DB::commit();

            return redirect()
                ->route('eco-score.show', $ecoScore)
                ->with('success', 'تم تحديث التقييم البيئي بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث التقييم: ' . $e->getMessage());
        }
    }

    public function destroy(EcoScore $ecoScore)
    {
        try {
            $ecoScore->delete();

            return redirect()
                ->route('eco-score.index')
                ->with('success', 'تم حذف التقييم البيئي بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف التقييم: ' . $e->getMessage());
        }
    }

    public function calculateScore(Request $request)
    {
        $propertyId = $request->input('property_id');
        $assessmentData = $request->input('assessment_data', []);

        $score = $this->performEcoScoreCalculation($propertyId, $assessmentData);

        return response()->json([
            'success' => true,
            'eco_score' => $score
        ]);
    }

    public function getImprovementPlan(EcoScore $ecoScore)
    {
        $plan = $this->generateImprovementPlan($ecoScore);

        return response()->json([
            'success' => true,
            'improvement_plan' => $plan
        ]);
    }

    public function trackProgress(EcoScore $ecoScore)
    {
        $progress = $this->calculateScoreProgress($ecoScore);

        return response()->json([
            'success' => true,
            'progress' => $progress
        ]);
    }

    public function generateCertificate(EcoScore $ecoScore)
    {
        try {
            $certificateData = $this->generateEcoCertificate($ecoScore);
            
            return response()->json([
                'success' => true,
                'certificate' => $certificateData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generateScoreBreakdown($request)
    {
        return [
            'energy_factors' => $request->input('energy_factors', []),
            'water_factors' => $request->input('water_factors', []),
            'waste_factors' => $request->input('waste_factors', []),
            'materials_factors' => $request->input('materials_factors', []),
            'transport_factors' => $request->input('transport_factors', []),
            'biodiversity_factors' => $request->input('biodiversity_factors', []),
            'assessment_methodology' => $request->input('assessment_methodology', 'standard'),
            'data_sources' => $request->input('data_sources', []),
            'last_updated' => now()->toDateTimeString(),
        ];
    }

    private function generateImprovementSuggestions($request)
    {
        return [
            'immediate_actions' => $request->input('immediate_actions', []),
            'short_term_improvements' => $request->input('short_term_improvements', []),
            'long_term_goals' => $request->input('long_term_goals', []),
            'priority_areas' => $request->input('priority_areas', []),
            'cost_benefit_analysis' => $request->input('cost_benefit_analysis', []),
            'implementation_timeline' => $request->input('implementation_timeline', []),
        ];
    }

    private function generateCertificationRequirements($request)
    {
        return [
            'current_level_requirements' => $request->input('current_level_requirements', []),
            'next_level_requirements' => $request->input('next_level_requirements', []),
            'missing_criteria' => $request->input('missing_criteria', []),
            'documentation_needed' => $request->input('documentation_needed', []),
            'assessment_dates' => $request->input('assessment_dates', []),
            'certification_body' => $request->input('certification_body', ''),
        ];
    }

    private function getScoresByLevel()
    {
        return EcoScore::select('eco_level', DB::raw('COUNT(*) as count, AVG(overall_score) as avg_score'))
            ->groupBy('eco_level')
            ->get();
    }

    private function getScoreTrends()
    {
        return EcoScore::selectRaw('MONTH(assessment_date) as month, AVG(overall_score) as avg_score')
            ->whereYear('assessment_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getTopPerformers()
    {
        return EcoScore::with(['property'])
            ->select('property_id', DB::raw('AVG(overall_score) as avg_score'))
            ->groupBy('property_id')
            ->orderBy('avg_score', 'desc')
            ->take(5)
            ->get();
    }

    private function getCommonImprovementAreas()
    {
        $allScores = EcoScore::all();
        $improvementAreas = [];

        foreach ($allScores as $score) {
            $areas = $score->getImprovementAreas();
            foreach ($areas as $area) {
                $improvementAreas[$area] = ($improvementAreas[$area] ?? 0) + 1;
            }
        }

        arsort($improvementAreas);
        return array_slice($improvementAreas, 0, 5, true);
    }

    private function getScoreDetails($ecoScore)
    {
        return [
            'score_grade' => $ecoScore->getScoreGrade(),
            'certification_ready' => $ecoScore->getCertificationReady(),
            'improvement_areas' => $ecoScore->getImprovementAreas(),
            'next_level_score' => $ecoScore->getNextLevelScore(),
            'points_to_next_level' => max(0, $ecoScore->getNextLevelScore() - $ecoScore->overall_score),
            'benchmark_comparison' => $this->getBenchmarkComparison($ecoScore),
        ];
    }

    private function getImprovementAnalysis($ecoScore)
    {
        return [
            'strongest_areas' => $this->getStrongestAreas($ecoScore),
            'weakest_areas' => $this->getWeakestAreas($ecoScore),
            'improvement_potential' => $this->calculateImprovementPotential($ecoScore),
            'priority_actions' => $this->getPriorityActions($ecoScore),
            'estimated_timeline' => $this->estimateImprovementTimeline($ecoScore),
        ];
    }

    private function getCertificationPath($ecoScore)
    {
        return [
            'current_level' => $ecoScore->eco_level,
            'next_level' => $this->getNextLevel($ecoScore->eco_level),
            'requirements_met' => $this->getRequirementsMet($ecoScore),
            'requirements_missing' => $this->getRequirementsMissing($ecoScore),
            'estimated_cost' => $this->estimateCertificationCost($ecoScore),
            'timeline' => $this->estimateCertificationTimeline($ecoScore),
        ];
    }

    private function getBenchmarkComparison($ecoScore)
    {
        $averageScore = EcoScore::avg('overall_score');
        
        return [
            'property_score' => $ecoScore->overall_score,
            'industry_average' => $averageScore,
            'percentile' => $this->calculatePercentile($ecoScore->overall_score),
            'performance_rating' => $ecoScore->overall_score > $averageScore ? 'Above Average' : 'Below Average',
        ];
    }

    private function calculatePercentile($score)
    {
        $total = EcoScore::count();
        $better = EcoScore::where('overall_score', '>', $score)->count();
        return (($total - $better) / $total) * 100;
    }

    private function getStrongestAreas($ecoScore)
    {
        $scores = [
            'energy' => $ecoScore->energy_score,
            'water' => $ecoScore->water_score,
            'waste' => $ecoScore->waste_score,
            'materials' => $ecoScore->materials_score,
            'transport' => $ecoScore->transport_score,
            'biodiversity' => $ecoScore->biodiversity_score,
        ];

        arsort($scores);
        return array_slice(array_keys($scores), 0, 3, true);
    }

    private function getWeakestAreas($ecoScore)
    {
        $scores = [
            'energy' => $ecoScore->energy_score,
            'water' => $ecoScore->water_score,
            'waste' => $ecoScore->waste_score,
            'materials' => $ecoScore->materials_score,
            'transport' => $ecoScore->transport_score,
            'biodiversity' => $ecoScore->biodiversity_score,
        ];

        asort($scores);
        return array_slice(array_keys($scores), 0, 3, true);
    }

    private function calculateImprovementPotential($ecoScore)
    {
        $maxPossibleScore = 100;
        $currentScore = $ecoScore->overall_score;
        
        return [
            'total_improvement_potential' => $maxPossibleScore - $currentScore,
            'realistic_improvement' => min(20, $maxPossibleScore - $currentScore),
            'target_score' => min($maxPossibleScore, $currentScore + 20),
        ];
    }

    private function getPriorityActions($ecoScore)
    {
        $actions = [];
        $weakestAreas = $this->getWeakestAreas($ecoScore);

        foreach ($weakestAreas as $area) {
            switch ($area) {
                case 'energy':
                    $actions[] = 'Improve energy efficiency';
                    break;
                case 'water':
                    $actions[] = 'Implement water conservation measures';
                    break;
                case 'waste':
                    $actions[] = 'Enhance waste management';
                    break;
                case 'materials':
                    $actions[] = 'Use sustainable materials';
                    break;
                case 'transport':
                    $actions[] = 'Promote sustainable transport';
                    break;
                case 'biodiversity':
                    $actions[] = 'Enhance biodiversity measures';
                    break;
            }
        }

        return $actions;
    }

    private function estimateImprovementTimeline($ecoScore)
    {
        $improvementNeeded = 100 - $ecoScore->overall_score;
        
        if ($improvementNeeded <= 10) return '3-6 months';
        if ($improvementNeeded <= 20) return '6-12 months';
        if ($improvementNeeded <= 30) return '1-2 years';
        return '2+ years';
    }

    private function getNextLevel($currentLevel)
    {
        $levels = ['bronze', 'silver', 'gold', 'platinum', 'diamond'];
        $currentIndex = array_search($currentLevel, $levels);
        
        return $levels[min($currentIndex + 1, count($levels) - 1)];
    }

    private function getRequirementsMet($ecoScore)
    {
        $requirements = $ecoScore->certification_requirements ?? [];
        return $requirements['current_level_requirements'] ?? [];
    }

    private function getRequirementsMissing($ecoScore)
    {
        $requirements = $ecoScore->certification_requirements ?? [];
        return $requirements['missing_criteria'] ?? [];
    }

    private function estimateCertificationCost($ecoScore)
    {
        $baseCost = 2000;
        $levelMultiplier = match($ecoScore->eco_level) {
            'bronze' => 1,
            'silver' => 1.5,
            'gold' => 2,
            'platinum' => 2.5,
            'diamond' => 3,
            default => 1,
        };

        return $baseCost * $levelMultiplier;
    }

    private function estimateCertificationTimeline($ecoScore)
    {
        $score = $ecoScore->overall_score;
        
        if ($score >= 90) return '1-2 months';
        if ($score >= 80) return '2-3 months';
        if ($score >= 70) return '3-6 months';
        return '6+ months';
    }

    private function performEcoScoreCalculation($propertyId, $assessmentData)
    {
        $energyScore = $assessmentData['energy_factors'] ?? 0;
        $waterScore = $assessmentData['water_factors'] ?? 0;
        $wasteScore = $assessmentData['waste_factors'] ?? 0;
        $materialsScore = $assessmentData['materials_factors'] ?? 0;
        $transportScore = $assessmentData['transport_factors'] ?? 0;
        $biodiversityScore = $assessmentData['biodiversity_factors'] ?? 0;

        $overallScore = ($energyScore + $waterScore + $wasteScore + $materialsScore + $transportScore + $biodiversityScore) / 6;

        return [
            'property_id' => $propertyId,
            'overall_score' => $overallScore,
            'energy_score' => $energyScore,
            'water_score' => $waterScore,
            'waste_score' => $wasteScore,
            'materials_score' => $materialsScore,
            'transport_score' => $transportScore,
            'biodiversity_score' => $biodiversityScore,
            'eco_level' => $this->determineEcoLevel($overallScore),
            'assessment_date' => now()->toDateString(),
        ];
    }

    private function determineEcoLevel($score)
    {
        if ($score >= 95) return 'diamond';
        if ($score >= 85) return 'platinum';
        if ($score >= 75) return 'gold';
        if ($score >= 65) return 'silver';
        return 'bronze';
    }

    private function generateImprovementPlan($ecoScore)
    {
        return [
            'current_score' => $ecoScore->overall_score,
            'target_score' => $ecoScore->getNextLevelScore(),
            'improvement_needed' => max(0, $ecoScore->getNextLevelScore() - $ecoScore->overall_score),
            'priority_areas' => $ecoScore->getImprovementAreas(),
            'recommended_actions' => $ecoScore->improvement_suggestions,
            'estimated_cost' => $this->estimateImprovementCost($ecoScore),
            'timeline' => $this->estimateImprovementTimeline($ecoScore),
            'success_metrics' => $this->defineSuccessMetrics($ecoScore),
        ];
    }

    private function estimateImprovementCost($ecoScore)
    {
        $improvementNeeded = max(0, $ecoScore->getNextLevelScore() - $ecoScore->overall_score);
        $costPerPoint = 100; // $100 per improvement point
        
        return $improvementNeeded * $costPerPoint;
    }

    private function defineSuccessMetrics($ecoScore)
    {
        return [
            'target_overall_score' => $ecoScore->getNextLevelScore(),
            'target_energy_score' => min(100, $ecoScore->energy_score + 10),
            'target_water_score' => min(100, $ecoScore->water_score + 10),
            'target_waste_score' => min(100, $ecoScore->waste_score + 10),
            'target_materials_score' => min(100, $ecoScore->materials_score + 10),
            'target_transport_score' => min(100, $ecoScore->transport_score + 10),
            'target_biodiversity_score' => min(100, $ecoScore->biodiversity_score + 10),
        ];
    }

    private function calculateScoreProgress($ecoScore)
    {
        return [
            'current_score' => $ecoScore->overall_score,
            'target_score' => $ecoScore->getNextLevelScore(),
            'progress_percentage' => ($ecoScore->overall_score / $ecoScore->getNextLevelScore()) * 100,
            'points_achieved' => $ecoScore->overall_score,
            'points_remaining' => max(0, $ecoScore->getNextLevelScore() - $ecoScore->overall_score),
        ];
    }

    private function generateEcoCertificate($ecoScore)
    {
        return [
            'certificate_id' => uniqid('eco_cert_'),
            'property_name' => $ecoScore->property->property_name,
            'eco_level' => $ecoScore->eco_level,
            'overall_score' => $ecoScore->overall_score,
            'assessment_date' => $ecoScore->assessment_date->toDateString(),
            'expiry_date' => now()->addYear()->toDateString(),
            'issued_by' => auth()->user()->name,
            'verification_code' => strtoupper(uniqid('ECO_')),
            'generated_at' => now()->toDateTimeString(),
        ];
    }
}
