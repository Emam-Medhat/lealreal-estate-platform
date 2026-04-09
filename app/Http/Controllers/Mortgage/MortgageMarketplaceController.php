<?php

namespace App\Http\Controllers\Mortgage;

use App\Http\Controllers\Controller;
use App\Models\MortgageApplication;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class MortgageMarketplaceController extends Controller
{
    public function index(): View
    {
        return view('mortgage.marketplace');
    }

    public function apply(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'property_price' => 'required|numeric|min:50000',
            'down_payment' => 'required|numeric|min:0',
            'loan_term_years' => 'required|integer|min:1|max:40',
        ]);

        $offers = [
            ['bank' => 'Bank A', 'rate' => 4.1, 'monthly' => 3900],
            ['bank' => 'Bank B', 'rate' => 4.4, 'monthly' => 4050],
            ['bank' => 'Bank C', 'rate' => 3.9, 'monthly' => 3820],
        ];

        $app = MortgageApplication::create([
            'user_id' => auth()->id(),
            'encrypted_blob_ref' => null,
            'status' => 'submitted',
            'offers_json' => $offers,
        ]);

        return response()->json([
            'success' => true,
            'id' => $app->id,
            'offers' => $offers,
        ]);
    }
}
