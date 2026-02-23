<?php

namespace App\Repositories\Contracts;

interface DaoMemberRepositoryInterface extends BaseRepositoryInterface
{
    public function getByDaoId(int $daoId, int $perPage = 20);
    public function getByUserId(int $userId, int $perPage = 20);
    public function findByDaoAndUser(int $daoId, int $userId);
    public function getAllByDaoId(int $daoId);
    public function getMemberStats(int $daoId);
}
