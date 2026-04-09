<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Events\SuspiciousLoginDetected; // Assumes event exists or will exist

class CheckDeviceFingerprint
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            $currentDevice = $request->userAgent();

            // Simple check: if last_login_device is stored and different
            if ($user->last_login_device && $user->last_login_device !== $currentDevice) {
                // Logic to handle new device:
                // 1. Notify user (NewDeviceLoginNotification)
                // 2. Or require 2FA if not already verified for this session

                // For this implementation, we just update it if it's considered 'safe' or log it
                // Ideally, we would have a 'user_devices' table to track all known devices.

                // If the user has a separate table for devices, we check that.
                // Assuming we use the 'user_devices' table from the file list

                $known = \Illuminate\Support\Facades\DB::table('user_devices')
                    ->where('user_id', $user->id)
                    ->where('user_agent', $currentDevice)
                    ->exists();

                if (!$known) {
                    // This is a new device
                    // Dispatch event or creating notification
                    // event(new \App\Events\NewDeviceDetected($user, $currentDevice, $request->ip()));
                }
            }
        }

        return $next($request);
    }
}
