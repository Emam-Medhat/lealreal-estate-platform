<?php

namespace App\Http\Controllers\Sustainability;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Sustainability\PropertySustainability;
use App\Models\Sustainability\RenewableEnergySource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class RenewableEnergyController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_sustainability');
    }

    public function index()
    {
        $renewableSources = RenewableEnergySource::with(['propertySustainability.property'])
            ->when(Auth::user()->hasRole('agent'), function($query) {
                $query->whereHas('propertySustainability.property', function($q) {
                    $q->where('agent_id', Auth::id());
                });
            })
            ->latest('installation_date')
            ->paginate(15);

        $stats = [
            'total_sources' => RenewableEnergySource::count(),
            'active_sources' => RenewableEnergySource::where('status', 'active')->count(),
            'total_capacity' => RenewableEnergySource::sum('capacity'),
            'solar_installations' => RenewableEnergySource::where('source_type', 'solar')->count(),
            'wind_installations' => RenewableEnergySource::where('source_type', 'wind')->count(),
        ];

        return view('sustainability.renewable-energy.index', compact('renewableSources', 'stats'));
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

        $sourceTypes = [
            'solar' => 'طاقة شمسية',
            'wind' => 'طاقة رياح',
            'geothermal' => 'طاقة حرارية أرضية',
            'biomass' => 'طاقة حيوية',
            'hydro' => 'طاقة مائية',
            'hybrid' => 'نظام هجين',
        ];

        return view('sustainability.renewable-energy.create', compact('properties', 'sourceTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_sustainability_id' => 'required|exists:property_sustainability,id',
            'source_type' => 'required|string|in:solar,wind,geothermal,biomass,hydro,hybrid',
            'capacity' => 'required|numeric|min:0',
            'unit' => 'required|string|in:kW,MW,W',
            'manufacturer' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'installation_date' => 'required|date|before_or_equal:today',
            'expected_lifespan' => 'required|integer|min:1|max:50',
            'efficiency_rating' => 'required|numeric|min:0|max:100',
            'installation_cost' => 'required|numeric|min:0',
            'maintenance_cost_per_year' => 'required|numeric|min:0',
            'annual_production' => 'nullable|numeric|min:0',
            'grid_connected' => 'required|boolean',
            'battery_storage' => 'required|boolean',
            'battery_capacity' => 'nullable|numeric|min:0',
            'monitoring_system' => 'required|boolean',
            'warranty_period' => 'required|integer|min:0',
            'certifications' => 'nullable|array',
            'certifications.*' => 'string',
            'location' => 'nullable|string|max:255',
            'orientation' => 'nullable|string|max:255',
            'tilt_angle' => 'nullable|numeric|min:0|max:90',
            'shading_factors' => 'nullable|array',
            'shading_factors.*' => 'string',
            'performance_data' => 'nullable|array',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'notes' => 'nullable|string',
        ]);

        // Handle document uploads
        $documents = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $path = $document->store('renewable-energy', 'public');
                $documents[] = $path;
            }
        }

        $renewableSource = RenewableEnergySource::create([
            'property_sustainability_id' => $validated['property_sustainability_id'],
            'source_type' => $validated['source_type'],
            'capacity' => $validated['capacity'],
            'unit' => $validated['unit'],
            'manufacturer' => $validated['manufacturer'],
            'model' => $validated['model'],
            'installation_date' => $validated['installation_date'],
            'expected_lifespan' => $validated['expected_lifespan'],
            'efficiency_rating' => $validated['efficiency_rating'],
            'installation_cost' => $validated['installation_cost'],
            'maintenance_cost_per_year' => $validated['maintenance_cost_per_year'],
            'annual_production' => $validated['annual_production'] ?? 0,
            'grid_connected' => $validated['grid_connected'],
            'battery_storage' => $validated['battery_storage'],
            'battery_capacity' => $validated['battery_capacity'] ?? 0,
            'monitoring_system' => $validated['monitoring_system'],
            'warranty_period' => $validated['warranty_period'],
            'certifications' => $validated['certifications'] ?? [],
            'location' => $validated['location'],
            'orientation' => $validated['orientation'],
            'tilt_angle' => $validated['tilt_angle'],
            'shading_factors' => $validated['shading_factors'] ?? [],
            'performance_data' => $validated['performance_data'] ?? [],
            'documents' => $documents,
            'status' => 'active',
            'created_by' => Auth::id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update property sustainability renewable energy percentage
        $this->updatePropertyRenewablePercentage($validated['property_sustainability_id']);

        return redirect()
            ->route('renewable-energy.show', $renewableSource)
            ->with('success', 'تم إضافة مصدر الطاقة المتجددة بنجاح');
    }

    public function show(RenewableEnergySource $renewableSource)
    {
        $renewableSource->load(['propertySustainability.property']);
        
        // Calculate performance metrics
        $performanceMetrics = $this->calculatePerformanceMetrics($renewableSource);
        
        // Get maintenance records
        $maintenanceRecords = $renewableSource->maintenanceRecords()->latest()->take(10)->get();

        return view('sustainability.renewable-energy.show', compact('renewableSource', 'performanceMetrics', 'maintenanceRecords'));
    }

    public function edit(RenewableEnergySource $renewableSource)
    {
        $renewableSource->load('propertySustainability.property');
        
        $sourceTypes = [
            'solar' => 'طاقة شمسية',
            'wind' => 'طاقة رياح',
            'geothermal' => 'طاقة حرارية أرضية',
            'biomass' => 'طاقة حيوية',
            'hydro' => 'طاقة مائية',
            'hybrid' => 'نظام هجين',
        ];

        return view('sustainability.renewable-energy.edit', compact('renewableSource', 'sourceTypes'));
    }

    public function update(Request $request, RenewableEnergySource $renewableSource)
    {
        $validated = $request->validate([
            'capacity' => 'required|numeric|min:0',
            'efficiency_rating' => 'required|numeric|min:0|max:100',
            'annual_production' => 'nullable|numeric|min:0',
            'maintenance_cost_per_year' => 'required|numeric|min:0',
            'status' => 'required|string|in:active,inactive,maintenance,decommissioned',
            'performance_data' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $renewableSource->update($validated);

        // Update property sustainability renewable energy percentage
        $this->updatePropertyRenewablePercentage($renewableSource->property_sustainability_id);

        return redirect()
            ->route('renewable-energy.show', $renewableSource)
            ->with('success', 'تم تحديث مصدر الطاقة المتجددة بنجاح');
    }

    public function destroy(RenewableEnergySource $renewableSource)
    {
        // Delete associated documents
        if ($renewableSource->documents) {
            foreach ($renewableSource->documents as $document) {
                Storage::disk('public')->delete($document);
            }
        }

        $propertySustainabilityId = $renewableSource->property_sustainability_id;
        $renewableSource->delete();

        // Update property sustainability renewable energy percentage
        $this->updatePropertyRenewablePercentage($propertySustainabilityId);

        return redirect()
            ->route('renewable-energy.index')
            ->with('success', 'تم حذف مصدر الطاقة المتجددة بنجاح');
    }

    public function performance(RenewableEnergySource $renewableSource)
    {
        $performanceData = $this->getDetailedPerformanceData($renewableSource);
        
        return view('sustainability.renewable-energy.performance', compact('renewableSource', 'performanceData'));
    }

    public function maintenance(RenewableEnergySource $renewableSource)
    {
        $maintenanceRecords = $renewableSource->maintenanceRecords()->latest()->paginate(15);
        $nextMaintenanceDate = $this->calculateNextMaintenanceDate($renewableSource);
        
        return view('sustainability.renewable-energy.maintenance', compact('renewableSource', 'maintenanceRecords', 'nextMaintenanceDate'));
    }

    public function addMaintenance(Request $request, RenewableEnergySource $renewableSource)
    {
        $validated = $request->validate([
            'maintenance_type' => 'required|string|in:routine,repair,inspection,cleaning,replacement',
            'description' => 'required|string|max:1000',
            'cost' => 'required|numeric|min:0',
            'performed_by' => 'required|string|max:255',
            'maintenance_date' => 'required|date|before_or_equal:today',
            'next_due_date' => 'nullable|date|after:maintenance_date',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'notes' => 'nullable|string',
        ]);

        // Handle document uploads
        $documents = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $path = $document->store('maintenance', 'public');
                $documents[] = $path;
            }
        }

        $maintenanceRecord = $renewableSource->maintenanceRecords()->create([
            'maintenance_type' => $validated['maintenance_type'],
            'description' => $validated['description'],
            'cost' => $validated['cost'],
            'performed_by' => $validated['performed_by'],
            'maintenance_date' => $validated['maintenance_date'],
            'next_due_date' => $validated['next_due_date'],
            'documents' => $documents,
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('renewable-energy.maintenance', $renewableSource)
            ->with('success', 'تم إضافة سجل الصيانة بنجاح');
    }

    public function analytics()
    {
        $sourceTypeDistribution = RenewableEnergySource::selectRaw('source_type, COUNT(*) as count, SUM(capacity) as total_capacity')
            ->groupBy('source_type')
            ->get();

        $capacityByPropertyType = RenewableEnergySource::join('property_sustainability', 'renewable_energy_sources.property_sustainability_id', '=', 'property_sustainability.id')
            ->join('properties', 'property_sustainability.property_id', '=', 'properties.id')
            ->selectRaw('properties.type, SUM(renewable_energy_sources.capacity) as total_capacity, COUNT(*) as count')
            ->groupBy('properties.type')
            ->get();

        $monthlyProduction = RenewableEnergySource::selectRaw('DATE_FORMAT(installation_date, "%Y-%m") as month, SUM(annual_production) as total_production, COUNT(*) as installations')
            ->where('installation_date', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $efficiencyTrends = RenewableEnergySource::selectRaw('source_type, AVG(efficiency_rating) as avg_efficiency')
            ->groupBy('source_type')
            ->get();

        return view('sustainability.renewable-energy.analytics', compact(
            'sourceTypeDistribution',
            'capacityByPropertyType',
            'monthlyProduction',
            'efficiencyTrends'
        ));
    }

    public function roi(RenewableEnergySource $renewableSource)
    {
        $roiData = $this->calculateROI($renewableSource);
        
        return view('sustainability.renewable-energy.roi', compact('renewableSource', 'roiData'));
    }

    private function updatePropertyRenewablePercentage($propertySustainabilityId)
    {
        $propertySustainability = PropertySustainability::find($propertySustainabilityId);
        $totalCapacity = $propertySustainability->renewableEnergySources()->sum('capacity');
        
        // Simple calculation - this could be more sophisticated based on property size and energy needs
        $renewablePercentage = min(100, $totalCapacity * 10); // Simplified calculation
        
        $propertySustainability->update(['renewable_energy_percentage' => $renewablePercentage]);
    }

    private function calculatePerformanceMetrics($renewableSource)
    {
        $metrics = [];
        
        // Calculate actual vs expected production
        if ($renewableSource->annual_production && $renewableSource->capacity) {
            $expectedProduction = $renewableSource->capacity * 8760 * ($renewableSource->efficiency_rating / 100); // kWh per year
            $metrics['production_efficiency'] = ($renewableSource->annual_production / $expectedProduction) * 100;
        }
        
        // Calculate uptime based on maintenance records
        $totalDowntime = $renewableSource->maintenanceRecords()
            ->where('maintenance_type', 'repair')
            ->sum('duration_hours');
        
        $operatingDays = $renewableSource->installation_date->diffInDays(now());
        $metrics['uptime_percentage'] = (($operatingDays * 24 - $totalDowntime) / ($operatingDays * 24)) * 100;
        
        // Calculate cost per kWh
        if ($renewableSource->annual_production > 0) {
            $totalCost = $renewableSource->installation_cost + 
                         ($renewableSource->maintenance_cost_per_year * $renewableSource->installation_date->diffInYears(now()));
            $metrics['cost_per_kwh'] = $totalCost / ($renewableSource->annual_production * $renewableSource->installation_date->diffInYears(now()));
        }
        
        return $metrics;
    }

    private function getDetailedPerformanceData($renewableSource)
    {
        // This would typically come from monitoring systems
        // For demo purposes, we'll return simulated data
        return [
            'daily_production' => $this->generateDailyProductionData($renewableSource),
            'monthly_production' => $this->generateMonthlyProductionData($renewableSource),
            'efficiency_trend' => $this->generateEfficiencyTrend($renewableSource),
        ];
    }

    private function calculateNextMaintenanceDate($renewableSource)
    {
        $lastMaintenance = $renewableSource->maintenanceRecords()
            ->orderBy('maintenance_date', 'desc')
            ->first();
        
        if (!$lastMaintenance) {
            return $renewableSource->installation_date->addMonths(6);
        }
        
        return $lastMaintenance->next_due_date ?? $lastMaintenance->maintenance_date->addMonths(6);
    }

    private function calculateROI($renewableSource)
    {
        $roi = [];
        
        // Calculate payback period
        $annualSavings = $renewableSource->annual_production * 0.15; // Assuming $0.15 per kWh
        $paybackPeriod = $renewableSource->installation_cost / $annualSavings;
        
        $roi['payback_period_years'] = $paybackPeriod;
        $roi['annual_savings'] = $annualSavings;
        $roi['total_investment'] = $renewableSource->installation_cost;
        $roi['lifetime_savings'] = $annualSavings * $renewableSource->expected_lifespan;
        $roi['net_profit'] = $roi['lifetime_savings'] - $roi['total_investment'];
        $roi['roi_percentage'] = ($roi['net_profit'] / $roi['total_investment']) * 100;
        
        return $roi;
    }

    private function generateDailyProductionData($renewableSource)
    {
        // Simulated daily production data for the last 30 days
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $baseProduction = $renewableSource->capacity * 24 * ($renewableSource->efficiency_rating / 100);
            $data[$date->format('Y-m-d')] = $baseProduction * (0.7 + (rand(0, 60) / 100)); // Random variation
        }
        return $data;
    }

    private function generateMonthlyProductionData($renewableSource)
    {
        // Simulated monthly production data for the last 12 months
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $baseProduction = $renewableSource->capacity * 730 * ($renewableSource->efficiency_rating / 100);
            $data[$date->format('Y-m')] = $baseProduction * (0.8 + (rand(0, 40) / 100));
        }
        return $data;
    }

    private function generateEfficiencyTrend($renewableSource)
    {
        // Simulated efficiency trend data
        $data = [];
        $baseEfficiency = $renewableSource->efficiency_rating;
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            // Efficiency typically degrades slightly over time
            $degradation = ($i * 0.1);
            $data[$date->format('Y-m')] = max(0, $baseEfficiency - $degradation + (rand(-2, 2)));
        }
        return $data;
    }
}
