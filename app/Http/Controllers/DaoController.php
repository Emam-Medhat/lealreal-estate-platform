<?php

namespace App\Http\Controllers;

use App\Models\Dao;
use App\Models\CryptoWallet;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $daos = Dao::with(['creator', 'members', 'proposals'])->latest()->paginate(20);
        
        return view('blockchain.daos.index', compact('daos'));
    }

    public function createDao(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255|unique:daos',
            'contract_address' => 'required|string|max:255',
            'creator_address' => 'required|string|max:255',
            'token_address' => 'nullable|string|max:255',
            'governance_token' => 'required|string|max:255',
            'quorum' => 'required|integer|min:1|max:100',
            'voting_period' => 'required|integer|min:1|max:10080',
            'proposal_threshold' => 'required|integer|min:1',
            'treasury_address' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'website' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'discord' => 'nullable|string|max:255',
            'telegram' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
            'metadata' => 'nullable|array',
            'created_at' => 'now()',
            'updated_at' => 'now()'
        ]);

        // Handle logo upload
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoPath = $logo->store('daos', 'public');
        }

        $dao = Dao::create([
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'contract_address' => $request->contract_address,
            'creator_address' => $request->creator_address,
            'token_address' => $request->token_address,
            'governance_token' => $request->governance_token,
            'quorum' => $request->quorum,
            'voting_period' => $request->voting_period,
            'proposal_threshold' => $request->proposal_threshold,
            'treasury_address' => $request->treasury_address,
            'logo' => $logoPath,
            'website' => $request->website,
            'twitter' => $request->twitter,
            'discord' => $request->discord,
            'telegram' => $request->telegram,
            'is_active' => $request->is_active,
            'metadata' => $request->metadata ?? [],
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'dao' => $dao
        ]);
    }

    public function getDaos(Request $request)
    {
        $query = Dao::with(['creator', 'members', 'proposals']);
        
        if ($request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }
        
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $daos = $query->latest()->paginate(20);
        
        return response()->json($daos);
    }

    public function getDao(Request $request)
    {
        $dao = Dao::with(['creator', 'members', 'proposals'])
            ->where('id', $request->id)
            ->orWhere('address', $request->address)
            ->first();
        
        if (!$dao) {
            return response()->json(['error' => 'DAO not found'], 404);
        }

        return response()->json($dao);
    }

    public function updateDao(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:daos,id',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'quorum' => 'nullable|integer|min:1|max:100',
            'voting_period' => 'nullable|integer|min:1|max:10080',
            'proposal_threshold' => 'nullable|integer|min:1',
            'website' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'discord' => 'nullable|string|max:255',
            'telegram' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'metadata' => 'nullable|array'
        ]);

        $dao = Dao::findOrFail($request->id);
        
        // Handle logo upload
        $logoPath = $dao->logo;
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoPath = $logo->store('daos', 'public');
        }

        $dao->update([
            'name' => $request->name ?? $dao->name,
            'description' => $request->description ?? $dao->description,
            'quorum' => $request->quorum ?? $dao->quorum,
            'voting_period' => $request->voting_period ?? $dao->voting_period,
            'proposal_threshold' => $request->proposal_threshold ?? $dao->proposal_threshold,
            'logo' => $logoPath,
            'website' => $request->website ?? $dao->website,
            'twitter' => $request->twitter ?? $dao->twitter,
            'discord' => $request->discord ?? $dao->discord,
            'telegram' => $request->telegram ?? $dao->telegram,
            'is_active' => $request->is_active ?? $dao->is_active,
            'metadata' => $request->metadata ?? $dao->metadata,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'dao' => $dao
        ]);
    }

    public function createProposal(Request $request)
    {
        $request->validate([
            'dao_id' => 'required|integer|exists:daos,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'proposer_address' => 'required|string|max:255',
            'proposal_type' => 'required|string|in:funding,governance,parameter_change,upgrade',
            'target_address' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|in:ETH,USDC,USDT',
            'execution_date' => 'nullable|date|after:now',
            'parameters' => 'nullable|array',
            'voting_starts_at' => 'required|date',
            'voting_ends_at' => 'required|date|after:voting_starts_at',
            'status' => 'required|string|in:pending,active,executed,failed,expired',
            'created_at' => 'now()',
            'updated_at' => 'now()'
        ]);

        $proposal = $this->createProposalRecord($request->all());

        return response()->json([
            'status' => 'success',
            'proposal' => $proposal
        ]);
    }

    public function getProposals(Request $request)
    {
        $daoId = $request->dao_id;
        
        if (!$daoId) {
            return response()->json(['error' => 'DAO ID is required'], 400);
        }

        $dao = Dao::findOrFail($daoId);
        
        $proposals = $this->getDaoProposals($dao);

        return response()->json($proposals);
    }

    public function getProposal(Request $request)
    {
        $proposalId = $request->id;
        
        $proposal = $this->getProposalById($proposalId);
        
        if (!$proposal) {
            return response()->json(['error' => 'Proposal not found'], 404);
        }

        return response()->json($proposal);
    }

    public function voteOnProposal(Request $request)
    {
        $request->validate([
            'proposal_id' => 'required|integer',
            'voter_address' => 'required|string|max:255',
            'vote' => 'required|string|in:for,against,abstain',
            'reason' => 'nullable|string|max:255',
            'voting_power' => 'required|integer|min:1'
        ]);

        $proposal = $this->getProposalById($request->proposal_id);
        
        if (!$proposal) {
            return response()->json(['error' => 'Proposal not found'], 404);
        }

        if ($proposal->status !== 'active') {
            return response()->json(['error' => 'Proposal is not active for voting'], 400);
        }

        if (now()->gt($proposal->voting_ends_at)) {
            return response()->json(['error' => 'Voting period has ended'], 400);
        }

        $result = $this->castVote($proposal, $request->all());

        return response()->json([
            'status' => $result['status'],
            'vote' => $result['vote']
        ]);
    }

    public function executeProposal(Request $request)
    {
        $request->validate([
            'proposal_id' => 'required|integer',
            'executor_address' => 'required|string|max:255'
        ]);

        $proposal = $this->getProposalById($request->proposal_id);
        
        if (!$proposal) {
            return response()->json(['error' => 'Proposal not found'], 404);
        }

        if ($proposal->status !== 'active') {
            return response()->json(['error' => 'Proposal cannot be executed'], 400);
        }

        if (!$this->canExecuteProposal($proposal)) {
            return response()->json(['error' => 'Proposal does not have enough votes to execute'], 400);
        }

        $result = $this->executeProposalAction($proposal, $request->executor_address);

        return response()->json([
            'status' => $result['status'],
            'transaction_hash' => $result['transaction_hash']
        ]);
    }

    public function getDaoStats(Request $request)
    {
        $daoId = $request->dao_id;
        
        if (!$daoId) {
            return response()->json(['error' => 'DAO ID is required'], 400);
        }

        $dao = Dao::findOrFail($daoId);
        
        $stats = [
            'dao_info' => [
                'name' => $dao->name,
                'address' => $dao->address,
                'creator_address' => $dao->creator_address,
                'governance_token' => $dao->governance_token,
                'quorum' => $dao->quorum,
                'voting_period' => $dao->voting_period,
                'proposal_threshold' => $dao->proposal_threshold,
                'created_at' => $dao->created_at
            ],
            'member_stats' => $this->getDaoMemberStats($dao),
            'proposal_stats' => $this->getDaoProposalStats($dao),
            'treasury_stats' => $this->getDaoTreasuryStats($dao),
            'voting_stats' => $this->getDaoVotingStats($dao)
        ];

        return response()->json($stats);
    }

    public function getDaoMembers(Request $request)
    {
        $daoId = $request->dao_id;
        
        if (!$daoId) {
            return response()->json(['error' => 'DAO ID is required'], 400);
        }

        $dao = Dao::findOrFail($daoId);
        
        $members = $this->buildDaoMembers($dao);

        return response()->json($members);
    }

    public function joinDao(Request $request)
    {
        $request->validate([
            'dao_id' => 'required|integer|exists:daos,id',
            'member_address' => 'required|string|max:255',
            'voting_power' => 'required|integer|min:1',
            'contribution' => 'nullable|numeric|min:0'
        ]);

        $dao = Dao::findOrFail($request->dao_id);
        
        $result = $this->addMemberToDao($dao, $request->all());

        return response()->json([
            'status' => $result['status'],
            'membership' => $result['membership']
        ]);
    }

    public function getDaoTreasury(Request $request)
    {
        $daoId = $request->dao_id;
        
        if (!$daoId) {
            return response()->json(['error' => 'DAO ID is required'], 400);
        }

        $dao = Dao::findOrFail($daoId);
        
        $treasury = $this->getDaoTreasuryBalance($dao);

        return response()->json($treasury);
    }

    private function createProposalRecord($data)
    {
        // Simplified proposal creation
        return [
            'id' => rand(1, 1000),
            'dao_id' => $data['dao_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'proposer_address' => $data['proposer_address'],
            'proposal_type' => $data['proposal_type'],
            'target_address' => $data['target_address'],
            'amount' => $data['amount'] ?? 0,
            'currency' => $data['currency'] ?? 'ETH',
            'execution_date' => $data['execution_date'],
            'parameters' => $data['parameters'] ?? [],
            'voting_starts_at' => $data['voting_starts_at'],
            'voting_ends_at' => $data['voting_ends_at'],
            'status' => $data['status'],
            'votes_for' => 0,
            'votes_against' => 0,
            'votes_abstain' => 0,
            'total_voting_power' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }

    private function getDaoProposals($dao)
    {
        // Simplified proposals generation
        return [
            [
                'id' => 1,
                'dao_id' => $dao->id,
                'title' => 'Sample Proposal 1',
                'description' => 'This is a sample proposal',
                'proposer_address' => $dao->creator_address,
                'proposal_type' => 'governance',
                'status' => 'active',
                'votes_for' => 100,
                'votes_against' => 50,
                'votes_abstain' => 10,
                'total_voting_power' => 160,
                'voting_starts_at' => now()->subDays(7),
                'voting_ends_at' => now()->addDays(7),
                'created_at' => now()->subDays(14)
            ],
            [
                'id' => 2,
                'dao_id' => $dao->id,
                'title' => 'Sample Proposal 2',
                'description' => 'This is another sample proposal',
                'proposer_address' => $dao->creator_address,
                'proposal_type' => 'funding',
                'status' => 'executed',
                'votes_for' => 200,
                'votes_against' => 20,
                'votes_abstain' => 5,
                'total_voting_power' => 225,
                'voting_starts_at' => now()->subDays(30),
                'voting_ends_at' => now()->subDays(23),
                'created_at' => now()->subDays(37)
            ]
        ];
    }

    private function getProposalById($id)
    {
        // Simplified proposal retrieval
        return [
            'id' => $id,
            'dao_id' => 1,
            'title' => 'Sample Proposal',
            'description' => 'This is a sample proposal',
            'proposer_address' => '0x' . substr(hash('sha256', $id), 0, 40),
            'proposal_type' => 'governance',
            'status' => 'active',
            'votes_for' => 100,
            'votes_against' => 50,
            'votes_abstain' => 10,
            'total_voting_power' => 160,
            'voting_starts_at' => now()->subDays(7),
            'voting_ends_at' => now()->addDays(7),
            'created_at' => now()->subDays(14)
        ];
    }

    private function castVote($proposal, $data)
    {
        // Simplified voting
        $proposal['votes_' . $data['vote']] += $data['voting_power'];
        $proposal['total_voting_power'] += $data['voting_power'];
        
        return [
            'status' => 'success',
            'vote' => [
                'proposal_id' => $proposal['id'],
                'voter_address' => $data['voter_address'],
                'vote' => $data['vote'],
                'voting_power' => $data['voting_power'],
                'reason' => $data['reason'] ?? '',
                'created_at' => now()
            ]
        ];
    }

    private function canExecuteProposal($proposal)
    {
        $totalVotes = $proposal['votes_for'] + $proposal['votes_against'];
        $quorum = 100; // Simplified quorum check
        
        return $totalVotes >= $quorum && $proposal['votes_for'] > $proposal['votes_against'];
    }

    private function executeProposalAction($proposal, $executorAddress)
    {
        $transactionHash = '0x' . bin2hex(random_bytes(32));
        
        $proposal['status'] = 'executed';
        
        return [
            'status' => 'success',
            'transaction_hash' => $transactionHash
        ];
    }

    private function getDaoMemberStats($dao)
    {
        return [
            'total_members' => rand(100, 10000),
            'active_members' => rand(50, 5000),
            'new_members_30d' => rand(10, 100),
            'average_voting_power' => rand(1, 1000),
            'total_voting_power' => rand(10000, 1000000)
        ];
    }

    private function getDaoProposalStats($dao)
    {
        return [
            'total_proposals' => rand(10, 100),
            'active_proposals' => rand(1, 10),
            'executed_proposals' => rand(5, 50),
            'failed_proposals' => rand(1, 20),
            'proposals_30d' => rand(1, 10),
            'average_participation' => rand(30, 80)
        ];
    }

    private function getDaoTreasuryStats($dao)
    {
        return [
            'total_balance' => rand(1000, 1000000),
            'eth_balance' => rand(100, 100000),
            'token_balance' => rand(1000, 1000000),
            'usd_value' => rand(100000, 10000000),
            'last_updated' => now()
        ];
    }

    private function getDaoVotingStats($dao)
    {
        return [
            'total_votes_cast' => rand(1000, 10000),
            'votes_30d' => rand(100, 1000),
            'average_participation' => rand(30, 80),
            'quorum_met_rate' => rand(60, 95),
            'proposal_success_rate' => rand(70, 90)
        ];
    }

    private function buildDaoMembers($dao)
    {
        $members = [];
        
        for ($i = 0; $i < 50; $i++) {
            $members[] = [
                'address' => '0x' . substr(hash('sha256', $i), 0, 40),
                'voting_power' => rand(1, 1000),
                'joined_at' => now()->subDays(rand(1, 365)),
                'last_vote' => now()->subDays(rand(0, 30)),
                'proposals_created' => rand(0, 10),
                'votes_cast' => rand(0, 50)
            ];
        }
        
        return $members;
    }

    private function addMemberToDao($dao, $data)
    {
        return [
            'status' => 'success',
            'membership' => [
                'dao_id' => $dao->id,
                'member_address' => $data['member_address'],
                'voting_power' => $data['voting_power'],
                'contribution' => $data['contribution'] ?? 0,
                'joined_at' => now()
            ]
        ];
    }

    private function getDaoTreasuryBalance($dao)
    {
        return [
            'eth_balance' => rand(10, 1000),
            'token_balances' => [
                [
                    'token_address' => '0x' . substr(hash('sha256', 'token1'), 0, 40),
                    'token_symbol' => 'TOKEN1',
                    'balance' => rand(1000, 100000)
                ],
                [
                    'token_address' => '0x' . substr(hash('sha256', 'token2'), 0, 40),
                    'token_symbol' => 'TOKEN2',
                    'balance' => rand(1000, 100000)
                ]
            ],
            'total_usd_value' => rand(100000, 10000000),
            'last_updated' => now()
        ];
    }

    public function exportDaos(Request $request)
    {
        $format = $request->format ?? 'json';
        $limit = $request->limit ?? 1000;
        
        $daos = Dao::with(['creator', 'members', 'proposals'])->latest()->limit($limit)->get();

        if ($format === 'csv') {
            return $this->exportDaosToCsv($daos);
        }

        return response()->json($daos);
    }

    private function exportDaosToCsv($daos)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="daos.csv"'
        ];

        $callback = function() use ($daos) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'ID', 'Name', 'Address', 'Contract Address', 'Creator Address', 
                'Governance Token', 'Quorum', 'Voting Period', 'Proposal Threshold', 
                'Active', 'Created At'
            ]);
            
            foreach ($daos as $dao) {
                fputcsv($file, [
                    $dao->id,
                    $dao->name,
                    $dao->address,
                    $dao->contract_address,
                    $dao->creator_address,
                    $dao->governance_token,
                    $dao->quorum,
                    $dao->voting_period,
                    $dao->proposal_threshold,
                    $dao->is_active,
                    $dao->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
