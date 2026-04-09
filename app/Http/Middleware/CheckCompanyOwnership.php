<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckCompanyOwnership
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user)
            return redirect()->route('login');

        // Admin can bypass
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        $company = $request->route('company');
        if (!($company instanceof \App\Models\Company)) {
            $id = $request->route('company') ?? $request->route('company_id') ?? $request->input('company_id');
            if ($id)
                $company = \App\Models\Company::find($id);
        }

        if (!$company) {
            return $next($request);
        }

        // Check if user owns the company
        if ($company->created_by !== $user->id) {
            // Check member role owner
            $isOwner = $user->companyMembers()
                ->where('company_id', $company->id)
                ->where('role', 'owner')
                ->exists();

            if (!$isOwner) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Unauthorized owner access'], 403);
                }
                abort(403, 'Unauthorized. Owner access required.');
            }
        }

        return $next($request);
    }
}
