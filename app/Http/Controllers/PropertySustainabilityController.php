<?php

namespace App\Http\Controllers;

use App\Models\PropertySustainability;
use App\Models\SmartProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PropertySustainabilityController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_properties' => PropertySustainability::count(),
            'certified_properties' => PropertySustainability::where('status', 'certified')->count(),
            'average_eco_score' => PropertySustainability::avg('eco_score'),
            'properties_by_level' => $this->getPropertiesByLevel(),
            'total_carbon_reduction' => $this->getTotalCarbonReduction(),
            'energy_efficiency_improvement' => $this->getEnergyEfficiencyImprovement(),
        ];

        $recentProperties = PropertySustainability::with(['property', 'carbonFootprints', 'energyEfficiency'])
            ->latest()
            ->take(10)
            ->get();

        $sustainabilityTrends = $this->getSustainabilityTrends();
        $levelDistribution = $this->getLevelDistribution();

        return view('sustainability.property-dashboard', compact(
            'stats', 
            'recentProperties', 
            'sustainabilityTrends', 
            'levelDistribution'
        ));
    }

    public function index(Request $request)
    {
        $query = PropertySustainability::with(['property', 'carbonFootprints', 'energyEfficiency']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('sustainability_level')) {
            $query->where('sustainability_level', $request->sustainability_level);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('eco_score_min')) {
            $query->where('eco_score', '>=', $request->eco_score_min);
        }

        if ($request->filled('eco_score_max')) {
            $query->where('eco_score', '<=', $request->eco_score_max);
        }

        $properties = $query->latest()->paginate(12);

        $sustainabilityLevels = ['basic', 'intermediate', 'advanced', 'excellent'];
        $statuses = ['active', 'inactive', 'pending', 'certified'];

        return view('sustainability.index', compact(
            'properties', 
            'sustainabilityLevels', 
            'statuses'
        ));
    }

    public function create()
    {
        $properties = SmartProperty::all();
        $sustainabilityLevels = ['basic', 'intermediate', 'advanced', 'excellent'];

        return view('sustainability.create', compact(
            'properties', 
            'sustainabilityLevels'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $sustainabilityData = $request->validate([
                'property_id' => 'required|exists:smart_properties,id',
                'eco_score' => 'required|numeric|min:0|max:100',
                'carbon_footprint' => 'required|numeric|min:0',
                'energy_efficiency' => 'required|numeric|min:0|max:100',
                'water_efficiency' => 'required|numeric|min:0|max:100',
                'waste_reduction' => 'required|numeric|min:0|max:100',
                'sustainability_metrics' => 'nullable|array',
                'certifications' => 'nullable|array',
                'sustainability_level' => 'required|in:basic,intermediate,advanced,excellent',
                'last_assessment_date' => 'required|date',
                'next_assessment_date' => 'nullable|date|after:last_assessment_date',
                'status' => 'required|in:active,inactive,pending,certified',
            ]);

            $sustainabilityData['created_by'] = auth()->id();
            $sustainabilityData['sustainability_metrics'] = $this->generateSustainabilityMetrics($request);
            $sustainabilityData['next_assessment_date'] = $this->calculateNextAssessmentDate($request);

            $sustainability = PropertySustainability::create($sustainabilityData);

            DB::commit();

            return redirect()
                ->route('sustainability.show', $sustainability)
                ->with('success', 'تم إضافة تقييم الاستدامة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة التقييم: ' . $e->getMessage());
        }
    }

    public function show(PropertySustainability $sustainability)
    {
        $sustainability->load(['property', 'carbonFootprints', 'energyEfficiency', 'waterConservation', 'sustainableMaterials']);
        $performanceMetrics = $this->getPropertyPerformanceMetrics($sustainability);
        $recommendations = $this->getPropertyRecommendations($sustainability);

        return view('sustainability.show', compact(
            'sustainability', 
            'performanceMetrics', 
            'recommendations'
        ));
    }

    public function edit(PropertySustainability $sustainability)
    {
        $properties = SmartProperty::all();
        $sustainabilityLevels = ['basic', 'intermediate', 'advanced', 'excellent'];

        return view('sustainability.edit', compact(
            'sustainability', 
            'properties', 
            'sustainabilityLevels'
        ));
    }

    public function update(Request $request, PropertySustainability $sustainability)
    {
        DB::beginTransaction();
        try {
            $sustainabilityData = $request->validate([
                'eco_score' => 'required|numeric|min:0|max:100',
                'carbon_footprint' => 'required|numeric|min:0',
                'energy_efficiency' => 'required|numeric|min:0|max:100',
                'water_efficiency' => 'required|numeric|min:0|max:100',
                'waste_reduction' => 'required|numeric|min:0|max:100',
                'sustainability_metrics' => 'nullable|array',
                'certifications' => 'nullable|array',
                'sustainability_level' => 'required|in:basic,intermediate,advanced,excellent',
                'last_assessment_date' => 'required|date',
                'next_assessment_date' => 'nullable|date|after:last_assessment_date',
                'status' => 'required|in:active,inactive,pending,certified',
            ]);

            $sustainabilityData['updated_by'] = auth()->id();
            $sustainabilityData['sustainability_metrics'] = $this->generateSustainabilityMetrics($request);
            $sustainabilityData['next_assessment_date'] = $this->calculateNextAssessmentDate($request);

            $sustainability->update($sustainabilityData);

            DB::commit();

            return redirect()
                ->route('sustainability.show', $sustainability)
                ->with('success', 'تم تحديث تقييم الاستدامة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث التقييم: ' . $e->getMessage());
        }
    }

    public function destroy(PropertySustainability $sustainability)
    {
        try {
            $sustainability->delete();

            return redirect()
                ->route('sustainability.index')
                ->with('success', 'تم حذف تقييم الاستدامة بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف التقييم: ' . $e->getMessage());
        }
    }

    public function getAnalytics(Request $request)
    {
        $propertyId = $request->input('property_id');
        $period = $request->input('period', 'month');

        $analytics = $this->generateSustainabilityAnalytics($propertyId, $period);

        return response()->json($analytics);
    }

    public function generateCertificate(PropertySustainability $sustainability)
    {
        try {
            $certificateData = $this->generateCertificateData($sustainability);
            
            return response()->json([
                'success' => true,
                'certificate' => $certificateData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function bulkAssess(Request $request)
    {
        $propertyIds = $request->input('property_ids', []);
        $results = [];

        foreach ($propertyIds as $propertyId) {
            $property = SmartProperty::find($propertyId);
            if ($property) {
                $assessment = $this->performPropertyAssessment($property);
                $results[] = $assessment;
            }
        }

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }

    private function generateSustainabilityMetrics($request)
    {
        return [
            'energy_consumption' => $request->input('energy_consumption', 0),
            'water_usage' => $request->input('water_usage', 0),
            'waste_generation' => $request->input('waste_generation', 0),
            'renewable_energy_percentage' => $request->input('renewable_energy_percentage', 0),
            'sustainable_materials_percentage' => $request->input('sustainable_materials_percentage', 0),
            'last_updated' => now()->toDateTimeString(),
        ];
    }

    private function calculateNextAssessmentDate($request)
    {
        $level = $request->input('sustainability_level');
        $months = match($level) {
            'basic' => 12,
            'intermediate' => 9,
            'advanced' => 6,
            'excellent' => 3,
            default => 12,
        };

        return now()->addMonths($months);
    }

    private function getPropertiesByLevel()
    {
        return PropertySustainability::select('sustainability_level', DB::raw('COUNT(*) as count'))
            ->groupBy('sustainability_level')
            ->get();
    }

    private function getTotalCarbonReduction()
    {
        return PropertySustainability::sum('carbon_footprint');
    }

    private function getEnergyEfficiencyImprovement()
    {
        return PropertySustainability::avg('energy_efficiency');
    }

    private function getSustainabilityTrends()
    {
        return PropertySustainability::selectRaw('DATE(created_at) as date, AVG(eco_score) as avg_score')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(12)
            ->get();
    }

    private function getLevelDistribution()
    {
        return PropertySustainability::select('sustainability_level', DB::raw('COUNT(*) as count'))
            ->groupBy('sustainability_level')
            ->get();
    }

    private function getPropertyPerformanceMetrics($sustainability)
    {
        return [
            'eco_score_trend' => $this->getEcoScoreTrend($sustainability),
            'carbon_reduction_trend' => $this->getCarbonReductionTrend($sustainability),
            'efficiency_improvement' => $this->getEfficiencyImprovementTrend($sustainability),
            'benchmark_comparison' => $this->getBenchmarkComparison($sustainability),
        ];
    }

    private function getPropertyRecommendations($sustainability)
    {
        $recommendations = [];

        if ($sustainability->eco_score < 70) {
            $recommendations[] = 'زيادة كفاءة الطاقة';
        }

        if ($sustainability->energy_efficiency < 70) {
            $recommendations[] = 'تحسين عزل المبنى';
        }

        if ($sustainability->water_efficiency < 70) {
            $recommendations[] = 'تركيب أجهزة حفظ المياه';
        }

        if ($sustainability->waste_reduction < 70) {
            $recommendations[] = 'برنامج إعادة التدوير';
        }

        return $recommendations;
    }

    private function generateSustainabilityAnalytics($propertyId, $period)
    {
        $query = PropertySustainability::where('property_id', $propertyId);

        switch ($period) {
            case 'week':
                $query->where('created_at', '>=', now()->subWeek());
                break;
            case 'month':
                $query->where('created_at', '>=', now()->subMonth());
                break;
            case 'year':
                $query->where('created_at', '>=', now()->subYear());
                break;
        }

        return $query->selectRaw('DATE(created_at) as date, AVG(eco_score) as avg_eco_score, AVG(energy_efficiency) as avg_energy_efficiency')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'desc')
            ->get();
    }

    private function generateCertificateData($sustainability)
    {
        return [
            'certificate_id' => uniqid('cert_'),
            'property_name' => $sustainability->property->property_name,
            'eco_score' => $sustainability->eco_score,
            'sustainability_level' => $sustainability->sustainability_level,
            'certification_date' => now()->toDateString(),
            'expiry_date' => now()->addYear()->toDateString(),
            'issued_by' => auth()->user()->name,
            'verification_code' => strtoupper(uniqid('VER_')),
        ];
    }

    private function performPropertyAssessment($property)
    {
        $baseScore = 50;
        
        // Calculate based on property features
        if ($property->energy_efficiency) $baseScore += 15;
        if ($property->water_efficiency) $baseScore += 10;
        if ($property->waste_management) $baseScore += 10;
        if ($property->green_spaces) $baseScore += 10;
        if ($property->sustainable_materials) $baseScore += 5;

        return [
            'property_id' => $property->id,
            'property_name' => $property->property_name,
            'eco_score' => min(100, $baseScore),
            'sustainability_level' => $this->determineSustainabilityLevel($baseScore),
            'assessment_date' => now()->toDateString(),
        ];
    }

    private function determineSustainabilityLevel($score)
    {
        if ($score >= 85) return 'excellent';
        if ($score >= 70) return 'advanced';
        if ($score >= 55) return 'intermediate';
        return 'basic';
    }

    private function getEcoScoreTrend($sustainability)
    {
        // Calculate trend based on historical data
        return 'improving';
    }

    private function getCarbonReductionTrend($sustainability)
    {
        return 'stable';
    }

    private function getEfficiencyImprovementTrend($sustainability)
    {
        return 'improving';
    }

    private function getBenchmarkComparison($sustainability)
    {
        return [
            'property_score' => $sustainability->eco_score,
            'average_score' => PropertySustainability::avg('eco_score'),
            'percentile' => $this->calculatePercentile($sustainability->eco_score),
        ];
    }

    private function calculatePercentile($score)
    {
        $total = PropertySustainability::count();
        $better = PropertySustainability::where('eco_score', '>', $score)->count();
        return (($total - $better) / $total) * 100;
    }
}
