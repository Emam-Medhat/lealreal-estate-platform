<?php

namespace App\Http\Controllers;

use App\Models\ClimateControl;
use App\Models\SmartProperty;
use App\Models\IotDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClimateControlController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_systems' => ClimateControl::count(),
            'active_systems' => ClimateControl::where('status', 'active')->count(),
            'average_temperature' => $this->getAverageTemperature(),
            'energy_savings' => $this->getEnergySavings(),
            'comfort_score' => $this->getAverageComfortScore(),
            'automation_count' => $this->getAutomationCount(),
        ];

        $recentSystems = ClimateControl::with(['property', 'devices'])
            ->latest()
            ->take(10)
            ->get();

        $temperatureTrends = $this->getTemperatureTrends();
        $systemStatus = $this->getClimateSystemStatus();

        return view('iot.climate-control.dashboard', compact(
            'stats', 
            'recentSystems', 
            'temperatureTrends', 
            'systemStatus'
        ));
    }

    public function index(Request $request)
    {
        $query = ClimateControl::with(['property', 'devices']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('system_type')) {
            $query->where('system_type', $request->system_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $systems = $query->latest()->paginate(12);

        $systemTypes = ['hvac', 'heating', 'cooling', 'ventilation', 'humidity_control'];
        $statuses = ['active', 'inactive', 'maintenance', 'error'];

        return view('iot.climate-control.index', compact(
            'systems', 
            'systemTypes', 
            'statuses'
        ));
    }

    public function create()
    {
        $properties = SmartProperty::all();
        $systemTypes = ['hvac', 'heating', 'cooling', 'ventilation', 'humidity_control'];
        $devices = IotDevice::where('device_type', 'climate')->get();

        return view('iot.climate-control.create', compact(
            'properties', 
            'systemTypes', 
            'devices'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $climateData = $request->validate([
                'property_id' => 'required|exists:smart_properties,id',
                'system_name' => 'required|string|max:255',
                'system_type' => 'required|in:hvac,heating,cooling,ventilation,humidity_control',
                'target_temperature' => 'required|numeric|min:10|max:40',
                'target_humidity' => 'nullable|numeric|min:20|max:80',
                'schedule' => 'nullable|array',
                'zones' => 'nullable|array',
                'energy_saving_mode' => 'required|boolean',
                'auto_adjustment' => 'required|boolean',
                'status' => 'required|in:active,inactive,maintenance,error',
            ]);

            $climateData['created_by'] = auth()->id();
            $climateData['climate_metadata'] = $this->generateClimateMetadata($request);

            $climate = ClimateControl::create($climateData);

            // Link climate devices
            if ($request->has('device_ids')) {
                $this->linkClimateDevices($climate, $request->device_ids);
            }

            // Set up zones
            if ($request->has('zones')) {
                $this->setupClimateZones($climate, $request->zones);
            }

            // Configure schedule
            if ($request->has('schedule')) {
                $this->setupClimateSchedule($climate, $request->schedule);
            }

            DB::commit();

            return redirect()
                ->route('climate-control.show', $climate)
                ->with('success', 'تم إعداد نظام التحكم في المناخ بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إعداد النظام: ' . $e->getMessage());
        }
    }

    public function show(ClimateControl $climate)
    {
        $climate->load(['property', 'devices']);
        $currentReadings = $this->getCurrentReadings($climate);
        $systemStatus = $this->getSystemRealTimeStatus($climate);
        $energyUsage = $this->getEnergyUsage($climate);

        return view('iot.climate-control.show', compact(
            'climate', 
            'currentReadings', 
            'systemStatus', 
            'energyUsage'
        ));
    }

    public function edit(ClimateControl $climate)
    {
        $properties = SmartProperty::all();
        $systemTypes = ['hvac', 'heating', 'cooling', 'ventilation', 'humidity_control'];
        $devices = IotDevice::where('device_type', 'climate')->get();

        return view('iot.climate-control.edit', compact(
            'climate', 
            'properties', 
            'systemTypes', 
            'devices'
        ));
    }

    public function update(Request $request, ClimateControl $climate)
    {
        DB::beginTransaction();
        try {
            $climateData = $request->validate([
                'system_name' => 'required|string|max:255',
                'system_type' => 'required|in:hvac,heating,cooling,ventilation,humidity_control',
                'target_temperature' => 'required|numeric|min:10|max:40',
                'target_humidity' => 'nullable|numeric|min:20|max:80',
                'schedule' => 'nullable|array',
                'zones' => 'nullable|array',
                'energy_saving_mode' => 'required|boolean',
                'auto_adjustment' => 'required|boolean',
                'status' => 'required|in:active,inactive,maintenance,error',
            ]);

            $climateData['updated_by'] = auth()->id();
            $climateData['climate_metadata'] = $this->generateClimateMetadata($request);

            $climate->update($climateData);

            // Update linked devices
            if ($request->has('device_ids')) {
                $this->updateClimateDevices($climate, $request->device_ids);
            }

            // Update zones
            if ($request->has('zones')) {
                $this->updateClimateZones($climate, $request->zones);
            }

            // Update schedule
            if ($request->has('schedule')) {
                $this->updateClimateSchedule($climate, $request->schedule);
            }

            DB::commit();

            return redirect()
                ->route('climate-control.show', $climate)
                ->with('success', 'تم تحديث نظام التحكم في المناخ بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث النظام: ' . $e->getMessage());
        }
    }

    public function destroy(ClimateControl $climate)
    {
        try {
            // Unlink devices
            $this->unlinkDevicesFromClimate($climate);

            // Delete climate system
            $climate->delete();

            return redirect()
                ->route('climate-control.index')
                ->with('success', 'تم حذف نظام التحكم في المناخ بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف النظام: ' . $e->getMessage());
        }
    }

    public function setTemperature(ClimateControl $climate, Request $request)
    {
        try {
            $temperatureData = $request->validate([
                'temperature' => 'required|numeric|min:10|max:40',
                'zone' => 'nullable|string',
            ]);

            $result = $this->adjustTemperature($climate, $temperatureData);

            return response()->json([
                'success' => true,
                'result' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function setHumidity(ClimateControl $climate, Request $request)
    {
        try {
            $humidityData = $request->validate([
                'humidity' => 'required|numeric|min:20|max:80',
                'zone' => 'nullable|string',
            ]);

            $result = $this->adjustHumidity($climate, $humidityData);

            return response()->json([
                'success' => true,
                'result' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function toggleMode(ClimateControl $climate, Request $request)
    {
        try {
            $modeData = $request->validate([
                'mode' => 'required|in:auto,manual,eco,comfort,sleep',
            ]);

            $result = $this->setClimateMode($climate, $modeData['mode']);

            return response()->json([
                'success' => true,
                'result' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getRealTimeData(ClimateControl $climate)
    {
        try {
            $realTimeData = [
                'current_temperature' => $this->getCurrentTemperature($climate),
                'target_temperature' => $climate->target_temperature,
                'current_humidity' => $this->getCurrentHumidity($climate),
                'target_humidity' => $climate->target_humidity,
                'system_mode' => $this->getCurrentMode($climate),
                'energy_usage' => $this->getCurrentEnergyUsage($climate),
                'zone_status' => $this->getZoneStatus($climate),
                'timestamp' => now()->toDateTimeString(),
            ];

            return response()->json($realTimeData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function generateReport(ClimateControl $climate, Request $request)
    {
        try {
            $reportData = $request->validate([
                'report_type' => 'required|in:daily,weekly,monthly,custom',
                'date_from' => 'required|date',
                'date_to' => 'required|date|after_or_equal:date_from',
                'format' => 'required|in:json,csv,pdf',
            ]);

            $report = $this->generateClimateReport($climate, $reportData);

            return response()->json([
                'success' => true,
                'report' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generateClimateMetadata($request)
    {
        return [
            'efficiency_rating' => $this->calculateEfficiencyRating($request),
            'zone_count' => count($request->zones ?? []),
            'automation_level' => $this->calculateAutomationLevel($request),
            'energy_optimization' => $this->calculateEnergyOptimization($request),
            'comfort_level' => $this->calculateComfortLevel($request),
            'created_at' => now()->toDateTimeString(),
        ];
    }

    private function linkClimateDevices($climate, $deviceIds)
    {
        foreach ($deviceIds as $deviceId) {
            $climate->devices()->attach($deviceId, [
                'role' => 'climate_controller',
                'created_at' => now(),
            ]);
        }
    }

    private function setupClimateZones($climate, $zones)
    {
        $climate->update([
            'zones' => $zones,
        ]);
    }

    private function setupClimateSchedule($climate, $schedule)
    {
        $climate->update([
            'schedule' => $schedule,
        ]);
    }

    private function adjustTemperature(ClimateControl $climate, $temperatureData)
    {
        $zone = $temperatureData['zone'] ?? 'all';
        $newTemperature = $temperatureData['temperature'];

        // Update target temperature
        if ($zone === 'all') {
            $climate->update(['target_temperature' => $newTemperature]);
        } else {
            $zones = $climate->zones ?? [];
            $zones[$zone]['target_temperature'] = $newTemperature;
            $climate->update(['zones' => $zones]);
        }

        // Send command to devices
        $this->sendTemperatureCommand($climate, $newTemperature, $zone);

        return [
            'command_id' => uniqid('cmd_'),
            'status' => 'executed',
            'temperature' => $newTemperature,
            'zone' => $zone,
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    private function adjustHumidity(ClimateControl $climate, $humidityData)
    {
        $zone = $humidityData['zone'] ?? 'all';
        $newHumidity = $humidityData['humidity'];

        // Update target humidity
        if ($zone === 'all') {
            $climate->update(['target_humidity' => $newHumidity]);
        } else {
            $zones = $climate->zones ?? [];
            $zones[$zone]['target_humidity'] = $newHumidity;
            $climate->update(['zones' => $zones]);
        }

        // Send command to devices
        $this->sendHumidityCommand($climate, $newHumidity, $zone);

        return [
            'command_id' => uniqid('cmd_'),
            'status' => 'executed',
            'humidity' => $newHumidity,
            'zone' => $zone,
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    private function setClimateMode(ClimateControl $climate, $mode)
    {
        $climate->update([
            'current_mode' => $mode,
        ]);

        // Send mode command to devices
        $this->sendModeCommand($climate, $mode);

        return [
            'command_id' => uniqid('cmd_'),
            'status' => 'executed',
            'mode' => $mode,
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    private function getCurrentTemperature($climate)
    {
        // Get current temperature from sensors
        return rand(18, 28); // Placeholder
    }

    private function getCurrentHumidity($climate)
    {
        // Get current humidity from sensors
        return rand(40, 70); // Placeholder
    }

    private function getCurrentMode($climate)
    {
        return $climate->current_mode ?? 'auto';
    }

    private function getCurrentEnergyUsage($climate)
    {
        // Calculate current energy usage
        return rand(1.5, 5.5); // Placeholder in kW
    }

    private function getZoneStatus($climate)
    {
        $zones = $climate->zones ?? [];
        $zoneStatus = [];

        foreach ($zones as $zoneName => $zoneData) {
            $zoneStatus[$zoneName] = [
                'current_temperature' => $zoneData['current_temperature'] ?? 22,
                'target_temperature' => $zoneData['target_temperature'] ?? 22,
                'current_humidity' => $zoneData['current_humidity'] ?? 50,
                'target_humidity' => $zoneData['target_humidity'] ?? 50,
                'status' => 'active',
            ];
        }

        return $zoneStatus;
    }

    private function sendTemperatureCommand($climate, $temperature, $zone)
    {
        // Send temperature adjustment command to devices
        // Implementation would depend on device communication protocol
    }

    private function sendHumidityCommand($climate, $humidity, $zone)
    {
        // Send humidity adjustment command to devices
        // Implementation would depend on device communication protocol
    }

    private function sendModeCommand($climate, $mode)
    {
        // Send mode change command to devices
        // Implementation would depend on device communication protocol
    }

    private function calculateEfficiencyRating($request)
    {
        $rating = 70; // Base rating
        
        if ($request->energy_saving_mode) $rating += 15;
        if ($request->auto_adjustment) $rating += 10;
        if ($request->has('schedule')) $rating += 5;
        
        return min(100, $rating);
    }

    private function calculateAutomationLevel($request)
    {
        $level = 0;
        
        if ($request->auto_adjustment) $level += 30;
        if ($request->has('schedule')) $level += 25;
        if ($request->energy_saving_mode) $level += 20;
        
        if ($level < 25) return 'basic';
        if ($level < 50) return 'intermediate';
        if ($level < 75) return 'advanced';
        return 'premium';
    }

    private function calculateEnergyOptimization($request)
    {
        $optimization = 20; // Base 20%
        
        if ($request->energy_saving_mode) $optimization += 15;
        if ($request->auto_adjustment) $optimization += 10;
        
        return min(50, $optimization);
    }

    private function calculateComfortLevel($request)
    {
        $comfort = 70; // Base comfort level
        
        if ($request->target_temperature >= 20 && $request->target_temperature <= 24) $comfort += 15;
        if ($request->target_humidity >= 40 && $request->target_humidity <= 60) $comfort += 10;
        
        return min(100, $comfort);
    }

    private function generateClimateReport($climate, $reportData)
    {
        // Generate climate report based on type and date range
        return [
            'report_id' => uniqid('report_'),
            'type' => $reportData['report_type'],
            'period' => [
                'from' => $reportData['date_from'],
                'to' => $reportData['date_to'],
            ],
            'data' => $this->getClimateReportData($climate, $reportData),
            'generated_at' => now()->toDateTimeString(),
        ];
    }

    private function getClimateReportData($climate, $reportData)
    {
        // Get climate data for the report period
        return [
            'average_temperature' => 22.5,
            'average_humidity' => 55,
            'energy_consumption' => 125.5,
            'comfort_score' => 85,
            'efficiency_rating' => 78,
        ];
    }

    private function updateClimateDevices($climate, $deviceIds)
    {
        $climate->devices()->sync($deviceIds);
    }

    private function updateClimateZones($climate, $zones)
    {
        $climate->update([
            'zones' => $zones,
        ]);
    }

    private function updateClimateSchedule($climate, $schedule)
    {
        $climate->update([
            'schedule' => $schedule,
        ]);
    }

    private function unlinkDevicesFromClimate($climate)
    {
        $climate->devices()->detach();
    }

    private function getCurrentReadings($climate)
    {
        return [
            'temperature' => $this->getCurrentTemperature($climate),
            'humidity' => $this->getCurrentHumidity($climate),
            'energy_usage' => $this->getCurrentEnergyUsage($climate),
            'last_updated' => now()->toDateTimeString(),
        ];
    }

    private function getSystemRealTimeStatus($climate)
    {
        return [
            'system_id' => $climate->id,
            'system_name' => $climate->system_name,
            'current_mode' => $this->getCurrentMode($climate),
            'target_temperature' => $climate->target_temperature,
            'target_humidity' => $climate->target_humidity,
            'device_count' => $climate->devices()->count(),
            'active_devices' => $climate->devices()->where('status', 'active')->count(),
            'energy_saving_mode' => $climate->energy_saving_mode,
            'auto_adjustment' => $climate->auto_adjustment,
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    private function getEnergyUsage($climate)
    {
        return [
            'current_usage' => $this->getCurrentEnergyUsage($climate),
            'daily_average' => 4.2,
            'monthly_total' => 126.5,
            'cost_estimate' => 18.97, // at $0.15 per kWh
        ];
    }

    private function getAverageTemperature()
    {
        return ClimateControl::avg('target_temperature') ?? 0;
    }

    private function getEnergySavings()
    {
        return ClimateControl::sum('energy_savings') ?? 0;
    }

    private function getAverageComfortScore()
    {
        return ClimateControl::avg('comfort_score') ?? 0;
    }

    private function getAutomationCount()
    {
        return ClimateControl::where('auto_adjustment', true)->count();
    }

    private function getTemperatureTrends()
    {
        return ClimateControl::selectRaw('DATE(created_at) as date, AVG(target_temperature) as avg_temp')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(30)
            ->get();
    }

    private function getClimateSystemStatus()
    {
        return [
            'total_systems' => ClimateControl::count(),
            'active_systems' => ClimateControl::where('status', 'active')->count(),
            'systems_by_type' => ClimateControl::select('system_type', DB::raw('COUNT(*) as count'))
                ->groupBy('system_type')
                ->get(),
        ];
    }
}
