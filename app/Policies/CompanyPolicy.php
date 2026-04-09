<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CompanyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Company $company): bool
    {
        // Admin can view any company
        if ($user->hasRole('admin')) {
            return true;
        }

        // User can view their own company
        if ($company->owner_id === $user->id) {
            return true;
        }

        // Company members can view company
        if ($user->companyMembers()->where('company_id', $company->id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Company $company): bool
    {
        // Admin can update any company
        if ($user->hasRole('admin')) {
            return true;
        }

        // Owner can update their company
        if ($company->owner_id === $user->id) {
            return true;
        }

        // Managers can update company
        $membership = $user->companyMembers()
            ->where('company_id', $company->id)
            ->where('role', 'manager')
            ->first();

        if ($membership) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Company $company): bool
    {
        // Admin can delete any company
        if ($user->hasRole('admin')) {
            return true;
        }

        // Owner can delete their company
        if ($company->owner_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can add members.
     */
    public function addMember(User $user, Company $company): bool
    {
        // Admin can add members to any company
        if ($user->hasRole('admin')) {
            return true;
        }

        // Owner can add members to their company
        if ($company->owner_id === $user->id) {
            return true;
        }

        // Managers can add members
        $membership = $user->companyMembers()
            ->where('company_id', $company->id)
            ->where('role', 'manager')
            ->first();

        if ($membership) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can remove members.
     */
    public function removeMember(User $user, Company $company): bool
    {
        // Admin can remove members from any company
        if ($user->hasRole('admin')) {
            return true;
        }

        // Owner can remove members from their company
        if ($company->owner_id === $user->id) {
            return true;
        }

        // Managers can remove members
        $membership = $user->companyMembers()
            ->where('company_id', $company->id)
            ->where('role', 'manager')
            ->first();

        if ($membership) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can manage branches.
     */
    public function manageBranches(User $user, Company $company): bool
    {
        // Admin can manage branches for any company
        if ($user->hasRole('admin')) {
            return true;
        }

        // Owner can manage branches for their company
        if ($company->owner_id === $user->id) {
            return true;
        }

        // Managers can manage branches
        $membership = $user->companyMembers()
            ->where('company_id', $company->id)
            ->where('role', 'manager')
            ->first();

        if ($membership) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view analytics.
     */
    public function viewAnalytics(User $user, Company $company): bool
    {
        // Admin can view analytics for any company
        if ($user->hasRole('admin')) {
            return true;
        }

        // Owner can view analytics for their company
        if ($company->owner_id === $user->id) {
            return true;
        }

        // Managers can view analytics
        $membership = $user->companyMembers()
            ->where('company_id', $company->id)
            ->where('role', 'manager')
            ->first();

        if ($membership) {
            return true;
        }

        // Agents can view analytics
        $membership = $user->companyMembers()
            ->where('company_id', $company->id)
            ->where('role', 'agent')
            ->first();

        if ($membership) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can manage settings.
     */
    public function manageSettings(User $user, Company $company): bool
    {
        // Admin can manage settings for any company
        if ($user->hasRole('admin')) {
            return true;
        }

        // Owner can manage settings for their company
        if ($company->owner_id === $user->id) {
            return true;
        }

        // Managers can manage settings
        $membership = $user->companyMembers()
            ->where('company_id', $company->id)
            ->where('role', 'manager')
            ->first();

        if ($membership) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view reports.
     */
    public function viewReports(User $user, Company $company): bool
    {
        // Admin can view reports for any company
        if ($user->hasRole('admin')) {
            return true;
        }

        // Owner can view reports for their company
        if ($company->owner_id === $user->id) {
            return true;
        }

        // Managers can view reports
        $membership = $user->companyMembers()
            ->where('company_id', $company->id)
            ->where('role', 'manager')
            ->first();

        if ($membership) {
            return true;
        }

        return false;
    }
}
