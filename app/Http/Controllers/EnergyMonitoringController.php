<?php

namespace App\Http\Controllers;

use App\Models\EnergyMonitoring;
use App\Models\SmartProperty;
use App\Models\IoTDevice;
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

        $recentData = EnergyMonitoring::with(['property', 'device'])
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
    
    public function create()
    {
        $properties = \App\Models\SmartProperty::with('property')
            ->get()
            ->sortBy(function($item) {
                return $item->property ? $item->property->title : '';
            });
        $devices = \App\Models\IoTDevice::orderBy('brand')->orderBy('model')->get();
        
        return view('iot.energy-monitoring.create', compact('properties', 'devices'));
    }
    
    public function addMonitoring()
    {
        return redirect()->route('iot.energy.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:smart_properties,id',
            'device_id' => 'nullable|exists:iot_devices,id',
            'consumption_kwh' => 'required|numeric|min:0',
            'savings_amount' => 'nullable|numeric|min:0',
            'efficiency_score' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive,maintenance',
            'monitoring_type' => 'required|string',
            'last_reading_at' => 'nullable|date',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            EnergyMonitoring::create($validated);

            return redirect()
                ->route('iot.energy.dashboard')
                ->with('success', 'Energy monitoring data created successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create energy monitoring data: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            // Try to find the record
            $data = EnergyMonitoring::find($id);
            
            if (!$data) {
                \Log::info("Energy monitoring record not found for ID: {$id}");
                return redirect()->route('iot.energy.dashboard')->with('error', 'Energy monitoring data not found');
            }
            
            // Load relationships separately with error handling
            try {
                $data->load(['property', 'device']);
            } catch (\Exception $e) {
                \Log::warning("Failed to load relationships for energy monitoring ID {$id}: " . $e->getMessage());
                // Continue without relationships if they fail
            }

            return view('iot.energy-monitoring.show', compact('data'));
            
        } catch (\Exception $e) {
            \Log::error("Error in show method: " . $e->getMessage());
            return redirect()->route('iot.energy.dashboard')->with('error', 'Error loading energy monitoring data');
        }
    }

    public function edit($id)
    {
        $data = EnergyMonitoring::find($id);
        $properties = \App\Models\SmartProperty::with('property')
            ->get()
            ->sortBy(function($item) {
                return $item->property ? $item->property->title : '';
            });
        $devices = \App\Models\IoTDevice::orderBy('brand')->orderBy('model')->get();
        
        if (!$data) {
            return redirect()->route('iot.energy.dashboard')->with('error', 'Energy monitoring data not found');
        }

        return view('iot.energy-monitoring.edit', compact('data', 'properties', 'devices'));
    }

    public function update(Request $request, $id)
    {
        $data = EnergyMonitoring::find($id);
        
        if (!$data) {
            return redirect()->route('iot.energy.dashboard')->with('error', 'Energy monitoring data not found');
        }

        $validated = $request->validate([
            'property_id' => 'required|exists:smart_properties,id',
            'device_id' => 'nullable|exists:iot_devices,id',
            'consumption_kwh' => 'required|numeric|min:0',
            'savings_amount' => 'nullable|numeric|min:0',
            'efficiency_score' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive,maintenance',
            'monitoring_type' => 'required|string',
            'last_reading_at' => 'nullable|date',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            $data->update($validated);

            return redirect()
                ->route('iot.energy.dashboard')
                ->with('success', 'Energy monitoring data updated successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update energy monitoring data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $data = EnergyMonitoring::find($id);
        
        if (!$data) {
            return redirect()->route('iot.energy.dashboard')->with('error', 'Energy monitoring data not found');
        }

        try {
            $data->delete();

            return redirect()
                ->route('iot.energy.dashboard')
                ->with('success', 'Energy monitoring data deleted successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete energy monitoring data: ' . $e->getMessage());
        }
    }

    public function generateReport()
    {
        $data = EnergyMonitoring::with(['property', 'device'])->get();
        
        return view('iot.energy-monitoring.report', compact('data'));
    }

    public function viewTrends()
    {
        $consumptionTrends = $this->getConsumptionTrends();
        $costAnalysis = $this->getCostAnalysis();
        $monthlyData = $this->getMonthlyTrends();
        $deviceEfficiency = $this->getDeviceEfficiency();
        
        return view('iot.energy-monitoring.trends', compact(
            'consumptionTrends', 
            'costAnalysis', 
            'monthlyData', 
            'deviceEfficiency'
        ));
    }

    // Helper methods
    private function getAverageDailyConsumption()
    {
        return EnergyMonitoring::avg('consumption_kwh');
    }

    private function getAverageEfficiencyScore()
    {
        return EnergyMonitoring::avg('efficiency_score');
    }

    private function getMonthlyTrends()
    {
        return EnergyMonitoring::selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                AVG(consumption_kwh) as avg_consumption,
                SUM(consumption_kwh) as total_consumption,
                AVG(efficiency_score) as avg_efficiency,
                COUNT(*) as count
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getDeviceEfficiency()
    {
        return EnergyMonitoring::with('device')
            ->selectRaw('
                device_id,
                AVG(consumption_kwh) as avg_consumption,
                AVG(efficiency_score) as avg_efficiency,
                COUNT(*) as count
            ')
            ->whereNotNull('device_id')
            ->groupBy('device_id')
            ->get();
    }

    private function getConsumptionTrends()
    {
        // Get last 30 days of consumption data
        return EnergyMonitoring::where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at')
            ->get()
            ->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            })
            ->map(function($group, $date) {
                return [
                    'date' => $date,
                    'consumption' => $group->sum('consumption_kwh'),
                    'count' => $group->count()
                ];
            });
    }

    private function getCostAnalysis()
    {
        return [
            'total_cost' => EnergyMonitoring::sum('consumption_kwh') * 0.12, // Assuming $0.12 per kWh
            'average_monthly' => EnergyMonitoring::avg('consumption_kwh') * 0.12 * 30,
            'potential_savings' => EnergyMonitoring::sum('savings_amount'),
            'efficiency_improvement' => $this->getAverageEfficiencyScore() - 75 // Baseline efficiency
        ];
    }
}
