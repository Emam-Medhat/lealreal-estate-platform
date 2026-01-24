<?php

namespace App\Http\Controllers;

use App\Models\EnergyEfficiency;
use App\Models\SmartProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EnergyEfficiencyController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_assessments' => EnergyEfficiency::count(),
            'assessed_properties' => EnergyEfficiency::distinct('property_id')->count(),
            'average_efficiency_score' => EnergyEfficiency::avg('efficiency_score'),
            'total_cost_savings' => EnergyEfficiency::sum('cost_savings'),
            'properties_by_level' => $this->getPropertiesByLevel(),
            'efficiency_trends' => $this->getEfficiencyTrends(),
        ];

        $recentAssessments = EnergyEfficiency::with(['property'])
            ->latest()
            ->take(10)
            ->get();

        $topPerformers = $this->getTopPerformers();
        $improvementOpportunities = $this->getImprovementOpportunities();

        return view('sustainability.energy-efficiency-dashboard', compact(
            'stats', 
            'recentAssessments', 
            'topPerformers', 
            'improvementOpportunities'
        ));
    }

    public function index(Request $request)
    {
        $query = EnergyEfficiency::with(['property']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('efficiency_level')) {
            $query->where('efficiency_level', $request->efficiency_level);
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

        if ($request->filled('efficiency_score_min')) {
            $query->where('efficiency_score', '>=', $request->efficiency_score_min);
        }

        if ($request->filled('efficiency_score_max')) {
            $query->where('efficiency_score', '<=', $request->efficiency_score_max);
        }

        $assessments = $query->latest()->paginate(12);

        $efficiencyLevels = ['poor', 'fair', 'good', 'excellent', 'outstanding'];
        $statuses = ['pending', 'assessed', 'improving', 'certified'];

        return view('sustainability.energy-efficiency-index', compact(
            'assessments', 
            'efficiencyLevels', 
            'statuses'
        ));
    }

    public function create()
    {
        $properties = SmartProperty::all();

        return view('sustainability.energy-efficiency-create', compact(
            'properties'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $efficiencyData = $request->validate([
                'property_id' => 'required|exists:smart_properties,id',
                'efficiency_score' => 'required|numeric|min:0|max:100',
                'energy_consumption_baseline' => 'required|numeric|min:0',
                'current_consumption' => 'required|numeric|min:0',
                'savings_percentage' => 'required|numeric|min:0|max:100',
                'efficiency_metrics' => 'nullable|array',
                'recommendations' => 'nullable|array',
                'efficiency_level' => 'required|in:poor,fair,good,excellent,outstanding',
                'applied_measures' => 'nullable|array',
                'cost_savings' => 'required|numeric|min:0',
                'assessment_date' => 'required|date',
                'next_assessment_date' => 'nullable|date|after:assessment_date',
                'status' => 'required|in:pending,assessed,improving,certified',
            ]);

            $efficiencyData['created_by'] = auth()->id();
            $efficiencyData['efficiency_metrics'] = $this->generateEfficiencyMetrics($request);
            $efficiencyData['recommendations'] = $this->generateRecommendations($request);
            $efficiencyData['applied_measures'] = $this->generateAppliedMeasures($request);

            $assessment = EnergyEfficiency::create($efficiencyData);

            DB::commit();

            return redirect()
                ->route('energy-efficiency.show', $assessment)
                ->with('success', 'تم إضافة تقييم كفاءة الطاقة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة التقييم: ' . $e->getMessage());
        }
    }

    public function show(EnergyEfficiency $assessment)
    {
        $assessment->load(['property']);
        $efficiencyDetails = $this->getEfficiencyDetails($assessment);
        $savingsAnalysis = $this->getSavingsAnalysis($assessment);
        $improvementPlan = $this->getImprovementPlan($assessment);

        return view('sustainability.energy-efficiency-show', compact(
            'assessment', 
            'efficiencyDetails', 
            'savingsAnalysis', 
            'improvementPlan'
        ));
    }

    public function edit(EnergyEfficiency $assessment)
    {
        $properties = SmartProperty::all();

        return view('sustainability.energy-efficiency-edit', compact(
            'assessment', 
            'properties'
        ));
    }

    public function update(Request $request, EnergyEfficiency $assessment)
    {
        DB::beginTransaction();
        try {
            $efficiencyData = $request->validate([
                'efficiency_score' => 'required|numeric|min:0|max:100',
                'energy_consumption_baseline' => 'required|numeric|min:0',
                'current_consumption' => 'required|numeric|min:0',
                'savings_percentage' => 'required|numeric|min:0|max:100',
                'efficiency_metrics' => 'nullable|array',
                'recommendations' => 'nullable|array',
                'efficiency_level' => 'required|in:poor,fair,good,excellent,outstanding',
                'applied_measures' => 'nullable|array',
                'cost_savings' => 'required|numeric|min:0',
                'assessment_date' => 'required|date',
                'next_assessment_date' => 'nullable|date|after:assessment_date',
                'status' => 'required|in:pending,assessed,improving,certified',
            ]);

            $efficiencyData['updated_by'] = auth()->id();
            $efficiencyData['efficiency_metrics'] = $this->generateEfficiencyMetrics($request);
            $efficiencyData['recommendations'] = $this->generateRecommendations($request);
            $efficiencyData['applied_measures'] = $this->generateAppliedMeasures($request);

            $assessment->update($efficiencyData);

            DB::commit();

            return redirect()
                ->route('energy-efficiency.show', $assessment)
                ->with('success', 'تم تحديث تقييم كفاءة الطاقة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث التقييم: ' . $e->getMessage());
        }
    }

    public function destroy(EnergyEfficiency $assessment)
    {
        try {
            $assessment->delete();

            return redirect()
                ->route('energy-efficiency.index')
                ->with('success', 'تم حذف تقييم كفاءة الطاقة بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف التقييم: ' . $e->getMessage());
        }
    }

    public function assessEnergy(Request $request)
    {
        $propertyId = $request->input('property_id');
        $assessmentData = $request->input('assessment_data', []);

        $assessment = $this->performEnergyAssessment($propertyId, $assessmentData);

        return response()->json([
            'success' => true,
            'assessment' => $assessment
        ]);
    }

    public function calculateSavings(EnergyEfficiency $assessment)
    {
        $savings = $this->calculateEnergySavings($assessment);

        return response()->json([
            'success' => true,
            'savings' => $savings
        ]);
    }

    public function getRecommendations(EnergyEfficiency $assessment)
    {
        $recommendations = $this->generateEnergyRecommendations($assessment);

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations
        ]);
    }

    public function generateReport(EnergyEfficiency $assessment)
    {
        try {
            $reportData = $this->generateEnergyReport($assessment);
            
            return response()->json([
                'success' => true,
                'report' => $reportData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generateEfficiencyMetrics($request)
    {
        return [
            'heating_efficiency' => $request->input('heating_efficiency', 0),
            'cooling_efficiency' => $request->input('cooling_efficiency', 0),
            'lighting_efficiency' => $request->input('lighting_efficiency', 0),
            'appliance_efficiency' => $request->input('appliance_efficiency', 0),
            'insulation_quality' => $request->input('insulation_quality', 0),
            'window_efficiency' => $request->input('window_efficiency', 0),
            'hvac_efficiency' => $request->input('hvac_efficiency', 0),
            'renewable_energy_percentage' => $request->input('renewable_energy_percentage', 0),
            'last_updated' => now()->toDateTimeString(),
        ];
    }

    private function generateRecommendations($request)
    {
        return [
            'immediate_actions' => $request->input('immediate_actions', []),
            'short_term_improvements' => $request->input('short_term_improvements', []),
            'long_term_investments' => $request->input('long_term_investments', []),
            'behavioral_changes' => $request->input('behavioral_changes', []),
            'technology_upgrades' => $request->input('technology_upgrades', []),
            'maintenance_recommendations' => $request->input('maintenance_recommendations', []),
        ];
    }

    private function generateAppliedMeasures($request)
    {
        return [
            'led_lighting' => $request->input('led_lighting', false),
            'smart_thermostat' => $request->input('smart_thermostat', false),
            'insulation_upgrade' => $request->input('insulation_upgrade', false),
            'window_replacement' => $request->input('window_replacement', false),
            'hvac_upgrade' => $request->input('hvac_upgrade', false),
            'solar_panels' => $request->input('solar_panels', false),
            'energy_monitoring' => $request->input('energy_monitoring', false),
            'appliance_upgrades' => $request->input('appliance_upgrades', false),
        ];
    }

    private function getPropertiesByLevel()
    {
        return EnergyEfficiency::select('efficiency_level', DB::raw('COUNT(*) as count'))
            ->groupBy('efficiency_level')
            ->get();
    }

    private function getEfficiencyTrends()
    {
        return EnergyEfficiency::selectRaw('MONTH(assessment_date) as month, AVG(efficiency_score) as avg_score')
            ->whereYear('assessment_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getTopPerformers()
    {
        return EnergyEfficiency::with(['property'])
            ->select('property_id', DB::raw('AVG(efficiency_score) as avg_score'))
            ->groupBy('property_id')
            ->orderBy('avg_score', 'desc')
            ->take(5)
            ->get();
    }

    private function getImprovementOpportunities()
    {
        return EnergyEfficiency::with(['property'])
            ->where('efficiency_score', '<', 70)
            ->where('status', '!=', 'certified')
            ->orderBy('efficiency_score', 'asc')
            ->take(10)
            ->get();
    }

    private function getEfficiencyDetails($assessment)
    {
        return [
            'energy_reduction' => $assessment->getEnergyReduction(),
            'annual_cost_savings' => $assessment->getAnnualCostSavings(),
            'efficiency_grade' => $this->getEfficiencyGrade($assessment->efficiency_score),
            'benchmark_comparison' => $this->getBenchmarkComparison($assessment),
            'potential_improvements' => $this->getPotentialImprovements($assessment),
        ];
    }

    private function getSavingsAnalysis($assessment)
    {
        return [
            'current_savings' => $assessment->cost_savings,
            'potential_savings' => $this->calculatePotentialSavings($assessment),
            'payback_period' => $this->calculatePaybackPeriod($assessment),
            'roi_percentage' => $this->calculateROI($assessment),
            'monthly_savings' => $assessment->cost_savings / 12,
        ];
    }

    private function getImprovementPlan($assessment)
    {
        return [
            'priority_actions' => $this->getPriorityActions($assessment),
            'estimated_costs' => $this->getEstimatedCosts($assessment),
            'implementation_timeline' => $this->getImplementationTimeline($assessment),
            'expected_benefits' => $this->getExpectedBenefits($assessment),
        ];
    }

    private function getEfficiencyGrade($score)
    {
        if ($score >= 90) return 'A+';
        if ($score >= 85) return 'A';
        if ($score >= 80) return 'B+';
        if ($score >= 75) return 'B';
        if ($score >= 70) return 'C+';
        if ($score >= 65) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }

    private function getBenchmarkComparison($assessment)
    {
        $averageScore = EnergyEfficiency::avg('efficiency_score');
        
        return [
            'property_score' => $assessment->efficiency_score,
            'industry_average' => $averageScore,
            'percentile' => $this->calculatePercentile($assessment->efficiency_score),
            'performance_rating' => $assessment->efficiency_score > $averageScore ? 'Above Average' : 'Below Average',
        ];
    }

    private function calculatePercentile($score)
    {
        $total = EnergyEfficiency::count();
        $better = EnergyEfficiency::where('efficiency_score', '>', $score)->count();
        return (($total - $better) / $total) * 100;
    }

    private function getPotentialImprovements($assessment)
    {
        $improvements = [];
        $metrics = $assessment->efficiency_metrics ?? [];

        if (($metrics['heating_efficiency'] ?? 0) < 70) {
            $improvements[] = 'Upgrade heating system';
        }

        if (($metrics['cooling_efficiency'] ?? 0) < 70) {
            $improvements[] = 'Improve cooling efficiency';
        }

        if (($metrics['lighting_efficiency'] ?? 0) < 70) {
            $improvements[] = 'Install LED lighting';
        }

        if (($metrics['insulation_quality'] ?? 0) < 70) {
            $improvements[] = 'Improve insulation';
        }

        return $improvements;
    }

    private function calculatePotentialSavings($assessment)
    {
        $currentSavings = $assessment->cost_savings;
        $potentialImprovement = 100 - $assessment->efficiency_score;
        return $currentSavings * (1 + ($potentialImprovement / 50));
    }

    private function calculatePaybackPeriod($assessment)
    {
        $annualSavings = $assessment->cost_savings;
        $estimatedInvestment = 10000; // Estimated investment amount
        
        return $annualSavings > 0 ? $estimatedInvestment / $annualSavings : 0;
    }

    private function calculateROI($assessment)
    {
        $annualSavings = $assessment->cost_savings;
        $estimatedInvestment = 10000;
        
        return $estimatedInvestment > 0 ? ($annualSavings / $estimatedInvestment) * 100 : 0;
    }

    private function getPriorityActions($assessment)
    {
        $actions = [];
        $recommendations = $assessment->recommendations ?? [];

        if (!empty($recommendations['immediate_actions'])) {
            $actions = array_merge($actions, $recommendations['immediate_actions']);
        }

        return $actions;
    }

    private function getEstimatedCosts($assessment)
    {
        return [
            'immediate_actions' => 5000,
            'short_term_improvements' => 15000,
            'long_term_investments' => 50000,
            'total_estimated' => 70000,
        ];
    }

    private function getImplementationTimeline($assessment)
    {
        return [
            'immediate_actions' => '1-3 months',
            'short_term_improvements' => '6-12 months',
            'long_term_investments' => '1-3 years',
        ];
    }

    private function getExpectedBenefits($assessment)
    {
        return [
            'energy_savings' => $assessment->cost_savings,
            'carbon_reduction' => $assessment->energy_consumption_baseline - $assessment->current_consumption,
            'comfort_improvement' => 'Significant',
            'property_value_increase' => '5-10%',
        ];
    }

    private function performEnergyAssessment($propertyId, $data)
    {
        $baseScore = 50;
        
        // Calculate efficiency score based on various factors
        if ($data['heating_efficiency'] ?? false) $baseScore += 10;
        if ($data['cooling_efficiency'] ?? false) $baseScore += 10;
        if ($data['lighting_efficiency'] ?? false) $baseScore += 10;
        if ($data['insulation_quality'] ?? false) $baseScore += 10;
        if ($data['smart_thermostat'] ?? false) $baseScore += 5;
        if ($data['renewable_energy'] ?? false) $baseScore += 5;

        return [
            'property_id' => $propertyId,
            'efficiency_score' => min(100, $baseScore),
            'efficiency_level' => $this->determineEfficiencyLevel($baseScore),
            'assessment_date' => now()->toDateString(),
        ];
    }

    private function determineEfficiencyLevel($score)
    {
        if ($score >= 90) return 'outstanding';
        if ($score >= 80) return 'excellent';
        if ($score >= 70) return 'good';
        if ($score >= 60) return 'fair';
        return 'poor';
    }

    private function calculateEnergySavings($assessment)
    {
        return [
            'current_savings' => $assessment->cost_savings,
            'energy_reduction' => $assessment->getEnergyReduction(),
            'annual_savings' => $assessment->getAnnualCostSavings(),
            'monthly_savings' => $assessment->cost_savings / 12,
        ];
    }

    private function generateEnergyRecommendations($assessment)
    {
        $recommendations = [];
        $score = $assessment->efficiency_score;

        if ($score < 70) {
            $recommendations[] = 'Upgrade to LED lighting';
            $recommendations[] = 'Improve insulation';
            $recommendations[] = 'Install smart thermostat';
        }

        if ($score < 80) {
            $recommendations[] = 'Upgrade HVAC system';
            $recommendations[] = 'Install energy monitoring system';
        }

        if ($score < 90) {
            $recommendations[] = 'Install renewable energy sources';
            $recommendations[] = 'Implement energy storage solutions';
        }

        return $recommendations;
    }

    private function generateEnergyReport($assessment)
    {
        return [
            'report_id' => uniqid('energy_report_'),
            'property_name' => $assessment->property->property_name,
            'assessment_date' => $assessment->assessment_date->toDateString(),
            'efficiency_score' => $assessment->efficiency_score,
            'efficiency_level' => $assessment->efficiency_level,
            'energy_reduction' => $assessment->getEnergyReduction(),
            'cost_savings' => $assessment->cost_savings,
            'recommendations' => $assessment->recommendations,
            'generated_at' => now()->toDateTimeString(),
        ];
    }
}
