<?php

namespace App\Repositories\Contracts;

interface BlockchainVerificationRepositoryInterface extends BaseRepositoryInterface
{
    public function getVerificationStats(int $userId);
    public function getRecentVerifications(int $userId, int $limit = 10);
    public function getVerificationTrends(int $userId);
    public function getNetworkDistribution(int $userId);
    public function getVerificationTypes(int $userId);
    public function getSuccessRates(int $userId);
    public function getGasUsageAnalysis(int $userId);
    public function getBlockchainPerformance(int $userId);
    public function getFilteredVerifications(array $filters);
}
