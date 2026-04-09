<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckCompanyMembership
{
    public function handle(Request $request, Closure $next, string $role = null)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        $company = $request->route('company');
        if (!$company) {
            return $next($request);
        }

        $companyId = $company instanceof \App\Models\Company ? $company->id : $company;

        $membership = $user->companyMembers()->where('company_id', $companyId)->first();
        if (!$membership || $membership->status !== 'active') {
            abort(403);
        }

        if ($role && $membership->role !== $role && $membership->role !== 'owner') {
            abort(403);
        }

        return $next($request);
    }
}
