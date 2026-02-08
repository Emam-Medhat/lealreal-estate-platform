<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Services\CacheService;

class ApiController extends Controller
{
    /**
     * Standard API response format
     *
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @param array $meta
     * @return JsonResponse
     */
    protected function apiResponse($data = null, string $message = 'Success', int $status = 200, array $meta = []): JsonResponse
    {
        $response = [
            'success' => $status >= 200 && $status < 300,
            'message' => $message,
            'data' => $data,
            'meta' => array_merge([
                'timestamp' => now()->toISOString(),
                'version' => config('app.version', '1.0.0'),
            ], $meta),
        ];

        return response()->json($response, $status);
    }

    /**
     * Paginated API response
     *
     * @param $paginator
     * @param string $message
     * @return JsonResponse
     */
    protected function paginatedResponse($paginator, string $message = 'Success'): JsonResponse
    {
        $data = $paginator->items();
        $meta = [
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'has_more' => $paginator->hasMorePages(),
            ],
        ];

        return $this->apiResponse($data, $message, 200, $meta);
    }

    /**
     * Error response
     *
     * @param string $message
     * @param int $status
     * @param array $errors
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $status = 400, array $errors = []): JsonResponse
    {
        return $this->apiResponse(null, $message, $status, ['errors' => $errors]);
    }

    /**
     * Validate API request
     *
     * @param Request $request
     * @param array $rules
     * @return array
     */
    protected function validateApiRequest(Request $request, array $rules): array
    {
        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Get cached API data
     *
     * @param string $key
     * @param callable $callback
     * @param string $duration
     * @return mixed
     */
    protected function getCachedData(string $key, callable $callback, string $duration = 'medium')
    {
        return CacheService::remember("api:{$key}", $callback, $duration, ['api']);
    }

    /**
     * Clear API cache
     *
     * @param string|null $key
     * @return void
     */
    protected function clearApiCache(?string $key = null): void
    {
        if ($key) {
            CacheService::forget("api:{$key}");
        } else {
            CacheService::clearTags(['api']);
        }
    }

    /**
     * Rate limiting for API
     *
     * @param Request $request
     * @param int $maxAttempts
     * @param int $decayMinutes
     * @return void
     */
    protected function rateLimit(Request $request, int $maxAttempts = 60, int $decayMinutes = 1): void
    {
        $key = 'api_rate_limit:' . $request->ip();
        
        if (Cache::get($key) >= $maxAttempts) {
            abort(429, 'Too many requests. Please try again later.');
        }

        Cache::put($key, Cache::get($key, 0) + 1, $decayMinutes * 60);
    }

    /**
     * Log API request for monitoring
     *
     * @param Request $request
     * @param mixed $response
     * @param float $executionTime
     * @return void
     */
    protected function logApiRequest(Request $request, $response, float $executionTime): void
    {
        $logData = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => $response->getStatusCode(),
            'execution_time' => $executionTime,
            'user_id' => auth()->id(),
        ];

        \Log::channel('api')->info('API Request', $logData);
    }
}
