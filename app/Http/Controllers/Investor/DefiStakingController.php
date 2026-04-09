<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Investor\InvestRequest;
use App\Models\Investor;
use App\Models\DefiStaking;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DefiStakingController extends Controller
{
    public function index(Request $request)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $staking = $investor->defiStaking()
            ->with(['token'])
            ->when($request->search, function ($query, $search) {
                $query->where('staking_purpose', 'like', "%{$search}%")
                    ->orWhere('token_symbol', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->blockchain_network, function ($query, $network) {
                $query->where('blockchain_network', $network);
            })
            ->latest('created_at')
            ->paginate(20);

        return view('investor.defi.staking.index', compact('staking'));
    }

    public function create()
    {
        return view('investor.defi.staking.create');
    }

    public function store(Request $request)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $request->validate([
            'staking_purpose' => 'required|string|max:255',
            'token_address' => 'required|string|max:255',
            'token_symbol' => 'required|string|max:10',
            'amount_staked' => 'required|numeric|min:0.00000001|max:1000000',
            'staking_period_days' => 'required|integer|min:1|max:3650',
            'apy_rate' => 'required|numeric|min:0|max:100',
            'reward_frequency' => 'required|in:continuous,daily,weekly,monthly,quarterly',
            'lockup_period' => 'required|in:none,flexible,30_days,60_days,90_days,180_days,1_year',
            'early_withdrawal_penalty' => 'nullable|numeric|min:0|max:100',
            'minimum_staking_amount' => 'nullable|numeric|min:0',
            'maximum_staking_amount' => 'nullable|numeric|min:0',
            'auto_compound' => 'nullable|boolean',
            'rewards_token_address' => 'nullable|string|max:255',
            'rewards_token_symbol' => 'nullable|string|max:10',
            'smart_contract_address' => 'required|string|max:255',
            'blockchain_network' => 'required|in:ethereum,polygon,bnb_chain,avalanche,arbitrum,solana',
            'protocol_name' => 'required|string|max:100',
            'protocol_version' => 'nullable|string|max:50',
            'risk_level' => 'required|in:low,medium,high,critical',
            'notes' => 'nullable|string|max:1000',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $staking = DefiStaking::create([
            'investor_id' => $investor->id,
            'staking_purpose' => $request->staking_purpose,
            'token_address' => $request->token_address,
            'token_symbol' => $request->token_symbol,
            'amount_staked' => $request->amount_staked,
            'staking_period_days' => $request->staking_period_days,
            'apy_rate' => $request->apy_rate,
            'reward_frequency' => $request->reward_frequency,
            'lockup_period' => $request->lockup_period,
            'early_withdrawal_penalty' => $request->early_withdrawal_penalty ?? 0,
            'minimum_staking_amount' => $request->minimum_staking_amount,
            'maximum_staking_amount' => $request->maximum_staking_amount,
            'auto_compound' => $request->auto_compound ?? false,
            'rewards_token_address' => $request->rewards_token_address,
            'rewards_token_symbol' => $request->rewards_token_symbol,
            'smart_contract_address' => $request->smart_contract_address,
            'blockchain_network' => $request->blockchain_network,
            'protocol_name' => $request->protocol_name,
            'protocol_version' => $request->protocol_version,
            'risk_level' => $request->risk_level,
            'status' => 'active',
            'expected_rewards' => $this->calculateExpectedRewards($request),
            'total_rewards_earned' => 0,
            'current_value' => $request->amount_staked, // Will be updated with current token price
            'unstake_date' => now()->addDays($request->staking_period_days),
            'notes' => $request->notes,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Handle documents upload
        if ($request->hasFile('documents')) {
            $documents = [];
            foreach ($request->file('documents') as $document) {
                $path = $document->store('defi-staking-documents', 'public');
                $documents[] = [
                    'path' => $path,
                    'name' => $document->getClientOriginalName(),
                    'type' => $document->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $staking->update(['documents' => $documents]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_defi_staking',
            'details' => "Created DeFi staking: {$staking->staking_purpose}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('investor.defi.staking.show', $staking)
            ->with('success', 'DeFi staking created successfully.');
    }

    public function show(DefiStaking $staking)
    {
        $this->authorize('view', $staking);
        
        $staking->load(['investor', 'rewards']);
        
        return view('investor.defi.staking.show', compact('staking'));
    }

    public function unstake(Request $request, DefiStaking $staking): JsonResponse
    {
        $this->authorize('update', $staking);
        
        $request->validate([
            'amount_to_unstake' => 'required|numeric|min:0|max:' . $staking->amount_staked,
            'reason' => 'nullable|string|max:500',
        ]);

        $amountToUnstake = $request->amount_to_unstake;
        $penalty = $this->calculateEarlyWithdrawalPenalty($staking, $amountToUnstake);
        $netAmount = $amountToUnstake - $penalty;

        $unstake = $staking->unstakes()->create([
            'amount_unstaked' => $amountToUnstake,
            'penalty_amount' => $penalty,
            'net_amount' => $netAmount,
            'unstake_date' => now(),
            'reason' => $request->reason,
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        // Update staking amount
        $staking->decrement('amount_staked', $amountToUnstake);

        // Update status if fully unstaked
        if ($staking->amount_staked <= 0) {
            $staking->update(['status' => 'completed']);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'unstaked_defi_position',
            'details' => "Unstaked {$amountToUnstake} from staking: {$staking->staking_purpose}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'unstake' => $unstake,
            'message' => 'Unstake request submitted successfully'
        ]);
    }

    public function claimRewards(Request $request, DefiStaking $staking): JsonResponse
    {
        $this->authorize('update', $staking);
        
        $request->validate([
            'amount_to_claim' => 'required|numeric|min:0|max:' . $staking->total_rewards_earned,
        ]);

        $amountToClaim = $request->amount_to_claim;

        $claim = $staking->claims()->create([
            'amount_claimed' => $amountToClaim,
            'claim_date' => now(),
            'transaction_hash' => null, // Will be updated after blockchain transaction
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        // Update rewards earned
        $staking->decrement('total_rewards_earned', $amountToClaim);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'claimed_defi_rewards',
            'details' => "Claimed {$amountToClaim} rewards from staking: {$staking->staking_purpose}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'claim' => $claim,
            'message' => 'Rewards claimed successfully'
        ]);
    }

    public function updateRewards(Request $request, DefiStaking $staking): JsonResponse
    {
        $this->authorize('update', $staking);
        
        $request->validate([
            'rewards_earned' => 'required|numeric|min:0',
            'current_token_price' => 'required|numeric|min:0',
        ]);

        $staking->increment('total_rewards_earned', $request->rewards_earned);
        $staking->update([
            'current_value' => $staking->amount_staked * $request->current_token_price,
            'last_rewards_update' => now(),
        ]);

        return response()->json([
            'success' => true,
            'total_rewards_earned' => $staking->total_rewards_earned,
            'current_value' => $staking->current_value,
            'message' => 'Rewards updated successfully'
        ]);
    }

    public function getStakingStats(): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $stats = [
            'total_staking_positions' => $investor->defiStaking()->count(),
            'active_positions' => $investor->defiStaking()->where('status', 'active')->count(),
            'completed_positions' => $investor->defiStaking()->where('status', 'completed')->count(),
            'total_amount_staked' => $investor->defiStaking()->sum('amount_staked'),
            'total_rewards_earned' => $investor->defiStaking()->sum('total_rewards_earned'),
            'current_portfolio_value' => $investor->defiStaking()->sum('current_value'),
            'average_apy' => $investor->defiStaking()->avg('apy_rate'),
            'by_protocol' => $investor->defiStaking()
                ->groupBy('protocol_name')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'total_staked' => $group->sum('amount_staked'),
                        'total_rewards' => $group->sum('total_rewards_earned'),
                        'average_apy' => $group->avg('apy_rate'),
                    ];
                }),
            'by_blockchain' => $investor->defiStaking()
                ->groupBy('blockchain_network')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'total_staked' => $group->sum('amount_staked'),
                        'total_rewards' => $group->sum('total_rewards_earned'),
                    ];
                }),
            'by_risk_level' => $investor->defiStaking()
                ->groupBy('risk_level')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'total_staked' => $group->sum('amount_staked'),
                        'total_rewards' => $group->sum('total_rewards_earned'),
                    ];
                }),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getStakingPerformance(DefiStaking $staking): JsonResponse
    {
        $this->authorize('view', $staking);
        
        $performance = [
            'staking_id' => $staking->id,
            'staking_purpose' => $staking->staking_purpose,
            'status' => $staking->status,
            'amount_staked' => $staking->amount_staked,
            'current_value' => $staking->current_value,
            'total_rewards_earned' => $staking->total_rewards_earned,
            'apy_rate' => $staking->apy_rate,
            'days_staked' => $staking->created_at->diffInDays(now()),
            'days_remaining' => $staking->unstake_date->diffInDays(now()),
            'roi_percentage' => $staking->amount_staked > 0 ? (($staking->total_rewards_earned / $staking->amount_staked) * 100) : 0,
            'expected_rewards' => $staking->expected_rewards,
            'actual_vs_expected' => $staking->expected_rewards > 0 ? (($staking->total_rewards_earned / $staking->expected_rewards) * 100) : 0,
            'protocol_name' => $staking->protocol_name,
            'token_symbol' => $staking->token_symbol,
        ];

        return response()->json([
            'success' => true,
            'performance' => $performance
        ]);
    }

    public function exportStaking(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:active,completed,cancelled',
            'blockchain_network' => 'nullable|in:ethereum,polygon,bnb_chain,avalanche,arbitrum,solana',
            'protocol_name' => 'nullable|string|max:100',
        ]);

        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $query = $investor->defiStaking()->with(['rewards', 'unstakes']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->blockchain_network) {
            $query->where('blockchain_network', $request->blockchain_network);
        }

        if ($request->protocol_name) {
            $query->where('protocol_name', $request->protocol_name);
        }

        $staking = $query->get();

        $filename = "defi_staking_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $staking,
            'filename' => $filename,
            'message' => 'DeFi staking exported successfully'
        ]);
    }

    private function calculateExpectedRewards(Request $request): float
    {
        $principal = $request->amount_staked;
        $apyRate = $request->apy_rate / 100;
        $stakingDays = $request->staking_period_days;
        
        return $principal * $apyRate * ($stakingDays / 365);
    }

    private function calculateEarlyWithdrawalPenalty(DefiStaking $staking, float $amount): float
    {
        $penaltyRate = $staking->early_withdrawal_penalty / 100;
        return $amount * $penaltyRate;
    }
}
