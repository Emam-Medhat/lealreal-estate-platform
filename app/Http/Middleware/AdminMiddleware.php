<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();
        
        // Check if user is admin or super_admin
        if (!in_array($user->user_type, ['admin', 'super_admin'])) {
            // Redirect regular users to their appropriate dashboard
            if ($user->user_type === 'agent') {
                return redirect()->route('agent.dashboard')->with('error', 'Access denied. Admin privileges required.');
            }
            
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
