<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Models\InvestmentSimulation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class InvestmentSimulatorController extends Controller
{
    public function index(): View
    {
        return view('tools.investment_simulator');
    }

    public function simulate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'purchase_price' => 'required|numeric|min:10000',
            'purchase_year' => 'required|integer|min:1980|max:' . date('Y'),
            'current_year' => 'required|integer|min:' . $request->purchase_year . '|max:' . (date('Y') + 1),
            'city' => 'nullable|string|max:100',
            'compare_asset' => 'nullable|string|in:gold,stocks,none',
        ]);

        $years = max(1, ($validated['current_year'] - $validated['purchase_year']));
        $avgCagr = 0.06;
        $futureValue = round($validated['purchase_price'] * pow(1 + $avgCagr, $years), 0);

        $compare = null;
        if (($validated['compare_asset'] ?? 'none') === 'gold') {
            $compare = round($validated['purchase_price'] * pow(1 + 0.04, $years), 0);
        } elseif (($validated['compare_asset'] ?? 'none') === 'stocks') {
            $compare = round($validated['purchase_price'] * pow(1 + 0.08, $years), 0);
        }

        $irr = (pow($futureValue / max(1, $validated['purchase_price']), 1 / $years) - 1);

        $sim = InvestmentSimulation::create([
            'inputs_json' => $validated,
            'results_json' => [
                'future_value' => $futureValue,
                'irr' => $irr,
                'years' => $years,
                'compare_value' => $compare,
            ],
            'share_card_url' => null,
        ]);

        return response()->json([
            'success' => true,
            'id' => $sim->id,
            'future_value' => $futureValue,
            'irr' => round($irr, 4),
            'years' => $years,
            'compare_value' => $compare,
        ]);
    }
}
