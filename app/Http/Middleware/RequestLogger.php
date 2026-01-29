<?php

namespace App\Http\Middleware;

use App\Models\Request as RequestModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequestLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        // Generate unique request ID
        $requestId = 'req_' . uniqid() . '_' . time();
        
        // Create request record
        $requestRecord = RequestModel::create([
            'request_id' => $requestId,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'headers' => $this->getImportantHeaders($request),
            'payload' => $this->getPayload($request),
            'status' => RequestModel::STATUS_PROCESSING,
            'started_at' => now(),
            'user_id' => Auth::id(),
        ]);
        
        // Store request ID in request for later use
        $request->request_id = $requestId;
        
        $response = $next($request);
        
        // Calculate response time
        $responseTime = (microtime(true) - $startTime) * 1000; // in milliseconds
        
        // Update request record
        $requestRecord->update([
            'status' => $response->getStatusCode() < 400 ? RequestModel::STATUS_COMPLETED : RequestModel::STATUS_FAILED,
            'response_code' => $response->getStatusCode(),
            'response_time' => $responseTime,
            'completed_at' => now(),
            'error_message' => $response->getStatusCode() >= 400 ? $response->statusText() : null,
        ]);
        
        return $response;
    }
    
    /**
     * Get important headers from request
     */
    private function getImportantHeaders(Request $request): array
    {
        $importantHeaders = [
            'content-type',
            'accept',
            'authorization',
            'x-requested-with',
            'referer',
            'accept-language',
        ];
        
        $headers = [];
        foreach ($importantHeaders as $header) {
            if ($request->hasHeader($header)) {
                $headers[$header] = $request->header($header);
            }
        }
        
        return $headers;
    }
    
    /**
     * Get request payload (excluding sensitive data)
     */
    private function getPayload(Request $request): ?array
    {
        if ($request->isMethod('GET') || $request->isMethod('HEAD')) {
            return null;
        }
        
        $payload = $request->all();
        
        // Remove sensitive data
        $sensitiveKeys = ['password', 'password_confirmation', 'token', 'secret', 'key'];
        
        foreach ($sensitiveKeys as $key) {
            if (isset($payload[$key])) {
                $payload[$key] = '***HIDDEN***';
            }
        }
        
        return $payload;
    }
}
