<?php

namespace App\Http\Controllers\Metaverse;

use App\Http\Controllers\Controller;
use App\Models\Metaverse\MetaverseTransaction;
use Illuminate\Http\Request;

class MetaverseTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function recent(Request $request)
    {
        $limit = (int) ($request->limit ?? 20);
        $limit = max(1, min(200, $limit));

        $transactions = MetaverseTransaction::query()
            ->latest()
            ->limit($limit)
            ->get();

        return response()->json($transactions);
    }

    public function getVolume(Request $request)
    {
        $days = (int) ($request->days ?? 30);
        $days = max(1, min(365, $days));
        $startDate = now()->subDays($days);

        $totalVolume = MetaverseTransaction::query()
            ->where('created_at', '>=', $startDate)
            ->sum('amount');

        $totalTransactions = MetaverseTransaction::query()
            ->where('created_at', '>=', $startDate)
            ->count();

        return response()->json([
            'days' => $days,
            'total_volume' => (float) $totalVolume,
            'total_transactions' => $totalTransactions,
        ]);
    }
}
