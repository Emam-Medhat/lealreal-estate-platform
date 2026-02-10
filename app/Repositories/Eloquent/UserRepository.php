<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Models\User;
use App\Services\CacheService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Default relations for eager loading
     */
    protected $defaultRelations;

    public function __construct(User $model)
    {
        parent::__construct($model);
        
        $this->defaultRelations = [
            'profile:id,user_id,first_name,last_name,bio,avatar',
            'permissions:id,name,description',
            'company:id,name,logo',
            'subscriptionPlan:id,name,features',
            'devices:id,user_id,device_type,last_used_at',
            'socialAccounts:id,user_id,provider,provider_id'
        ];
    }

    /**
     * Get dashboard statistics for a specific user.
     *
     * @param int $userId
     * @return array
     */
    public function getDashboardStats(int $userId): array
    {
        return $this->remember('getDashboardStats', function () use ($userId) {
            $user = $this->findById($userId);
            
            if (!$user) {
                return [];
            }
            
            return [
                'total_properties' => $user->properties()->count(),
                'active_listings' => $user->properties()->where('status', 'active')->count(),
                'sold_properties' => $user->properties()->where('status', 'sold')->count(),
                'total_sales_value' => $user->properties()->where('status', 'sold')->sum('price'),
                'commission_earned' => $user->properties()->where('status', 'sold')->sum('price') * 0.05,
            ];
        }, ['user_id' => $userId], 600);
    }

    /**
     * Find user by email address.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Count users by status.
     *
     * @param string $status
     * @return int
     */
    public function countByStatus(string $status): int
    {
        return $this->model->where('status', $status)->count();
    }

    /**
     * Count users by date.
     *
     * @param string $date
     * @return int
     */
    public function countByDate(string $date): int
    {
        return $this->model->whereDate('created_at', $date)->count();
    }

    /**
     * Count users by type.
     *
     * @param string $type
     * @return int
     */
    public function countByType(string $type): int
    {
        return $this->model->where('user_type', $type)->count();
    }

    /**
     * Get recent users.
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecent(int $limit = 5)
    {
        return $this->getRecentUsers($limit);
    }

    /**
     * Get recent activity logs for a specific user with caching
     */
    public function getActivityLogs(int $userId, int $limit = 10): Collection
    {
        return $this->remember('getActivityLogs', function () use ($userId, $limit) {
            return \App\Models\UserActivityLog::where('user_id', $userId)
                ->with(['user:id,full_name,email'])
                ->latest()
                ->limit($limit)
                ->get([
                    'id', 'user_id', 'action', 'description', 'ip_address', 
                    'user_agent', 'created_at'
                ]);
        }, ['user_id' => $userId, 'limit' => $limit], 300);
    }

    /**
     * Get paginated users with filtering and caching
     */
    public function getPaginated(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return $this->remember('getPaginated', function () use ($filters, $perPage) {
            $query = $this->model->with($this->defaultRelations);

            // Apply filters efficiently
            $this->applyUserFilters($query, $filters);

            return $query->paginate($perPage, [
                'id',
                'uuid',
                'first_name',
                'last_name',
                'full_name',
                'email',
                'phone',
                'user_type',
                'account_status',
                'kyc_status',
                'is_agent',
                'is_company',
                'is_developer',
                'is_investor',
                'last_login_at',
                'created_at',
                'updated_at'
            ]);
        }, ['filters' => $filters, 'perPage' => $perPage], 600);
    }

    /**
     * Get filtered users with advanced search and caching
     */
    public function getFilteredUsers(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->remember('getFilteredUsers', function () use ($filters, $perPage) {
            $query = $this->model->with($this->defaultRelations);

            // Apply filters efficiently
            $this->applyUserFilters($query, $filters);

            // Apply search if provided
            if (!empty($filters['search'])) {
                $this->applyUserSearch($query, $filters['search']);
            }

            // Apply sorting
            $this->applyUserSorting($query, $filters);

            return $query->paginate($perPage, [
                'id',
                'uuid',
                'first_name',
                'last_name',
                'full_name',
                'email',
                'phone',
                'user_type',
                'account_status',
                'kyc_status',
                'is_agent',
                'is_company',
                'is_developer',
                'is_investor',
                'last_login_at',
                'created_at',
                'updated_at'
            ]);
        }, ['filters' => $filters, 'perPage' => $perPage], 600);
    }

    /**
     * Search users with full-text search optimization
     */
    public function searchUsers(string $query, array $filters = [], int $limit = 50): Collection
    {
        return $this->remember('searchUsers', function () use ($query, $filters, $limit) {
            $userQuery = $this->model->with($this->defaultRelations);

            // Use full-text search if available
            if (DB::getDriverName() === 'mysql') {
                $userQuery->whereRaw("MATCH(first_name, last_name, full_name, email, company) AGAINST(? IN BOOLEAN MODE)", [$query]);
            } else {
                // Fallback to LIKE search
                $userQuery->where(function ($q) use ($query) {
                    $q->where('first_name', 'LIKE', "%{$query}%")
                      ->orWhere('last_name', 'LIKE', "%{$query}%")
                      ->orWhere('full_name', 'LIKE', "%{$query}%")
                      ->orWhere('email', 'LIKE', "%{$query}%")
                      ->orWhere('phone', 'LIKE', "%{$query}%")
                      ->orWhere('agent_company', 'LIKE', "%{$query}%");
                });
            }

            // Apply additional filters
            $this->applyUserFilters($userQuery, $filters);

            return $userQuery->orderBy('created_at', 'desc')
                           ->take($limit)
                           ->get([
                               'id', 'uuid', 'first_name', 'last_name', 'full_name', 'email', 'phone',
                               'user_type', 'account_status', 'is_agent', 'is_company'
                           ]);
        }, ['query' => $query, 'filters' => $filters, 'limit' => $limit], 300);
    }

    /**
     * Get users for export with memory-efficient chunking
     */
    public function getUsersForExport(array $filters = []): \Generator
    {
        $query = $this->model->with([
            'profile:id,user_id,bio',
            'company:id,name',
            'subscriptionPlan:id,name'
        ]);

        // Apply filters
        $this->applyUserFilters($query, $filters);

        // Use chunking for memory efficiency
        foreach ($query->orderBy('created_at', 'desc')->chunk(1000) as $chunk) {
            yield $chunk;
        }
    }

    /**
     * Get user statistics with single query optimization
     */
    public function getUserStats(): array
    {
        return $this->remember('getUserStats', function () {
            $stats = $this->model->selectRaw('
                COUNT(*) as total_users,
                COUNT(CASE WHEN account_status = "active" THEN 1 END) as active_users,
                COUNT(CASE WHEN account_status = "inactive" THEN 1 END) as inactive_users,
                COUNT(CASE WHEN account_status = "suspended" THEN 1 END) as suspended_users,
                COUNT(CASE WHEN user_type = "admin" THEN 1 END) as admin_users,
                COUNT(CASE WHEN user_type = "agent" THEN 1 END) as agent_users,
                COUNT(CASE WHEN user_type = "company" THEN 1 END) as company_users,
                COUNT(CASE WHEN user_type = "developer" THEN 1 END) as developer_users,
                COUNT(CASE WHEN user_type = "investor" THEN 1 END) as investor_users,
                COUNT(CASE WHEN is_agent = 1 THEN 1 END) as verified_agents,
                COUNT(CASE WHEN kyc_status = "verified" THEN 1 END) as kyc_verified_users,
                COUNT(CASE WHEN kyc_status = "pending" THEN 1 END) as kyc_pending_users,
                COUNT(CASE WHEN two_factor_enabled = 1 THEN 1 END) as two_factor_enabled_users,
                COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as users_this_week,
                COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as users_this_month,
                COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY) THEN 1 END) as users_this_quarter,
                COUNT(CASE WHEN last_login_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as active_this_week,
                COUNT(CASE WHEN last_login_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as active_this_month
            ')->first();

            return [
                'total_users' => (int) $stats->total_users,
                'active_users' => (int) $stats->active_users,
                'inactive_users' => (int) $stats->inactive_users,
                'suspended_users' => (int) $stats->suspended_users,
                'admin_users' => (int) $stats->admin_users,
                'agent_users' => (int) $stats->agent_users,
                'company_users' => (int) $stats->company_users,
                'developer_users' => (int) $stats->developer_users,
                'investor_users' => (int) $stats->investor_users,
                'verified_agents' => (int) $stats->verified_agents,
                'kyc_verified_users' => (int) $stats->kyc_verified_users,
                'kyc_pending_users' => (int) $stats->kyc_pending_users,
                'two_factor_enabled_users' => (int) $stats->two_factor_enabled_users,
                'users_this_week' => (int) $stats->users_this_week,
                'users_this_month' => (int) $stats->users_this_month,
                'users_this_quarter' => (int) $stats->users_this_quarter,
                'active_this_week' => (int) $stats->active_this_week,
                'active_this_month' => (int) $stats->active_this_month,
                'growth_rate' => $this->calculateGrowthRate(),
                'activation_rate' => $this->calculateActivationRate(),
                'kyc_completion_rate' => $this->calculateKycCompletionRate(),
                'two_factor_adoption_rate' => $this->calculateTwoFactorAdoptionRate(),
            ];
        }, [], 1800);
    }

    /**
     * Get users by type with caching
     */
    public function getUsersByType(string $userType, int $limit = 50): Collection
    {
        return $this->remember('getUsersByType', function () use ($userType, $limit) {
            return $this->model->where('user_type', $userType)
                ->with(['profile:id,user_id,bio,avatar', 'company:id,name,logo'])
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get([
                    'id', 'first_name', 'last_name', 'full_name', 'email', 'phone',
                    'user_type', 'account_status', 'last_login_at', 'created_at'
                ]);
        }, ['userType' => $userType, 'limit' => $limit], 900);
    }

    /**
     * Get users by status with caching
     */
    public function getUsersByStatus(string $status, int $limit = 50): Collection
    {
        return $this->remember('getUsersByStatus', function () use ($status, $limit) {
            return $this->model->where('account_status', $status)
                ->with(['profile:id,user_id,bio,avatar', 'company:id,name'])
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get([
                    'id', 'first_name', 'last_name', 'full_name', 'email', 'phone',
                    'user_type', 'account_status', 'last_login_at', 'created_at'
                ]);
        }, ['status' => $status, 'limit' => $limit], 900);
    }

    /**
     * Get recent users with caching
     */
    public function getRecentUsers(int $limit = 10): Collection
    {
        return $this->remember('getRecentUsers', function () use ($limit) {
            return $this->model->with(['profile:id,user_id,bio,avatar', 'company:id,name'])
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get([
                    'id', 'first_name', 'last_name', 'full_name', 'email', 'phone',
                    'user_type', 'account_status', 'kyc_status', 'created_at'
                ]);
        }, ['limit' => $limit], 300);
    }

    /**
     * Get active users with caching
     */
    public function getActiveUsers(int $limit = 50): Collection
    {
        return $this->remember('getActiveUsers', function () use ($limit) {
            return $this->model->where('account_status', 'active')
                ->where('last_login_at', '>=', now()->subDays(30))
                ->with(['profile:id,user_id,bio,avatar', 'company:id,name'])
                ->orderBy('last_login_at', 'desc')
                ->take($limit)
                ->get([
                    'id', 'first_name', 'last_name', 'full_name', 'email', 'phone',
                    'user_type', 'account_status', 'last_login_at', 'created_at'
                ]);
        }, ['limit' => $limit], 900);
    }

    /**
     * Get user performance metrics
     */
    public function getUserPerformanceMetrics(): array
    {
        return $this->remember('getUserPerformanceMetrics', function () {
            $metrics = $this->model->select([
                'user_type', 'account_status', 'kyc_status', 'created_at', 'last_login_at'
            ])->where('created_at', '>=', now()->subDays(30))->get();

            return [
                'average_registration_time' => $this->calculateAverageRegistrationTime($metrics),
                'most_active_user_type' => $this->getMostActiveUserType($metrics),
                'user_type_distribution' => $this->getUserTypeDistribution($metrics),
                'kyc_status_distribution' => $this->getKycStatusDistribution($metrics),
                'account_status_flow' => $this->getAccountStatusFlow($metrics),
                'user_engagement_metrics' => $this->getUserEngagementMetrics(),
            ];
        }, [], 3600);
    }

    /**
     * Apply user filters efficiently
     */
    private function applyUserFilters($query, array $filters): void
    {
        // User type filter
        if (!empty($filters['user_type'])) {
            if (is_array($filters['user_type'])) {
                $query->whereIn('user_type', $filters['user_type']);
            } else {
                $query->where('user_type', $filters['user_type']);
            }
        }

        // Account status filter
        if (!empty($filters['account_status'])) {
            if (is_array($filters['account_status'])) {
                $query->whereIn('account_status', $filters['account_status']);
            } else {
                $query->where('account_status', $filters['account_status']);
            }
        }

        // KYC status filter
        if (!empty($filters['kyc_status'])) {
            if (is_array($filters['kyc_status'])) {
                $query->whereIn('kyc_status', $filters['kyc_status']);
            } else {
                $query->where('kyc_status', $filters['kyc_status']);
            }
        }

        // Agent filter
        if (isset($filters['is_agent'])) {
            $query->where('is_agent', $filters['is_agent']);
        }

        // Company filter
        if (isset($filters['is_company'])) {
            $query->where('is_company', $filters['is_company']);
        }

        // Developer filter
        if (isset($filters['is_developer'])) {
            $query->where('is_developer', $filters['is_developer']);
        }

        // Investor filter
        if (isset($filters['is_investor'])) {
            $query->where('is_investor', $filters['is_investor']);
        }

        // Two-factor filter
        if (isset($filters['two_factor_enabled'])) {
            $query->where('two_factor_enabled', $filters['two_factor_enabled']);
        }

        // Created date range filter
        if (!empty($filters['created_at'])) {
            $this->applyDateRangeFilter($query, $filters['created_at'], 'created_at');
        }

        // Last login date range filter
        if (!empty($filters['last_login_at'])) {
            $this->applyDateRangeFilter($query, $filters['last_login_at'], 'last_login_at');
        }

        // Company filter
        if (!empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }
    }

    /**
     * Apply user search
     */
    private function applyUserSearch($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('first_name', 'LIKE', "%{$search}%")
              ->orWhere('last_name', 'LIKE', "%{$search}%")
              ->orWhere('full_name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('phone', 'LIKE', "%{$search}%")
              ->orWhere('agent_company', 'LIKE', "%{$search}%")
              ->orWhere('agent_license_number', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Apply user sorting
     */
    private function applyUserSorting($query, array $filters): void
    {
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        $allowedSorts = [
            'created_at', 'updated_at', 'first_name', 'last_name', 'full_name',
            'email', 'user_type', 'account_status', 'last_login_at', 'login_count'
        ];

        if (in_array($sortBy, $allowedSorts) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }
    }

    /**
     * Apply date range filter
     */
    private function applyDateRangeFilter($query, array $dateRange, string $field): void
    {
        if (isset($dateRange['start'])) {
            $query->whereDate($field, '>=', $dateRange['start']);
        }
        
        if (isset($dateRange['end'])) {
            $query->whereDate($field, '<=', $dateRange['end']);
        }
    }

    /**
     * Calculate growth rate
     */
    private function calculateGrowthRate(): float
    {
        $thisMonth = $this->model->whereMonth('created_at', now()->month)->count();
        $lastMonth = $this->model->whereMonth('created_at', now()->subMonth())->count();

        return $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;
    }

    /**
     * Calculate activation rate
     */
    private function calculateActivationRate(): float
    {
        $totalUsers = $this->model->count();
        $activeUsers = $this->model->where('account_status', 'active')->count();
        
        return $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0;
    }

    /**
     * Calculate KYC completion rate
     */
    private function calculateKycCompletionRate(): float
    {
        $totalUsers = $this->model->count();
        $kycVerifiedUsers = $this->model->where('kyc_status', 'verified')->count();
        
        return $totalUsers > 0 ? ($kycVerifiedUsers / $totalUsers) * 100 : 0;
    }

    /**
     * Calculate two-factor adoption rate
     */
    private function calculateTwoFactorAdoptionRate(): float
    {
        $totalUsers = $this->model->count();
        $twoFactorUsers = $this->model->where('two_factor_enabled', true)->count();
        
        return $totalUsers > 0 ? ($twoFactorUsers / $totalUsers) * 100 : 0;
    }

    /**
     * Calculate average registration time
     */
    private function calculateAverageRegistrationTime(Collection $users): float
    {
        // This would need additional data like email verification time
        // For now, return a placeholder
        return 0.0;
    }

    /**
     * Get most active user type
     */
    private function getMostActiveUserType(Collection $users): string
    {
        return $users->groupBy('user_type')
            ->map->count()
            ->sortDesc()
            ->keys()
            ->first() ?? 'unknown';
    }

    /**
     * Get user type distribution
     */
    private function getUserTypeDistribution(Collection $users): array
    {
        return $users->groupBy('user_type')
            ->map->count()
            ->toArray();
    }

    /**
     * Get KYC status distribution
     */
    private function getKycStatusDistribution(Collection $users): array
    {
        return $users->groupBy('kyc_status')
            ->map->count()
            ->toArray();
    }

    /**
     * Get account status flow
     */
    private function getAccountStatusFlow(Collection $users): array
    {
        return $users->groupBy('account_status')
            ->map(function ($statusUsers) {
                return [
                    'count' => $statusUsers->count(),
                    'kyc_completion_rate' => $statusUsers->where('kyc_status', 'verified')->count() / $statusUsers->count() * 100,
                    'two_factor_adoption_rate' => $statusUsers->where('two_factor_enabled', true)->count() / $statusUsers->count() * 100
                ];
            })
            ->toArray();
    }

    /**
     * Get user engagement metrics
     */
    private function getUserEngagementMetrics(): array
    {
        return [
            'average_login_frequency' => $this->model->avg('login_count') ?? 0,
            'users_with_properties' => $this->model->where('properties_count', '>', 0)->count(),
            'users_with_leads' => $this->model->where('leads_count', '>', 0)->count(),
            'users_with_transactions' => $this->model->where('transactions_count', '>', 0)->count(),
            'users_with_reviews' => $this->model->where('reviews_count', '>', 0)->count(),
        ];
    }
}
