<?php

namespace App\Http\Controllers\Defi;

use App\Http\Controllers\Controller;
use App\Models\Defi\PropertyDao;
use App\Models\Defi\PropertyToken;
use App\Models\Defi\DefiPropertyInvestment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PropertyDaoController extends Controller
{
    /**
     * Display a listing of property DAOs.
     */
    public function index(Request $request)
    {
        $query = PropertyDao::with(['creator', 'token', 'property', 'members', 'proposals', 'votes'])
            ->where('is_public', true);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by governance model
        if ($request->has('governance_model') && $request->governance_model) {
            $query->where('governance_model', $request->governance_model);
        }

        // Filter by voting period
        if ($request->has('voting_period') && $request->voting_period) {
            $query->where('voting_period', $request->voting_period);
        }

        $daos = $query->orderBy('created_at', 'desc')
            ->paginate(12);

        // Get statistics
        $stats = [
            'total_daos' => PropertyDao::where('is_public', true)->count(),
            'active_daos' => PropertyDao::where('is_public', true)
                ->where('status', 'active')->count(),
            'total_members' => PropertyDao::where('is_public', true)
                ->where('status', 'active')->sum('member_count'),
            'total_proposals' => PropertyDao::where('is_public', true)
                ->where('status', 'active')->sum('proposal_count'),
            'total_votes' => PropertyDao::where('is_public', true)
                ->where('status', 'active')->sum('vote_count'),
            'total_treasury' => PropertyDao::where('is_public', true)
                ->where('status', 'active')->sum('treasury_balance'),
            'average_voting_power' => PropertyDao::where('is_public', true)
                ->where('status', 'active')->avg('average_voting_power'),
        ];

        return Inertia::render('defi/dao/index', [
            'daos' => $daos,
            'stats' => $stats,
            'filters' => $request->only(['status', 'type', 'governance_model', 'voting_period']),
        ]);
    }

    /**
     * Show the form for creating a new property DAO.
     */
    public function create()
    {
        // Get user's tokens that can be used for DAOs
        $tokens = PropertyToken::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->where('distributed_supply', '>', 0)
            ->get();

        return Inertia::render('defi/dao/create', [
            'tokens' => $tokens,
        ]);
    }

    /**
     * Store a newly created property DAO in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'property_token_id' => 'required|exists:property_tokens,id',
            'type' => 'required|in:property,token,investment,community',
            'governance_model' => 'required|in:direct,representative,liquid_democracy,quadratic,delegated',
            'voting_period' => 'required|in:24h,48h,72h,7d,14d,30d',
            'quorum_percentage' => 'required|numeric|min:1|max:100',
            'proposal_threshold' => 'required|numeric|min:1|max:100',
            'execution_delay' => 'required|integer|min:0|max:30',
            'treasury_address' => 'nullable|string|max:255',
            'min_stake_amount' => 'required|numeric|min:1',
            'max_stake_amount' => 'nullable|numeric|min:0',
            'voting_power_per_token' => 'required|numeric|min:0.000001',
            'proposal_fee' => 'required|numeric|min:0|max:10',
            'voting_fee' => 'required|numeric|min:0|max:5',
            'is_public' => 'boolean',
            'metadata' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            // Validate token ownership
            $token = PropertyToken::findOrFail($request->property_token_id);
            if ($token->owner_id !== auth()->id()) {
                abort(403, 'غير مصرح لك بإنشاء DAO لهذا التوكن');
            }

            // Create DAO
            $dao = PropertyDao::create([
                'creator_id' => auth()->id(),
                'property_token_id' => $request->property_token_id,
                'property_id' => $token->property_id,
                'name' => $request->name,
                'description' => $request->description,
                'type' => $request->type,
                'governance_model' => $request->governance_model,
                'voting_period' => $request->voting_period,
                'quorum_percentage' => $request->quorum_percentage,
                'proposal_threshold' => $request->proposal_threshold,
                'execution_delay' => $request->execution_delay,
                'treasury_address' => $request->treasury_address,
                'treasury_balance' => 0,
                'member_count' => 1, // Creator is first member
                'proposal_count' => 0,
                'vote_count' => 0,
                'average_voting_power' => 0,
                'min_stake_amount' => $request->min_stake_amount,
                'max_stake_amount' => $request->max_stake_amount,
                'voting_power_per_token' => $request->voting_power_per_token,
                'proposal_fee' => $request->proposal_fee,
                'voting_fee' => $request->voting_fee,
                'status' => 'pending',
                'is_public' => $request->is_public,
                'smart_contract_address' => null, // Will be set when deployed
                'created_at' => now(),
            ]);

            // Add creator as member
            $dao->members()->create([
                'user_id' => auth()->id(),
                'role' => 'founder',
                'voting_power' => 1000000, // Founder gets max voting power
                'staked_amount' => 0,
                'joined_at' => now(),
                'status' => 'active',
                'created_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('defi.dao.show', $dao)
                ->with('success', 'تم إنشاء DAO بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء DAO: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified property DAO.
     */
    public function show(PropertyDao $dao)
    {
        $dao->load(['creator', 'token', 'property', 'members.user', 'proposals', 'votes']);

        // Calculate DAO statistics
        $statistics = [
            'member_count' => $dao->member_count,
            'proposal_count' => $dao->proposal_count,
            'vote_count' => $dao->vote_count,
            'treasury_balance' => $dao->treasury_balance,
            'average_voting_power' => $dao->average_voting_power,
            'user_voting_power' => $this->getUserVotingPower($dao),
            'user_staked_amount' => $this->getUserStakedAmount($dao),
            'user_role' => $this->getUserRole($dao),
            'can_propose' => $this->canUserPropose($dao),
            'can_vote' => $this->canUserVote($dao),
            'active_proposals' => $dao->proposals()->where('status', 'active')->count(),
            'pending_proposals' => $dao->proposals()->where('status', 'pending')->count(),
            'completed_proposals' => $dao->proposals()->where('status', 'completed')->count(),
            'participation_rate' => $this->calculateParticipationRate($dao),
            'proposal_success_rate' => $this->calculateProposalSuccessRate($dao),
            'next_voting_deadline' => $this->calculateNextVotingDeadline($dao),
        ];

        return Inertia::render('defi/dao/show', [
            'dao' => $dao,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show the form for editing the specified property DAO.
     */
    public function edit(PropertyDao $dao)
    {
        // Check if user can edit the DAO
        if (!$this->canEditDao($dao)) {
            abort(403, 'لا يمكن تعديل هذا DAO');
        }

        return Inertia::render('defi/dao/edit', [
            'dao' => $dao,
        ]);
    }

    /**
     * Update the specified property DAO in storage.
     */
    public function update(Request $request, PropertyDao $dao)
    {
        // Check if user can edit the DAO
        if (!$this->canEditDao($dao)) {
            abort(403, 'لا يمكن تعديل هذا DAO');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'quorum_percentage' => 'required|numeric|min:1|max:100',
            'proposal_threshold' => 'required|numeric|min:1|max:100',
            'execution_delay' => 'required|integer|min:0|max:30',
            'min_stake_amount' => 'required|numeric|min:1',
            'max_stake_amount' => 'nullable|numeric|min:0',
            'voting_power_per_token' => 'required|numeric|min:0.000001',
            'proposal_fee' => 'required|numeric|min:0|max:10',
            'voting_fee' => 'required|numeric|min:0|max:5',
            'is_public' => 'boolean',
            'metadata' => 'nullable|array',
        ]);

        $dao->update([
            'name' => $request->name,
            'description' => $request->description,
            'quorum_percentage' => $request->quorum_percentage,
            'proposal_threshold' => $request->proposal_threshold,
            'execution_delay' => $request->execution_delay,
            'min_stake_amount' => $request->min_stake_amount,
            'max_stake_amount' => $request->max_stake_amount,
            'voting_power_per_token' => $request->voting_power_per_token,
            'proposal_fee' => $request->proposal_fee,
            'voting_fee' => $request->voting_fee,
            'is_public' => $request->is_public,
            'metadata' => $request->metadata,
            'updated_at' => now(),
        ]);

        return redirect()->route('defi.dao.show', $dao)
            ->with('success', 'تم تحديث DAO بنجاحاح');
    }

    /**
     * Remove the specified property DAO from storage.
     */
    public function destroy(PropertyDao $dao)
    {
        // Check if user can delete the DAO
        if (!$this->canDeleteDao($dao)) {
            abort(403, 'لا يمكن حذف هذا DAO');
        }

        DB::beginTransaction();

        try {
            // Delete members
            $dao->members()->delete();
            
            // Delete proposals and votes
            $dao->proposals()->delete();
            $dao->votes()->delete();
            
            // Delete DAO
            $dao->delete();

            DB::commit();

            return redirect()->route('defi.dao.index')
                ->with('success', 'تم حذف DAO بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حذف DAO: ' . $e->getMessage());
        }
    }

    /**
     * Deploy DAO smart contract.
     */
    public function deploy(Request $request, PropertyDao $dao)
    {
        // Check if user can deploy the DAO
        if (!$this->canEditDao($dao)) {
            abort(403, 'غير مصرح لك بنشر هذا DAO');
        }

        if ($dao->status !== 'pending') {
            abort(403, 'DAO ليس في حالة انتظار');
        }

        DB::beginTransaction();

        try {
            // Deploy smart contract
            $smartContractAddress = $this->deployDaoSmartContract($dao);

            // Update DAO status
            $dao->update([
                'status' => 'active',
                'smart_contract_address' => $smartContractAddress,
                'deployed_at' => now(),
                'deployed_by' => auth()->id(),
            ]);

            DB::commit();

            return back()->with('success', 'تم نشر DAO بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء نشر DAO: ' . $e->getMessage());
        }
    }

    /**
     * Join DAO.
     */
    public function join(Request $request, PropertyDao $dao)
    {
        if ($dao->status !== 'active') {
            abort(403, 'DAO غير نشط');
        }

        if (!$dao->is_public) {
            abort(403, 'DAO خاص، لا يمكن الانضمام');
        }

        // Check if user is already a member
        if ($dao->members()->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'أنت بالفعل عضو في هذا DAO');
        }

        DB::beginTransaction();

        try {
            // Add member
            $dao->members()->create([
                'user_id' => auth()->id(),
                'role' => 'member',
                'voting_power' => 0, // Will be calculated based on staking
                'staked_amount' => 0,
                'joined_at' => now(),
                'status' => 'active',
                'created_at' => now(),
            ]);

            // Update member count
            $dao->increment('member_count');

            DB::commit();

            return back()->with('success', 'تم الانضمام إلى DAO بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الانضمام إلى DAO: ' . $e->getMessage());
        }
    }

    /**
     * Leave DAO.
     */
    public function leave(Request $request, PropertyDao $dao)
    {
        $member = $dao->members()->where('user_id', auth()->id())->first();
        
        if (!$member) {
            abort(403, 'أنت لست عضو في هذا DAO');
        }

        if ($member->role === 'founder') {
            abort(403, 'لا يمكن للمؤسس مغادرة DAO');
        }

        DB::beginTransaction();

        try {
            // Remove member
            $member->delete();

            // Update member count
            $dao->decrement('member_count');

            // Recalculate average voting power
            $this->recalculateAverageVotingPower($dao);

            DB::commit();

            return back()->with('success', 'تم مغادرة DAO بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء مغادرة DAO: ' . $e->getMessage());
        }
    }

    /**
     * Stake tokens in DAO.
     */
    public function stake(Request $request, PropertyDao $dao)
    {
        $request->validate([
            'amount' => 'required|numeric|min:' . $dao->min_stake_amount,
        ]);

        if ($dao->max_stake_amount && $request->amount > $dao->max_stake_amount) {
            return back()->with('error', 'المبلغ يتجاوز الحد الأقصى للتخزين');
        }

        $member = $dao->members()->where('user_id', auth()->id())->first();
        
        if (!$member) {
            return back()->with('error', 'يجب الانضمام إلى DAO أولاً');
        }

        DB::beginTransaction();

        try {
            // Calculate voting power
            $votingPower = $request->amount * $dao->voting_power_per_token;

            // Update member staking
            $member->update([
                'staked_amount' => $member->staked_amount + $request->amount,
                'voting_power' => $member->voting_power + $votingPower,
                'updated_at' => now(),
            ]);

            // Update treasury balance
            $dao->increment('treasury_balance', $request->amount);

            // Recalculate average voting power
            $this->recalculateAverageVotingPower($dao);

            DB::commit();

            return back()->with('success', 'تم تخزين التوكنات بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تخزين التوكنات: ' . $e->getMessage());
        }
    }

    /**
     * Unstake tokens from DAO.
     */
    public function unstake(Request $request, PropertyDao $dao)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $member = $dao->members()->where('user_id', auth()->id())->first();
        
        if (!$member) {
            return back()->with('error', 'أنت لست عضو في هذا DAO');
        }

        if ($member->staked_amount < $request->amount) {
            return back()->with('error', 'المبلغ المطلوب يتجاوز الرصيد المخزون');
        }

        DB::beginTransaction();

        try {
            // Calculate voting power to lose
            $votingPowerToLose = $request->amount * $dao->voting_power_per_token;

            // Update member staking
            $member->update([
                'staked_amount' => $member->staked_amount - $request->amount,
                'voting_power' => $member->voting_power - $votingPowerToLose,
                'updated_at' => now(),
            ]);

            // Update treasury balance
            $dao->decrement('treasury_balance', $request->amount);

            // Recalculate average voting power
            $this->recalculateAverageVotingPower($dao);

            DB::commit();

            return back()->with('success', 'تم سحب التوكنات بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء سحب التوكنات: ' . $e->getMessage());
        }
    }

    /**
     * Get DAO analytics.
     */
    public function analytics()
    {
        $userDaos = PropertyDao::whereHas('members', function ($query) {
            $query->where('user_id', auth()->id());
        })->get();

        $analytics = [
            'total_daos' => $userDaos->count(),
            'active_daos' => $userDaos->where('status', 'active')->count(),
            'total_staked' => $userDaos->where('status', 'active')->sum(function ($dao) {
                return $dao->members()->where('user_id', auth()->id())->sum('staked_amount');
            }),
            'total_voting_power' => $userDaos->where('status', 'active')->sum(function ($dao) {
                return $dao->members()->where('user_id', auth()->id())->sum('voting_power');
            }),
            'proposal_participation' => $this->calculateProposalParticipation($userDaos),
            'voting_participation' => $this->calculateVotingParticipation($userDaos),
            'dao_types' => $userDaos->groupBy('type')->map->count(),
            'governance_models' => $userDaos->groupBy('governance_model')->map->count(),
            'monthly_earnings' => $this->calculateMonthlyEarnings($userDaos),
            'top_influential_daos' => $this->getTopInfluentialDaos($userDaos),
        ];

        return Inertia::render('defi/dao/analytics', [
            'analytics' => $analytics,
        ]);
    }

    /**
     * Get user's DAO memberships.
     */
    public function myMemberships()
    {
        $memberships = PropertyDao::with(['token', 'property'])
            ->whereHas('members', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->get()
            ->map(function ($dao) {
                $member = $dao->members()->where('user_id', auth()->id())->first();
                
                return [
                    'dao' => $dao,
                    'member' => $member,
                    'voting_power_percentage' => $dao->average_voting_power > 0 ? ($member->voting_power / $dao->average_voting_power) * 100 : 0,
                    'staked_amount' => $member->staked_amount,
                    'can_propose' => $this->canUserPropose($dao),
                    'can_vote' => $this->canUserVote($dao),
                    'role' => $member->role,
                ];
            });

        return Inertia::render('defi/dao/memberships', [
            'memberships' => $memberships,
        ]);
    }

    /**
     * Deploy DAO smart contract.
     */
    private function deployDaoSmartContract($dao): string
    {
        // This would integrate with a smart contract deployment service
        // For now, return a mock address
        return '0x' . bin2hex(random_bytes(20));
    }

    /**
     * Check if user can edit DAO.
     */
    private function canEditDao($dao): bool
    {
        return $dao->creator_id === auth()->id() || 
               $dao->members()->where('user_id', auth()->id())->where('role', 'admin')->exists();
    }

    /**
     * Check if user can delete DAO.
     */
    private function canDeleteDao($dao): bool
    {
        return $dao->creator_id === auth()->id() && 
               $dao->status === 'pending' &&
               $dao->member_count <= 1;
    }

    /**
     * Get user voting power.
     */
    private function getUserVotingPower($dao): float
    {
        $member = $dao->members()->where('user_id', auth()->id())->first();
        return $member ? $member->voting_power : 0;
    }

    /**
     * Get user staked amount.
     */
    private function getUserStakedAmount($dao): float
    {
        $member = $dao->members()->where('user_id', auth()->id())->first();
        return $member ? $member->staked_amount : 0;
    }

    /**
     * Get user role.
     */
    private function getUserRole($dao): string
    {
        $member = $dao->members()->where('user_id', auth()->id())->first();
        return $member ? $member->role : 'none';
    }

    /**
     * Check if user can propose.
     */
    private function canUserPropose($dao): bool
    {
        $member = $dao->members()->where('user_id', auth()->id())->first();
        if (!$member) return false;
        
        return in_array($member->role, ['founder', 'admin', 'member']) && 
               $member->voting_power >= $dao->proposal_threshold;
    }

    /**
     * Check if user can vote.
     */
    private function canUserVote($dao): bool
    {
        $member = $dao->members()->where('user_id', auth()->id())->first();
        return $member && $member->voting_power > 0;
    }

    /**
     * Calculate participation rate.
     */
    private function calculateParticipationRate($dao): float
    {
        $totalMembers = $dao->member_count;
        $activeMembers = $dao->members()->where('status', 'active')->count();
        
        return $totalMembers > 0 ? ($activeMembers / $totalMembers) * 100 : 0;
    }

    /**
     * Calculate proposal success rate.
     */
    private function calculateProposalSuccessRate($dao): float
    {
        $totalProposals = $dao->proposal_count;
        $completedProposals = $dao->proposals()->where('status', 'completed')->count();
        
        return $totalProposals > 0 ? ($completedProposals / $totalProposals) * 100 : 0;
    }

    /**
     * Calculate next voting deadline.
     */
    private function calculateNextVotingDeadline($dao): string
    {
        $period = $dao->voting_period;
        $lastVote = $dao->votes()->latest('created_at')->first();
        
        if (!$lastVote) {
            return now()->addDays(7)->format('Y-m-d');
        }
        
        switch ($period) {
            case '24h':
                return $lastVote->created_at->addDay()->format('Y-m-d');
            case '48h':
                return $lastVote->created_at->addDays(2)->format('Y-m-d');
            case '72h':
                return $lastVote->created_at->addDays(3)->format('Y-m-d');
            case '7d':
                return $lastVote->created_at->addWeek()->format('Y-m-d');
            case '14d':
                return $lastVote->created_at->addWeeks(2)->format('Y-m-d');
            case '30d':
                return $lastVote->created_at->addMonth()->format('Y-m-d');
            default:
                return now()->addDays(7)->format('Y-m-d');
        }
    }

    /**
     * Recalculate average voting power.
     */
    private function recalculateAverageVotingPower($dao): void
    {
        $totalVotingPower = $dao->members()->sum('voting_power');
        $memberCount = $dao->members()->count();
        
        $dao->update([
            'average_voting_power' => $memberCount > 0 ? $totalVotingPower / $memberCount : 0,
        ]);
    }

    /**
     * Calculate proposal participation.
     */
    private function calculateProposalParticipation($daos): array
    {
        $participation = [];
        
        foreach ($daos as $dao) {
            $userProposals = $dao->proposals()->where('proposer_id', auth()->id())->count();
            $totalProposals = $dao->proposals()->count();
            
            $participation[] = [
                'dao_name' => $dao->name,
                'user_proposals' => $userProposals,
                'total_proposals' => $totalProposals,
                'participation_rate' => $totalProposals > 0 ? ($userProposals / $totalProposals) * 100 : 0,
            ];
        }
        
        return $participation;
    }

    /**
     * Calculate voting participation.
     */
    private function calculateVotingParticipation($daos): array
    {
        $participation = [];
        
        foreach ($daos as $dao) {
            $userVotes = $dao->votes()->where('voter_id', auth()->id())->count();
            $totalVotes = $dao->votes()->count();
            
            $participation[] = [
                'dao_name' => $dao->name,
                'user_votes' => $userVotes,
                'total_votes' => $totalVotes,
                'participation_rate' => $totalVotes > 0 ? ($userVotes / $totalVotes) * 100 : 0,
            ];
        }
        
        return $participation;
    }

    /**
     * Calculate monthly earnings.
     */
    private function calculateMonthlyEarnings($daos): array
    {
        $earnings = [];
        
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthEarnings = 0;
            
            foreach ($daos->where('status', 'active') as $dao) {
                // This would calculate based on actual DAO earnings
                // For now, return a mock calculation
                $monthlyEarnings += $dao->treasury_balance * 0.01; // 1% monthly yield
            }
            
            $earnings[$date->format('Y-m')] = $monthEarnings;
        }
        
        return $earnings;
    }

    /**
     * Get top influential DAOs.
     */
    private function getTopInfluentialDaos($daos): array
    {
        return $daos->where('status', 'active')
            ->sortByDesc('average_voting_power')
            ->take(5)
            ->values()
            ->toArray();
    }
}
