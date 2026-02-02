<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Investor\InvestRequest;
use App\Models\Investor;
use App\Models\InvestmentOpportunity;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvestmentOpportunityController extends Controller
{
    public function index(Request $request)
    {
        $opportunities = InvestmentOpportunity::where('status', 'published')
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('sector', 'like', "%{$search}%");
            })
            ->when($request->sector, function ($query, $sector) {
                $query->where('sector', $sector);
            })
            ->when($request->investment_type, function ($query, $type) {
                $query->where('investment_type', $type);
            })
            ->when($request->risk_level, function ($query, $risk) {
                $query->where('risk_level', $risk);
            })
            ->when($request->min_investment, function ($query, $min) {
                $query->where('min_investment', '>=', $min);
            })
            ->when($request->max_investment, function ($query, $max) {
                $query->where('max_investment', '<=', $max);
            })
            ->when($request->expected_return, function ($query, $return) {
                $query->where('expected_return', '>=', $return);
            })
            ->latest('published_at')
            ->paginate(20);

        return view('investor.opportunities', compact('opportunities'));
    }

    public function show(InvestmentOpportunity $opportunity)
    {
        $opportunity->load(['documents', 'images']);
        
        return view('investor.opportunities.show', compact('opportunity'));
    }

    public function invest(Request $request, InvestmentOpportunity $opportunity)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $request->validate([
            'investment_amount' => 'required|numeric|min:' . $opportunity->min_investment . '|max:' . $opportunity->max_investment,
            'investment_terms_accepted' => 'required|accepted',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if investor has already invested
        $existingInvestment = $investor->investments()
            ->where('opportunity_id', $opportunity->id)
            ->first();

        if ($existingInvestment) {
            return back()->with('error', 'You have already invested in this opportunity.');
        }

        $investment = $investor->investments()->create([
            'opportunity_id' => $opportunity->id,
            'investor_id' => $investor->id,
            'investment_amount' => $request->investment_amount,
            'status' => 'pending',
            'investment_date' => now(),
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        // Update opportunity invested amount
        $opportunity->increment('total_invested', $request->investment_amount);
        $opportunity->increment('investor_count');

        // Update investor total invested
        $investor->increment('total_invested', $request->investment_amount);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'invested_in_opportunity',
            'details' => "Invested {$request->investment_amount} in opportunity: {$opportunity->title}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('investor.opportunities.show', $opportunity)
            ->with('success', 'Investment submitted successfully.');
    }

    public function getMyInvestments(Request $request)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $investments = $investor->investments()
            ->with(['opportunity'])
            ->when($request->status, function ($query, $status) {
                $query->where('investments.status', $status);
            })
            ->latest('investment_date')
            ->paginate(20);

        return view('investor.opportunities.my-investments', compact('investments'));
    }

    public function getOpportunityStats(): JsonResponse
    {
        $stats = [
            'total_opportunities' => InvestmentOpportunity::where('status', 'published')->count(),
            'by_sector' => InvestmentOpportunity::where('status', 'published')
                ->groupBy('sector')
                ->map(function ($group) {
                    return $group->count();
                }),
            'by_investment_type' => InvestmentOpportunity::where('status', 'published')
                ->groupBy('investment_type')
                ->map(function ($group) {
                    return $group->count();
                }),
            'by_risk_level' => InvestmentOpportunity::where('status', 'published')
                ->groupBy('risk_level')
                ->map(function ($group) {
                    return $group->count();
                }),
            'average_expected_return' => InvestmentOpportunity::where('status', 'published')->avg('expected_return'),
            'total_funding_goal' => InvestmentOpportunity::where('status', 'published')->sum('funding_goal'),
            'total_funded' => InvestmentOpportunity::where('status', 'published')->sum('total_invested'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getRecommendedOpportunities(): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $recommendations = InvestmentOpportunity::where('status', 'published')
            ->where('sector', $investor->preferred_sectors ?? [])
            ->where('risk_level', $investor->risk_tolerance ?? 'medium')
            ->where('min_investment', '<=', $investor->max_investment ?? 100000)
            ->orderByDesc('expected_return')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations
        ]);
    }

    public function watchOpportunity(Request $request, InvestmentOpportunity $opportunity): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $watchlist = $investor->watchlist ?? [];
        
        if (!in_array($opportunity->id, $watchlist)) {
            $watchlist[] = $opportunity->id;
            $investor->update(['watchlist' => $watchlist]);
        }

        return response()->json([
            'success' => true,
            'watched' => true,
            'message' => 'Opportunity added to watchlist'
        ]);
    }

    public function unwatchOpportunity(Request $request, InvestmentOpportunity $opportunity): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $watchlist = $investor->watchlist ?? [];
        $watchlist = array_diff($watchlist, [$opportunity->id]);
        
        $investor->update(['watchlist' => array_values($watchlist)]);

        return response()->json([
            'success' => true,
            'watched' => false,
            'message' => 'Opportunity removed from watchlist'
        ]);
    }

    public function getWatchlist(): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $watchlist = InvestmentOpportunity::whereIn('id', $investor->watchlist ?? [])
            ->get(['id', 'title', 'sector', 'expected_return', 'risk_level', 'min_investment', 'max_investment']);

        return response()->json([
            'success' => true,
            'watchlist' => $watchlist
        ]);
    }

    public function calculateInvestmentReturn(Request $request, InvestmentOpportunity $opportunity): JsonResponse
    {
        $request->validate([
            'investment_amount' => 'required|numeric|min:' . $opportunity->min_investment,
            'investment_period' => 'required|in:1_month,3_months,6_months,1_year,3_years,5_years',
        ]);

        $investmentAmount = $request->investment_amount;
        $expectedReturn = $opportunity->expected_return;
        $periodInYears = $this->getPeriodInYears($request->investment_period);
        
        $simpleReturn = $investmentAmount * (1 + ($expectedReturn / 100) * $periodInYears);
        $compoundReturn = $investmentAmount * pow(1 + ($expectedReturn / 100), $periodInYears);
        
        return response()->json([
            'success' => true,
            'calculations' => [
                'investment_amount' => $investmentAmount,
                'expected_return' => $expectedReturn,
                'investment_period' => $request->investment_period,
                'period_in_years' => $periodInYears,
                'simple_return' => round($simpleReturn, 2),
                'compound_return' => round($compoundReturn, 2),
                'total_return' => round($compoundReturn - $investmentAmount, 2),
                'roi_percentage' => round((($compoundReturn - $investmentAmount) / $investmentAmount) * 100, 2),
            ],
            'message' => 'Investment return calculated successfully'
        ]);
    }

    public function compareOpportunities(Request $request): JsonResponse
    {
        $request->validate([
            'opportunity_ids' => 'required|array|min:2|max:5',
            'opportunity_ids.*' => 'exists:investment_opportunities,id',
        ]);

        $opportunities = InvestmentOpportunity::whereIn('id', $request->opportunity_ids)->get();

        $comparison = $opportunities->map(function ($opportunity) {
            return [
                'id' => $opportunity->id,
                'title' => $opportunity->title,
                'expected_return' => $opportunity->expected_return,
                'risk_level' => $opportunity->risk_level,
                'min_investment' => $opportunity->min_investment,
                'max_investment' => $opportunity->max_investment,
                'sector' => $opportunity->sector,
                'investment_type' => $opportunity->investment_type,
                'funding_progress' => $opportunity->funding_goal > 0 ? ($opportunity->total_invested / $opportunity->funding_goal) * 100 : 0,
            ];
        });

        return response()->json([
            'success' => true,
            'comparison' => $comparison,
            'message' => 'Opportunities compared successfully'
        ]);
    }

    public function exportOpportunities(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'sector' => 'nullable|string|max:100',
            'investment_type' => 'nullable|in:stocks,bonds,real_estate,commodities,crypto,mutual_funds,etf,alternative',
            'risk_level' => 'nullable|in:low,medium,high,critical',
        ]);

        $query = InvestmentOpportunity::where('status', 'published');

        if ($request->sector) {
            $query->where('sector', $request->sector);
        }

        if ($request->investment_type) {
            $query->where('investment_type', $request->investment_type);
        }

        if ($request->risk_level) {
            $query->where('risk_level', $request->risk_level);
        }

        $opportunities = $query->get();

        $filename = "investment_opportunities_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $opportunities,
            'filename' => $filename,
            'message' => 'Opportunities exported successfully'
        ]);
    }

    private function getPeriodInYears(string $period): float
    {
        $periods = [
            '1_month' => 1/12,
            '3_months' => 3/12,
            '6_months' => 6/12,
            '1_year' => 1,
            '3_years' => 3,
            '5_years' => 5,
        ];
        
        return $periods[$period] ?? 1;
    }
}
