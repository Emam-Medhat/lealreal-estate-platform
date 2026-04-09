<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IotAlertController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        return response()->json(['status' => 'success', 'data' => []]);
    }

    public function show($alert)
    {
        return response()->json(['status' => 'success', 'data' => ['id' => $alert]]);
    }

    public function markRead($alert)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $alert,
                'read' => true,
                'timestamp' => now()->toDateTimeString(),
            ],
        ]);
    }

    public function dismiss($alert)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $alert,
                'dismissed' => true,
                'timestamp' => now()->toDateTimeString(),
            ],
        ]);
    }
}
