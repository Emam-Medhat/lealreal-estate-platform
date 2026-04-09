<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if ($request->user() && $request->user()->hasRole('admin')) {
            return $next($request);
        }

        if (!$request->user() || !Gate::allows($permission)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
