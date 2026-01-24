<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Route;

class CheckAgentVerification
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Admin can bypass verification check
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Check if user is verified
        if (!$user->agent_verified) {
            if ($request->expectsJson()) {
                $redirectUrl = Route::has('agent.verification.submit') ? route('agent.verification.submit') : route('home');
                return response()->json([
                    'message' => 'يجب التحقق من هويتك كوكلاء',
                    'redirect' => $redirectUrl
                ], 403);
            }

            $route = Route::has('agent.verification.submit') ? 'agent.verification.submit' : 'home';
            return redirect()->route($route)
                ->with('error', 'يجب التحقق من هويتك كوكلاء');
        }

        return $next($request);
    }
}
