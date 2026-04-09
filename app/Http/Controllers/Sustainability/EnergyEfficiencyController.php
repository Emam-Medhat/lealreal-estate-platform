<?php

namespace App\Http\Controllers\Sustainability;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Sustainability\PropertySustainability;
use App\Models\Sustainability\EnergyEfficiency;
use App\Http\Requests\Sustainability\AssessEnergyEfficiencyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EnergyEfficiencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_sustainability');
    }

    public function index()
    {
        $energyEfficiencies = EnergyEfficiency::with(['propertySustainability.property'])
            ->when(Auth::user()->hasRole('agent'), function($query) {
                $query->whereHas('propertySustainability.property', function($q) {
                    $q->where('agent_id', Auth::id());
                });
            })
            ->latest('assessment_date')
            ->paginate(15);

        $stats = [
            'total_assessments' => EnergyEfficiency::count(),
            'average_rating' => EnergyEfficiency::avg('efficiency_rating'),
            'high_efficiency_properties' => EnergyEfficiency::where('efficiency_rating', '>=', 80)->count(),
            'properties_with_renewable_energy' => EnergyEfficiency::where('renewable_energy_percentage', '>', 0)->count(),
        ];

        return view('sustainability.energy-efficiency.index', compact('energyEfficiencies', 'stats'));
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

        return view('sustainability.energy-efficiency.create', compact('properties'));
    }

    public function store(AssessEnergyEfficiencyRequest $request)
    {
        $validated = $request->validated();

        // Calculate energy efficiency rating
        $efficiencyRating = $this->calculateEfficiencyRating($validated);

        $energyEfficiency = EnergyEfficiency::create([
            'property_sustainability_id' => $validated['property_sustainability_id'],
            'efficiency_rating' => $efficiencyRating,
            'energy_consumption' => $validated['energy_consumption'],
            'energy_source' => $validated['energy_source'],
            'renewable_energy_percentage' => $validated['renewable_energy_percentage'] ?? 0,
            'insulation_rating' => $validated['insulation_rating'] ?? 0,
            'hvac_efficiency' => $validated['hvac_efficiency'] ?? 0,
            'lighting_efficiency' => $validated['lighting_efficiency'] ?? 0,
            'appliance_efficiency' => $validated['appliance_efficiency'] ?? 0,
            'solar_panels' => $validated['solar_panels'] ?? false,
            'solar_capacity' => $validated['solar_capacity'] ?? 0,
            'smart_thermostat' => $validated['smart_thermostat'] ?? false,
            'energy_monitoring' => $validated['energy_monitoring'] ?? false,
            'led_lighting' => $validated['led_lighting'] ?? false,
            'double_glazing' => $validated['double_glazing'] ?? false,
            'energy_star_appliances' => $validated['energy_star_appliances'] ?? false,
            'assessment_date' => now(),
            'next_assessment_date' => now()->addYear(),
            'assessed_by' => Auth::id(),
            'recommendations' => $this->generateRecommendations($validated, $efficiencyRating),
            'potential_savings' => $this->calculatePotentialSavings($validated),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update property sustainability energy efficiency rating
        $propertySustainability = PropertySustainability::find($validated['property_sustainability_id']);
        $propertySustainability->update(['energy_efficiency_rating' => $efficiencyRating]);

        return redirect()
            ->route('energy-efficiency.show', $energyEfficiency)
            ->with('success', 'تم تقييم كفاءة الطاقة بنجاح');
    }

    public function show(EnergyEfficiency $energyEfficiency)
    {
        $energyEfficiency->load(['propertySustainability.property', 'propertySustainability.renewableEnergySources']);
        
        // Get historical data for comparison
        $historicalData = EnergyEfficiency::where('property_sustainability_id', $energyEfficiency->property_sustainability_id)
            ->where('id', '!=', $energyEfficiency->id)
            ->orderBy('assessment_date', 'desc')
            ->take(12)
            ->get();

        // Benchmark against similar properties
        $benchmark = $this->getBenchmarkData($energyEfficiency);

        return view('sustainability.energy-efficiency.show', compact('energyEfficiency', 'historicalData', 'benchmark'));
    }

    public function edit(EnergyEfficiency $energyEfficiency)
    {
        $energyEfficiency->load('propertySustainability.property');
        return view('sustainability.energy-efficiency.edit', compact('energyEfficiency'));
    }

    public function update(AssessEnergyEfficiencyRequest $request, EnergyEfficiency $energyEfficiency)
    {
        $validated = $request->validated();

        // Recalculate efficiency rating
        $efficiencyRating = $this->calculateEfficiencyRating($validated);

        $energyEfficiency->update([
            'efficiency_rating' => $efficiencyRating,
            'energy_consumption' => $validated['energy_consumption'],
            'energy_source' => $validated['energy_source'],
            'renewable_energy_percentage' => $validated['renewable_energy_percentage'] ?? 0,
            'insulation_rating' => $validated['insulation_rating'] ?? 0,
            'hvac_efficiency' => $validated['hvac_efficiency'] ?? 0,
            'lighting_efficiency' => $validated['lighting_efficiency'] ?? 0,
            'appliance_efficiency' => $validated['appliance_efficiency'] ?? 0,
            'solar_panels' => $validated['solar_panels'] ?? false,
            'solar_capacity' => $validated['solar_capacity'] ?? 0,
            'smart_thermostat' => $validated['smart_thermostat'] ?? false,
            'energy_monitoring' => $validated['energy_monitoring'] ?? false,
            'led_lighting' => $validated['led_lighting'] ?? false,
            'double_glazing' => $validated['double_glazing'] ?? false,
            'energy_star_appliances' => $validated['energy_star_appliances'] ?? false,
            'assessment_date' => now(),
            'next_assessment_date' => now()->addYear(),
            'recommendations' => $this->generateRecommendations($validated, $efficiencyRating),
            'potential_savings' => $this->calculatePotentialSavings($validated),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update property sustainability
        $propertySustainability = PropertySustainability::find($validated['property_sustainability_id']);
        $propertySustainability->update(['energy_efficiency_rating' => $efficiencyRating]);

        return redirect()
            ->route('energy-efficiency.show', $energyEfficiency)
            ->with('success', 'تم تحديث تقييم كفاءة الطاقة بنجاح');
    }

    public function destroy(EnergyEfficiency $energyEfficiency)
    {
        $energyEfficiency->delete();

        return redirect()
            ->route('energy-efficiency.index')
            ->with('success', 'تم حذف تقييم كفاءة الطاقة بنجاح');
    }

    public function calculator()
    {
        return view('sustainability.energy-efficiency.calculator');
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'property_size' => 'required|numeric|min:0',
            'energy_consumption' => 'required|numeric|min:0',
            'energy_source' => 'required|string',
            'insulation_rating' => 'required|integer|min:1|max:10',
            'hvac_efficiency' => 'required|integer|min:1|max:10',
            'lighting_type' => 'required|string',
            'solar_panels' => 'required|boolean',
            'solar_capacity' => 'nullable|numeric|min:0',
            'smart_thermostat' => 'required|boolean',
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
        $monthlyTrends = EnergyEfficiency::selectRaw('DATE_FORMAT(assessment_date, "%Y-%m") as month, AVG(efficiency_rating) as avg_rating, COUNT(*) as count')
            ->where('assessment_date', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $efficiencyByPropertyType = EnergyEfficiency::join('property_sustainability', 'energy_efficiency.property_sustainability_id', '=', 'property_sustainability.id')
            ->join('properties', 'property_sustainability.property_id', '=', 'properties.id')
            ->selectRaw('properties.type, AVG(energy_efficiency.efficiency_rating) as avg_rating, COUNT(*) as count')
            ->groupBy('properties.type')
            ->get();

        $topPerforming = EnergyEfficiency::with(['propertySustainability.property'])
            ->orderBy('efficiency_rating', 'desc')
            ->take(10)
            ->get();

        $improvementOpportunities = EnergyEfficiency::with(['propertySustainability.property'])
            ->where('efficiency_rating', '<', 60)
            ->orderBy('efficiency_rating', 'asc')
            ->take(10)
            ->get();

        return view('sustainability.energy-efficiency.analytics', compact(
            'monthlyTrends',
            'efficiencyByPropertyType',
            'topPerforming',
            'improvementOpportunities'
        ));
    }

    public function improvementPlan(EnergyEfficiency $energyEfficiency)
    {
        $improvementPlan = $this->generateImprovementPlan($energyEfficiency);
        
        return view('sustainability.energy-efficiency.improvement-plan', compact('energyEfficiency', 'improvementPlan'));
    }

    public function benchmark(EnergyEfficiency $energyEfficiency)
    {
        $benchmarkData = $this->getDetailedBenchmarkData($energyEfficiency);
        
        return view('sustainability.energy-efficiency.benchmark', compact('energyEfficiency', 'benchmarkData'));
    }

    private function calculateEfficiencyRating($data)
    {
        $weights = [
            'consumption' => 0.25,
            'insulation' => 0.20,
            'hvac' => 0.15,
            'lighting' => 0.10,
            'appliances' => 0.10,
            'renewable' => 0.15,
            'smart_features' => 0.05,
        ];

        // Normalize scores to 0-100 scale
        $consumptionScore = max(0, 100 - ($data['energy_consumption'] / 10)); // Simplified calculation
        $insulationScore = ($data['insulation_rating'] ?? 5) * 10;
        $hvacScore = ($data['hvac_efficiency'] ?? 5) * 10;
        $lightingScore = ($data['lighting_efficiency'] ?? 5) * 10;
        $applianceScore = ($data['appliance_efficiency'] ?? 5) * 10;
        $renewableScore = ($data['renewable_energy_percentage'] ?? 0);
        
        $smartFeaturesScore = 0;
        if ($data['smart_thermostat'] ?? false) $smartFeaturesScore += 25;
        if ($data['energy_monitoring'] ?? false) $smartFeaturesScore += 25;
        if ($data['led_lighting'] ?? false) $smartFeaturesScore += 25;
        if ($data['double_glazing'] ?? false) $smartFeaturesScore += 25;

        $totalScore = (
            $consumptionScore * $weights['consumption'] +
            $insulationScore * $weights['insulation'] +
            $hvacScore * $weights['hvac'] +
            $lightingScore * $weights['lighting'] +
            $applianceScore * $weights['appliances'] +
            $renewableScore * $weights['renewable'] +
            $smartFeaturesScore * $weights['smart_features']
        );

        return round(min(100, max(0, $totalScore)), 1);
    }

    private function generateRecommendations($data, $rating)
    {
        $recommendations = [];

        if ($rating < 60) {
            $recommendations[] = 'تحسين عزل المبنى لتقليل استهلاك الطاقة';
            $recommendations[] = 'تركيب نظام تكييف فعال من حيث الطاقة';
            $recommendations[] = 'استبدال الإضاءة بإضاءة LED';
        }

        if ($data['energy_consumption'] > 1000) {
            $recommendations[] = 'تركيب ألواح شمسية لتقليل الاعتماد على الشبكة';
        }

        if (!($data['smart_thermostat'] ?? false)) {
            $recommendations[] = 'تركيب منظم حراري ذكي لتحسين كفاءة التدفئة والتبريد';
        }

        if (!($data['led_lighting'] ?? false)) {
            $recommendations[] = 'التحول إلى إضاءة LED لتوفير 75% من استهلاك الطاقة';
        }

        if (!($data['double_glazing'] ?? false)) {
            $recommendations[] = 'تركيب نوافذ مزدوجة الزجاج لتحسين العزل الحراري';
        }

        return $recommendations;
    }

    private function calculatePotentialSavings($data)
    {
        $currentConsumption = $data['energy_consumption'] ?? 0;
        $potentialReduction = 0;

        // Calculate potential savings from improvements
        if (!($data['led_lighting'] ?? false)) {
            $potentialReduction += $currentConsumption * 0.15; // 15% savings from LED
        }

        if (!($data['smart_thermostat'] ?? false)) {
            $potentialReduction += $currentConsumption * 0.10; // 10% savings from smart thermostat
        }

        if (!($data['double_glazing'] ?? false)) {
            $potentialReduction += $currentConsumption * 0.10; // 10% savings from double glazing
        }

        if (($data['insulation_rating'] ?? 5) < 7) {
            $potentialReduction += $currentConsumption * 0.20; // 20% savings from better insulation
        }

        return round($potentialReduction, 2);
    }

    private function getBenchmarkData($energyEfficiency)
    {
        $propertyType = $energyEfficiency->propertySustainability->property->type;
        
        $benchmark = EnergyEfficiency::join('property_sustainability', 'energy_efficiency.property_sustainability_id', '=', 'property_sustainability.id')
            ->join('properties', 'property_sustainability.property_id', '=', 'properties.id')
            ->where('properties.type', $propertyType)
            ->where('energy_efficiency.id', '!=', $energyEfficiency->id)
            ->selectRaw('AVG(energy_efficiency.efficiency_rating) as avg_rating, MIN(energy_efficiency.efficiency_rating) as min_rating, MAX(energy_efficiency.efficiency_rating) as max_rating, COUNT(*) as count')
            ->first();

        return $benchmark;
    }

    private function quickEfficiencyCalculation($data)
    {
        $baseScore = 50;
        
        // Adjust based on various factors
        if ($data['solar_panels']) $baseScore += 15;
        if ($data['smart_thermostat']) $baseScore += 10;
        if ($data['lighting_type'] === 'led') $baseScore += 10;
        
        $baseScore += ($data['insulation_rating'] - 5) * 3;
        $baseScore += ($data['hvac_efficiency'] - 5) * 2;
        
        // Penalty for high consumption
        if ($data['energy_consumption'] > 1000) {
            $baseScore -= 15;
        }

        return max(0, min(100, $baseScore));
    }

    private function quickSavingsCalculation($data)
    {
        $currentCost = $data['energy_consumption'] * 0.15; // Assuming $0.15 per kWh
        $potentialSavings = 0;

        if (!$data['solar_panels']) {
            $potentialSavings += $currentCost * 0.30;
        }

        if (!$data['smart_thermostat']) {
            $potentialSavings += $currentCost * 0.15;
        }

        if ($data['lighting_type'] !== 'led') {
            $potentialSavings += $currentCost * 0.10;
        }

        return $potentialSavings;
    }

    private function getQuickRecommendations($data, $rating)
    {
        $recommendations = [];

        if ($rating < 70) {
            $recommendations[] = 'تحسين كفاءة الطاقة أمر ضروري';
        }

        if (!$data['solar_panels']) {
            $recommendations[] = 'فكر في تركيب الألواح الشمسية';
        }

        if (!$data['smart_thermostat']) {
            $recommendations[] = 'تركيب منظم حراري ذكي يمكن أن يوفر 15% من تكاليف الطاقة';
        }

        return $recommendations;
    }

    private function generateImprovementPlan($energyEfficiency)
    {
        $plan = [];
        $rating = $energyEfficiency->efficiency_rating;

        if ($rating < 40) {
            $plan[] = [
                'priority' => 'عاجل',
                'action' => 'تركيب عزل حراري عالي الجودة',
                'cost' => 'متوسط',
                'savings' => '20-30%',
                'timeline' => '1-2 أشهر',
            ];
        }

        if ($rating < 60) {
            $plan[] = [
                'priority' => 'مرتفع',
                'action' => 'تركيب ألواح شمسية',
                'cost' => 'مرتفع',
                'savings' => '30-50%',
                'timeline' => '2-3 أشهر',
            ];
        }

        if (!$energyEfficiency->smart_thermostat) {
            $plan[] = [
                'priority' => 'متوسط',
                'action' => 'تركيب منظم حراري ذكي',
                'cost' => 'منخفض',
                'savings' => '10-15%',
                'timeline' => '1 أسبوع',
            ];
        }

        return $plan;
    }

    private function getDetailedBenchmarkData($energyEfficiency)
    {
        $propertyType = $energyEfficiency->propertySustainability->property->type;
        
        return EnergyEfficiency::join('property_sustainability', 'energy_efficiency.property_sustainability_id', '=', 'property_sustainability.id')
            ->join('properties', 'property_sustainability.property_id', '=', 'properties.id')
            ->where('properties.type', $propertyType)
            ->selectRaw('
                AVG(energy_efficiency.efficiency_rating) as avg_rating,
                AVG(energy_efficiency.energy_consumption) as avg_consumption,
                AVG(energy_efficiency.renewable_energy_percentage) as avg_renewable,
                COUNT(*) as total_properties,
                SUM(CASE WHEN energy_efficiency.solar_panels = 1 THEN 1 ELSE 0 END) as properties_with_solar,
                SUM(CASE WHEN energy_efficiency.smart_thermostat = 1 THEN 1 ELSE 0 END) as properties_with_thermostat
            ')
            ->first();
    }
}
