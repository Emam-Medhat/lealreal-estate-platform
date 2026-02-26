<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Admin dashboard for Intelligence System.
 */
class IntelligenceAdminController extends Controller
{
    public function index()
    {
        $stats = [
            'overpriced' => Property::where('overpriced_flag', true)->count(),
            'restricted' => Property::whereNotNull('restricted_at')->count(),
            'needs_review' => Property::where('requires_manual_review', true)->count(),
            'scored' => Property::whereNotNull('investment_score')->count(),
        ];

        $topRisk = Property::orderByDesc('fraud_score')
            ->take(10)
            ->get(['id', 'title', 'city', 'price', 'fraud_score', 'fraud_risk_level']);

        $active = Property::where('status', 'active');
        $avgDaysOnMarket = (clone $active)
            ->selectRaw('AVG(DATEDIFF(NOW(), created_at)) as avg_days')
            ->value('avg_days') ?? 0;

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
            ->limit(8)
            ->get();

        $clusters = Property::select('user_id', 'city', DB::raw('COUNT(*) as cnt'), DB::raw('AVG(fraud_score) as avg_score'))
            ->where('fraud_score', '>=', 80)
            ->whereNotNull('user_id')
            ->groupBy('user_id', 'city')
            ->orderByDesc('cnt')
            ->limit(8)
            ->get();

        return view('admin.intelligence.index', compact('stats', 'topRisk', 'avgDaysOnMarket', 'demandSupply', 'fastestAreas', 'clusters'));
    }
}
