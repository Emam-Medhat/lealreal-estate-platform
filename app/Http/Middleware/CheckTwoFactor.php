<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckTwoFactor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // 2FA Routes to exclude from the check to prevent redirection loops
        $excludedRoutes = [
            'two-factor.show',
            'two-factor.verify',
            'logout',
        ];

        if ($user && $user->two_factor_enabled && !in_array($request->route()->getName(), $excludedRoutes)) {
            // Check if 2FA session key is set
            if (!$request->session()->has('auth.2fa_verified')) {
                return redirect()->route('two-factor.show');
            }
        }

        return $next($request);
    }
}
