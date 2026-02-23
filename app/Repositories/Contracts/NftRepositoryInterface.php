<?php

namespace App\Repositories\Contracts;

interface NftRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get recent NFTs.
     */
    public function getRecent(int $limit = 10);

    /**
     * Get NFTs by standard.
     */
    public function getByStandard(string $standard, int $limit = 10);

    /**
     * Get NFTs by property ID.
     */
    public function getByPropertyId(int $propertyId);

    public function getPaginated(int $perPage = 20);
    public function countListed();
    public function sumPrice();
    public function countOwners();
    public function getFiltered(array $filters, int $perPage = 15);

    public function getStats();
    public function search(array $filters, int $perPage = 20);
    public function getNetworkStats();
    public function getStandardStats();
}
