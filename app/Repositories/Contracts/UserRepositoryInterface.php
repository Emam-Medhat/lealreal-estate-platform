<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get dashboard statistics for a specific user.
     *
     * @param int $userId
     * @return array
     */
    public function getDashboardStats(int $userId): array;

    /**
     * Get recent activity logs for a specific user.
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getActivityLogs(int $userId, int $limit = 10);
    public function getPaginated(array $filters, int $perPage = 20): LengthAwarePaginator;
    public function getFilteredUsers(array $filters, int $perPage = 20): LengthAwarePaginator;
    public function findByEmail(string $email): ?User;
    public function countByStatus(string $status): int;
    public function countByDate(string $date): int;
    public function countByType(string $type): int;
    public function getRecent(int $limit = 5);
}
