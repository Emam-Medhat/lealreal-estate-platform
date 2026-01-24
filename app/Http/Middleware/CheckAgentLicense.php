<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Route;

class CheckAgentLicense
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Admin can bypass license check
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Check if user has valid license
        if (!$user->agent_license || $user->agent_license_expired) {
            if ($request->expectsJson()) {
                $redirectUrl = Route::has('agent.license.apply') ? route('agent.license.apply') : route('home');
                return response()->json([
                    'message' => 'يجب الحصول على رخصة وكلاء صالحة',
                    'redirect' => $redirectUrl
                ], 403);
            }

            $route = Route::has('agent.license.apply') ? 'agent.license.apply' : 'home';
            return redirect()->route($route)
                ->with('error', 'يجب الحصول على رخصة وكلاء صالحة');
        }

        // Check if license is expired
        if ($user->agent_license_expired && now()->greaterThan($user->agent_license_expires_at)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'انتهت صلاحية رخصتك. يرجى تجديدها',
                    'redirect' => route('agent.license.renew')
                ], 403);
            }

            return redirect()->route('agent.license.renew')
                ->with('error', 'انتهت صلاحية رخصتك. يرجى تجديدها');
        }

        return $next($request);
    }
}
