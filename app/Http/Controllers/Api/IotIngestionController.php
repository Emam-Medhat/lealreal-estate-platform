<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IoTDevice;
use App\Models\EnergyMonitoringData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class IotIngestionController extends Controller
{
    public function ingest(Request $request)
    {
        // 1. Authenticate Device
        $token = $request->header('X-Device-Token') ?? $request->input('device_token');
        
        if (!$token) {
            return response()->json(['error' => 'Unauthorized: Missing Device Token'], 401);
        }

        $deviceId = Cache::remember("device_id_by_token_{$token}", 3600, function () use ($token) {
            return IoTDevice::where('oauth_token', $token)->value('id');
        });

        if (!$deviceId) {
            return response()->json(['error' => 'Unauthorized: Invalid Device Token'], 401);
        }

        $device = IoTDevice::find($deviceId);
        
        if (!$device) {
            Cache::forget("device_id_by_token_{$token}");
            return response()->json(['error' => 'Device not found'], 404);
        }

        // 2. Validate Payload
        $validator = Validator::make($request->all(), [
            'timestamp' => 'nullable|date',
            'data' => 'required|array',
            'status' => 'nullable|string',
            'battery_level' => 'nullable|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            // 3. Process Data
            $payload = $request->input('data');
            
            // Store specific data types
            if ($device->device_type === 'energy_meter' || isset($payload['energy_usage'])) {
                $this->storeEnergyData($device, $payload);
            }

            // Update Device Status
            $device->update([
                'last_seen_at' => now(),
                'last_data_received_at' => now(),
                'battery_level' => $request->input('battery_level', $device->battery_level),
                'status' => 'active',
                'signal_strength' => $request->input('signal_strength', $device->signal_strength),
                // Update generic usage stats
                'usage_statistics' => array_merge($device->usage_statistics ?? [], [
                    'last_ingest_count' => count($payload),
                    'last_ingest_time' => now()->toDateTimeString()
                ])
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Data ingested successfully',
                'device_id' => $device->id
            ]);

        } catch (\Exception $e) {
            Log::error('IoT Ingestion Error', ['error' => $e->getMessage(), 'device_id' => $device->id]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    private function storeEnergyData($device, $data)
    {
        EnergyMonitoringData::create([
            'smart_property_id' => $device->smart_property_id,
            'device_id' => $device->id,
            'recorded_at' => now(),
            'current_usage_kw' => $data['current_usage'] ?? 0,
            'daily_usage_kwh' => $data['daily_usage'] ?? 0,
            'voltage' => $data['voltage'] ?? null,
            'current_amperes' => $data['current'] ?? null,
            'power_factor' => $data['power_factor'] ?? null,
            'frequency_hz' => $data['frequency'] ?? null,
            'data_source' => 'device_ingestion',
        ]);
    }
}
