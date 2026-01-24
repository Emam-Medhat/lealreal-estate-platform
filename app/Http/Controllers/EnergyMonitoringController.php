<?php

namespace App\Http\Controllers;

use App\Models\EnergyMonitoring;
use App\Models\SmartProperty;
use App\Models\IotDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EnergyMonitoringController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_properties' => EnergyMonitoring::count(),
            'active_monitoring' => EnergyMonitoring::where('status', 'active')->count(),
            'total_consumption' => EnergyMonitoring::sum('consumption_kwh'),
            'average_daily' => $this->getAverageDailyConsumption(),
            'energy_savings' => EnergyMonitoring::sum('savings_amount'),
            'efficiency_score' => $this->getAverageEfficiencyScore(),
        ];

        $recentData = EnergyMonitoring::with(['property', 'devices'])
            ->latest()
            ->take(10)
            ->get();

        $consumptionTrends = $this->getConsumptionTrends();
        $costAnalysis = $this->getCostAnalysis();

        return view('iot.energy-monitoring.dashboard', compact(
            'stats', 
            'recentData', 
            'consumptionTrends', 
            'costAnalysis'
        ));
    }

    public function index(Request $request)
    {
        $query = EnergyMonitoring::with(['property', 'devices']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $monitoringData = $query->latest()->paginate(12);

        $statuses = ['active', 'inactive', 'maintenance', 'error'];
        $monitoringTypes = ['basic', 'comprehensive', 'advanced', 'premium'];

        return view('iot.energy-monitoring.index', compact(
            'monitoringData', 
            'statuses', 
            'monitoringTypes'
        ));
    }

    public function create()
    {
        $properties = SmartProperty::all();
        $devices = IotDevice::where('device_type', 'sensor')->get();
        $monitoringTypes = ['basic', 'comprehensive', 'advanced', 'premium'];

        return view('iot.energy-monitoring.create', compact(
            'properties', 
            'devices', 
            'monitoringTypes'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $monitoringData = $request->validate([
                'property_id' => 'required|exists:smart_properties,id',
                'monitoring_type' => 'required|in:basic,comprehensive,advanced,premium',
                'target_consumption' => 'required|numeric|min:0',
                'alert_thresholds' => 'nullable|array',
                'optimization_enabled' => 'required|boolean',
                'renewable_sources' => 'nullable|array',
                'peak_hours' => 'nullable|array',
                'status' => 'required|in:active,inactive,maintenance,error',
            ]);

            $monitoringData['created_by'] = auth()->id();
            $monitoringData['energy_metadata'] = $this->generateEnergyMetadata($request);

            $monitoring = EnergyMonitoring::create($monitoringData);

            // Link devices to monitoring
            if ($request->has('device_ids')) {
                $this->linkDevicesToMonitoring($monitoring, $request->device_ids);
            }

            // Set up alert thresholds
            if ($request->has('alert_thresholds')) {
                $this->setupAlertThresholds($monitoring, $request->alert_thresholds);
            }

            // Configure renewable sources
            if ($request->has('renewable_sources')) {
                $this->configureRenewableSources($monitoring, $request->renewable_sources);
            }

            DB::commit();

            return redirect()
                ->route('energy-monitoring.show', $monitoring)
                ->with('success', 'تم إعداد مراقبة استهلاك الطاقة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إعداد المراقبة: ' . $e->getMessage());
        }
    }

    public function show(EnergyMonitoring $monitoring)
    {
        $monitoring->load(['property', 'devices']);
        $consumptionData = $this->getConsumptionData($monitoring);
        $costAnalysis = $this->getPropertyCostAnalysis($monitoring);
        $efficiencyMetrics = $this->getEfficiencyMetrics($monitoring);

        return view('iot.energy-monitoring.show', compact(
            'monitoring', 
            'consumptionData', 
            'costAnalysis', 
            'efficiencyMetrics'
        ));
    }

    public function edit(EnergyMonitoring $monitoring)
    {
        $properties = SmartProperty::all();
        $devices = IotDevice::where('device_type', 'sensor')->get();
        $monitoringTypes = ['basic', 'comprehensive', 'advanced', 'premium'];

        return view('iot.energy-monitoring.edit', compact(
            'monitoring', 
            'properties', 
            'devices', 
            'monitoringTypes'
        ));
    }

    public function update(Request $request, EnergyMonitoring $monitoring)
    {
        DB::beginTransaction();
        try {
            $monitoringData = $request->validate([
                'monitoring_type' => 'required|in:basic,comprehensive,advanced,premium',
                'target_consumption' => 'required|numeric|min:0',
                'alert_thresholds' => 'nullable|array',
                'optimization_enabled' => 'required|boolean',
                'renewable_sources' => 'nullable|array',
                'peak_hours' => 'nullable|array',
                'status' => 'required|in:active,inactive,maintenance,error',
            ]);

            $monitoringData['updated_by'] = auth()->id();
            $monitoringData['energy_metadata'] = $this->generateEnergyMetadata($request);

            $monitoring->update($monitoringData);

            // Update linked devices
            if ($request->has('device_ids')) {
                $this->updateLinkedDevices($monitoring, $request->device_ids);
            }

            // Update alert thresholds
            if ($request->has('alert_thresholds')) {
                $this->updateAlertThresholds($monitoring, $request->alert_thresholds);
            }

            // Update renewable sources
            if ($request->has('renewable_sources')) {
                $this->updateRenewableSources($monitoring, $request->renewable_sources);
            }

            DB::commit();

            return redirect()
                ->route('energy-monitoring.show', $monitoring)
                ->with('success', 'تم تحديث مراقبة استهلاك الطاقة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث المراقبة: ' . $e->getMessage());
        }
    }

    public function destroy(EnergyMonitoring $monitoring)
    {
        try {
            // Unlink devices
            $this->unlinkDevicesFromMonitoring($monitoring);

            // Delete monitoring
            $monitoring->delete();

            return redirect()
                ->route('energy-monitoring.index')
                ->with('success', 'تم حذف مراقبة استهلاك الطاقة بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف المراقبة: ' . $e->getMessage());
        }
    }

    public function getRealTimeData(EnergyMonitoring $monitoring)
    {
        try {
            $realTimeData = [
                'current_consumption' => $this->getCurrentConsumption($monitoring),
                'daily_consumption' => $this->getDailyConsumption($monitoring),
                'monthly_consumption' => $this->getMonthlyConsumption($monitoring),
                'cost_estimate' => $this->getCostEstimate($monitoring),
                'efficiency_score' => $this->getEfficiencyScore($monitoring),
                'device_status' => $this->getDeviceStatus($monitoring),
                'timestamp' => now()->toDateTimeString(),
            ];

            return response()->json($realTimeData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function generateReport(EnergyMonitoring $monitoring, Request $request)
    {
        try {
            $reportData = $request->validate([
                'report_type' => 'required|in:daily,weekly,monthly,custom',
                'date_from' => 'required|date',
                'date_to' => 'required|date|after_or_equal:date_from',
                'format' => 'required|in:json,csv,pdf',
            ]);

            $report = $this->generateEnergyReport($monitoring, $reportData);

            return response()->json([
                'success' => true,
                'report' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function optimizeUsage(EnergyMonitoring $monitoring)
    {
        try {
            $optimization = $this->optimizeEnergyUsage($monitoring);

            return response()->json([
                'success' => true,
                'optimization' => $optimization
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function setAlertThresholds(EnergyMonitoring $monitoring, Request $request)
    {
        try {
            $thresholds = $request->validate([
                'daily_limit' => 'nullable|numeric|min:0',
                'monthly_limit' => 'nullable|numeric|min:0',
                'peak_threshold' => 'nullable|numeric|min:0',
                'efficiency_threshold' => 'nullable|numeric|min:0|max:100',
            ]);

            $this->updateAlertThresholds($monitoring, $thresholds);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حدود التنبيه بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generateEnergyMetadata($request)
    {
        return [
            'monitoring_level' => $this->calculateMonitoringLevel($request),
            'device_count' => count($request->device_ids ?? []),
            'renewable_percentage' => $this->calculateRenewablePercentage($request),
            'optimization_potential' => $this->calculateOptimizationPotential($request),
            'estimated_savings' => $this->estimateSavings($request),
            'created_at' => now()->toDateTimeString(),
        ];
    }

    private function linkDevicesToMonitoring($monitoring, $deviceIds)
    {
        foreach ($deviceIds as $deviceId) {
            $monitoring->devices()->attach($deviceId, [
                'role' => 'monitor',
                'created_at' => now(),
            ]);
        }
    }

    private function setupAlertThresholds($monitoring, $thresholds)
    {
        $monitoring->update([
            'alert_thresholds' => $thresholds,
        ]);
    }

    private function configureRenewableSources($monitoring, $renewableSources)
    {
        $monitoring->update([
            'renewable_sources' => $renewableSources,
        ]);
    }

    private function getCurrentConsumption($monitoring)
    {
        // Get current consumption from devices
        return rand(1.5, 8.5); // Placeholder
    }

    private function getDailyConsumption($monitoring)
    {
        return $monitoring->consumptionData()
            ->whereDate('created_at', today())
            ->sum('consumption_kwh');
    }

    private function getMonthlyConsumption($monitoring)
    {
        return $monitoring->consumptionData()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('consumption_kwh');
    }

    private function getCostEstimate($monitoring)
    {
        $consumption = $this->getDailyConsumption($monitoring);
        $rate = 0.15; // $0.15 per kWh
        
        return $consumption * $rate;
    }

    private function getEfficiencyScore($monitoring)
    {
        $actualConsumption = $this->getDailyConsumption($monitoring);
        $targetConsumption = $monitoring->target_consumption;
        
        if ($targetConsumption === 0) return 100;
        
        $efficiency = ($targetConsumption / $actualConsumption) * 100;
        return min(100, $efficiency);
    }

    private function getDeviceStatus($monitoring)
    {
        return $monitoring->devices()
            ->select('device_name', 'status', 'last_heartbeat')
            ->get()
            ->toArray();
    }

    private function generateEnergyReport($monitoring, $reportData)
    {
        // Generate report based on type and date range
        return [
            'report_id' => uniqid('report_'),
            'type' => $reportData['report_type'],
            'period' => [
                'from' => $reportData['date_from'],
                'to' => $reportData['date_to'],
            ],
            'data' => $this->getReportData($monitoring, $reportData),
            'generated_at' => now()->toDateTimeString(),
        ];
    }

    private function optimizeEnergyUsage($monitoring)
    {
        return [
            'optimization_id' => uniqid('opt_'),
            'recommendations' => [
                'reduce_peak_usage' => true,
                'adjust_thermostat' => true,
                'optimize_lighting' => true,
                'schedule_appliances' => true,
            ],
            'estimated_savings' => rand(10, 30), // 10-30% savings
            'implementation_priority' => 'high',
        ];
    }

    private function calculateMonitoringLevel($request)
    {
        $score = 0;
        
        if ($request->monitoring_type === 'comprehensive') $score += 25;
        if ($request->monitoring_type === 'advanced') $score += 50;
        if ($request->monitoring_type === 'premium') $score += 75;
        
        if ($request->optimization_enabled) $score += 15;
        if ($request->has('renewable_sources')) $score += 10;
        
        if ($score < 30) return 'basic';
        if ($score < 60) return 'intermediate';
        if ($score < 80) return 'advanced';
        return 'premium';
    }

    private function calculateRenewablePercentage($request)
    {
        $totalSources = count($request->renewable_sources ?? []);
        $renewableSources = $totalSources; // Assuming all provided are renewable
        
        if ($totalSources === 0) return 0;
        
        return ($renewableSources / $totalSources) * 100;
    }

    private function calculateOptimizationPotential($request)
    {
        $potential = 20; // Base 20%
        
        if ($request->optimization_enabled) $potential += 10;
        if ($request->has('peak_hours')) $potential += 15;
        
        return min(50, $potential);
    }

    private function estimateSavings($request)
    {
        $baseSavings = 50; // $50 base savings
        
        if ($request->optimization_enabled) $baseSavings *= 1.5;
        if ($request->has('renewable_sources')) $baseSavings *= 1.2;
        
        return $baseSavings;
    }

    private function getReportData($monitoring, $reportData)
    {
        // Get consumption data for the report period
        return $monitoring->consumptionData()
            ->whereBetween('created_at', [$reportData['date_from'], $reportData['date_to']])
            ->get();
    }

    private function updateLinkedDevices($monitoring, $deviceIds)
    {
        $monitoring->devices()->sync($deviceIds);
    }

    private function updateAlertThresholds($monitoring, $thresholds)
    {
        $monitoring->update([
            'alert_thresholds' => $thresholds,
        ]);
    }

    private function updateRenewableSources($monitoring, $renewableSources)
    {
        $monitoring->update([
            'renewable_sources' => $renewableSources,
        ]);
    }

    private function unlinkDevicesFromMonitoring($monitoring)
    {
        $monitoring->devices()->detach();
    }

    private function getAverageDailyConsumption()
    {
        return EnergyMonitoring::avg('consumption_kwh') ?? 0;
    }

    private function getAverageEfficiencyScore()
    {
        return EnergyMonitoring::avg('efficiency_score') ?? 0;
    }

    private function getConsumptionTrends()
    {
        return EnergyMonitoring::selectRaw('DATE(created_at) as date, AVG(consumption_kwh) as avg_consumption')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(30)
            ->get();
    }

    private function getCostAnalysis()
    {
        return [
            'total_cost' => EnergyMonitoring::sum('consumption_kwh') * 0.15,
            'average_daily_cost' => EnergyMonitoring::avg('consumption_kwh') * 0.15,
            'cost_trend' => 'decreasing',
        ];
    }

    private function getConsumptionData($monitoring)
    {
        return $monitoring->consumptionData()
            ->latest()
            ->take(30)
            ->get();
    }

    private function getPropertyCostAnalysis($monitoring)
    {
        return [
            'current_month_cost' => $this->getMonthlyConsumption($monitoring) * 0.15,
            'target_monthly_cost' => $monitoring->target_consumption * 0.15,
            'savings_potential' => ($monitoring->target_consumption - $this->getMonthlyConsumption($monitoring)) * 0.15,
        ];
    }

    private function getEfficiencyMetrics($monitoring)
    {
        return [
            'current_efficiency' => $this->getEfficiencyScore($monitoring),
            'target_efficiency' => 85,
            'improvement_needed' => max(0, 85 - $this->getEfficiencyScore($monitoring)),
        ];
    }
}
