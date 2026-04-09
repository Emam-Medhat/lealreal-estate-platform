<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
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
    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the profile.
     */
    public function viewProfile(User $user, User $targetUser): bool
    {
        // Users can view their own profile
        if ($user->id === $targetUser->id) {
            return true;
        }
        
        // Admin can view any profile
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Agents can view profiles of users in their agency
        if ($user->role === 'agent' && $user->agency_id === $targetUser->agency_id) {
            return true;
        }
        
        // Users can view public profiles
        if ($targetUser->profile_visibility === 'public') {
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
    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the profile.
     */
    public function updateProfile(User $user, User $targetUser): bool
    {
        // Users can update their own profile
        if ($user->id === $targetUser->id) {
            return true;
        }
        
        // Admin can update any profile
        if ($user->hasRole('admin')) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Users can delete their own account
        if ($user->id === $model->id) {
            return true;
        }
        
        // Admin can delete any account
        if ($user->hasRole('admin')) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage the wallet.
     */
    public function manageWallet(User $user, User $targetUser): bool
    {
        // Users can manage their own wallet
        if ($user->id === $targetUser->id) {
            return true;
        }
        
        // Admin can manage any wallet
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Finance managers can manage wallets
        if ($user->role === 'finance_manager') {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can view activity log.
     */
    public function viewActivityLog(User $user, User $targetUser): bool
    {
        // Users can view their own activity log
        if ($user->id === $targetUser->id) {
            return true;
        }
        
        // Admin can view any activity log
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Managers can view activity logs of their team members
        if (in_array($user->role, ['manager', 'agency_owner']) && 
            $user->agency_id === $targetUser->agency_id) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can verify KYC.
     */
    public function verifyKyc(User $user, User $targetUser): bool
    {
        // Users can submit their own KYC
        if ($user->id === $targetUser->id) {
            return true;
        }
        
        // Admin and KYC reviewers can verify KYC
        if (in_array($user->role, ['admin', 'kyc_reviewer'])) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can upload documents.
     */
    public function uploadDocuments(User $user, User $targetUser): bool
    {
        // Users can upload their own documents
        if ($user->id === $targetUser->id) {
            return true;
        }
        
        // Admin can upload documents for any user
        if ($user->hasRole('admin')) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can view sensitive information.
     */
    public function viewSensitiveInfo(User $user, User $targetUser): bool
    {
        // Users can view their own sensitive info
        if ($user->id === $targetUser->id) {
            return true;
        }
        
        // Admin can view any sensitive info
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Compliance officers can view sensitive info
        if ($user->role === 'compliance_officer') {
            return true;
        }
        
        return false;
    }

    public function impersonate(User $user, User $model): bool
    {
        return $user->hasRole('admin') && !$model->hasRole('admin');
    }

    public function manageSessions(User $user): bool
    {
        return true; // Users can manage their own sessions mostly
    }
}
