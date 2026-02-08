<?php

namespace App\Services;

use App\Models\User;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class FinancialPermissionService
{
    /**
     * Check if user has financial permission
     */
    public function hasFinancialPermission(string $permission, ?int $userId = null): bool
    {
        $user = $userId ? User::find($userId) : Auth::user();
        
        if (!$user) {
            return false;
        }

        // Super admin has all permissions
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        // Check user-specific permissions
        if ($this->hasUserPermission($user, $permission)) {
            return true;
        }

        // Check role-based permissions
        if ($this->hasRolePermission($user, $permission)) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can perform financial action on resource
     */
    public function canPerformFinancialAction(string $action, string $resource, ?int $resourceId = null, ?int $userId = null): bool
    {
        $user = $userId ? User::find($userId) : Auth::user();
        
        if (!$user) {
            return false;
        }

        $permission = $this->getFinancialPermissionForAction($action, $resource);
        
        return $this->hasFinancialPermission($permission, $user->id);
    }

    /**
     * Get user's financial permissions
     */
    public function getUserFinancialPermissions(?int $userId = null): array
    {
        $user = $userId ? User::find($userId) : Auth::user();
        
        if (!$user) {
            return [];
        }

        $permissions = Cache::remember("user_financial_permissions_{$user->id}", 3600, function () use ($user) {
            $userPermissions = $this->getUserPermissions($user);
            $rolePermissions = $this->getRolePermissions($user);
            
            return array_unique(array_merge($userPermissions, $rolePermissions));
        });

        return $permissions;
    }

    /**
     * Get financial permissions hierarchy
     */
    public function getFinancialPermissionsHierarchy(): array
    {
        return [
            'financial' => [
                'financial.view' => 'View financial data',
                'financial.create' => 'Create financial records',
                'financial.update' => 'Update financial records',
                'financial.delete' => 'Delete financial records',
                'financial.export' => 'Export financial data',
                'financial.import' => 'Import financial data',
                'financial.approve' => 'Approve financial transactions',
                'financial.reject' => 'Reject financial transactions',
                'financial.refund' => 'Process refunds',
                'financial.audit' => 'Access financial audit logs',
                'financial.reports' => 'Access financial reports',
                'financial.analytics' => 'Access financial analytics',
                'financial.settings' => 'Manage financial settings',
            ],
            'invoices' => [
                'invoices.view' => 'View invoices',
                'invoices.create' => 'Create invoices',
                'invoices.update' => 'Update invoices',
                'invoices.delete' => 'Delete invoices',
                'invoices.approve' => 'Approve invoices',
                'invoices.reject' => 'Reject invoices',
                'invoices.cancel' => 'Cancel invoices',
                'invoices.export' => 'Export invoices',
                'invoices.send' => 'Send invoices',
                'invoices.print' => 'Print invoices',
                'invoices.download' => 'Download invoices',
                'invoices.view.all' => 'View all invoices',
                'invoices.view.own' => 'View own invoices',
                'invoices.view.team' => 'View team invoices',
                'invoices.view.department' => 'View department invoices',
            ],
            'payments' => [
                'payments.view' => 'View payments',
                'payments.create' => 'Create payments',
                'payments.update' => 'Update payments',
                'payments.delete' => 'Delete payments',
                'payments.process' => 'Process payments',
                'payments.refund' => 'Process refunds',
                'payments.verify' => 'Verify payments',
                'payments.export' => 'Export payments',
                'payments.view.all' => 'View all payments',
                'payments.view.own' => 'View own payments',
                'payments.view.team' => 'View team payments',
                'payments.view.department' => 'View department payments',
            ],
            'expenses' => [
                'expenses.view' => 'View expenses',
                'expenses.create' => 'Create expenses',
                'expenses.update' => 'Update expenses',
                'expenses.delete' => 'Delete expenses',
                'expenses.approve' => 'Approve expenses',
                'expenses.reject' => 'Reject expenses',
                'expenses.export' => 'Export expenses',
                'expenses.view.all' => 'View all expenses',
                'expenses.view.own' => 'View own expenses',
                'expenses.view.team' => 'View team expenses',
                'expenses.view.department' => 'View department expenses',
            ],
            'reports' => [
                'reports.financial' => 'Access financial reports',
                'reports.revenue' => 'Access revenue reports',
                'reports.expenses' => 'Access expense reports',
                'reports.profit_loss' => 'Access profit & loss reports',
                'reports.cash_flow' => 'Access cash flow reports',
                'reports.audit' => 'Access audit reports',
                'reports.compliance' => 'Access compliance reports',
                'reports.export' => 'Export reports',
                'reports.schedule' => 'Schedule reports',
                'reports.view.all' => 'View all reports',
                'reports.view.own' => 'View own reports',
                'reports.view.team' => 'View team reports',
                'reports.view.department' => 'View department reports',
            ],
            'settings' => [
                'settings.financial' => 'Manage financial settings',
                'settings.payment_gateways' => 'Manage payment gateways',
                'settings.tax_rates' => 'Manage tax rates',
                'settings.currencies' => 'Manage currencies',
                'settings.invoicing' => 'Manage invoicing settings',
                'settings.expense_categories' => 'Manage expense categories',
                'settings.approval_workflows' => 'Manage approval workflows',
                'settings.audit_trail' => 'Manage audit trail settings',
            ],
        ];
    }

    /**
     * Check if user can access resource based on ownership
     */
    public function canAccessResource(string $resource, int $resourceId, string $action, ?int $userId = null): bool
    {
        $user = $userId ? User::find($userId) : Auth::user();
        
        if (!$user) {
            return false;
        }

        // Super admin can access everything
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        // Check ownership-based permissions
        $permission = $this->getOwnershipPermission($resource, $action);
        
        if (!$this->hasFinancialPermission($permission, $user->id)) {
            return false;
        }

        // Check if user owns the resource or has access through hierarchy
        return $this->ownsResourceOrHasAccess($resource, $resourceId, $user);
    }

    /**
     * Get financial approval limits for user
     */
    public function getFinancialApprovalLimits(?int $userId = null): array
    {
        $user = $userId ? User::find($userId) : Auth::user();
        
        if (!$user) {
            return [];
        }

        return Cache::remember("user_approval_limits_{$user->id}", 3600, function () use ($user) {
            $roleLimits = $this->getRoleApprovalLimits($user);
            $userLimits = $this->getUserApprovalLimits($user);
            
            return array_merge($roleLimits, $userLimits);
        });
    }

    /**
     * Check if user can approve amount
     */
    public function canApproveAmount(float $amount, string $currency = 'USD', ?int $userId = null): bool
    {
        $limits = $this->getFinancialApprovalLimits($userId);
        
        $currencyLimit = $limits[$currency] ?? $limits['USD'] ?? 0;
        
        return $amount <= $currencyLimit;
    }

    /**
     * Log financial permission check
     */
    public function logPermissionCheck(string $permission, bool $granted, array $context = [], ?int $userId = null): void
    {
        $user = $userId ? User::find($userId) : Auth::user();
        
        if (!$user) {
            return;
        }

        $logData = [
            'user_id' => $user->id,
            'permission' => $permission,
            'granted' => $granted,
            'context' => $context,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ];

        // Log to database or file system
        \Log::info('Financial Permission Check', $logData);
    }

    // Private Methods
    private function isSuperAdmin(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermission('super_admin');
    }

    private function getUserPermissions(User $user): array
    {
        return $user->permissions()
                    ->where('type', 'financial')
                    ->pluck('name')
                    ->toArray();
    }

    private function getRolePermissions(User $user): array
    {
        $permissions = [];
        
        foreach ($user->roles as $role) {
            $rolePermissions = $role->permissions()
                                ->where('type', 'financial')
                                ->pluck('name')
                                ->toArray();
            $permissions = array_merge($permissions, $rolePermissions);
        }
        
        return array_unique($permissions);
    }

    private function hasUserPermission(User $user, string $permission): bool
    {
        return $user->permissions()->where('name', $permission)->exists();
    }

    private function hasRolePermission(User $user, string $permission): bool
    {
        foreach ($user->roles as $role) {
            if ($role->permissions()->where('name', $permission)->exists()) {
                return true;
            }
        }
        
        return false;
    }

    private function getFinancialPermissionForAction(string $action, string $resource): string
    {
        $actionMap = [
            'view' => "{$resource}.view",
            'create' => "{$resource}.create",
            'update' => "{$resource}.update",
            'delete' => "{$resource}.delete",
            'edit' => "{$resource}.update",
            'remove' => "{$resource}.delete",
            'approve' => "{$resource}.approve",
            'reject' => "{$resource}.reject",
            'cancel' => "{$resource}.cancel",
            'export' => "{$resource}.export",
            'import' => "{$resource}.import",
            'send' => "{$resource}.send",
            'print' => "{$resource}.print",
            'download' => "{$resource}.download",
            'process' => "{$resource}.process",
            'verify' => "{$resource}.verify",
            'refund' => "{$resource}.refund",
        ];

        return $actionMap[$action] ?? "{$resource}.view";
    }

    private function getOwnershipPermission(string $resource, string $action): string
    {
        return "{$resource}.{$action}";
    }

    private function ownsResourceOrHasAccess(string $resource, int $resourceId, User $user): bool
    {
        // This would depend on your specific resource models
        // For example, for invoices:
        if ($resource === 'invoices') {
            $invoice = \App\Models\Invoice::find($resourceId);
            return $invoice && ($invoice->user_id === $user->id || $this->hasAccessThroughHierarchy($invoice, $user));
        }
        
        // Add other resource checks as needed
        return false;
    }

    private function hasAccessThroughHierarchy($resource, User $user): bool
    {
        // Check if user has access through department/team hierarchy
        // This would depend on your organizational structure
        return false;
    }

    private function getRoleApprovalLimits(User $user): array
    {
        $limits = [];
        
        foreach ($user->roles as $role) {
            $roleLimits = $role->approval_limits ?? [];
            $limits = array_merge($limits, $roleLimits);
        }
        
        return $limits;
    }

    private function getUserApprovalLimits(User $user): array
    {
        return $user->approval_limits ?? [];
    }
}
