<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Dao extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'contract_address',
        'creator_address',
        'governance_token_address',
        'treasury_address',
        'total_members',
        'active_members',
        'voting_period',
        'quorum_percentage',
        'proposal_threshold',
        'voting_delay',
        'execution_delay',
        'total_proposals',
        'active_proposals',
        'executed_proposals',
        'treasury_balance',
        'governance_token_supply',
        'governance_token_holders',
        'voting_power_distribution',
        'participation_rate',
        'last_activity_timestamp',
        'status',
        'is_verified',
        'verification_status',
        'tags',
        'metadata',
        'social_links',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'quorum_percentage' => 'decimal:5',
        'proposal_threshold' => 'decimal:18',
        'treasury_balance' => 'decimal:18',
        'governance_token_supply' => 'decimal:18',
        'voting_power_distribution' => 'array',
        'last_activity_timestamp' => 'datetime',
        'is_verified' => 'boolean',
        'tags' => 'array',
        'metadata' => 'array',
        'social_links' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByCreator($query, $address)
    {
        return $query->where('creator_address', $address);
    }

    public function scopeByToken($query, $address)
    {
        return $query->where('governance_token_address', $address);
    }

    public function scopeByMemberCount($query, $min = 0, $max = null)
    {
        $query->where('total_members', '>=', $min);
        if ($max !== null) {
            $query->where('total_members', '<=', $max);
        }
        return $query;
    }

    public function scopeByTreasuryBalance($query, $min = 0, $max = null)
    {
        $query->where('treasury_balance', '>=', $min);
        if ($max !== null) {
            $query->where('treasury_balance', '<=', $max);
        }
        return $query;
    }

    // Accessors
    public function getFormattedTreasuryBalanceAttribute()
    {
        return number_format($this->treasury_balance, 8);
    }

    public function getFormattedGovernanceTokenSupplyAttribute()
    {
        return number_format($this->governance_token_supply, 8);
    }

    public function getFormattedProposalThresholdAttribute()
    {
        return number_format($this->proposal_threshold, 8);
    }

    public function getQuorumPercentageLabelAttribute()
    {
        return number_format($this->quorum_percentage, 2) . '%';
    }

    public function getParticipationRateLabelAttribute()
    {
        return number_format($this->participation_rate, 2) . '%';
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'suspended' => 'معلق',
            'dissolved' => 'منحل'
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function getVerificationStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'قيد الانتظار',
            'verified' => 'تم التحقق',
            'rejected' => 'مرفوض',
            'not_verified' => 'لم يتم التحقق'
        ];
        return $labels[$this->verification_status] ?? $this->verification_status;
    }

    public function getDaoUrlAttribute()
    {
        return "https://etherscan.io/address/{$this->contract_address}";
    }

    public function getGovernanceTokenUrlAttribute()
    {
        return "https://etherscan.io/token/{$this->governance_token_address}";
    }

    public function getTreasuryUrlAttribute()
    {
        return "https://etherscan.io/address/{$this->treasury_address}";
    }

    public function getCreatorUrlAttribute()
    {
        return "https://etherscan.io/address/{$this->creator_address}";
    }

    public function getActiveMemberRateAttribute()
    {
        if ($this->total_members == 0) return 0;
        return ($this->active_members / $this->total_members) * 100;
    }

    public function getFormattedActiveMemberRateAttribute()
    {
        return number_format($this->active_member_rate, 2) . '%';
    }

    public function getProposalSuccessRateAttribute()
    {
        if ($this->total_proposals == 0) return 0;
        return ($this->executed_proposals / $this->total_proposals) * 100;
    }

    public function getFormattedProposalSuccessRateAttribute()
    {
        return number_format($this->proposal_success_rate, 2) . '%';
    }

    public function getDaysSinceLastActivityAttribute()
    {
        return $this->last_activity_timestamp ? 
               $this->last_activity_timestamp->diffInDays(now()) : 
               0;
    }

    public function getFormattedVotingPeriodAttribute()
    {
        $periods = [
            3600 => '1 ساعة',
            86400 => '24 ساعة',
            604800 => '7 أيام',
            1209600 => '14 يوم',
            2592000 => '30 يوم'
        ];
        
        return $periods[$this->voting_period] ?? $this->voting_period . ' ثانية';
    }

    public function getFormattedVotingDelayAttribute()
    {
        $delays = [
            0 => 'فوري',
            3600 => '1 ساعة',
            86400 => '24 ساعة',
            172800 => '48 ساعة'
        ];
        
        return $delays[$this->voting_delay] ?? $this->voting_delay . ' ثانية';
    }

    // Methods
    public function isVerified()
    {
        return $this->is_verified;
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function hasQuorum($votes)
    {
        $totalSupply = $this->governance_token_supply;
        return $totalSupply > 0 && ($votes / $totalSupply) >= ($this->quorum_percentage / 100);
    }

    public function meetsThreshold($votes)
    {
        return $votes >= $this->proposal_threshold;
    }

    public function canPropose($votingPower)
    {
        return $votingPower >= $this->proposal_threshold;
    }

    public function getVotingPower($address)
    {
        // This would get actual voting power from blockchain
        return 0;
    }

    public function calculateQuorumVotes()
    {
        return ($this->governance_token_supply * $this->quorum_percentage) / 100;
    }

    public function getFormattedQuorumVotesAttribute()
    {
        return number_format($this->calculateQuorumVotes(), 8);
    }

    public function getAverageVotingPower()
    {
        if ($this->total_members == 0) return 0;
        return $this->governance_token_supply / $this->total_members;
    }

    public function getFormattedAverageVotingPowerAttribute()
    {
        return number_format($this->average_voting_power, 8);
    }

    public function getTreasuryPerMember()
    {
        if ($this->active_members == 0) return 0;
        return $this->treasury_balance / $this->active_members;
    }

    public function getFormattedTreasuryPerMemberAttribute()
    {
        return number_format($this->treasury_per_member, 8);
    }

    public function getGovernanceTokenPrice()
    {
        // This would get actual token price from external API
        return 0;
    }

    public function getTreasuryValueInUSD()
    {
        $tokenPrice = $this->governance_token_price;
        return $this->treasury_balance * $tokenPrice;
    }

    public function getFormattedTreasuryValueInUSDAttribute()
    {
        return number_format($this->treasury_value_in_usd, 2);
    }

    public function getProposalStats()
    {
        return [
            'total_proposals' => $this->total_proposals,
            'active_proposals' => $this->active_proposals,
            'executed_proposals' => $this->executed_proposals,
            'success_rate' => $this->proposal_success_rate,
            'average_votes' => 0, // Would calculate from actual proposals
            'participation_rate' => $this->participation_rate
        ];
    }

    public function getMemberStats()
    {
        return [
            'total_members' => $this->total_members,
            'active_members' => $this->active_members,
            'active_rate' => $this->active_member_rate,
            'average_voting_power' => $this->average_voting_power,
            'voting_power_distribution' => $this->voting_power_distribution
        ];
    }

    public function getTreasuryStats()
    {
        return [
            'balance' => $this->treasury_balance,
            'balance_usd' => $this->treasury_value_in_usd,
            'per_member' => $this->treasury_per_member,
            'token_address' => $this->treasury_address
        ];
    }

    // Relationships
    public function smartContract(): BelongsTo
    {
        return $this->belongsTo(SmartContract::class, 'contract_address', 'address');
    }

    public function governanceToken(): BelongsTo
    {
        return $this->belongsTo(Token::class, 'governance_token_address', 'address');
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(DaoProposal::class, 'dao_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(DaoMember::class, 'dao_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(DaoVote::class, 'dao_id');
    }

    public function treasuryTransactions(): HasMany
    {
        return $this->hasMany(DaoTreasuryTransaction::class, 'dao_id');
    }

    // Static Methods
    public static function getStats()
    {
        return [
            'total_daos' => self::count(),
            'active_daos' => self::active()->count(),
            'verified_daos' => self::verified()->count(),
            'total_members' => self::sum('total_members'),
            'active_members' => self::sum('active_members'),
            'total_treasury_balance' => self::sum('treasury_balance'),
            'total_proposals' => self::sum('total_proposals'),
            'executed_proposals' => self::sum('executed_proposals'),
            'average_members_per_dao' => self::avg('total_members'),
            'average_treasury_balance' => self::avg('treasury_balance'),
            'daos_today' => self::whereDate('created_at', today())->count(),
            'daos_this_week' => self::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'daos_this_month' => self::whereMonth('created_at', now()->month)->count(),
        ];
    }

    public static function getTopDaos($limit = 20)
    {
        return self::orderBy('treasury_balance', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getMostActiveDaos($limit = 20)
    {
        return self::orderBy('active_members', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getNewDaos($limit = 20)
    {
        return self::orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getVerifiedDaos($limit = 50)
    {
        return self::verified()
                   ->orderBy('treasury_balance', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getDaosByMemberCount($min = 0, $max = null, $limit = 50)
    {
        return self::byMemberCount($min, $max)
                   ->orderBy('total_members', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getDaosByTreasuryBalance($min = 0, $max = null, $limit = 50)
    {
        return self::byTreasuryBalance($min, $max)
                   ->orderBy('treasury_balance', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function searchDaos($query, $limit = 50)
    {
        return self::where(function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('contract_address', 'like', "%{$query}%");
                })
                ->orderBy('treasury_balance', 'desc')
                ->limit($limit)
                ->get();
    }

    public static function getDailyDaoCount($days = 30)
    {
        return self::where('created_at', '>=', now()->subDays($days))
                   ->groupBy('date')
                   ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                   ->orderBy('date', 'desc')
                   ->get();
    }

    public static function getMemberCountDistribution()
    {
        return self::selectRaw('
                CASE 
                    WHEN total_members < 10 THEN "1-9"
                    WHEN total_members < 50 THEN "10-49"
                    WHEN total_members < 100 THEN "50-99"
                    WHEN total_members < 500 THEN "100-499"
                    WHEN total_members < 1000 THEN "500-999"
                    ELSE "1000+"
                END as member_range,
                COUNT(*) as count
            ')
            ->groupBy('member_range')
            ->orderBy('count', 'desc')
            ->get();
    }

    public static function getTreasuryDistribution()
    {
        return self::selectRaw('
                CASE 
                    WHEN treasury_balance < 1 THEN "< 1 ETH"
                    WHEN treasury_balance < 10 THEN "1-10 ETH"
                    WHEN treasury_balance < 100 THEN "10-100 ETH"
                    WHEN treasury_balance < 1000 THEN "100-1000 ETH"
                    ELSE "> 1000 ETH"
                END as treasury_range,
                COUNT(*) as count
            ')
            ->groupBy('treasury_range')
            ->orderBy('count', 'desc')
            ->get();
    }

    public static function getGovernanceStats()
    {
        return [
            'total_governance_tokens' => self::sum('governance_token_supply'),
            'total_treasury_balance' => self::sum('treasury_balance'),
            'average_quorum_percentage' => self::avg('quorum_percentage'),
            'average_participation_rate' => self::avg('participation_rate'),
            'total_proposals' => self::sum('total_proposals'),
            'executed_proposals' => self::sum('executed_proposals'),
            'proposal_success_rate' => self::sum('executed_proposals') / max(self::sum('total_proposals'), 1) * 100,
        ];
    }

    // Export Methods
    public static function exportToCsv($daos)
    {
        $headers = [
            'Name', 'Contract Address', 'Governance Token', 'Total Members', 
            'Active Members', 'Treasury Balance', 'Total Proposals', 
            'Executed Proposals', 'Status', 'Verified', 'Created At'
        ];

        $rows = $daos->map(function ($dao) {
            return [
                $dao->name,
                $dao->contract_address,
                $dao->governance_token_address,
                $dao->total_members,
                $dao->active_members,
                $dao->formatted_treasury_balance,
                $dao->total_proposals,
                $dao->executed_proposals,
                $dao->status_label,
                $dao->is_verified ? 'Yes' : 'No',
                $dao->created_at
            ];
        });

        return collect([$headers])->concat($rows);
    }

    // Validation Methods
    public function validateDao()
    {
        $errors = [];
        
        if (empty($this->name)) {
            $errors[] = 'DAO name is required';
        }
        
        if (empty($this->contract_address)) {
            $errors[] = 'Contract address is required';
        }
        
        if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $this->contract_address)) {
            $errors[] = 'Invalid contract address format';
        }
        
        if (empty($this->creator_address)) {
            $errors[] = 'Creator address is required';
        }
        
        if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $this->creator_address)) {
            $errors[] = 'Invalid creator address format';
        }
        
        if ($this->quorum_percentage < 0 || $this->quorum_percentage > 100) {
            $errors[] = 'Quorum percentage must be between 0 and 100';
        }
        
        if ($this->proposal_threshold < 0) {
            $errors[] = 'Proposal threshold must be positive';
        }
        
        if ($this->total_members < 0) {
            $errors[] = 'Total members must be positive';
        }
        
        if ($this->active_members < 0) {
            $errors[] = 'Active members must be positive';
        }
        
        if ($this->active_members > $this->total_members) {
            $errors[] = 'Active members cannot exceed total members';
        }
        
        return $errors;
    }

    // DAO Operations
    public function createProposal($proposer, $title, $description, $actions = [])
    {
        // This would create actual proposal on blockchain
        return DaoProposal::create([
            'dao_id' => $this->id,
            'proposer_address' => $proposer,
            'title' => $title,
            'description' => $description,
            'actions' => $actions,
            'status' => 'active',
            'created_at' => now()
        ]);
    }

    public function voteOnProposal($proposalId, $voter, $support, $votingPower)
    {
        // This would vote on actual proposal on blockchain
        return DaoVote::create([
            'dao_id' => $this->id,
            'proposal_id' => $proposalId,
            'voter_address' => $voter,
            'support' => $support,
            'voting_power' => $votingPower,
            'voted_at' => now()
        ]);
    }

    public function executeProposal($proposalId, $executor)
    {
        // This would execute actual proposal on blockchain
        $proposal = DaoProposal::find($proposalId);
        if ($proposal) {
            $proposal->status = 'executed';
            $proposal->executed_at = now();
            $proposal->executor_address = $executor;
            $proposal->save();
        }
        
        return $proposal;
    }

    public function addMember($memberAddress, $votingPower)
    {
        // This would add member to DAO on blockchain
        return DaoMember::create([
            'dao_id' => $this->id,
            'member_address' => $memberAddress,
            'voting_power' => $votingPower,
            'joined_at' => now()
        ]);
    }

    public function removeMember($memberAddress)
    {
        // This would remove member from DAO on blockchain
        return DaoMember::where('dao_id', $this->id)
                       ->where('member_address', $memberAddress)
                       ->delete();
    }

    public function transferTreasury($to, $amount, $reason)
    {
        // This would transfer treasury funds on blockchain
        return DaoTreasuryTransaction::create([
            'dao_id' => $this->id,
            'from_address' => $this->treasury_address,
            'to_address' => $to,
            'amount' => $amount,
            'reason' => $reason,
            'transaction_hash' => '0x' . bin2hex(random_bytes(32)),
            'executed_at' => now()
        ]);
    }

    public function updateMemberVotingPower($memberAddress, $newVotingPower)
    {
        // This would update member voting power on blockchain
        $member = DaoMember::where('dao_id', $this->id)
                          ->where('member_address', $memberAddress)
                          ->first();
        
        if ($member) {
            $member->voting_power = $newVotingPower;
            $member->updated_at = now();
            $member->save();
        }
        
        return $member;
    }

    public function getProposalResults($proposalId)
    {
        $proposal = DaoProposal::find($proposalId);
        if (!$proposal) return null;
        
        $votes = DaoVote::where('proposal_id', $proposalId)->get();
        
        $forVotes = $votes->where('support', true)->sum('voting_power');
        $againstVotes = $votes->where('support', false)->sum('voting_power');
        $totalVotes = $forVotes + $againstVotes;
        
        return [
            'proposal' => $proposal,
            'for_votes' => $forVotes,
            'against_votes' => $againstVotes,
            'total_votes' => $totalVotes,
            'quorum_reached' => $this->hasQuorum($totalVotes),
            'threshold_met' => $this->meetsThreshold($forVotes),
            'passed' => $forVotes > $againstVotes,
            'participation_rate' => $this->governance_token_supply > 0 ? 
                                  ($totalVotes / $this->governance_token_supply) * 100 : 0
        ];
    }
}
