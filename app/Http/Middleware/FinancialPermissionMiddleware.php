<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\FinancialPermissionService;
use Illuminate\Support\Facades\Auth;

class FinancialPermissionMiddleware
{
    protected FinancialPermissionService $permissionService;

    public function __construct(FinancialPermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Check if user has the required financial permission
        if (!$this->permissionService->hasFinancialPermission($permission)) {
            $this->permissionService->logPermissionCheck($permission, false, [
                'route' => $request->route()->getName(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
            ]);

            return response()->json(['error' => 'Insufficient permissions'], 403);
        }

        // Log successful permission check
        $this->permissionService->logPermissionCheck($permission, true, [
            'route' => $request->route()->getName(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
        ]);

        return $next($request);
    }
}
