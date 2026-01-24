<?php

namespace App\Models\Defi;

use App\Models\User;
use App\Models\Defi\PropertyToken;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Support\Facades\DB;

class PropertyDao extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'creator_id',
        'property_token_id',
        'property_id',
        'name',
        'description',
        'type',
        'governance_model',
        'voting_period',
        'quorum_percentage',
        'proposal_threshold',
        'execution_delay',
        'treasury_address',
        'treasury_balance',
        'member_count',
        'proposal_count',
        'vote_count',
        'average_voting_power',
        'min_stake_amount',
        'max_stake_amount',
        'voting_power_per_token',
        'proposal_fee',
        'voting_fee',
        'status',
        'is_public',
        'smart_contract_address',
        'deployed_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quorum_percentage' => 'decimal:5',
        'proposal_threshold' => 'decimal:5',
        'treasury_balance' => 'decimal:18',
        'average_voting_power' => 'decimal:18',
        'min_stake_amount' => 'decimal:18',
        'max_stake_amount' => 'decimal:18',
        'voting_power_per_token' => 'decimal:18',
        'proposal_fee' => 'decimal:5',
        'voting_fee' => 'decimal:5',
        'is_public' => 'boolean',
        'deployed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'smart_contract_address',
    ];

    /**
     * Get the creator of the DAO.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get the token associated with the DAO.
     */
    public function token(): BelongsTo
    {
        return $this->belongsTo(PropertyToken::class, 'property_token_id');
    }

    /**
     * Get the property associated with the DAO.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Metaverse\MetaverseProperty::class, 'property_id');
    }

    /**
     * Get the members of the DAO.
     */
    public function members(): HasMany
    {
        return $this->hasMany(PropertyDaoMember::class);
    }

    /**
     * Get the proposals for the DAO.
     */
    public function proposals(): HasMany
    {
        return $this->hasMany(PropertyDaoProposal::class);
    }

    /**
     * Get the votes for the DAO.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(PropertyDaoVote::class);
    }

    /**
     * Get the treasury transactions for the DAO.
     */
    public function treasuryTransactions(): HasMany
    {
        return $this->hasMany(PropertyDaoTreasuryTransaction::class);
    }

    /**
     * Scope a query to only include active DAOs.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include public DAOs.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to only include DAOs by creator.
     */
    public function scopeByCreator($query, $creatorId)
    {
        return $query->where('creator_id', $creatorId);
    }

    /**
     * Scope a query to only include DAOs by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include DAOs by governance model.
     */
    public function scopeByGovernanceModel($query, $model)
    {
        return $query->where('governance_model', $model);
    }

    /**
     * Scope a query to only include DAOs by voting period.
     */
    public function scopeByVotingPeriod($query, $period)
    {
        return $query->where('voting_period', $period);
    }

    /**
     * Get the status text attribute.
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'active' => 'نشط',
            'suspended' => 'معلق',
            'completed' => 'مكتمل',
            'failed' => 'فشل',
            'deleted' => 'محذوف',
            default => $this->status,
        };
    }

    /**
     * Get the type text attribute.
     */
    public function getTypeTextAttribute(): string
    {
        return match($this->type) {
            'property' => 'عقار',
            'token' => 'توكن',
            'investment' => 'استثمار',
            'community' => 'مجتمع',
            default => $this->type,
        };
    }

    /**
     * Get the governance model text attribute.
     */
    public function getGovernanceModelTextAttribute(): string
    {
        return match($this->governance_model) {
            'direct' => 'مباشر',
            'representative' => 'ممثل',
            'liquid_democracy' => 'ديمقراط سائل',
            'quadratic' => 'ربعي',
            'delegated' => 'مندوب',
            default => $this->governance_model,
        };
    }

    /**
     * Get the voting period text attribute.
     */
    public function getVotingPeriodTextAttribute(): string
    {
        return match($this->voting_period) {
            '24h' => '24 ساعة',
            '48h' => '48 ساعة',
            '72h' => '72 ساعة',
            '7d' => '7 أيام',
            '14d' => '14 يوم',
            '30d' => '30 يوم',
            default => $this->voting_period,
        };
    }

    /**
     * Get the member count.
     */
    public function getMemberCountAttribute(): int
    {
        return $this->members()->where('status', 'active')->count();
    }

    /**
     * Get the proposal count.
     */
    public function getProposalCountAttribute(): int
    {
        return $this->proposals()->count();
    }

    /**
     * Get the vote count.
     */
    public function getVoteCountAttribute(): int
    {
        return $this->votes()->count();
    }

    /**
     * Get the active proposals count.
     */
    public function getActiveProposalsAttribute(): int
    {
        return $this->proposals()->where('status', 'active')->count();
    }
    /**
     * Get the pending proposals count.
     */
    public function getPendingProposalsAttribute(): int
    {
        return $this->proposals()->where('status', 'pending')->count();
    }

    /**
     * Get the completed proposals count.
     */
    public function getCompletedProposalsAttribute(): int
    {
        return $this->proposals()->where('status', 'completed')->count();
    }

    /**
     * Get the participation rate.
     */
    public function getParticipationRateAttribute(): float
    {
        $totalMembers = $this->member_count;
        $activeMembers = $this->members()->where('status', 'active')->count();
        
        return $totalMembers > 0 ? ($activeMembers / $totalMembers) * 100 : 0;
    }

    /**
     * Get the proposal success rate.
     */
    public function getProposalSuccessRateAttribute(): float
    {
        $totalProposals = $this->proposal_count;
        $completedProposals = $this->completed_proposals;
        
        return $totalProposals > 0 ? ($completedProposals / $totalProposals) * 100 : 0;
    }

    /**
     * Get the next voting deadline.
     */
    public function getNextVotingDeadlineAttribute(): string
    {
        $period = $this->voting_period;
        $lastVote = $this->votes()->latest('created_at')->first();
        
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
                return $lastVote->created_at->addWeek()->format('y-m-d');
            case '14d':
                return $lastVote->created_at->addWeeks(2)->format('Y-m-d');
            case '30d':
                return $lastVote->created_at->addMonth()->format('Y-m-d');
            default:
                return now()->addDays(7)->format('Y-m-d');
        }
    }

    /**
     * Get the user voting power.
     */
    public function getUserVotingPowerAttribute(): float
    {
        $member = $this->members()->where('user_id', auth()->id())->first();
        return $member ? $member->voting_power : 0;
    }

    /**
     * Get the user staked amount.
     */
    public function getUserStakedAmountAttribute(): float
    {
        $member = $this->members()->where('user_id', auth()->id())->first();
        return $member ? $member->staked_amount : 0;
    }

    /**
     * Get the user role.
     */
    public function getUserRoleAttribute(): string
    {
        $member = $this->members()->where('user_id', auth()->id())->first();
        return $member ? $member->role : 'none';
    }

    /**
     * Get the user can propose status.
     */
    public function getUserCanProposeAttribute(): bool
    {
        $member = $this->members()->where('user_id', auth()->id())->first();
        
        if (!$member) {
            return false;
        }
        
        return in_array($member->role, ['founder', 'admin', 'member']) && 
               $member->voting_power >= $this->proposal_threshold;
    }

    /**
     * Get the user can vote status.
     */
    public function getUserCanVoteAttribute(): bool
    {
        $member = $this->members()->where('user_id', auth()->id())->first();
        return $member && $member->voting_power > 0;
    }

    /**
     * Check if the DAO is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the DAO is deployed.
     */
    public function isDeployed(): bool
    {
        return !is_null($this->deployed_at);
    }

    /**
     * Check if the DAO is public.
     */
    public function isPublic(): bool
    {
        return $this->is_public;
    }

    /**
     * Check if the user is a member.
     */
    public function isMember(): bool
    {
        return $this->members()->where('user_id', auth()->id())->exists();
    }

    /**
     * Check if the user is a founder.
     */
    public function isFounder(): bool
    {
        $member = $this->members()->where('user_id', auth()->id())->first();
        return $member && $member->role === 'founder';
    }

     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        $member = $this->members()->where('user_id', auth()->id())->first();
        return $member && $member->role === 'admin';
    }

    /**
     * Join the DAO.
     */
    public function join(): bool
    {
        if ($this->isMember()) {
            return false;
        }

        if (!$this->is_public) {
            return false;
        }

        DB::beginTransaction();

        try {
            // Add member
            $this->members()->create([
                'user_id' => auth()->id(),
                'role' => 'member',
                'voting_power' => 0, // Will be calculated based on staking
                'staked_amount' => 0,
                'joined_at' => now(),
                'status' => 'active',
                'created_at' => now(),
            ]);

            // Update member count
            $this->increment('member_count');

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Leave the DAO.
     */
    public function leave(): bool
    {
        $member = $this->members()->where('user_id', auth()->id())->first();
        
        if (!$member) {
            return false;
        }

        if ($member->role === 'founder') {
            return false;
        }

        DB::beginTransaction();

        try {
            // Remove member
            $member->delete();

            // Update member count
            $this->decrement('member_count');

            // Recalculate average voting power
            $this->recalculateAverageVotingPower();

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Stake tokens in the DAO.
     */
    public function stake($amount): bool
    {
        $member = $this->members()->where('user_id', auth()->id())->first();
        
        if (!$member) {
            return false;
        }

        if ($amount < $this->min_stake_amount) {
            return false;
        }

        if ($this->max_stake_amount && $amount > $this->max_stake_amount) {
            return false;
        }

        $votingPower = $amount * $this->voting_power_per_token;

        DB::beginTransaction();

        try {
            // Update member staking
            $member->update([
                'staked_amount' => $member->staked_amount + $amount,
                'voting_power' => $member->voting_power + $votingPower,
                'updated_at' => now(),
            ]);

            // Update treasury balance
            $this->increment('treasury_balance', $amount);

            // Recalculate average voting power
            $this->recalculateAverageVotingPower();

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Unstake tokens from the DAO.
     */
    public function unstake($amount): bool
    {
        $member = $this->members()->where('user_id', auth()->id())->first();
        
        if (!$member) {
            return false;
        }

        if ($member->staked_amount < $amount) {
            return false;
        }

        $votingPowerToLose = $amount * $this->voting_power_per_token;

        DB::beginTransaction();

        try {
            // Update member staking
            $member->update([
                'staked_amount' => $member->staked_amount - $amount,
                'voting_power' => $member->voting_power - $votingPowerToLose,
                'updated_at' => now(),
            ]);

            // Update treasury balance
            $this->decrement('treasury_balance', $amount);

            // Recalculate average voting power
            $this->recalculateAverageVotingPower();

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB->rollBack();
            return false;
        }
    }

    /**
     * Create a proposal.
     */
    public function createProposal($title, $description, $type = 'general'): bool
    {
        if (!$this->getUserCanPropose()) {
            return false;
        }

        $fee = $this->proposal_fee;

        DB::beginTransaction();

        try {
            // Create proposal
            $proposal = $this->proposals()->create([
                'title' => $title,
                'description' => $description,
                'type' => $type,
                'proposer_id' => auth()->id(),
                'status' => 'pending',
                'votes_for' => 0,
                'votes_against' => 0,
                'abstain' => 0,
                'quorum_reached' => false,
                'execution_delay' => $this->execution_delay,
                'executed_at' => null,
                'created_at' => now(),
            ]);

            // Charge proposal fee
            if ($fee > 0) {
                $this->treasuryTransactions()->create([
                    'type' => 'proposal_fee',
                    'amount' => $fee,
                    'currency' => 'USD',
                    'status' => 'completed',
                    'processed_at' => now(),
                    'created_at' => now(),
                ]);
            }

            // Update proposal count
            $this->increment('proposal_count');

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Vote on a proposal.
     */
    public function vote($proposalId, $vote, $reason = null): bool
    {
        if (!$this->getUserCanVote()) {
            return false;
        }

        $proposal = $this->proposals()->find($proposalId);
        
        if (!$proposal || $proposal->status !== 'active') {
            return false;
        }

        $member = $this->members()->where('user_id', auth()->id())->first();
        $votingPower = $member->voting_power;

        DB::beginTransaction();

        try {
            // Create vote
            $this->votes()->create([
                'proposal_id' => $proposalId,
                'voter_id' => auth()->id(),
                'vote' => $vote,
                'voting_power' => $votingPower,
                'reason' => $reason,
                'created_at' => now(),
            ]);

            // Update proposal vote counts
            if ($vote === 'for') {
                $proposal->increment('votes_for');
            } else {
                $proposal->increment('votes_against');
            }

            // Update proposal status
            $this->updateProposalStatus($proposal);

            // Update vote count
            $this->increment('vote_count');

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Execute a proposal.
     */
    public function executeProposal($proposalId): bool
    {
        $proposal = $this->proposals()->find($proposalId);
        
        if (!$proposal || $proposal->status !== 'approved') {
            return false;
        }

        if ($proposal->execution_delay > 0) {
            $proposal->update([
                'status' => 'pending_execution',
                'execution_scheduled_at' => now()->addDays($proposal->execution_delay),
            ]);
            return true;
        }

        DB::beginTransaction();

        try {
            // Execute proposal
            $proposal->update([
                'status' => 'executed',
                'executed_at' => now(),
            ]);

            // Create treasury transaction
            $this->treasuryTransactions()->create([
                'type' => 'proposal_execution',
                'amount' => 0,
                'currency' => 'USD',
                'status' => 'completed',
                'processed_at' => now(),
                'created_at' => now(),
            ]);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB->rollBack();
            return false;
        }
    }

    /**
     * Deploy the DAO.
     */
    public function deploy($smartContractAddress = null): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        return $this->update([
            'status' => 'active',
            'smart_contract_address' => $smartContractAddress,
            'deployed_at' => now(),
            'deployed_by' => auth()->id(),
        ]);
    }

    /**
     * Suspend the DAO.
     */
    public function suspend(): bool
    {
        return $this->update([
            'status' => 'suspended',
            'suspended_at' => now(),
        ]);
    }

    /**
     * Update proposal status.
     */
    private function updateProposalStatus($proposal): void
    {
        $totalVotes = $proposal->votes_for + $proposal->votes_against;
        $totalVotingPower = $this->average_voting_power * $this->member_count;
        $quorumReached = ($totalVotes / $totalVotingPower) * 100 >= $this->quorum_percentage;

        $proposal->update([
            'quorum_reached' => $quorumReached,
            'status' => $quorumReached ? 'active' : 'pending',
        ]);
    }

    /**
     * Recalculate average voting power.
     */
    private function recalculateAverageVotingPower(): void
    {
        $totalVotingPower = $this->members()->sum('voting_power');
        $memberCount = $this->members()->count();
        
        $this->update([
            'average_voting_power' => $memberCount > 0 ? $totalVotingPower / $memberCount : 0,
        ]);
    }

    /**
     * Get DAO statistics.
     */
    public static function getStatistics(): array
    {
        $stats = [
            'total_daos' => self::count(),
            'active_daos' => self::active()->count(),
            'public_daos' => self::public()->count(),
            'total_members' => self::active()->sum('member_count'),
            'total_proposals' => self::active()->sum('proposal_count'),
            'total_votes' => self::active()->sum('vote_count'),
            'total_treasury' => self::active()->sum('treasury_balance'),
            'average_voting_power' => self::active()->avg('average_voting_power'),
            'governance_distribution' => self::active()->groupBy('governance_model')->map->count()->toArray(),
            'voting_period_distribution' => self::active()->groupBy('voting_period')->map->count()->toArray(),
        ];

        return $stats;
    }

    /**
     * Get monthly DAO data.
     */
    public static function getMonthlyData(): array
    {
        $monthlyData = [];
        
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthData = [
                'month' => $date->format('Y-m'),
                'new_daos' => self::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'deployed_daos' => self::whereMonth('deployed_at', $date->month)
                    ->whereYear('deployed_at', $date->year)
                    ->count(),
                'new_members' => self::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('member_count'),
                'new_proposals' => self::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('proposal_count'),
                'new_votes' => self::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('vote_count'),
            ];
            
            $monthlyData[] = $monthData;
        }

        return $monthlyData;
    }

    /**
     * Get top DAOs by TVL.
     */
    public static function getTopDaosByTvl($limit = 10): array
    {
        return self::with(['creator', 'token', 'property'])
            ->active()
            ->orderBy('treasury_balance', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($dao) {
                return [
                    'id' => $dao->id,
                    'name' => $dao->name,
                    'type' => $dao->type,
                    'governance_model' => $dao->governance_model,
                    'member_count' => $dao->member_count,
                    'treasury_balance' => $dao->treasury_balance,
                    'proposal_count' => $dao->proposal_count,
                    'vote_count' => $dao->vote_count',
                    'average_voting_power' => $dao->average_voting_power',
                    'creator' => $dao->creator,
                    'token' => $dao->token,
                    'property' => $dao->property,
                ];
            })
            ->toArray();
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($dao) {
            if (!$dao->member_count) {
                $dao->member_count = 0;
            }
            
            if (!$dao->proposal_count) {
                $dao->proposal_count = 0;
            }
            
            if (!$dao->vote_count) {
                $dao->vote_count = 0;
            }
            
            if (!$dao->average_voting_power) {
                $dao->average_voting_power = 0;
            }
        });

        static::updating(function ($dao) {
            if ($dao->isDirty('member_count') || $dao->isDirty('proposal_count') || $dao->isDirty('vote_count')) {
                $dao->recalculateAverageVotingPower();
            }
        });
    }
}
