<?php

namespace App\Http\Controllers\Sustainability;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Sustainability\PropertySustainability;
use App\Models\Sustainability\CarbonFootprint;
use App\Http\Requests\Sustainability\CalculateCarbonFootprintRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CarbonFootprintController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_sustainability');
    }

    public function index()
    {
        $carbonFootprints = CarbonFootprint::with(['propertySustainability.property'])
            ->when(Auth::user()->hasRole('agent'), function($query) {
                $query->whereHas('propertySustainability.property', function($q) {
                    $q->where('agent_id', Auth::id());
                });
            })
            ->latest('calculated_at')
            ->paginate(15);

        $stats = [
            'total_footprints' => CarbonFootprint::count(),
            'average_footprint' => CarbonFootprint::avg('total_footprint'),
            'properties_with_low_footprint' => CarbonFootprint::where('total_footprint', '<', 50)->count(),
            'total_co2_saved' => CarbonFootprint::sum('co2_saved'),
        ];

        return view('sustainability.carbon-footprints.index', compact('carbonFootprints', 'stats'));
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

        return view('sustainability.carbon-footprints.create', compact('properties'));
    }

    public function store(CalculateCarbonFootprintRequest $request)
    {
        $validated = $request->validated();

        // Calculate carbon footprint components
        $energyFootprint = $this->calculateEnergyFootprint($validated);
        $transportFootprint = $this->calculateTransportFootprint($validated);
        $wasteFootprint = $this->calculateWasteFootprint($validated);
        $waterFootprint = $this->calculateWaterFootprint($validated);
        $materialsFootprint = $this->calculateMaterialsFootprint($validated);

        $totalFootprint = $energyFootprint + $transportFootprint + $wasteFootprint + $waterFootprint + $materialsFootprint;

        // Calculate CO2 saved from renewable energy and sustainable practices
        $co2Saved = $this->calculateCO2Saved($validated, $totalFootprint);

        $carbonFootprint = CarbonFootprint::create([
            'property_sustainability_id' => $validated['property_sustainability_id'],
            'energy_footprint' => $energyFootprint,
            'transport_footprint' => $transportFootprint,
            'waste_footprint' => $wasteFootprint,
            'water_footprint' => $waterFootprint,
            'materials_footprint' => $materialsFootprint,
            'total_footprint' => $totalFootprint,
            'co2_saved' => $co2Saved,
            'net_footprint' => $totalFootprint - $co2Saved,
            'calculation_method' => $validated['calculation_method'] ?? 'standard',
            'data_source' => $validated['data_source'] ?? 'manual',
            'calculated_at' => now(),
            'calculated_by' => Auth::id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update property sustainability carbon footprint
        $propertySustainability = PropertySustainability::find($validated['property_sustainability_id']);
        $propertySustainability->update(['carbon_footprint' => $totalFootprint]);

        return redirect()
            ->route('carbon-footprints.show', $carbonFootprint)
            ->with('success', 'تم حساب البصمة الكربونية بنجاح');
    }

    public function show(CarbonFootprint $carbonFootprint)
    {
        $carbonFootprint->load(['propertySustainability.property', 'propertySustainability.renewableEnergySources']);
        
        // Get historical data for comparison
        $historicalData = CarbonFootprint::where('property_sustainability_id', $carbonFootprint->property_sustainability_id)
            ->where('id', '!=', $carbonFootprint->id)
            ->orderBy('calculated_at', 'desc')
            ->take(12)
            ->get();

        // Benchmark against similar properties
        $benchmark = $this->getBenchmarkData($carbonFootprint);

        return view('sustainability.carbon-footprints.show', compact('carbonFootprint', 'historicalData', 'benchmark'));
    }

    public function edit(CarbonFootprint $carbonFootprint)
    {
        $carbonFootprint->load('propertySustainability.property');
        return view('sustainability.carbon-footprints.edit', compact('carbonFootprint'));
    }

    public function update(CalculateCarbonFootprintRequest $request, CarbonFootprint $carbonFootprint)
    {
        $validated = $request->validated();

        // Recalculate carbon footprint
        $energyFootprint = $this->calculateEnergyFootprint($validated);
        $transportFootprint = $this->calculateTransportFootprint($validated);
        $wasteFootprint = $this->calculateWasteFootprint($validated);
        $waterFootprint = $this->calculateWaterFootprint($validated);
        $materialsFootprint = $this->calculateMaterialsFootprint($validated);

        $totalFootprint = $energyFootprint + $transportFootprint + $wasteFootprint + $waterFootprint + $materialsFootprint;
        $co2Saved = $this->calculateCO2Saved($validated, $totalFootprint);

        $carbonFootprint->update([
            'energy_footprint' => $energyFootprint,
            'transport_footprint' => $transportFootprint,
            'waste_footprint' => $wasteFootprint,
            'water_footprint' => $waterFootprint,
            'materials_footprint' => $materialsFootprint,
            'total_footprint' => $totalFootprint,
            'co2_saved' => $co2Saved,
            'net_footprint' => $totalFootprint - $co2Saved,
            'calculation_method' => $validated['calculation_method'] ?? 'standard',
            'data_source' => $validated['data_source'] ?? 'manual',
            'calculated_at' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update property sustainability
        $propertySustainability = PropertySustainability::find($validated['property_sustainability_id']);
        $propertySustainability->update(['carbon_footprint' => $totalFootprint]);

        return redirect()
            ->route('carbon-footprints.show', $carbonFootprint)
            ->with('success', 'تم تحديث البصمة الكربونية بنجاح');
    }

    public function destroy(CarbonFootprint $carbonFootprint)
    {
        $carbonFootprint->delete();

        return redirect()
            ->route('carbon-footprints.index')
            ->with('success', 'تم حذف البصمة الكربونية بنجاح');
    }

    public function calculator()
    {
        return view('sustainability.carbon-calculator');
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'property_type' => 'required|string',
            'property_size' => 'required|numeric|min:0',
            'energy_consumption' => 'required|numeric|min:0',
            'energy_source' => 'required|string',
            'transportation_method' => 'required|string',
            'daily_commute_distance' => 'nullable|numeric|min:0',
            'waste_generation' => 'required|numeric|min:0',
            'recycling_percentage' => 'required|integer|min:0|max:100',
            'water_consumption' => 'required|numeric|min:0',
            'construction_materials' => 'nullable|string',
        ]);

        // Quick calculation for demo purposes
        $energyFootprint = $this->quickEnergyCalculation($validated);
        $transportFootprint = $this->quickTransportCalculation($validated);
        $wasteFootprint = $this->quickWasteCalculation($validated);
        $waterFootprint = $this->quickWaterCalculation($validated);
        $materialsFootprint = $this->quickMaterialsCalculation($validated);

        $totalFootprint = $energyFootprint + $transportFootprint + $wasteFootprint + $waterFootprint + $materialsFootprint;

        return response()->json([
            'energy_footprint' => round($energyFootprint, 2),
            'transport_footprint' => round($transportFootprint, 2),
            'waste_footprint' => round($wasteFootprint, 2),
            'water_footprint' => round($waterFootprint, 2),
            'materials_footprint' => round($materialsFootprint, 2),
            'total_footprint' => round($totalFootprint, 2),
            'recommendations' => $this->getRecommendations($validated, $totalFootprint),
        ]);
    }

    public function analytics()
    {
        $monthlyTrends = CarbonFootprint::selectRaw('DATE_FORMAT(calculated_at, "%Y-%m") as month, AVG(total_footprint) as avg_footprint, COUNT(*) as count')
            ->where('calculated_at', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $footprintByPropertyType = CarbonFootprint::join('property_sustainability', 'carbon_footprints.property_sustainability_id', '=', 'property_sustainability.id')
            ->join('properties', 'property_sustainability.property_id', '=', 'properties.id')
            ->selectRaw('properties.type, AVG(carbon_footprints.total_footprint) as avg_footprint, COUNT(*) as count')
            ->groupBy('properties.type')
            ->get();

        $topEmitters = CarbonFootprint::with(['propertySustainability.property'])
            ->orderBy('total_footprint', 'desc')
            ->take(10)
            ->get();

        $mostImproved = CarbonFootprint::selectRaw('property_sustainability_id, MIN(total_footprint) as min_footprint, MAX(total_footprint) as max_footprint, MAX(total_footprint) - MIN(total_footprint) as improvement')
            ->groupBy('property_sustainability_id')
            ->having('improvement', '>', 0)
            ->orderBy('improvement', 'desc')
            ->take(10)
            ->with('propertySustainability.property')
            ->get();

        return view('sustainability.carbon-footprints.analytics', compact(
            'monthlyTrends',
            'footprintByPropertyType',
            'topEmitters',
            'mostImproved'
        ));
    }

    public function reductionPlan(CarbonFootprint $carbonFootprint)
    {
        $reductionStrategies = $this->generateReductionStrategies($carbonFootprint);
        
        return view('sustainability.carbon-footprints.reduction-plan', compact('carbonFootprint', 'reductionStrategies'));
    }

    private function calculateEnergyFootprint($data)
    {
        // Energy consumption in kWh * emission factor
        $emissionFactors = [
            'electricity_grid' => 0.5, // kg CO2 per kWh
            'solar' => 0.02,
            'wind' => 0.01,
            'natural_gas' => 0.2,
            'oil' => 0.3,
        ];

        $factor = $emissionFactors[$data['energy_source']] ?? 0.5;
        return ($data['energy_consumption'] ?? 0) * $factor * 12; // Annual calculation
    }

    private function calculateTransportFootprint($data)
    {
        // Transportation emissions based on method and distance
        $emissionFactors = [
            'car' => 0.21, // kg CO2 per km
            'public_transport' => 0.04,
            'bicycle' => 0,
            'walking' => 0,
        ];

        $factor = $emissionFactors[$data['transportation_method']] ?? 0.21;
        return ($data['daily_commute_distance'] ?? 0) * $factor * 365; // Annual calculation
    }

    private function calculateWasteFootprint($data)
    {
        // Waste generation in kg per year
        $wasteAmount = ($data['waste_generation'] ?? 0) * 52; // Weekly to annual
        $recyclingReduction = $wasteAmount * (($data['recycling_percentage'] ?? 0) / 100) * 0.5;
        
        return max(0, $wasteAmount - $recyclingReduction);
    }

    private function calculateWaterFootprint($data)
    {
        // Water consumption in liters per year
        $waterConsumption = ($data['water_consumption'] ?? 0) * 365; // Daily to annual
        return $waterConsumption * 0.0003; // kg CO2 per liter
    }

    private function calculateMaterialsFootprint($data)
    {
        // Construction materials footprint (simplified)
        $materialFactors = [
            'concrete' => 0.9,
            'steel' => 2.0,
            'wood' => 0.3,
            'recycled' => 0.2,
        ];

        $material = $data['construction_materials'] ?? 'concrete';
        $factor = $materialFactors[$material] ?? 0.9;
        
        return $factor * 100; // Base amount for calculation
    }

    private function calculateCO2Saved($data, $totalFootprint)
    {
        $savings = 0;

        // Renewable energy savings
        if (isset($data['renewable_energy_percentage'])) {
            $savings += $totalFootprint * ($data['renewable_energy_percentage'] / 100) * 0.8;
        }

        // Green building features
        if (isset($data['green_building_features'])) {
            $savings += $totalFootprint * 0.15;
        }

        // Sustainable materials
        if (isset($data['sustainable_materials_percentage'])) {
            $savings += $totalFootprint * ($data['sustainable_materials_percentage'] / 100) * 0.1;
        }

        return $savings;
    }

    private function getBenchmarkData($carbonFootprint)
    {
        $propertyType = $carbonFootprint->propertySustainability->property->type;
        
        $benchmark = CarbonFootprint::join('property_sustainability', 'carbon_footprints.property_sustainability_id', '=', 'property_sustainability.id')
            ->join('properties', 'property_sustainability.property_id', '=', 'properties.id')
            ->where('properties.type', $propertyType)
            ->where('carbon_footprints.id', '!=', $carbonFootprint->id)
            ->selectRaw('AVG(carbon_footprints.total_footprint) as avg_footprint, MIN(carbon_footprints.total_footprint) as min_footprint, MAX(carbon_footprints.total_footprint) as max_footprint, COUNT(*) as count')
            ->first();

        return $benchmark;
    }

    private function getRecommendations($data, $totalFootprint)
    {
        $recommendations = [];

        if ($data['energy_consumption'] > 1000) {
            $recommendations[] = 'Consider installing solar panels to reduce energy footprint';
        }

        if ($data['recycling_percentage'] < 50) {
            $recommendations[] = 'Increase recycling to reduce waste footprint';
        }

        if ($data['water_consumption'] > 300) {
            $recommendations[] = 'Install water-saving fixtures to reduce water footprint';
        }

        if ($totalFootprint > 100) {
            $recommendations[] = 'Your carbon footprint is above average. Consider comprehensive sustainability measures';
        }

        return $recommendations;
    }

    private function quickEnergyCalculation($data)
    {
        $factors = ['electricity_grid' => 0.5, 'solar' => 0.02, 'wind' => 0.01];
        return ($data['energy_consumption'] ?? 0) * ($factors[$data['energy_source']] ?? 0.5) * 12;
    }

    private function quickTransportCalculation($data)
    {
        $factors = ['car' => 0.21, 'public_transport' => 0.04, 'bicycle' => 0];
        return ($data['daily_commute_distance'] ?? 0) * ($factors[$data['transportation_method']] ?? 0.21) * 365;
    }

    private function quickWasteCalculation($data)
    {
        $waste = ($data['waste_generation'] ?? 0) * 52;
        return max(0, $waste - ($waste * ($data['recycling_percentage'] / 100) * 0.5));
    }

    private function quickWaterCalculation($data)
    {
        return ($data['water_consumption'] ?? 0) * 365 * 0.0003;
    }

    private function quickMaterialsCalculation($data)
    {
        $factors = ['concrete' => 90, 'steel' => 200, 'wood' => 30, 'recycled' => 20];
        return $factors[$data['construction_materials'] ?? 'concrete'] ?? 90;
    }

    private function generateReductionStrategies($carbonFootprint)
    {
        $strategies = [];

        $total = $carbonFootprint->total_footprint;

        if ($carbonFootprint->energy_footprint > $total * 0.3) {
            $strategies[] = [
                'category' => 'الطاقة',
                'potential_reduction' => '30-50%',
                'actions' => ['تركيب الألواح الشمسية', 'تحسين عزل المبنى', 'استخدام أجهزة موفرة للطاقة'],
                'estimated_savings' => $carbonFootprint->energy_footprint * 0.4,
            ];
        }

        if ($carbonFootprint->transport_footprint > $total * 0.2) {
            $strategies[] = [
                'category' => 'النقل',
                'potential_reduction' => '40-60%',
                'actions' => ['استخدام النقل العام', 'العمل عن بعد', 'السيارات الكهربائية'],
                'estimated_savings' => $carbonFootprint->transport_footprint * 0.5,
            ];
        }

        if ($carbonFootprint->waste_footprint > $total * 0.15) {
            $strategies[] = [
                'category' => 'النفايات',
                'potential_reduction' => '50-70%',
                'actions' => ['زيادة إعادة التدوير', 'الكمposting', 'تقليل الاستهلاك'],
                'estimated_savings' => $carbonFootprint->waste_footprint * 0.6,
            ];
        }

        return $strategies;
    }
}
