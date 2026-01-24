<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class TrackDeviceFingerprint
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (! $user) {
            return $next($request);
        }

        // Generate device fingerprint
        $fingerprint = $this->generateFingerprint($request);
        
        // Track or update device
        UserDevice::updateOrCreate(
            [
                'user_id' => $user->id,
                'fingerprint' => $fingerprint,
            ],
            [
                'device_name' => $this->getDeviceName($request),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'last_seen_at' => now(),
            ]
        );

        return $next($request);
    }

    /**
     * Generate device fingerprint
     */
    private function generateFingerprint(Request $request): string
    {
        $data = [
            $request->ip(),
            $request->userAgent(),
            $request->header('Accept-Language'),
            $request->header('Accept-Encoding'),
        ];

        return Hash::make(implode('|', $data));
    }

    /**
     * Get device name from user agent
     */
    private function getDeviceName(Request $request): string
    {
        $userAgent = $request->userAgent();
        
        // Detect browser
        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome Browser';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox Browser';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            return 'Safari Browser';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            return 'Edge Browser';
        } else {
            return 'Unknown Browser';
        }
    }
}
