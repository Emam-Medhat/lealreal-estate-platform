<?php

namespace App\Http\Controllers\Blockchain;

use App\Http\Controllers\Controller;
use App\Models\PropertyContract;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ContractController extends Controller
{
    public function index(): View
    {
        return view('blockchain.contracts');
    }

    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|integer|min:1',
        ]);

        $pc = PropertyContract::create([
            'property_id' => $validated['property_id'],
            'ipfs_hash' => 'QmStubHash',
            'chain_tx' => null,
            'status' => 'generated',
            'party_a_ekyc_id' => null,
            'party_b_ekyc_id' => null,
        ]);

        return response()->json([
            'success' => true,
            'id' => $pc->id,
            'status' => $pc->status,
            'ipfs_hash' => $pc->ipfs_hash,
        ]);
    }
}
