<?php

namespace App\Http\Controllers\Api\Intelligence;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * API: Intelligence market analytics overview.
 */
class AnalyticsController extends Controller
{
    public function overview(Request $request)
    {
        $days = (int) ($request->query('days', 90));
        $start = now()->subDays($days);

        $active = Property::where('status', 'active');

        $avgDaysOnMarket = (clone $active)
            ->selectRaw('AVG(DATEDIFF(NOW(), created_at)) as avg_days')
            ->value('avg_days') ?? 0;

        $inventory = Property::where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->groupBy('d')->orderBy('d')->get();

        $priceTrends = Property::where('created_at', '>=', $start)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as m, AVG(price) as avg_price')
            ->groupBy('m')->orderBy('m')->get();

        $demand = (clone $active)->sum('views_count') + (clone $active)->sum('inquiries_count');
        $supply = (clone $active)->count();
        $demandSupply = [
            'demand' => (int) $demand,
            'supply' => (int) $supply,
            'ratio' => $supply > 0 ? round($demand / $supply, 2) : 0,
        ];

        $fastestAreas = Property::select('city', DB::raw('AVG(DATEDIFF(NOW(), created_at)) as avg_days'))
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderBy('avg_days')
            ->limit(10)
            ->get();

        $fraudActivity = Property::where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as d, SUM(CASE WHEN fraud_score >= 60 THEN 1 ELSE 0 END) as high_risk')
            ->groupBy('d')->orderBy('d')->get();

        $highRiskAccounts = Property::where('fraud_score', '>=', 80)
            ->whereNotNull('user_id')
            ->select('user_id', DB::raw('COUNT(*) as cnt'), DB::raw('AVG(fraud_score) as avg_score'))
            ->groupBy('user_id')->orderByDesc('cnt')->limit(10)->get();

        return response()->json([
            'avg_days_on_market' => round((float) $avgDaysOnMarket, 2),
            'inventory' => $inventory,
            'price_trends' => $priceTrends,
            'demand_supply' => $demandSupply,
            'fastest_areas' => $fastestAreas,
            'fraud_activity' => $fraudActivity,
            'high_risk_accounts' => $highRiskAccounts,
            'range_days' => $days,
        ]);
    }
}
