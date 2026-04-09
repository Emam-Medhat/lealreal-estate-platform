<?php

namespace App\Repositories\Contracts;

interface BlockchainTransactionRepositoryInterface extends BaseRepositoryInterface
{
    public function getStatsByNetwork();
    public function getGasStats();
    public function getCostStats();
    public function getCostByNetwork();
    public function getCostByContractType();
    public function getNetworkAggregates();
    public function getRecent(int $limit = 10);
    public function getPaginated(int $perPage = 15);
    public function getByUser(int $userId, int $perPage = 15);
}
