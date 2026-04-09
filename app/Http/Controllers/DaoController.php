<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\DaoRepositoryInterface;
use App\Repositories\Contracts\DaoMemberRepositoryInterface;
use App\Repositories\Contracts\DaoProposalRepositoryInterface;
use App\Services\DaoService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DaoController extends Controller
{
    protected $daoRepository;
    protected $daoMemberRepository;
    protected $daoProposalRepository;
    protected $daoService;

    public function __construct(
        DaoRepositoryInterface $daoRepository,
        DaoMemberRepositoryInterface $daoMemberRepository,
        DaoProposalRepositoryInterface $daoProposalRepository,
        DaoService $daoService
    ) {
        $this->middleware('auth');
        $this->daoRepository = $daoRepository;
        $this->daoMemberRepository = $daoMemberRepository;
        $this->daoProposalRepository = $daoProposalRepository;
        $this->daoService = $daoService;
    }

    public function show($id)
    {
        $dao = $this->daoRepository->findByIdWithRelations($id);
        
        $treasuryStats = $this->daoRepository->getTreasuryStats($id);
        $votingStats = $this->daoRepository->getVotingStats($id);

        return view('blockchain.daos.show', compact('dao', 'treasuryStats', 'votingStats'));
    }

    public function members($id)
    {
        $dao = $this->daoRepository->findByIdWithRelations($id);
        
        return view('blockchain.daos.members', compact('dao'));
    }

    public function proposals($id)
    {
        $dao = $this->daoRepository->findByIdWithRelations($id);
        
        return view('blockchain.daos.proposals', compact('dao'));
    }

    public function createProposal($id)
    {
        $dao = $this->daoRepository->find($id);
        
        return view('blockchain.daos.create-proposal', compact('dao'));
    }

    public function storeProposal(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:funding,parameter_change,member,other',
            'amount_requested' => 'nullable|numeric|min:0',
            'recipient_address' => 'nullable|string|max:255',
            'status' => 'required|in:active,passed,rejected,executed',
            'voting_starts_at' => 'required|date',
            'voting_ends_at' => 'required|date|after:voting_starts_at'
        ]);

        $dao = $this->daoRepository->find($id);

        $this->daoProposalRepository->create([
            'dao_id' => $dao->id,
            'proposer_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'amount_requested' => $request->amount_requested,
            'recipient_address' => $request->recipient_address,
            'status' => $request->status,
            'voting_starts_at' => $request->voting_starts_at,
            'voting_ends_at' => $request->voting_ends_at,
            'votes_for' => 0,
            'votes_against' => 0,
            'votes_abstain' => 0
        ]);

        return redirect()->route('blockchain.dao.proposals', $dao->id)
            ->with('success', 'تم إنشاء المقترح بنجاح');
    }

    public function showProposal($id, $proposal_id)
    {
        $dao = $this->daoRepository->find($id);
        $proposal = $this->daoProposalRepository->find($proposal_id);
        
        if ($proposal->dao_id !== $dao->id) {
            abort(404);
        }
        
        return view('blockchain.daos.show-proposal', compact('dao', 'proposal'));
    }

    public function editProposal($id, $proposal_id)
    {
        $dao = $this->daoRepository->find($id);
        $proposal = $this->daoProposalRepository->find($proposal_id);
        
        if ($proposal->dao_id !== $dao->id) {
            abort(404);
        }
        
        return view('blockchain.daos.edit-proposal', compact('dao', 'proposal'));
    }

    public function updateProposal(Request $request, $id, $proposal_id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:funding,parameter_change,member,other',
            'amount_requested' => 'nullable|numeric|min:0',
            'recipient_address' => 'nullable|string|max:255',
            'status' => 'required|in:active,passed,rejected,executed',
            'voting_starts_at' => 'required|date',
            'voting_ends_at' => 'required|date|after:voting_starts_at'
        ]);

        $dao = $this->daoRepository->find($id);
        $proposal = $this->daoProposalRepository->find($proposal_id);

        if ($proposal->dao_id !== $dao->id) {
            abort(404);
        }

        $this->daoProposalRepository->update($proposal_id, [
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'amount_requested' => $request->amount_requested,
            'recipient_address' => $request->recipient_address,
            'status' => $request->status,
            'voting_starts_at' => $request->voting_starts_at,
            'voting_ends_at' => $request->voting_ends_at
        ]);

        return redirect()->route('blockchain.dao.proposals', $dao->id)
            ->with('success', 'تم تحديث المقترح بنجاح');
    }

    public function deleteProposal($id, $proposal_id)
    {
        $dao = $this->daoRepository->find($id);
        $proposal = $this->daoProposalRepository->find($proposal_id);
        
        if ($proposal->dao_id !== $dao->id) {
            abort(404);
        }
        
        $this->daoProposalRepository->delete($proposal_id);

        return redirect()->route('blockchain.dao.proposals', $dao->id)
            ->with('success', 'تم حذف المقترح بنجاح');
    }

    public function voteOnProposal(Request $request, $id, $proposal_id)
    {
        $request->validate([
            'vote' => 'required|in:for,against,abstain',
            'reason' => 'nullable|string|max:255'
        ]);

        $dao = $this->daoRepository->find($id);
        
        try {
            $this->daoService->voteOnProposal($proposal_id, auth()->id(), $request->vote, $request->reason);
            return redirect()->route('blockchain.dao.proposals', $dao->id)
                ->with('success', 'تم تسجيل تصويتك بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function executeProposal($id, $proposal_id)
    {
        $dao = $this->daoRepository->find($id);
        
        try {
            $this->daoService->executeProposal($proposal_id);
            return redirect()->route('blockchain.dao.proposals', $dao->id)
                ->with('success', 'تم تنفيذ المقترح بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function vote($id)
    {
        $dao = $this->daoRepository->find($id);
        
        return view('blockchain.daos.vote', compact('dao'));
    }

    public function addMember($id)
    {
        $dao = $this->daoRepository->find($id);
        
        return view('blockchain.daos.add-member', compact('dao'));
    }

    public function storeMember(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:member,admin,treasurer',
            'voting_power' => 'required|numeric|min:0',
            'tokens_held' => 'required|numeric|min:0'
        ]);

        $dao = $this->daoRepository->find($id);

        $this->daoMemberRepository->create([
            'dao_id' => $dao->id,
            'user_id' => $request->user_id,
            'role' => $request->role,
            'voting_power' => $request->voting_power,
            'tokens_held' => $request->tokens_held,
            'joined_at' => now()
        ]);

        return redirect()->route('blockchain.dao.members', $dao->id)
            ->with('success', 'تم إضافة العضو بنجاح');
    }

    public function showMember($id, $member_id)
    {
        $dao = $this->daoRepository->find($id);
        $member = $this->daoMemberRepository->find($member_id);
        
        if ($member->dao_id !== $dao->id) {
            abort(404);
        }
        
        return view('blockchain.daos.show-member', compact('dao', 'member'));
    }

    public function editMember($id, $member_id)
    {
        $dao = $this->daoRepository->find($id);
        $member = $this->daoMemberRepository->find($member_id);
        
        if ($member->dao_id !== $dao->id) {
            abort(404);
        }
        
        return view('blockchain.daos.edit-member', compact('dao', 'member'));
    }

    public function updateMember(Request $request, $id, $member_id)
    {
        $request->validate([
            'role' => 'required|in:member,admin,treasurer',
            'voting_power' => 'required|numeric|min:0',
            'tokens_held' => 'required|numeric|min:0'
        ]);

        $dao = $this->daoRepository->find($id);
        $member = $this->daoMemberRepository->find($member_id);

        if ($member->dao_id !== $dao->id) {
            abort(404);
        }

        $this->daoMemberRepository->update($member_id, [
            'role' => $request->role,
            'voting_power' => $request->voting_power,
            'tokens_held' => $request->tokens_held
        ]);

        return redirect()->route('blockchain.dao.members', $dao->id)
            ->with('success', 'تم تحديث العضو بنجاح');
    }

    public function deleteMember($id, $member_id)
    {
        $dao = $this->daoRepository->find($id);
        $member = $this->daoMemberRepository->find($member_id);
        
        if ($member->dao_id !== $dao->id) {
            abort(404);
        }
        
        $this->daoMemberRepository->delete($member_id);

        return redirect()->route('blockchain.dao.members', $dao->id)
            ->with('success', 'تم حذف العضو بنجاح');
    }

    public function exportMembers($id)
    {
        $dao = $this->daoRepository->find($id);
        $members = $this->daoMemberRepository->getAllByDaoId($id);
        
        // Export logic here - could return CSV or Excel
        return response()->json([
            'dao' => $dao->name,
            'members_count' => $members->count(),
            'members' => $members
        ]);
    }

    public function treasury($id)
    {
        $dao = $this->daoRepository->findByIdWithRelations($id);
        
        return view('blockchain.daos.treasury', compact('dao'));
    }

    public function create()
    {
        return view('blockchain.daos.create');
    }

    public function index()
    {
        $daos = $this->daoRepository->getAll();
        
        return view('blockchain.daos.index', compact('daos'));
    }

    public function getDao(Request $request)
    {
        $dao = $this->daoRepository->getByIdOrAddress($request->id, $request->address);
        
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

        $dao = $this->daoRepository->find($request->id);
        
        // Handle logo upload
        $logoPath = $dao->logo;
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoPath = $logo->store('daos', 'public');
        }

        $this->daoRepository->update($request->id, [
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

        // Fetch updated DAO
        $dao = $this->daoRepository->find($request->id);

        return response()->json([
            'status' => 'success',
            'dao' => $dao
        ]);
    }

    public function getDaos(Request $request)
    {
        $daoId = $request->dao_id;
        
        if (!$daoId) {
            return response()->json(['error' => 'DAO ID is required'], 400);
        }

        $proposals = $this->daoProposalRepository->getByDaoId($daoId);

        return response()->json($proposals);
    }

    public function getProposal(Request $request)
    {
        $proposalId = $request->id;
        
        $proposal = $this->daoProposalRepository->find($proposalId);
        
        if (!$proposal) {
            return response()->json(['error' => 'Proposal not found'], 404);
        }

        return response()->json($proposal);
    }

    public function joinDao(Request $request)
    {
        $request->validate([
            'dao_id' => 'required|integer|exists:daos,id',
            'member_address' => 'required|string|max:255',
            'voting_power' => 'required|integer|min:1',
            'contribution' => 'nullable|numeric|min:0'
        ]);

        try {
            // Assume user is authenticated for this action.
            $userId = auth()->id();
            if (!$userId) {
                return response()->json(['error' => 'User must be authenticated'], 401);
            }

            $member = $this->daoService->joinDao($request->dao_id, $userId, [
                'voting_power' => $request->voting_power,
                'contribution' => $request->contribution ?? 0
            ]);

            return response()->json([
                'status' => 'success',
                'membership' => $member
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getDaoTreasury(Request $request)
    {
        $daoId = $request->dao_id;
        
        if (!$daoId) {
            return response()->json(['error' => 'DAO ID is required'], 400);
        }

        $treasury = $this->daoRepository->getTreasuryStats($daoId);

        return response()->json($treasury);
    }

    public function exportDaos(Request $request)
    {
        $format = $request->format ?? 'json';
        $limit = $request->limit ?? 1000;
        
        // We should implement limit in repository if needed, but getAll is paginated.
        // For export, we might need a non-paginated method or use getAll with large perPage.
        // Assuming getAll handles perPage properly.
        $daos = $this->daoRepository->getAll($limit); 

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
