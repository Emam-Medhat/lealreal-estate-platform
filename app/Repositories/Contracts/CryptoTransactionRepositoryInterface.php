<?php

namespace App\Repositories\Contracts;

interface CryptoTransactionRepositoryInterface extends BaseRepositoryInterface
{
    public function getByAddress(string $address, int $perPage = 50);
    public function getRecent(int $limit = 10);
    public function getStats(string $period = '30d');
    public function findByHash(string $hash);
    public function getByBlockHash(string $blockHash);
    public function getFiltered(array $filters, int $perPage = 50);
    public function getUserStats(int $userId);
    public function getTokenStats(string $contractAddress);
    public function totalVolume();
}
