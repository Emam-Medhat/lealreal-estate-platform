<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckBannedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->account_status === 'banned') {
                Auth::logout();
                return redirect()->route('login')->withErrors(['email' => 'Your account has been banned.']);
            }

            if ($user->account_status === 'suspended') {
                // Check suspension expiry if needed
                if ($user->suspended_until && now()->lessThan($user->suspended_until)) {
                    Auth::logout();
                    return redirect()->route('login')->withErrors(['email' => 'Your account is suspended until ' . $user->suspended_until]);
                }
            }
        }

        return $next($request);
    }
}
