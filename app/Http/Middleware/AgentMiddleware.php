<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && (Auth::user()->user_type === 'agent' || Auth::user()->agent()->exists())) {
            return $next($request);
        }

        if (Auth::check()) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Agent role required.');
        }

        return redirect()->route('login')->with('error', 'Please login to access this page.');
    }
}
