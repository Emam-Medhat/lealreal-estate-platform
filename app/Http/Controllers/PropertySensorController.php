<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PropertySensorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        return response()->json(['status' => 'success', 'data' => []]);
    }

    public function create(Request $request)
    {
        return response()->json(['status' => 'success']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'property_id' => 'nullable|integer',
            'device_id' => 'nullable|integer',
            'sensor_type' => 'nullable|string|max:255',
        ]);

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function show($sensor)
    {
        return response()->json(['status' => 'success', 'data' => ['id' => $sensor]]);
    }

    public function edit($sensor)
    {
        return response()->json(['status' => 'success', 'data' => ['id' => $sensor]]);
    }

    public function update(Request $request, $sensor)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'sensor_type' => 'nullable|string|max:255',
        ]);

        return response()->json(['status' => 'success', 'data' => ['id' => $sensor, 'updates' => $data]]);
    }

    public function destroy($sensor)
    {
        return response()->json(['status' => 'success', 'data' => ['id' => $sensor]]);
    }

    public function getLatestReadings($sensor)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $sensor,
                'readings' => [],
                'timestamp' => now()->toDateTimeString(),
            ],
        ]);
    }
}
