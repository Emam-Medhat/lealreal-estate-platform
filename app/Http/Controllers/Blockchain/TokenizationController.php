<?php

namespace App\Http\Controllers\Blockchain;

use App\Http\Controllers\Controller;
use App\Models\TokenAllocation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TokenizationController extends Controller
{
    public function index(): View
    {
        return view('blockchain.tokenization');
    }

    public function tokenize(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|integer|min:1',
            'user_id' => 'nullable|integer|min:1',
            'tokens' => 'required|numeric|min:0.0001',
        ]);

        $ta = TokenAllocation::create([
            'property_id' => $validated['property_id'],
            'user_id' => $validated['user_id'] ?? auth()->id(),
            'tokens' => $validated['tokens'],
            'ledger_tx' => null,
            'income_accrual_json' => [
                'monthly_income_share' => 0,
            ],
        ]);

        return response()->json([
            'success' => true,
            'id' => $ta->id,
            'tokens' => $ta->tokens,
        ]);
    }
}
