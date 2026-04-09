<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\InvestorAlert;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class InvestorAlertController extends Controller
{
    /**
     * Store a new investor alert.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'email' => 'required|email',
                'amount_min' => 'nullable|numeric|min:0',
                'amount_max' => 'nullable|numeric|min:0',
                'property_type' => 'nullable|string',
                'location' => 'nullable|string',
            ]);

            // Create the alert
            $alert = InvestorAlert::create([
                'user_id' => Auth::id(), // Nullable if not logged in
                'email' => $validated['email'],
                'amount_min' => $validated['amount_min'] ?? null,
                'amount_max' => $validated['amount_max'] ?? null,
                'property_type' => $validated['property_type'] ?? null,
                'location' => $validated['location'] ?? null,
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Alert created successfully',
                'data' => $alert
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
