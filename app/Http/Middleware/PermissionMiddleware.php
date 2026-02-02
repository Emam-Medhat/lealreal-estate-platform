<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();
        
        // For now, allow all admin and super_admin users to access everything
        // This can be enhanced later with a proper permission system
        if (in_array($user->user_type, ['admin', 'super_admin'])) {
            return $next($request);
        }

        // If you want to implement specific permissions, you can add logic here
        // For now, deny access to non-admin users for permission-protected routes
        return redirect()->route('dashboard')->with('error', 'Access denied. Insufficient permissions.');
    }
}
