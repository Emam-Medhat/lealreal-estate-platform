<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckCompanyPermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $company = $request->route('company');
        if (!($company instanceof \App\Models\Company)) {
            $company = $request->route('company_id') ? \App\Models\Company::find($request->route('company_id')) : $user->company;
        }

        if (!$company) {
            abort(404, 'Company not found.');
        }

        // Admin can bypass
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Get user's role in company
        $membership = $user->companyMembers()
            ->where('company_id', $company->id)
            ->first();

        if (!$membership || $membership->status !== 'active') {
            abort(403, 'Unauthorized.');
        }

        if ($membership->role === 'owner')
            return $next($request);

        // Map short permission to actual check logic if needed
        // For now, check against role permissions defined in getRolePermissions
        $rolePermissions = $this->getRolePermissions($membership->role);

        if (!in_array($permission, $rolePermissions) && !in_array('all', $rolePermissions)) {
            abort(403, 'Insufficient permissions.');
        }

        return $next($request);
    }

    /**
     * Get required permissions for the current route
     */
    private function getRequiredPermissions($request): array
    {
        $routeName = $request->route()->getName();

        $permissions = [
            'companies.properties.create' => ['manage_properties'],
            'companies.properties.edit' => ['manage_properties'],
            'companies.properties.delete' => ['manage_properties'],
            'companies.members.add' => ['manage_members'],
            'companies.members.remove' => ['manage_members'],
            'companies.branches.create' => ['manage_branches'],
            'companies.branches.edit' => ['manage_branches'],
            'companies.branches.delete' => ['manage_branches'],
            'companies.analytics.view' => ['view_analytics'],
            'companies.settings.manage' => ['manage_settings'],
            'companies.reports.view' => ['view_reports']
        ];

        return $permissions[$routeName] ?? [];
    }

    /**
     * Check if user has specific permission
     */
    private function hasPermission($membership, $permission): bool
    {
        $rolePermissions = $this->getRolePermissions($membership->role);

        return in_array($permission, $rolePermissions);
    }

    /**
     * Get permissions for role
     */
    private function getRolePermissions($role): array
    {
        $permissions = [
            'member' => ['view_properties', 'view_analytics'],
            'agent' => ['manage_properties', 'view_analytics', 'view_reports'],
            'manager' => ['manage_properties', 'manage_members', 'manage_branches', 'view_analytics', 'view_reports', 'manage_settings'],
            'owner' => ['all']
        ];

        return $permissions[$role] ?? [];
    }

    /**
     * Get permission name in Arabic
     */
    private function getPermissionName($permission): string
    {
        $names = [
            'manage_properties' => 'إدارة العقارات',
            'manage_members' => 'إدارة الأعضاء',
            'manage_branches' => 'إدارة الفروع',
            'view_analytics' => 'عرض التحليلات',
            'view_reports' => 'عرض التقارير',
            'manage_settings' => 'إدارة الإعدادات'
        ];

        return $names[$permission] ?? $permission;
    }
}
