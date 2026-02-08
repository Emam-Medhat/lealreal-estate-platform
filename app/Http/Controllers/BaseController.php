<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as RoutingController;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BaseController extends RoutingController
{
    use AuthorizesRequests, ValidatesRequests;

    
    /**
     * Get cached data with automatic key generation
     *
     * @param string $key
     * @param callable $callback
     * @param string $duration
     * @param array $tags
     * @return mixed
     */
    protected function getCachedData(string $key, callable $callback, string $duration = 'medium', array $tags = [])
    {
        return CacheService::remember($key, $callback, $duration, $tags);
    }

    /**
     * Clear cache with automatic tag generation
     *
     * @param string|null $key
     * @param array $tags
     * @return void
     */
    protected function clearCache(?string $key = null, array $tags = []): void
    {
        if ($key) {
            $this->cacheService->forget($key);
        }

        if (!empty($tags)) {
            $this->cacheService->clearTags($tags);
        }
    }

    /**
     * Return JSON response with standard format
     *
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @param array $meta
     * @return JsonResponse
     */
    protected function jsonResponse($data = null, string $message = 'Success', int $status = 200, array $meta = []): JsonResponse
    {
        $response = [
            'success' => $status >= 200 && $status < 300,
            'message' => $message,
            'data' => $data,
            'meta' => array_merge([
                'timestamp' => now()->toISOString(),
                'version' => config('app.version', '1.0.0'),
                'execution_time' => round(microtime(true) - LARAVEL_START, 3),
            ], $meta),
        ];

        return response()->json($response, $status);
    }

    /**
     * Return paginated response
     *
     * @param $paginator
     * @param string $message
     * @param array $meta
     * @return JsonResponse
     */
    protected function paginatedResponse($paginator, string $message = 'Success', array $meta = []): JsonResponse
    {
        $data = $paginator->items();
        $paginationMeta = [
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'has_more' => $paginator->hasMorePages(),
                'total_pages' => $paginator->lastPage(),
            ],
        ];

        return $this->jsonResponse($data, $message, 200, array_merge($meta, $paginationMeta));
    }

    /**
     * Return error response
     *
     * @param string $message
     * @param int $status
     * @param array $errors
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $status = 400, array $errors = []): JsonResponse
    {
        return $this->jsonResponse(null, $message, $status, ['errors' => $errors]);
    }

    /**
     * Validate request with automatic error handling
     *
     * @param Request $request
     * @param array $rules
     * @param array $messages
     * @return array
     */
    protected function validateRequest(Request $request, array $rules, array $messages = []): array
    {
        $validator = validator($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Apply rate limiting
     *
     * @param Request $request
     * @param int $maxAttempts
     * @param int $decayMinutes
     * @return void
     */
    protected function rateLimit(Request $request, int $maxAttempts = 60, int $decayMinutes = 1): void
    {
        $key = 'rate_limit:' . $request->ip() . ':' . $request->route()->getName();

        if (cache()->get($key) >= $maxAttempts) {
            abort(429, 'Too many requests. Please try again later.');
        }

        cache()->put($key, cache()->get($key, 0) + 1, $decayMinutes * 60);
    }

    /**
     * Log performance metrics
     *
     * @param Request $request
     * @param mixed $response
     * @param float $startTime
     * @return void
     */
    protected function logPerformance(Request $request, $response, float $startTime): void
    {
        $executionTime = microtime(true) - $startTime;
        $memoryUsage = memory_get_usage(true) / 1024 / 1024; // MB

        $logData = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'status' => $response->getStatusCode(),
            'execution_time' => round($executionTime * 1000, 2), // ms
            'memory_usage' => round($memoryUsage, 2), // MB
            'user_id' => auth()->id(),
            'query_count' => \DB::getQueryCount(),
        ];

        // Log slow requests
        if ($executionTime > 1.0) {
            \Log::warning('Slow request detected', $logData);
        }

        // Log high memory usage
        if ($memoryUsage > 50) {
            \Log::warning('High memory usage detected', $logData);
        }

        // Log in development
        if (app()->environment('local')) {
            \Log::debug('Request performance', $logData);
        }
    }

    /**
     * Get user-specific cache key
     *
     * @param string $key
     * @return string
     */
    protected function getUserCacheKey(string $key): string
    {
        $userId = auth()->id() ?? 'guest';
        return "user_{$userId}:{$key}";
    }

    /**
     * Get role-specific cache key
     *
     * @param string $key
     * @return string
     */
    protected function getRoleCacheKey(string $key): string
    {
        $role = auth()->user()?->role ?? 'guest';
        return "role_{$role}:{$key}";
    }

    /**
     * Apply eager loading optimization
     *
     * @param $query
     * @param array $relations
     * @param array $select
     * @return mixed
     */
    protected function applyEagerLoading($query, array $relations, array $select = ['*'])
    {
        return $query->with($relations)->select($select);
    }

    /**
     * Apply pagination with limits
     *
     * @param Request $request
     * @param int $default
     * @param int $max
     * @return int
     */
    protected function getPerPage(Request $request, int $default = 20, int $max = 100): int
    {
        $perPage = (int) $request->get('per_page', $default);
        return min(max($perPage, 1), $max);
    }

    /**
     * Apply search filters
     *
     * @param $query
     * @param Request $request
     * @param array $searchableFields
     * @return mixed
     */
    protected function applySearchFilters($query, Request $request, array $searchableFields)
    {
        $search = $request->get('search');

        if ($search && !empty($searchableFields)) {
            $query->where(function ($q) use ($search, $searchableFields) {
                foreach ($searchableFields as $field) {
                    $q->orWhere($field, 'LIKE', "%{$search}%");
                }
            });
        }

        return $query;
    }

    /**
     * Apply date range filters
     *
     * @param $query
     * @param Request $request
     * @param string $field
     * @return mixed
     */
    protected function applyDateRange($query, Request $request, string $field = 'created_at')
    {
        $dateRange = $request->get('date_range');

        if ($dateRange && is_array($dateRange)) {
            if (isset($dateRange['start'])) {
                $query->whereDate($field, '>=', $dateRange['start']);
            }

            if (isset($dateRange['end'])) {
                $query->whereDate($field, '<=', $dateRange['end']);
            }
        }

        return $query;
    }

    /**
     * Apply sorting
     *
     * @param $query
     * @param Request $request
     * @param array $sortableFields
     * @param string $defaultField
     * @param string $defaultDirection
     * @return mixed
     */
    protected function applySorting($query, Request $request, array $sortableFields, string $defaultField = 'created_at', string $defaultDirection = 'desc')
    {
        $sortBy = $request->get('sort_by', $defaultField);
        $sortDirection = $request->get('sort_direction', $defaultDirection);

        if (in_array($sortBy, $sortableFields) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy($defaultField, $defaultDirection);
        }

        return $query;
    }

    /**
     * Handle file upload with validation
     *
     * @param Request $request
     * @param string $field
     * @param array $rules
     * @return string|null
     */
    protected function handleFileUpload(Request $request, string $field, array $rules = ['image', 'max:2048']): ?string
    {
        if (!$request->hasFile($field)) {
            return null;
        }

        $request->validate([$field => $rules]);

        $file = $request->file($field);
        $path = $file->store('uploads', 'public');

        return $path;
    }

    /**
     * Get authenticated user with caching
     *
     * @return \App\Models\User|null
     */
    protected function getAuthenticatedUser()
    {
        if (!auth()->check()) {
            return null;
        }

        return $this->getCachedData(
            $this->getUserCacheKey('profile'),
            function () {
                return auth()->user()->load(['profile', 'permissions']);
            },
            'medium'
        );
    }

    /**
     * Check if user has permission
     *
     * @param string $permission
     * @return bool
     */
    protected function hasPermission(string $permission): bool
    {
        $user = $this->getAuthenticatedUser();

        if (!$user) {
            return false;
        }

        return $user->hasPermission($permission);
    }

    /**
     * Abort with permission error
     *
     * @param string $permission
     * @return void
     */
    protected function authorizePermission(string $permission): void
    {
        if (!$this->hasPermission($permission)) {
            abort(403, 'You do not have permission to perform this action.');
        }
    }

    /**
     * Get system statistics with caching
     *
     * @return array
     */
    protected function getSystemStats(): array
    {
        return $this->getCachedData(
            'system_stats',
            function () {
                return [
                    'total_users' => \App\Models\User::count(),
                    'active_users' => \App\Models\User::where('account_status', 'active')->count(),
                    'total_properties' => \App\Models\Property::count(),
                    'total_leads' => \App\Models\Lead::count(),
                    'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2),
                    'disk_usage' => $this->getDiskUsage(),
                ];
            },
            'long'
        );
    }

    /**
     * Get disk usage
     *
     * @return array
     */
    private function getDiskUsage(): array
    {
        try {
            $total = disk_total_space('/');
            $free = disk_free_space('/');
            $used = $total - $free;

            return [
                'total' => round($total / 1024 / 1024 / 1024, 2), // GB
                'used' => round($used / 1024 / 1024 / 1024, 2), // GB
                'free' => round($free / 1024 / 1024 / 1024, 2), // GB
                'percentage' => round(($used / $total) * 100, 2),
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
