<?php

namespace App\Http\Controllers;

use App\Models\RenewableEnergySource;
use App\Models\SmartProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RenewableEnergyController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_sources' => RenewableEnergySource::count(),
            'active_sources' => RenewableEnergySource::where('status', 'active')->count(),
            'total_capacity' => RenewableEnergySource::sum('capacity'),
            'current_output' => RenewableEnergySource::sum('current_output'),
            'total_energy_generated' => RenewableEnergySource::sum('energy_generated'),
            'total_carbon_offset' => RenewableEnergySource::sum('carbon_offset'),
            'sources_by_type' => $this->getSourcesByType(),
            'energy_trends' => $this->getEnergyTrends(),
        ];

        $recentSources = RenewableEnergySource::with(['property'])
            ->latest()
            ->take(10)
            ->get();

        $topPerformers = $this->getTopPerformers();
        $maintenanceSchedule = $this->getMaintenanceSchedule();

        return view('sustainability.renewable-energy-dashboard', compact(
            'stats', 
            'recentSources', 
            'topPerformers', 
            'maintenanceSchedule'
        ));
    }

    public function index(Request $request)
    {
        $query = RenewableEnergySource::with(['property']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('source_type')) {
            $query->where('source_type', $request->source_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('installation_date_from')) {
            $query->whereDate('installation_date', '>=', $request->installation_date_from);
        }

        if ($request->filled('installation_date_to')) {
            $query->whereDate('installation_date', '<=', $request->installation_date_to);
        }

        if ($request->filled('capacity_min')) {
            $query->where('capacity', '>=', $request->capacity_min);
        }

        if ($request->filled('capacity_max')) {
            $query->where('capacity', '<=', $request->capacity_max);
        }

        $sources = $query->latest()->paginate(12);

        $sourceTypes = ['solar', 'wind', 'hydro', 'geothermal', 'biomass'];
        $statuses = ['active', 'inactive', 'maintenance', 'offline'];

        return view('sustainability.renewable-energy-index', compact(
            'sources', 
            'sourceTypes', 
            'statuses'
        ));
    }

    public function create()
    {
        $properties = SmartProperty::all();

        return view('sustainability.renewable-energy-create', compact(
            'properties'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $energyData = $request->validate([
                'property_id' => 'required|exists:smart_properties,id',
                'source_type' => 'required|string|max:255',
                'capacity' => 'required|numeric|min:0',
                'current_output' => 'required|numeric|min:0',
                'energy_generated' => 'required|numeric|min:0',
                'carbon_offset' => 'required|numeric|min:0',
                'source_details' => 'nullable|array',
                'performance_metrics' => 'nullable|array',
                'installation_date' => 'required|date',
                'last_maintenance_date' => 'nullable|date',
                'status' => 'required|in:active,inactive,maintenance,offline',
                'efficiency_rating' => 'nullable|numeric|min:0|max:100',
                'maintenance_schedule' => 'nullable|array',
            ]);

            $energyData['created_by'] = auth()->id();
            $energyData['source_details'] = $this->generateSourceDetails($request);
            $energyData['performance_metrics'] = $this->generatePerformanceMetrics($request);
            $energyData['maintenance_schedule'] = $this->generateMaintenanceSchedule($request);

            $source = RenewableEnergySource::create($energyData);

            DB::commit();

            return redirect()
                ->route('renewable-energy.show', $source)
                ->with('success', 'تم إضافة مصدر الطاقة المتجددة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة المصدر: ' . $e->getMessage());
        }
    }

    public function show(RenewableEnergySource $source)
    {
        $source->load(['property']);
        $sourceDetails = $this->getSourceDetails($source);
        $performanceAnalysis = $this->getPerformanceAnalysis($source);
        $maintenanceStatus = $this->getMaintenanceStatus($source);

        return view('sustainability.renewable-energy-show', compact(
            'source', 
            'sourceDetails', 
            'performanceAnalysis', 
            'maintenanceStatus'
        ));
    }

    public function edit(RenewableEnergySource $source)
    {
        $properties = SmartProperty::all();

        return view('sustainability.renewable-energy-edit', compact(
            'source', 
            'properties'
        ));
    }

    public function update(Request $request, RenewableEnergySource $source)
    {
        DB::beginTransaction();
        try {
            $energyData = $request->validate([
                'source_type' => 'required|string|max:255',
                'capacity' => 'required|numeric|min:0',
                'current_output' => 'required|numeric|min:0',
                'energy_generated' => 'required|numeric|min:0',
                'carbon_offset' => 'required|numeric|min:0',
                'source_details' => 'nullable|array',
                'performance_metrics' => 'nullable|array',
                'installation_date' => 'required|date',
                'last_maintenance_date' => 'nullable|date',
                'status' => 'required|in:active,inactive,maintenance,offline',
                'efficiency_rating' => 'nullable|numeric|min:0|max:100',
                'maintenance_schedule' => 'nullable|array',
            ]);

            $energyData['updated_by'] = auth()->id();
            $energyData['source_details'] = $this->generateSourceDetails($request);
            $energyData['performance_metrics'] = $this->generatePerformanceMetrics($request);
            $energyData['maintenance_schedule'] = $this->generateMaintenanceSchedule($request);

            $source->update($energyData);

            DB::commit();

            return redirect()
                ->route('renewable-energy.show', $source)
                ->with('success', 'تم تحديث مصدر الطاقة المتجددة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث المصدر: ' . $e->getMessage());
        }
    }

    public function destroy(RenewableEnergySource $source)
    {
        try {
            $source->delete();

            return redirect()
                ->route('renewable-energy.index')
                ->with('success', 'تم حذف مصدر الطاقة المتجددة بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف المصدر: ' . $e->getMessage());
        }
    }

    public function monitorPerformance(RenewableEnergySource $source)
    {
        $performance = $this->getRealTimePerformance($source);

        return response()->json([
            'success' => true,
            'performance' => $performance
        ]);
    }

    public function scheduleMaintenance(RenewableEnergySource $source, Request $request)
    {
        try {
            $maintenanceData = $this->scheduleSourceMaintenance($source, $request);
            
            return response()->json([
                'success' => true,
                'maintenance' => $maintenanceData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function calculateOutput(RenewableEnergySource $source)
    {
        $output = $this->calculateEnergyOutput($source);

        return response()->json([
            'success' => true,
            'output' => $output
        ]);
    }

    public function generateReport(RenewableEnergySource $source)
    {
        try {
            $reportData = $this->generateEnergyReport($source);
            
            return response()->json([
                'success' => true,
                'report' => $reportData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generateSourceDetails($request)
    {
        return [
            'manufacturer' => $request->input('manufacturer', ''),
            'model' => $request->input('model', ''),
            'serial_number' => $request->input('serial_number', ''),
            'installation_company' => $request->input('installation_company', ''),
            'warranty_expiry' => $request->input('warranty_expiry', ''),
            'technical_specifications' => $request->input('technical_specifications', []),
            'location_details' => $request->input('location_details', []),
            'grid_connection' => $request->input('grid_connection', false),
        ];
    }

    private function generatePerformanceMetrics($request)
    {
        return [
            'peak_output' => $request->input('peak_output', 0),
            'average_output' => $request->input('average_output', 0),
            'capacity_factor' => $request->input('capacity_factor', 0),
            'availability' => $request->input('availability', 0),
            'performance_ratio' => $request->input('performance_ratio', 0),
            'uptime_percentage' => $request->input('uptime_percentage', 0),
            'last_updated' => now()->toDateTimeString(),
        ];
    }

    private function generateMaintenanceSchedule($request)
    {
        return [
            'routine_maintenance' => $request->input('routine_maintenance', []),
            'preventive_maintenance' => $request->input('preventive_maintenance', []),
            'corrective_maintenance' => $request->input('corrective_maintenance', []),
            'next_maintenance_date' => $request->input('next_maintenance_date', ''),
            'maintenance_interval' => $request->input('maintenance_interval', 90),
            'last_maintenance_type' => $request->input('last_maintenance_type', ''),
        ];
    }

    private function getSourcesByType()
    {
        return RenewableEnergySource::select('source_type', DB::raw('COUNT(*) as count, SUM(capacity) as total_capacity'))
            ->groupBy('source_type')
            ->get();
    }

    private function getEnergyTrends()
    {
        return RenewableEnergySource::selectRaw('MONTH(installation_date) as month, SUM(energy_generated) as total_energy')
            ->whereYear('installation_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getTopPerformers()
    {
        return RenewableEnergySource::with(['property'])
            ->select('property_id', 'source_type', DB::raw('AVG(efficiency_rating) as avg_efficiency'))
            ->where('efficiency_rating', '>', 0)
            ->groupBy('property_id', 'source_type')
            ->orderBy('avg_efficiency', 'desc')
            ->take(5)
            ->get();
    }

    private function getMaintenanceSchedule()
    {
        return RenewableEnergySource::with(['property'])
            ->where('status', '!=', 'offline')
            ->where('last_maintenance_date', '<', now()->subDays(80))
            ->orderBy('last_maintenance_date', 'asc')
            ->take(10)
            ->get();
    }

    private function getSourceDetails($source)
    {
        return [
            'efficiency_percentage' => $source->getEfficiencyPercentage(),
            'monthly_generation' => $source->getMonthlyEnergyGeneration(),
            'annual_carbon_offset' => $source->getAnnualCarbonOffset(),
            'needs_maintenance' => $source->needsMaintenance(),
            'days_since_maintenance' => $source->last_maintenance_date ? 
                $source->last_maintenance_date->diffInDays(now()) : 0,
            'performance_grade' => $this->getPerformanceGrade($source->efficiency_rating),
        ];
    }

    private function getPerformanceAnalysis($source)
    {
        return [
            'current_performance' => $source->current_output,
            'maximum_performance' => $source->capacity,
            'performance_ratio' => $source->getEfficiencyPercentage(),
            'energy_generated_today' => $source->energy_generated / 365,
            'carbon_offset_today' => $source->carbon_offset / 365,
            'cost_savings' => $this->calculateCostSavings($source),
            'environmental_impact' => $this->calculateEnvironmentalImpact($source),
        ];
    }

    private function getMaintenanceStatus($source)
    {
        return [
            'last_maintenance' => $source->last_maintenance_date?->toDateString(),
            'next_maintenance' => $this->calculateNextMaintenanceDate($source),
            'maintenance_overdue' => $source->needsMaintenance(),
            'maintenance_history' => $source->maintenance_schedule ?? [],
            'maintenance_cost' => $this->estimateMaintenanceCost($source),
        ];
    }

    private function getPerformanceGrade($efficiency)
    {
        if ($efficiency >= 90) return 'A+';
        if ($efficiency >= 85) return 'A';
        if ($efficiency >= 80) return 'B+';
        if ($efficiency >= 75) return 'B';
        if ($efficiency >= 70) return 'C+';
        if ($efficiency >= 65) return 'C';
        if ($efficiency >= 60) return 'D';
        return 'F';
    }

    private function calculateCostSavings($source)
    {
        $energyGenerated = $source->energy_generated;
        $gridCostPerKwh = 0.15; // $0.15 per kWh
        
        return $energyGenerated * $gridCostPerKwh;
    }

    private function calculateEnvironmentalImpact($source)
    {
        return [
            'carbon_offset' => $source->carbon_offset,
            'trees_equivalent' => $source->carbon_offset / 21, // 21kg CO2 per tree per year
            'cars_off_road' => $source->carbon_offset / 4600, // 4600kg CO2 per car per year
            'homes_powered' => $source->energy_generated / 10920, // 10920 kWh per home per year
        ];
    }

    private function calculateNextMaintenanceDate($source)
    {
        $lastMaintenance = $source->last_maintenance_date ?? $source->installation_date;
        $interval = $source->maintenance_schedule['maintenance_interval'] ?? 90;
        
        return $lastMaintenance->addDays($interval)->toDateString();
    }

    private function estimateMaintenanceCost($source)
    {
        $baseCost = 500;
        $capacityMultiplier = $source->capacity / 10; // Cost per 10kW
        
        return $baseCost * $capacityMultiplier;
    }

    private function getRealTimePerformance($source)
    {
        return [
            'current_output' => $source->current_output,
            'efficiency' => $source->getEfficiencyPercentage(),
            'status' => $source->status,
            'last_updated' => now()->toDateTimeString(),
            'alerts' => $this->getPerformanceAlerts($source),
        ];
    }

    private function getPerformanceAlerts($source)
    {
        $alerts = [];
        
        if ($source->getEfficiencyPercentage() < 70) {
            $alerts[] = 'Low efficiency detected';
        }

        if ($source->needsMaintenance()) {
            $alerts[] = 'Maintenance required';
        }

        if ($source->status === 'offline') {
            $alerts[] = 'System offline';
        }

        return $alerts;
    }

    private function scheduleSourceMaintenance($source, $request)
    {
        $maintenanceDate = $request->input('maintenance_date');
        $maintenanceType = $request->input('maintenance_type');
        $description = $request->input('description', '');

        return [
            'maintenance_id' => uniqid('maint_'),
            'source_id' => $source->id,
            'scheduled_date' => $maintenanceDate,
            'maintenance_type' => $maintenanceType,
            'description' => $description,
            'estimated_cost' => $this->estimateMaintenanceCost($source),
            'status' => 'scheduled',
        ];
    }

    private function calculateEnergyOutput($source)
    {
        $capacity = $source->capacity;
        $efficiency = $source->efficiency_rating / 100;
        $hoursPerDay = 24;
        
        return [
            'daily_output' => $capacity * $efficiency * $hoursPerDay,
            'monthly_output' => $capacity * $efficiency * $hoursPerDay * 30,
            'annual_output' => $capacity * $efficiency * $hoursPerDay * 365,
        ];
    }

    private function generateEnergyReport($source)
    {
        return [
            'report_id' => uniqid('energy_report_'),
            'source_id' => $source->id,
            'source_type' => $source->source_type,
            'property_name' => $source->property->property_name,
            'report_period' => now()->subMonth()->toDateString() . ' to ' . now()->toDateString(),
            'energy_generated' => $source->energy_generated,
            'carbon_offset' => $source->carbon_offset,
            'efficiency_rating' => $source->efficiency_rating,
            'performance_metrics' => $source->performance_metrics,
            'generated_at' => now()->toDateTimeString(),
        ];
    }
}
