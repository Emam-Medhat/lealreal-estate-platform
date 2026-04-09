<?php

namespace App\Http\Controllers;

use App\Models\IotDevice;
use App\Models\SmartProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IotDeviceController extends Controller
{
    public function dashboard()
    {
        // Optimized aggregation query
        $aggregates = IotDevice::selectRaw('
            count(*) as total,
            count(case when status = "active" then 1 end) as active,
            count(case when status = "offline" then 1 end) as offline,
            count(case when status = "maintenance" then 1 end) as maintenance
        ')->first();

        $stats = [
            'total_devices' => $aggregates->total,
            'active_devices' => $aggregates->active,
            'offline_devices' => $aggregates->offline,
            'maintenance_devices' => $aggregates->maintenance,
            'device_types' => $this->getDeviceTypeDistribution(),
            'battery_status' => $this->getBatteryStatusOverview(),
        ];

        $recentDevices = IotDevice::with(['property', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('iot.devices.dashboard', compact('stats', 'recentDevices'));
    }

    public function index(Request $request)
    {
        $query = IotDevice::with(['property', 'user']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $devices = $query->latest()->paginate(12);

        $deviceTypes = ['sensor', 'actuator', 'controller', 'monitor', 'security'];
        $statuses = ['active', 'inactive', 'offline', 'maintenance', 'error'];

        return view('iot.devices.index', compact('devices', 'deviceTypes', 'statuses'));
    }

    public function create()
    {
        $properties = SmartProperty::all();
        $deviceTypes = ['sensor', 'actuator', 'controller', 'monitor', 'security'];
        $manufacturers = ['Philips', 'Nest', 'Amazon', 'Google', 'Apple', 'Samsung', 'Xiaomi'];

        return view('iot.devices.create', compact('properties', 'deviceTypes', 'manufacturers'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $deviceData = $request->validate([
                'property_id' => 'required|exists:smart_properties,id',
                'user_id' => 'required|exists:users,id',
                'device_name' => 'required|string|max:255',
                'device_type' => 'required|in:sensor,actuator,controller,monitor,security',
                'device_model' => 'required|string|max:255',
                'manufacturer' => 'required|string|max:255',
                'serial_number' => 'required|string|max:255|unique:iot_devices',
                'location' => 'required|string|max:255',
                'configuration' => 'nullable|array',
                'firmware_version' => 'nullable|string|max:50',
                'mac_address' => 'nullable|string|max:17',
                'ip_address' => 'nullable|ip',
                'status' => 'required|in:active,inactive,offline,maintenance,error',
            ]);

            $deviceData['created_by'] = auth()->id();
            $deviceData['device_metadata'] = $this->generateDeviceMetadata($request);

            $device = IotDevice::create($deviceData);

            // Initialize device connection
            $this->initializeDeviceConnection($device);

            DB::commit();

            return redirect()
                ->route('iot-device.show', $device)
                ->with('success', 'تم تسجيل الجهاز الذكي بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تسجيل الجهاز: ' . $e->getMessage());
        }
    }

    public function show(IotDevice $device)
    {
        $device->load(['property', 'user']);
        $deviceData = $this->getDeviceRealTimeData($device);
        $deviceHistory = $this->getDeviceHistory($device);

        return view('iot.devices.show', compact('device', 'deviceData', 'deviceHistory'));
    }

    public function edit(IotDevice $device)
    {
        $properties = SmartProperty::all();
        $deviceTypes = ['sensor', 'actuator', 'controller', 'monitor', 'security'];
        $manufacturers = ['Philips', 'Nest', 'Amazon', 'Google', 'Apple', 'Samsung', 'Xiaomi'];

        return view('iot.devices.edit', compact('device', 'properties', 'deviceTypes', 'manufacturers'));
    }

    public function update(Request $request, IotDevice $device)
    {
        DB::beginTransaction();
        try {
            $deviceData = $request->validate([
                'device_name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'configuration' => 'nullable|array',
                'firmware_version' => 'nullable|string|max:50',
                'ip_address' => 'nullable|ip',
                'status' => 'required|in:active,inactive,offline,maintenance,error',
            ]);

            $deviceData['updated_by'] = auth()->id();
            $deviceData['device_metadata'] = $this->generateDeviceMetadata($request);

            $device->update($deviceData);

            // Update device configuration
            $this->updateDeviceConfiguration($device, $request->configuration ?? []);

            DB::commit();

            return redirect()
                ->route('iot-device.show', $device)
                ->with('success', 'تم تحديث الجهاز الذكي بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الجهاز: ' . $e->getMessage());
        }
    }

    public function destroy(IotDevice $device)
    {
        try {
            // Disconnect device
            $this->disconnectDevice($device);

            // Delete device
            $device->delete();

            return redirect()
                ->route('iot-device.index')
                ->with('success', 'تم حذف الجهاز الذكي بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف الجهاز: ' . $e->getMessage());
        }
    }

    public function controlDevice(Request $request, IotDevice $device)
    {
        try {
            $command = $request->validate([
                'command' => 'required|string',
                'parameters' => 'nullable|array',
                'timeout' => 'nullable|integer|min:1|max:60',
            ]);

            $result = $this->executeDeviceCommand($device, $command);

            return response()->json([
                'success' => true,
                'result' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getDeviceData(IotDevice $device)
    {
        try {
            $data = $this->getDeviceRealTimeData($device);

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateFirmware(Request $request, IotDevice $device)
    {
        try {
            $firmwareData = $request->validate([
                'firmware_file' => 'required|file|mimes:bin,hex|max:10240',
                'version' => 'required|string|max:50',
                'release_notes' => 'nullable|string',
            ]);

            $result = $this->updateDeviceFirmware($device, $firmwareData);

            return response()->json([
                'success' => true,
                'result' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function restartDevice(IotDevice $device)
    {
        try {
            $result = $this->restartDeviceService($device);

            return response()->json([
                'success' => true,
                'result' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function diagnostics(IotDevice $device)
    {
        try {
            $diagnostics = $this->runDeviceDiagnostics($device);

            return response()->json($diagnostics);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generateDeviceMetadata($request)
    {
        return [
            'connection_type' => $request->connection_type ?? 'wifi',
            'protocol_version' => $request->protocol_version ?? '1.0',
            'encryption_enabled' => $request->encryption_enabled ?? true,
            'battery_level' => $request->battery_level ?? 100,
            'signal_strength' => $request->signal_strength ?? 'excellent',
            'last_maintenance' => $request->last_maintenance ?? null,
            'created_at' => now()->toDateTimeString(),
        ];
    }

    private function initializeDeviceConnection($device)
    {
        // Initialize device connection and send initial configuration
        $device->update([
            'last_heartbeat' => now(),
            'connection_status' => 'connected',
        ]);
    }

    private function getDeviceRealTimeData($device)
    {
        return [
            'device_id' => $device->id,
            'device_name' => $device->device_name,
            'status' => $device->status,
            'last_heartbeat' => $device->last_heartbeat,
            'battery_level' => $device->device_metadata['battery_level'] ?? 100,
            'signal_strength' => $device->device_metadata['signal_strength'] ?? 'good',
            'current_data' => $device->device_data ?? [],
            'configuration' => $device->configuration ?? [],
        ];
    }

    private function getDeviceHistory($device)
    {
        return $device->logs()
            ->latest()
            ->take(50)
            ->get();
    }

    private function executeDeviceCommand($device, $command)
    {
        // Execute command on device
        return [
            'command_id' => uniqid('cmd_'),
            'status' => 'executed',
            'timestamp' => now()->toDateTimeString(),
            'response' => 'Command executed successfully',
        ];
    }

    private function updateDeviceConfiguration($device, $configuration)
    {
        // Update device configuration
        $device->update([
            'configuration' => $configuration,
            'last_config_update' => now(),
        ]);
    }

    private function disconnectDevice($device)
    {
        // Disconnect device from network
        $device->update([
            'status' => 'offline',
            'connection_status' => 'disconnected',
        ]);
    }

    private function updateDeviceFirmware($device, $firmwareData)
    {
        // Update device firmware
        $device->update([
            'firmware_version' => $firmwareData['version'],
            'last_firmware_update' => now(),
        ]);

        return [
            'status' => 'success',
            'version' => $firmwareData['version'],
            'updated_at' => now()->toDateTimeString(),
        ];
    }

    private function restartDeviceService($device)
    {
        // Restart device service
        $device->update([
            'last_restart' => now(),
        ]);

        return [
            'status' => 'restarted',
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    private function runDeviceDiagnostics($device)
    {
        return [
            'device_id' => $device->id,
            'tests' => [
                'connectivity' => 'pass',
                'battery' => $device->device_metadata['battery_level'] ?? 100,
                'firmware' => 'up_to_date',
                'memory' => 'optimal',
                'network' => 'stable',
            ],
            'overall_status' => 'healthy',
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    private function getDeviceTypeDistribution()
    {
        return IotDevice::select('device_type', DB::raw('COUNT(*) as count'))
            ->groupBy('device_type')
            ->get();
    }

    private function getBatteryStatusOverview()
    {
        return [
            'excellent' => IotDevice::whereJsonContains('device_metadata->battery_level', '>=', 75)->count(),
            'good' => IotDevice::whereJsonContains('device_metadata->battery_level', '>=', 50)->count(),
            'low' => IotDevice::whereJsonContains('device_metadata->battery_level', '<', 25)->count(),
            'critical' => IotDevice::whereJsonContains('device_metadata->battery_level', '<', 10)->count(),
        ];
    }
}
