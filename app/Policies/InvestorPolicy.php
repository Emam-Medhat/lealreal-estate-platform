<?php

namespace App\Policies;

use App\Models\Investor;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InvestorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Investor $investor): bool
    {
        // User can view if they created the investor or if they're admin
        return $user->id === $investor->user_id || $user->id === $investor->created_by || $user->is_admin;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Investor $investor): bool
    {
        // User can update if they created the investor or if they're admin
        return $user->id === $investor->user_id || $user->id === $investor->created_by || $user->is_admin;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Investor $investor): bool
    {
        // User can delete if they created the investor or if they're admin
        return $user->id === $investor->user_id || $user->id === $investor->created_by || $user->is_admin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Investor $investor): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Investor $investor): bool
    {
        return false;
    }
}
