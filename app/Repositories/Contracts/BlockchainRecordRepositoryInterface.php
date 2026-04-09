<?php

namespace App\Repositories\Contracts;

interface BlockchainRecordRepositoryInterface extends BaseRepositoryInterface
{
    public function getRecent(int $limit = 5);
    public function getStats();
    public function getLatestBlock();
    public function findByHash(string $hash);
    public function findByHeight(int $height);
    public function getPaginatedRecords(int $limit = 50, int $offset = 0);
}
