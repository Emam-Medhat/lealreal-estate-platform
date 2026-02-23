<?php

namespace App\Repositories\Contracts;

interface SmartContractRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get verified contracts.
     */
    public function getVerified(int $limit = 10);
    public function getRecent(int $limit = 10);

    /**
     * Get contracts by type.
     */
    public function getByType(string $type, int $limit = 10);
    public function getFiltered(array $filters, int $perPage = 20);
    public function findByAddress(string $address);
    public function findByAddressAndNetwork(string $address, string $network);
    public function getNetworkStats();
    public function getTypeStats();
}
