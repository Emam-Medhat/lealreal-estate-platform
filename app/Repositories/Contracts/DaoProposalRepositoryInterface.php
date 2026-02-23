<?php

namespace App\Repositories\Contracts;

interface DaoProposalRepositoryInterface extends BaseRepositoryInterface
{
    public function getByDaoId(int $daoId, int $perPage = 20);
    public function getByStatus(string $status, int $perPage = 20);
    public function vote(int $proposalId, int $userId, string $vote, ?string $reason = null);
    public function execute(int $proposalId);
    public function getProposalStats(int $daoId);
}
