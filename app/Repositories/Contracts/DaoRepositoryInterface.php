<?php

namespace App\Repositories\Contracts;

interface DaoRepositoryInterface extends BaseRepositoryInterface
{
    public function getByIdOrAddress($id = null, $address = null);
    public function getAll(int $perPage = 20);
    public function findByIdWithRelations(int $id);
    public function getTreasuryStats(int $id);
    public function getVotingStats(int $id);
}
