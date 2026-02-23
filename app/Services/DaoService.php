<?php

namespace App\Services;

use App\Repositories\Contracts\DaoRepositoryInterface;
use App\Repositories\Contracts\DaoMemberRepositoryInterface;
use App\Repositories\Contracts\DaoProposalRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class DaoService
{
    protected $daoRepository;
    protected $daoMemberRepository;
    protected $daoProposalRepository;

    public function __construct(
        DaoRepositoryInterface $daoRepository,
        DaoMemberRepositoryInterface $daoMemberRepository,
        DaoProposalRepositoryInterface $daoProposalRepository
    ) {
        $this->daoRepository = $daoRepository;
        $this->daoMemberRepository = $daoMemberRepository;
        $this->daoProposalRepository = $daoProposalRepository;
    }

    public function createProposal(int $daoId, array $data)
    {
        // Validation could be here or in FormRequest. Controller does validation.
        
        $data['dao_id'] = $daoId;
        $data['proposer_id'] = auth()->id();
        $data['votes_for'] = 0;
        $data['votes_against'] = 0;
        $data['votes_abstain'] = 0;

        return $this->daoProposalRepository->create($data);
    }

    public function voteOnProposal(int $proposalId, int $userId, string $vote, ?string $reason = null)
    {
        // Check if user is member of DAO
        $proposal = $this->daoProposalRepository->findById($proposalId);
        if (!$proposal) {
            throw new Exception("Proposal not found");
        }

        $member = $this->daoMemberRepository->findByDaoAndUser($proposal->dao_id, $userId);
        if (!$member) {
            throw new Exception("User is not a member of this DAO");
        }

        // Logic to check if user already voted could be added here
        
        return $this->daoProposalRepository->vote($proposalId, $userId, $vote, $reason);
    }

    public function executeProposal(int $proposalId)
    {
        $proposal = $this->daoProposalRepository->findById($proposalId);
        if (!$proposal) {
            throw new Exception("Proposal not found");
        }

        // Check if proposal passed
        // This logic is simplified; real logic would check quorum and vote counts vs threshold
        // Using repository/model logic or helper
        
        return $this->daoProposalRepository->execute($proposalId);
    }

    public function addMember(int $daoId, array $data)
    {
        $data['dao_id'] = $daoId;
        $data['joined_at'] = now();
        return $this->daoMemberRepository->create($data);
    }

    public function joinDao(int $daoId, int $userId, array $data)
    {
        // Check if already member
        $existing = $this->daoMemberRepository->findByDaoAndUser($daoId, $userId);
        if ($existing) {
            throw new Exception("User is already a member of this DAO");
        }

        $data['dao_id'] = $daoId;
        $data['user_id'] = $userId;
        $data['joined_at'] = now();
        $data['role'] = 'member'; // Default role
        
        return $this->daoMemberRepository->create($data);
    }
}
