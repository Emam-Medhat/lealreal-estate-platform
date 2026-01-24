<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SmartLightingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function dashboard(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_lights' => 0,
                'active_scenes' => 0,
                'energy_saving_mode' => false,
                'timestamp' => now()->toDateTimeString(),
            ],
        ]);
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
            'status' => 'nullable|string',
        ]);

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function show($lighting)
    {
        return response()->json(['status' => 'success', 'data' => ['id' => $lighting]]);
    }

    public function edit($lighting)
    {
        return response()->json(['status' => 'success', 'data' => ['id' => $lighting]]);
    }

    public function update(Request $request, $lighting)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'status' => 'nullable|string',
        ]);

        return response()->json(['status' => 'success', 'data' => ['id' => $lighting, 'updates' => $data]]);
    }

    public function destroy($lighting)
    {
        return response()->json(['status' => 'success', 'data' => ['id' => $lighting]]);
    }

    public function toggle($lighting, Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $lighting,
                'toggled' => true,
                'timestamp' => now()->toDateTimeString(),
            ],
        ]);
    }

    public function setScene($lighting, Request $request)
    {
        $data = $request->validate([
            'scene' => 'nullable|string|max:255',
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $lighting,
                'scene' => $data['scene'] ?? null,
                'timestamp' => now()->toDateTimeString(),
            ],
        ]);
    }
}
