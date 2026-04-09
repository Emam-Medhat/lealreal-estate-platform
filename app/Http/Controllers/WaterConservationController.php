<?php

namespace App\Http\Controllers;

use App\Models\WaterConservation;
use App\Models\SmartProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WaterConservationController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_measures' => WaterConservation::count(),
            'active_measures' => WaterConservation::where('status', 'active')->count(),
            'total_water_saved' => WaterConservation::sum('water_saved'),
            'average_conservation' => WaterConservation::avg('conservation_percentage'),
            'total_cost_savings' => WaterConservation::sum('cost_savings'),
            'measures_by_level' => $this->getMeasuresByLevel(),
            'conservation_trends' => $this->getConservationTrends(),
        ];

        $recentMeasures = WaterConservation::with(['property'])
            ->latest()
            ->take(10)
            ->get();

        $topPerformers = $this->getTopPerformers();
        $leakDetection = $this->getLeakDetectionData();

        return view('sustainability.water-conservation-dashboard', compact(
            'stats', 
            'recentMeasures', 
            'topPerformers', 
            'leakDetection'
        ));
    }

    public function index(Request $request)
    {
        $query = WaterConservation::with(['property']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('conservation_level')) {
            $query->where('conservation_level', $request->conservation_level);
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

        if ($request->filled('conservation_min')) {
            $query->where('conservation_percentage', '>=', $request->conservation_min);
        }

        if ($request->filled('conservation_max')) {
            $query->where('conservation_percentage', '<=', $request->conservation_max);
        }

        $measures = $query->latest()->paginate(12);

        $conservationLevels = ['poor', 'fair', 'good', 'excellent', 'outstanding'];
        $statuses = ['active', 'monitoring', 'improving', 'certified'];

        return view('sustainability.water-conservation-index', compact(
            'measures', 
            'conservationLevels', 
            'statuses'
        ));
    }

    public function create()
    {
        $properties = SmartProperty::all();

        return view('sustainability.water-conservation-create', compact(
            'properties'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $conservationData = $request->validate([
                'property_id' => 'required|exists:smart_properties,id',
                'water_consumption_baseline' => 'required|numeric|min:0',
                'current_consumption' => 'required|numeric|min:0',
                'water_saved' => 'required|numeric|min:0',
                'conservation_percentage' => 'required|numeric|min:0|max:100',
                'conservation_measures' => 'nullable|array',
                'water_usage_breakdown' => 'nullable|array',
                'conservation_level' => 'required|in:poor,fair,good,excellent,outstanding',
                'implemented_fixtures' => 'nullable|array',
                'cost_savings' => 'required|numeric|min:0',
                'leak_detection_data' => 'nullable|array',
                'assessment_date' => 'required|date',
                'next_assessment_date' => 'nullable|date|after:assessment_date',
                'status' => 'required|in:active,monitoring,improving,certified',
            ]);

            $conservationData['created_by'] = auth()->id();
            $conservationData['conservation_measures'] = $this->generateConservationMeasures($request);
            $conservationData['water_usage_breakdown'] = $this->generateWaterUsageBreakdown($request);
            $conservationData['implemented_fixtures'] = $this->generateImplementedFixtures($request);
            $conservationData['leak_detection_data'] = $this->generateLeakDetectionData($request);

            $measure = WaterConservation::create($conservationData);

            DB::commit();

            return redirect()
                ->route('water-conservation.show', $measure)
                ->with('success', 'تم إضافة تدابير حفظ المياه بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة التدابير: ' . $e->getMessage());
        }
    }

    public function show(WaterConservation $measure)
    {
        $measure->load(['property']);
        $conservationDetails = $this->getConservationDetails($measure);
        $usageAnalysis = $this->getUsageAnalysis($measure);
        $savingsBreakdown = $this->getSavingsBreakdown($measure);

        return view('sustainability.water-conservation-show', compact(
            'measure', 
            'conservationDetails', 
            'usageAnalysis', 
            'savingsBreakdown'
        ));
    }

    public function edit(WaterConservation $measure)
    {
        $properties = SmartProperty::all();

        return view('sustainability.water-conservation-edit', compact(
            'measure', 
            'properties'
        ));
    }

    public function update(Request $request, WaterConservation $measure)
    {
        DB::beginTransaction();
        try {
            $conservationData = $request->validate([
                'water_consumption_baseline' => 'required|numeric|min:0',
                'current_consumption' => 'required|numeric|min:0',
                'water_saved' => 'required|numeric|min:0',
                'conservation_percentage' => 'required|numeric|min:0|max:100',
                'conservation_measures' => 'nullable|array',
                'water_usage_breakdown' => 'nullable|array',
                'conservation_level' => 'required|in:poor,fair,good,excellent,outstanding',
                'implemented_fixtures' => 'nullable|array',
                'cost_savings' => 'required|numeric|min:0',
                'leak_detection_data' => 'nullable|array',
                'assessment_date' => 'required|date',
                'next_assessment_date' => 'nullable|date|after:assessment_date',
                'status' => 'required|in:active,monitoring,improving,certified',
            ]);

            $conservationData['updated_by'] = auth()->id();
            $conservationData['conservation_measures'] = $this->generateConservationMeasures($request);
            $conservationData['water_usage_breakdown'] = $this->generateWaterUsageBreakdown($request);
            $conservationData['implemented_fixtures'] = $this->generateImplementedFixtures($request);
            $conservationData['leak_detection_data'] = $this->generateLeakDetectionData($request);

            $measure->update($conservationData);

            DB::commit();

            return redirect()
                ->route('water-conservation.show', $measure)
                ->with('success', 'تم تحديث تدابير حفظ المياه بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث التدابير: ' . $e->getMessage());
        }
    }

    public function destroy(WaterConservation $measure)
    {
        try {
            $measure->delete();

            return redirect()
                ->route('water-conservation.index')
                ->with('success', 'تم حذف تدابير حفظ المياه بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف التدابير: ' . $e->getMessage());
        }
    }

    public function detectLeaks(WaterConservation $measure)
    {
        $leakDetection = $this->performLeakDetection($measure);

        return response()->json([
            'success' => true,
            'leak_detection' => $leakDetection
        ]);
    }

    public function calculateSavings(WaterConservation $measure)
    {
        $savings = $this->calculateWaterSavings($measure);

        return response()->json([
            'success' => true,
            'savings' => $savings
        ]);
    }

    public function getRecommendations(WaterConservation $measure)
    {
        $recommendations = $this->generateWaterRecommendations($measure);

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations
        ]);
    }

    public function generateReport(WaterConservation $measure)
    {
        try {
            $reportData = $this->generateWaterReport($measure);
            
            return response()->json([
                'success' => true,
                'report' => $reportData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generateConservationMeasures($request)
    {
        return [
            'low_flow_fixtures' => $request->input('low_flow_fixtures', false),
            'rainwater_harvesting' => $request->input('rainwater_harvesting', false),
            'greywater_recycling' => $request->input('greywater_recycling', false),
            'smart_irrigation' => $request->input('smart_irrigation', false),
            'leak_detection_systems' => $request->input('leak_detection_systems', false),
            'water_metering' => $request->input('water_metering', false),
            'education_programs' => $request->input('education_programs', false),
            'maintenance_schedules' => $request->input('maintenance_schedules', false),
        ];
    }

    private function generateWaterUsageBreakdown($request)
    {
        return [
            'domestic_usage' => $request->input('domestic_usage', 0),
            'commercial_usage' => $request->input('commercial_usage', 0),
            'irrigation_usage' => $request->input('irrigation_usage', 0),
            'cooling_usage' => $request->input('cooling_usage', 0),
            'process_usage' => $request->input('process_usage', 0),
            'other_usage' => $request->input('other_usage', 0),
            'last_updated' => now()->toDateTimeString(),
        ];
    }

    private function generateImplementedFixtures($request)
    {
        return [
            'low_flow_toilets' => $request->input('low_flow_toilets', 0),
            'low_flow_showers' => $request->input('low_flow_showers', 0),
            'low_flow_faucets' => $request->input('low_flow_faucets', 0),
            'waterless_urinals' => $request->input('waterless_urinals', 0),
            'smart_meters' => $request->input('smart_meters', 0),
            'rainwater_tanks' => $request->input('rainwater_tanks', 0),
            'greywater_systems' => $request->input('greywater_systems', 0),
            'leak_sensors' => $request->input('leak_sensors', 0),
        ];
    }

    private function generateLeakDetectionData($request)
    {
        return [
            'leaks_detected' => $request->input('leaks_detected', false),
            'leak_locations' => $request->input('leak_locations', []),
            'leak_severity' => $request->input('leak_severity', 'low'),
            'estimated_water_loss' => $request->input('estimated_water_loss', 0),
            'detection_date' => $request->input('detection_date', now()->toDateString()),
            'repair_status' => $request->input('repair_status', 'pending'),
            'repair_cost' => $request->input('repair_cost', 0),
        ];
    }

    private function getMeasuresByLevel()
    {
        return WaterConservation::select('conservation_level', DB::raw('COUNT(*) as count'))
            ->groupBy('conservation_level')
            ->get();
    }

    private function getConservationTrends()
    {
        return WaterConservation::selectRaw('MONTH(assessment_date) as month, AVG(conservation_percentage) as avg_conservation')
            ->whereYear('assessment_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getTopPerformers()
    {
        return WaterConservation::with(['property'])
            ->select('property_id', DB::raw('AVG(conservation_percentage) as avg_conservation'))
            ->groupBy('property_id')
            ->orderBy('avg_conservation', 'desc')
            ->take(5)
            ->get();
    }

    private function getLeakDetectionData()
    {
        return WaterConservation::with(['property'])
            ->whereRaw('JSON_EXTRACT(leak_detection_data, "$.leaks_detected") = true')
            ->orderBy('assessment_date', 'desc')
            ->take(10)
            ->get();
    }

    private function getConservationDetails($measure)
    {
        return [
            'water_reduction_percentage' => $measure->getWaterReductionPercentage(),
            'annual_water_savings' => $measure->getAnnualWaterSavings(),
            'annual_cost_savings' => $measure->getAnnualCostSavings(),
            'has_leaks' => $measure->hasLeaks(),
            'conservation_grade' => $this->getConservationGrade($measure->conservation_percentage),
            'benchmark_comparison' => $this->getBenchmarkComparison($measure),
        ];
    }

    private function getUsageAnalysis($measure)
    {
        $breakdown = $measure->water_usage_breakdown ?? [];
        
        return [
            'total_usage' => array_sum($breakdown),
            'usage_by_category' => $breakdown,
            'highest_usage_category' => $this->getHighestUsageCategory($breakdown),
            'usage_patterns' => $this->analyzeUsagePatterns($measure),
            'efficiency_rating' => $this->calculateEfficiencyRating($measure),
        ];
    }

    private function getSavingsBreakdown($measure)
    {
        return [
            'water_savings' => $measure->water_saved,
            'cost_savings' => $measure->cost_savings,
            'environmental_impact' => $this->calculateEnvironmentalImpact($measure),
            'roi_percentage' => $this->calculateROI($measure),
            'payback_period' => $this->calculatePaybackPeriod($measure),
        ];
    }

    private function getConservationGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 85) return 'A';
        if ($percentage >= 80) return 'B+';
        if ($percentage >= 75) return 'B';
        if ($percentage >= 70) return 'C+';
        if ($percentage >= 65) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }

    private function getBenchmarkComparison($measure)
    {
        $averageConservation = WaterConservation::avg('conservation_percentage');
        
        return [
            'property_conservation' => $measure->conservation_percentage,
            'industry_average' => $averageConservation,
            'percentile' => $this->calculatePercentile($measure->conservation_percentage),
            'performance_rating' => $measure->conservation_percentage > $averageConservation ? 'Above Average' : 'Below Average',
        ];
    }

    private function calculatePercentile($percentage)
    {
        $total = WaterConservation::count();
        $better = WaterConservation::where('conservation_percentage', '>', $percentage)->count();
        return (($total - $better) / $total) * 100;
    }

    private function getHighestUsageCategory($breakdown)
    {
        return array_keys($breakdown, max($breakdown))[0] ?? 'none';
    }

    private function analyzeUsagePatterns($measure)
    {
        return [
            'peak_usage_times' => ['morning', 'evening'],
            'seasonal_variations' => 'higher in summer',
            'usage_trend' => 'decreasing',
            'efficiency_trend' => 'improving',
        ];
    }

    private function calculateEfficiencyRating($measure)
    {
        $reduction = $measure->getWaterReductionPercentage();
        
        if ($reduction >= 50) return 'Excellent';
        if ($reduction >= 35) return 'Good';
        if ($reduction >= 20) return 'Fair';
        return 'Poor';
    }

    private function calculateEnvironmentalImpact($measure)
    {
        $waterSaved = $measure->getAnnualWaterSavings();
        
        return [
            'water_saved_liters' => $waterSaved,
            'energy_saved_kwh' => $waterSaved * 0.003, // Energy for water treatment
            'carbon_saved_kg' => $waterSaved * 0.0005, // Carbon for water treatment
            'equivalent_showers' => $waterSaved / 65, // 65 liters per shower
        ];
    }

    private function calculateROI($measure)
    {
        $annualSavings = $measure->cost_savings;
        $estimatedCost = 5000; // Estimated implementation cost
        
        return $estimatedCost > 0 ? ($annualSavings / $estimatedCost) * 100 : 0;
    }

    private function calculatePaybackPeriod($measure)
    {
        $annualSavings = $measure->cost_savings;
        $estimatedCost = 5000;
        
        return $annualSavings > 0 ? $estimatedCost / $annualSavings : 0;
    }

    private function performLeakDetection($measure)
    {
        return [
            'detection_id' => uniqid('leak_detect_'),
            'property_id' => $measure->property_id,
            'detection_date' => now()->toDateString(),
            'leaks_found' => $measure->hasLeaks(),
            'leak_details' => $measure->leak_detection_data ?? [],
            'estimated_water_loss' => $measure->leak_detection_data['estimated_water_loss'] ?? 0,
            'recommended_actions' => $this->getLeakRepairRecommendations($measure),
        ];
    }

    private function getLeakRepairRecommendations($measure)
    {
        $recommendations = [];
        
        if ($measure->hasLeaks()) {
            $recommendations[] = 'Immediate inspection required';
            $recommendations[] = 'Repair identified leaks';
            $recommendations[] = 'Install leak detection sensors';
        }
        
        return $recommendations;
    }

    private function calculateWaterSavings($measure)
    {
        return [
            'daily_savings' => $measure->water_saved / 365,
            'monthly_savings' => $measure->water_saved / 12,
            'annual_savings' => $measure->water_saved,
            'cost_savings' => $measure->cost_savings,
            'conservation_percentage' => $measure->getWaterReductionPercentage(),
        ];
    }

    private function generateWaterRecommendations($measure)
    {
        $recommendations = [];
        $conservation = $measure->conservation_percentage;

        if ($conservation < 30) {
            $recommendations[] = 'Install low-flow fixtures';
            $recommendations[] = 'Implement leak detection system';
            $recommendations[] = 'Educate occupants on water conservation';
        }

        if ($conservation < 50) {
            $recommendations[] = 'Install rainwater harvesting system';
            $recommendations[] = 'Implement greywater recycling';
            $recommendations[] = 'Upgrade to smart irrigation';
        }

        if ($conservation < 70) {
            $recommendations[] = 'Install water metering systems';
            $recommendations[] = 'Implement water recycling programs';
            $recommendations[] = 'Upgrade water-efficient appliances';
        }

        return $recommendations;
    }

    private function generateWaterReport($measure)
    {
        return [
            'report_id' => uniqid('water_report_'),
            'property_name' => $measure->property->property_name,
            'assessment_date' => $measure->assessment_date->toDateString(),
            'conservation_percentage' => $measure->conservation_percentage,
            'water_saved' => $measure->water_saved,
            'cost_savings' => $measure->cost_savings,
            'conservation_level' => $measure->conservation_level,
            'implemented_measures' => $measure->conservation_measures,
            'leak_detection' => $measure->leak_detection_data,
            'generated_at' => now()->toDateTimeString(),
        ];
    }
}
