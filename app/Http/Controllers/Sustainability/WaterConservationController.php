<?php

namespace App\Http\Controllers\Sustainability;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Sustainability\PropertySustainability;
use App\Models\Sustainability\WaterConservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WaterConservationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_sustainability');
    }

    public function index()
    {
        $waterConservations = WaterConservation::with(['propertySustainability.property'])
            ->when(Auth::user()->hasRole('agent'), function($query) {
                $query->whereHas('propertySustainability.property', function($q) {
                    $q->where('agent_id', Auth::id());
                });
            })
            ->latest('assessment_date')
            ->paginate(15);

        $stats = [
            'total_assessments' => WaterConservation::count(),
            'average_efficiency' => WaterConservation::avg('water_efficiency_rating'),
            'high_efficiency_properties' => WaterConservation::where('water_efficiency_rating', '>=', 80)->count(),
            'properties_with_rainwater' => WaterConservation::where('rainwater_harvesting', true)->count(),
        ];

        return view('sustainability.water-conservation.index', compact('waterConservations', 'stats'));
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

        return view('sustainability.water-conservation.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_sustainability_id' => 'required|exists:property_sustainability,id',
            'water_consumption' => 'required|numeric|min:0',
            'consumption_unit' => 'required|string|in:liters_per_day,gallons_per_day,cubic_meters_per_month',
            'water_efficiency_rating' => 'required|integer|min:1|max:100',
            'rainwater_harvesting' => 'required|boolean',
            'rainwater_capacity' => 'nullable|numeric|min:0',
            'greywater_recycling' => 'required|boolean',
            'greywater_capacity' => 'nullable|numeric|min:0',
            'low_flow_fixtures' => 'required|boolean',
            'fixture_types' => 'nullable|array',
            'fixture_types.*' => 'string',
            'smart_irrigation' => 'required|boolean',
            'irrigation_type' => 'nullable|string',
            'drip_irrigation' => 'required|boolean',
            'xeriscaping' => 'required|boolean',
            'native_plants' => 'required|boolean',
            'leak_detection_system' => 'required|boolean',
            'water_metering' => 'required|boolean',
            'water_pressure_optimization' => 'required|boolean',
            'hot_water_efficiency' => 'required|boolean',
            'hot_water_system_type' => 'nullable|string',
            'pool_cover' => 'required|boolean',
            'pool_recycling_system' => 'required|boolean',
            'water_treatment_system' => 'required|boolean',
            'treatment_type' => 'nullable|string',
            'conservation_goals' => 'nullable|array',
            'conservation_goals.*' => 'string',
            'monitoring_frequency' => 'required|string|in:daily,weekly,monthly,quarterly',
            'assessment_date' => 'required|date|before_or_equal:today',
            'next_assessment_date' => 'required|date|after:assessment_date',
            'notes' => 'nullable|string',
        ]);

        // Calculate water efficiency rating if not provided
        if (!isset($validated['water_efficiency_rating'])) {
            $validated['water_efficiency_rating'] = $this->calculateWaterEfficiency($validated);
        }

        $waterConservation = WaterConservation::create([
            'property_sustainability_id' => $validated['property_sustainability_id'],
            'water_consumption' => $validated['water_consumption'],
            'consumption_unit' => $validated['consumption_unit'],
            'water_efficiency_rating' => $validated['water_efficiency_rating'],
            'rainwater_harvesting' => $validated['rainwater_harvesting'],
            'rainwater_capacity' => $validated['rainwater_capacity'] ?? 0,
            'greywater_recycling' => $validated['greywater_recycling'],
            'greywater_capacity' => $validated['greywater_capacity'] ?? 0,
            'low_flow_fixtures' => $validated['low_flow_fixtures'],
            'fixture_types' => $validated['fixture_types'] ?? [],
            'smart_irrigation' => $validated['smart_irrigation'],
            'irrigation_type' => $validated['irrigation_type'],
            'drip_irrigation' => $validated['drip_irrigation'],
            'xeriscaping' => $validated['xeriscaping'],
            'native_plants' => $validated['native_plants'],
            'leak_detection_system' => $validated['leak_detection_system'],
            'water_metering' => $validated['water_metering'],
            'water_pressure_optimization' => $validated['water_pressure_optimization'],
            'hot_water_efficiency' => $validated['hot_water_efficiency'],
            'hot_water_system_type' => $validated['hot_water_system_type'],
            'pool_cover' => $validated['pool_cover'],
            'pool_recycling_system' => $validated['pool_recycling_system'],
            'water_treatment_system' => $validated['water_treatment_system'],
            'treatment_type' => $validated['treatment_type'],
            'conservation_goals' => $validated['conservation_goals'] ?? [],
            'monitoring_frequency' => $validated['monitoring_frequency'],
            'assessment_date' => $validated['assessment_date'],
            'next_assessment_date' => $validated['next_assessment_date'],
            'assessed_by' => Auth::id(),
            'potential_savings' => $this->calculatePotentialSavings($validated),
            'recommendations' => $this->generateRecommendations($validated),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update property sustainability water efficiency rating
        $propertySustainability = PropertySustainability::find($validated['property_sustainability_id']);
        $propertySustainability->update(['water_efficiency_rating' => $validated['water_efficiency_rating']]);

        return redirect()
            ->route('water-conservation.show', $waterConservation)
            ->with('success', 'تم تقييم حفظ المياه بنجاح');
    }

    public function show(WaterConservation $waterConservation)
    {
        $waterConservation->load(['propertySustainability.property']);
        
        // Get historical data for comparison
        $historicalData = WaterConservation::where('property_sustainability_id', $waterConservation->property_sustainability_id)
            ->where('id', '!=', $waterConservation->id)
            ->orderBy('assessment_date', 'desc')
            ->take(12)
            ->get();

        // Benchmark against similar properties
        $benchmark = $this->getBenchmarkData($waterConservation);

        return view('sustainability.water-conservation.show', compact('waterConservation', 'historicalData', 'benchmark'));
    }

    public function edit(WaterConservation $waterConservation)
    {
        $waterConservation->load('propertySustainability.property');
        return view('sustainability.water-conservation.edit', compact('waterConservation'));
    }

    public function update(Request $request, WaterConservation $waterConservation)
    {
        $validated = $request->validate([
            'water_consumption' => 'required|numeric|min:0',
            'consumption_unit' => 'required|string|in:liters_per_day,gallons_per_day,cubic_meters_per_month',
            'water_efficiency_rating' => 'required|integer|min:1|max:100',
            'rainwater_harvesting' => 'required|boolean',
            'rainwater_capacity' => 'nullable|numeric|min:0',
            'greywater_recycling' => 'required|boolean',
            'greywater_capacity' => 'nullable|numeric|min:0',
            'low_flow_fixtures' => 'required|boolean',
            'fixture_types' => 'nullable|array',
            'fixture_types.*' => 'string',
            'smart_irrigation' => 'required|boolean',
            'irrigation_type' => 'nullable|string',
            'drip_irrigation' => 'required|boolean',
            'xeriscaping' => 'required|boolean',
            'native_plants' => 'required|boolean',
            'leak_detection_system' => 'required|boolean',
            'water_metering' => 'required|boolean',
            'water_pressure_optimization' => 'required|boolean',
            'hot_water_efficiency' => 'required|boolean',
            'hot_water_system_type' => 'nullable|string',
            'pool_cover' => 'required|boolean',
            'pool_recycling_system' => 'required|boolean',
            'water_treatment_system' => 'required|boolean',
            'treatment_type' => 'nullable|string',
            'conservation_goals' => 'nullable|array',
            'conservation_goals.*' => 'string',
            'monitoring_frequency' => 'required|string|in:daily,weekly,monthly,quarterly',
            'next_assessment_date' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);

        $waterConservation->update([
            'water_consumption' => $validated['water_consumption'],
            'consumption_unit' => $validated['consumption_unit'],
            'water_efficiency_rating' => $validated['water_efficiency_rating'],
            'rainwater_harvesting' => $validated['rainwater_harvesting'],
            'rainwater_capacity' => $validated['rainwater_capacity'] ?? 0,
            'greywater_recycling' => $validated['greywater_recycling'],
            'greywater_capacity' => $validated['greywater_capacity'] ?? 0,
            'low_flow_fixtures' => $validated['low_flow_fixtures'],
            'fixture_types' => $validated['fixture_types'] ?? [],
            'smart_irrigation' => $validated['smart_irrigation'],
            'irrigation_type' => $validated['irrigation_type'],
            'drip_irrigation' => $validated['drip_irrigation'],
            'xeriscaping' => $validated['xeriscaping'],
            'native_plants' => $validated['native_plants'],
            'leak_detection_system' => $validated['leak_detection_system'],
            'water_metering' => $validated['water_metering'],
            'water_pressure_optimization' => $validated['water_pressure_optimization'],
            'hot_water_efficiency' => $validated['hot_water_efficiency'],
            'hot_water_system_type' => $validated['hot_water_system_type'],
            'pool_cover' => $validated['pool_cover'],
            'pool_recycling_system' => $validated['pool_recycling_system'],
            'water_treatment_system' => $validated['water_treatment_system'],
            'treatment_type' => $validated['treatment_type'],
            'conservation_goals' => $validated['conservation_goals'] ?? [],
            'monitoring_frequency' => $validated['monitoring_frequency'],
            'next_assessment_date' => $validated['next_assessment_date'],
            'potential_savings' => $this->calculatePotentialSavings($validated),
            'recommendations' => $this->generateRecommendations($validated),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update property sustainability
        $propertySustainability = PropertySustainability::find($validated['property_sustainability_id']);
        $propertySustainability->update(['water_efficiency_rating' => $validated['water_efficiency_rating']]);

        return redirect()
            ->route('water-conservation.show', $waterConservation)
            ->with('success', 'تم تحديث تقييم حفظ المياه بنجاح');
    }

    public function destroy(WaterConservation $waterConservation)
    {
        $waterConservation->delete();

        return redirect()
            ->route('water-conservation.index')
            ->with('success', 'تم حذف تقييم حفظ المياه بنجاح');
    }

    public function calculator()
    {
        return view('sustainability.water-conservation.calculator');
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'property_size' => 'required|numeric|min:0',
            'occupants' => 'required|integer|min:1',
            'water_consumption' => 'required|numeric|min:0',
            'rainwater_harvesting' => 'required|boolean',
            'greywater_recycling' => 'required|boolean',
            'low_flow_fixtures' => 'required|boolean',
            'smart_irrigation' => 'required|boolean',
            'drip_irrigation' => 'required|boolean',
        ]);

        // Quick calculation for demo purposes
        $efficiencyRating = $this->quickEfficiencyCalculation($validated);
        $potentialSavings = $this->quickSavingsCalculation($validated);

        return response()->json([
            'efficiency_rating' => round($efficiencyRating, 1),
            'potential_savings' => round($potentialSavings, 2),
            'recommendations' => $this->getQuickRecommendations($validated, $efficiencyRating),
        ]);
    }

    public function analytics()
    {
        $monthlyTrends = WaterConservation::selectRaw('DATE_FORMAT(assessment_date, "%Y-%m") as month, AVG(water_efficiency_rating) as avg_rating, COUNT(*) as count')
            ->where('assessment_date', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $efficiencyByPropertyType = WaterConservation::join('property_sustainability', 'water_conservation.property_sustainability_id', '=', 'property_sustainability.id')
            ->join('properties', 'property_sustainability.property_id', '=', 'properties.id')
            ->selectRaw('properties.type, AVG(water_conservation.water_efficiency_rating) as avg_rating, COUNT(*) as count')
            ->groupBy('properties.type')
            ->get();

        $conservationMethods = WaterConservation::selectRaw('
            SUM(CASE WHEN rainwater_harvesting = 1 THEN 1 ELSE 0 END) as rainwater_count,
            SUM(CASE WHEN greywater_recycling = 1 THEN 1 ELSE 0 END) as greywater_count,
            SUM(CASE WHEN low_flow_fixtures = 1 THEN 1 ELSE 0 END) as low_flow_count,
            SUM(CASE WHEN smart_irrigation = 1 THEN 1 ELSE 0 END) as smart_irrigation_count
        ')->first();

        $topPerforming = WaterConservation::with(['propertySustainability.property'])
            ->orderBy('water_efficiency_rating', 'desc')
            ->take(10)
            ->get();

        return view('sustainability.water-conservation.analytics', compact(
            'monthlyTrends',
            'efficiencyByPropertyType',
            'conservationMethods',
            'topPerforming'
        ));
    }

    public function conservationPlan(WaterConservation $waterConservation)
    {
        $conservationPlan = $this->generateConservationPlan($waterConservation);
        
        return view('sustainability.water-conservation.conservation-plan', compact('waterConservation', 'conservationPlan'));
    }

    private function calculateWaterEfficiency($data)
    {
        $baseScore = 50;
        
        // Adjust based on various conservation measures
        if ($data['rainwater_harvesting']) $baseScore += 15;
        if ($data['greywater_recycling']) $baseScore += 15;
        if ($data['low_flow_fixtures']) $baseScore += 10;
        if ($data['smart_irrigation']) $baseScore += 10;
        if ($data['drip_irrigation']) $baseScore += 10;
        if ($data['xeriscaping']) $baseScore += 8;
        if ($data['native_plants']) $baseScore += 5;
        if ($data['leak_detection_system']) $baseScore += 7;
        if ($data['water_metering']) $baseScore += 5;
        if ($data['water_pressure_optimization']) $baseScore += 5;
        
        // Penalty for high consumption
        $consumption = $this->normalizeConsumption($data['water_consumption'], $data['consumption_unit']);
        if ($consumption > 500) { // liters per day
            $baseScore -= min(20, ($consumption - 500) / 50);
        }

        return max(0, min(100, $baseScore));
    }

    private function calculatePotentialSavings($data)
    {
        $currentConsumption = $this->normalizeConsumption($data['water_consumption'], $data['consumption_unit']);
        $potentialSavings = 0;

        // Calculate potential savings from improvements
        if (!$data['rainwater_harvesting']) {
            $potentialSavings += $currentConsumption * 0.30; // 30% savings from rainwater
        }

        if (!$data['greywater_recycling']) {
            $potentialSavings += $currentConsumption * 0.25; // 25% savings from greywater
        }

        if (!$data['low_flow_fixtures']) {
            $potentialSavings += $currentConsumption * 0.20; // 20% savings from low flow fixtures
        }

        if (!$data['smart_irrigation']) {
            $potentialSavings += $currentConsumption * 0.15; // 15% savings from smart irrigation
        }

        return round($potentialSavings, 2);
    }

    private function generateRecommendations($data)
    {
        $recommendations = [];

        if (!$data['rainwater_harvesting']) {
            $recommendations[] = 'تركيب نظام تجميع مياه الأمطار يمكن أن يوفر 30% من استهلاك المياه';
        }

        if (!$data['greywater_recycling']) {
            $recommendations[] = 'إعادة تدوير المياه الرمادية يمكن أن توفر 25% من استهلاك المياه';
        }

        if (!$data['low_flow_fixtures']) {
            $recommendations[] = 'تركيب أجهزة منخفضة التدفق يمكن أن توفر 20% من استهلاك المياه';
        }

        if (!$data['smart_irrigation']) {
            $recommendations[] = 'استخدام الري الذكي يمكن أن يوفر 15% من استهلاك المياه';
        }

        if (!$data['leak_detection_system']) {
            $recommendations[] = 'تركيب نظام كشف التسريبات يمكن أن يمنع فقدان المياه';
        }

        return $recommendations;
    }

    private function getBenchmarkData($waterConservation)
    {
        $propertyType = $waterConservation->propertySustainability->property->type;
        
        return WaterConservation::join('property_sustainability', 'water_conservation.property_sustainability_id', '=', 'property_sustainability.id')
            ->join('properties', 'property_sustainability.property_id', '=', 'properties.id')
            ->where('properties.type', $propertyType)
            ->where('water_conservation.id', '!=', $waterConservation->id)
            ->selectRaw('AVG(water_conservation.water_efficiency_rating) as avg_rating, MIN(water_conservation.water_efficiency_rating) as min_rating, MAX(water_conservation.water_efficiency_rating) as max_rating, COUNT(*) as count')
            ->first();
    }

    private function normalizeConsumption($consumption, $unit)
    {
        switch ($unit) {
            case 'gallons_per_day':
                return $consumption * 3.785; // Convert to liters
            case 'cubic_meters_per_month':
                return ($consumption * 1000) / 30; // Convert to liters per day
            default:
                return $consumption; // Already in liters per day
        }
    }

    private function quickEfficiencyCalculation($data)
    {
        $baseScore = 50;
        
        if ($data['rainwater_harvesting']) $baseScore += 15;
        if ($data['greywater_recycling']) $baseScore += 15;
        if ($data['low_flow_fixtures']) $baseScore += 10;
        if ($data['smart_irrigation']) $baseScore += 10;
        if ($data['drip_irrigation']) $baseScore += 10;
        
        // Penalty for high consumption (assuming liters per day)
        if ($data['water_consumption'] > 500) {
            $baseScore -= 15;
        }

        return max(0, min(100, $baseScore));
    }

    private function quickSavingsCalculation($data)
    {
        $currentConsumption = $data['water_consumption'];
        $potentialSavings = 0;

        if (!$data['rainwater_harvesting']) {
            $potentialSavings += $currentConsumption * 0.30;
        }

        if (!$data['greywater_recycling']) {
            $potentialSavings += $currentConsumption * 0.25;
        }

        if (!$data['low_flow_fixtures']) {
            $potentialSavings += $currentConsumption * 0.20;
        }

        return $potentialSavings;
    }

    private function getQuickRecommendations($data, $rating)
    {
        $recommendations = [];

        if ($rating < 70) {
            $recommendations[] = 'تحسين كفاءة استخدام المياه أمر ضروري';
        }

        if (!$data['rainwater_harvesting']) {
            $recommendations[] = 'فكر في تركيب نظام تجميع مياه الأمطار';
        }

        if (!$data['greywater_recycling']) {
            $recommendations[] = 'إعادة تدوير المياه الرمادية يمكن أن توفر الكثير';
        }

        return $recommendations;
    }

    private function generateConservationPlan($waterConservation)
    {
        $plan = [];
        $rating = $waterConservation->water_efficiency_rating;

        if ($rating < 40) {
            $plan[] = [
                'priority' => 'عاجل',
                'action' => 'تركيب نظام تجميع مياه الأمطار',
                'cost' => 'متوسط',
                'savings' => '30%',
                'timeline' => '1-2 أشهر',
            ];
        }

        if ($rating < 60) {
            $plan[] = [
                'priority' => 'مرتفع',
                'action' => 'تركيب نظام إعادة تدوير المياه الرمادية',
                'cost' => 'مرتفع',
                'savings' => '25%',
                'timeline' => '2-3 أشهر',
            ];
        }

        if (!$waterConservation->low_flow_fixtures) {
            $plan[] = [
                'priority' => 'متوسط',
                'action' => 'تركيب أجهزة منخفضة التدفق',
                'cost' => 'منخفض',
                'savings' => '20%',
                'timeline' => '1-2 أسابيع',
            ];
        }

        return $plan;
    }
}
