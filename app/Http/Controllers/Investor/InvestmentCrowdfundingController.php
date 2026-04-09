<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Investor\InvestRequest;
use App\Models\Investor;
use App\Models\InvestmentCrowdfunding;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvestmentCrowdfundingController extends Controller
{
    public function index(Request $request)
    {
        $campaigns = InvestmentCrowdfunding::where('status', 'published')
            ->when($request->search, function ($query, $search) {
                $query->where('campaign_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            })
            ->when($request->category, function ($query, $category) {
                $query->where('category', $category);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->min_investment, function ($query, $min) {
                $query->where('minimum_investment', '>=', $min);
            })
            ->when($request->max_investment, function ($query, $max) {
                $query->where('maximum_investment', '<=', $max);
            })
            ->latest('published_at')
            ->paginate(20);

        return view('investor.crowdfunding.index', compact('campaigns'));
    }

    public function show(InvestmentCrowdfunding $campaign)
    {
        $campaign->load(['documents', 'images', 'updates', 'investors']);
        
        return view('investor.crowdfunding.show', compact('campaign'));
    }

    public function invest(Request $request, InvestmentCrowdfunding $campaign)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $request->validate([
            'investment_amount' => 'required|numeric|min:' . $campaign->minimum_investment . '|max:' . $campaign->maximum_investment,
            'investment_terms_accepted' => 'required|accepted',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if investor has already invested
        $existingInvestment = $investor->crowdfundingInvestments()
            ->where('campaign_id', $campaign->id)
            ->first();

        if ($existingInvestment) {
            return back()->with('error', 'You have already invested in this campaign.');
        }

        $investment = $investor->crowdfundingInvestments()->create([
            'campaign_id' => $campaign->id,
            'investor_id' => $investor->id,
            'investment_amount' => $request->investment_amount,
            'equity_percentage' => ($request->investment_amount / $campaign->funding_goal) * $campaign->equity_offered,
            'status' => 'pending',
            'investment_date' => now(),
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        // Update campaign totals
        $campaign->increment('total_raised', $request->investment_amount);
        $campaign->increment('investor_count');

        // Update investor total invested
        $investor->increment('total_invested', $request->investment_amount);

        // Check if campaign is fully funded
        if ($campaign->total_raised >= $campaign->funding_goal) {
            $campaign->update(['status' => 'funded']);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'invested_in_crowdfunding',
            'details' => "Invested {$request->investment_amount} in campaign: {$campaign->campaign_name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('investor.crowdfunding.show', $campaign)
            ->with('success', 'Crowdfunding investment submitted successfully.');
    }

    public function getMyCrowdfundingInvestments(Request $request)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $investments = $investor->crowdfundingInvestments()
            ->with(['campaign'])
            ->when($request->status, function ($query, $status) {
                $query->where('investments.status', $status);
            })
            ->latest('investment_date')
            ->paginate(20);

        return view('investor.crowdfunding.my-investments', compact('investments'));
    }

    public function getCampaignStats(): JsonResponse
    {
        $stats = [
            'total_campaigns' => InvestmentCrowdfunding::where('status', 'published')->count(),
            'by_category' => InvestmentCrowdfunding::where('status', 'published')
                ->groupBy('category')
                ->map(function ($group) {
                    return $group->count();
                }),
            'by_status' => InvestmentCrowdfunding::groupBy('status')
                ->map(function ($group) {
                    return $group->count();
                }),
            'average_funding_goal' => InvestmentCrowdfunding::where('status', 'published')->avg('funding_goal'),
            'total_funding_goal' => InvestmentCrowdfunding::where('status', 'published')->sum('funding_goal'),
            'total_raised' => InvestmentCrowdfunding::sum('total_raised'),
            'success_rate' => $this->calculateSuccessRate(),
            'average_investment' => InvestmentCrowdfunding::avg('minimum_investment'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getRecommendedCampaigns(): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $recommendations = InvestmentCrowdfunding::where('status', 'published')
            ->where('category', $investor->preferred_sectors ?? [])
            ->where('minimum_investment', '<=', $investor->max_investment ?? 100000)
            ->where('risk_level', $investor->risk_tolerance ?? 'medium')
            ->orderByDesc('funding_progress')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations
        ]);
    }

    public function watchCampaign(Request $request, InvestmentCrowdfunding $campaign): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $watchlist = $investor->crowdfunding_watchlist ?? [];
        
        if (!in_array($campaign->id, $watchlist)) {
            $watchlist[] = $campaign->id;
            $investor->update(['crowdfunding_watchlist' => $watchlist]);
        }

        return response()->json([
            'success' => true,
            'watched' => true,
            'message' => 'Campaign added to watchlist'
        ]);
    }

    public function unwatchCampaign(Request $request, InvestmentCrowdfunding $campaign): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $watchlist = $investor->crowdfunding_watchlist ?? [];
        $watchlist = array_diff($watchlist, [$campaign->id]);
        
        $investor->update(['crowdfunding_watchlist' => array_values($watchlist)]);

        return response()->json([
            'success' => true,
            'watched' => false,
            'message' => 'Campaign removed from watchlist'
        ]);
    }

    public function getCampaignUpdates(InvestmentCrowdfunding $campaign): JsonResponse
    {
        $updates = $campaign->updates()
            ->latest('created_at')
            ->get(['id', 'title', 'content', 'created_at']);

        return response()->json([
            'success' => true,
            'updates' => $updates
        ]);
    }

    public function getCampaignProgress(InvestmentCrowdfunding $campaign): JsonResponse
    {
        $progress = [
            'funding_goal' => $campaign->funding_goal,
            'total_raised' => $campaign->total_raised,
            'funding_progress' => $campaign->funding_goal > 0 ? ($campaign->total_raised / $campaign->funding_goal) * 100 : 0,
            'days_remaining' => $campaign->end_date->diffInDays(now()),
            'investor_count' => $campaign->investor_count,
            'status' => $campaign->status,
        ];

        return response()->json([
            'success' => true,
            'progress' => $progress
        ]);
    }

    public function calculateInvestmentReturn(Request $request, InvestmentCrowdfunding $campaign): JsonResponse
    {
        $request->validate([
            'investment_amount' => 'required|numeric|min:' . $campaign->minimum_investment,
            'investment_period' => 'required|in:1_year,3_years,5_years,10_years',
        ]);

        $investmentAmount = $request->investment_amount;
        $equityPercentage = ($investmentAmount / $campaign->funding_goal) * $campaign->equity_offered;
        $projectedReturn = $campaign->projected_return_rate;
        $periodInYears = $this->getPeriodInYears($request->investment_period);
        
        $projectedValue = $investmentAmount * pow(1 + ($projectedReturn / 100), $periodInYears);
        $projectedGain = $projectedValue - $investmentAmount;
        $projectedRoi = ($projectedGain / $investmentAmount) * 100;

        return response()->json([
            'success' => true,
            'calculation' => [
                'investment_amount' => $investmentAmount,
                'equity_percentage' => round($equityPercentage, 2),
                'projected_return_rate' => $projectedReturn,
                'investment_period' => $request->investment_period,
                'period_in_years' => $periodInYears,
                'projected_value' => round($projectedValue, 2),
                'projected_gain' => round($projectedGain, 2),
                'projected_roi' => round($projectedRoi, 2),
            ],
            'message' => 'Investment return calculated successfully'
        ]);
    }

    public function compareCampaigns(Request $request): JsonResponse
    {
        $request->validate([
            'campaign_ids' => 'required|array|min:2|max:5',
            'campaign_ids.*' => 'exists:investment_crowdfunding,id',
        ]);

        $campaigns = InvestmentCrowdfunding::whereIn('id', $request->campaign_ids)->get();

        $comparison = $campaigns->map(function ($campaign) {
            return [
                'id' => $campaign->id,
                'campaign_name' => $campaign->campaign_name,
                'category' => $campaign->category,
                'funding_goal' => $campaign->funding_goal,
                'total_raised' => $campaign->total_raised,
                'funding_progress' => $campaign->funding_goal > 0 ? ($campaign->total_raised / $campaign->funding_goal) * 100 : 0,
                'minimum_investment' => $campaign->minimum_investment,
                'maximum_investment' => $campaign->maximum_investment,
                'equity_offered' => $campaign->equity_offered,
                'projected_return_rate' => $campaign->projected_return_rate,
                'risk_level' => $campaign->risk_level,
                'days_remaining' => $campaign->end_date->diffInDays(now()),
                'investor_count' => $campaign->investor_count,
            ];
        });

        return response()->json([
            'success' => true,
            'comparison' => $comparison,
            'message' => 'Campaigns compared successfully'
        ]);
    }

    public function exportCampaigns(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'category' => 'nullable|string|max:100',
            'status' => 'nullable|in:published,funded,cancelled,completed',
        ]);

        $query = InvestmentCrowdfunding::with(['documents', 'images']);

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $campaigns = $query->get();

        $filename = "crowdfunding_campaigns_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $campaigns,
            'filename' => $filename,
            'message' => 'Campaigns exported successfully'
        ]);
    }

    private function calculateSuccessRate(): float
    {
        $total = InvestmentCrowdfunding::count();
        $successful = InvestmentCrowdfunding::where('status', 'funded')->count();
        
        return $total > 0 ? round(($successful / $total) * 100, 2) : 0;
    }

    private function getPeriodInYears(string $period): float
    {
        $periods = [
            '1_year' => 1,
            '3_years' => 3,
            '5_years' => 5,
            '10_years' => 10,
        ];
        
        return $periods[$period] ?? 1;
    }
}
