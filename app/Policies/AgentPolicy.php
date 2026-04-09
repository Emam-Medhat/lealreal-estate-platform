<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Agent;
use App\Models\Lead;
use App\Models\Client;
use Illuminate\Auth\Access\Response;

class AgentPolicy
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
    public function view(User $user, Agent $agent): bool
    {
        // Admin can view any agent
        if ($user->hasRole('admin')) {
            return true;
        }

        // Agent can view their own profile
        if ($user->id === $agent->user_id) {
            return true;
        }

        // Company owner can view agents in their company
        if ($user->company_id && $user->company->agents()->where('id', $agent->id)->exists()) {
            return true;
        }

        // Company managers can view agents in their company
        if ($user->company_id && $user->companyMembers()->where('role', 'manager')->exists()) {
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
    public function update(User $user, Agent $agent): bool
    {
        // Admin can update any agent
        if ($user->hasRole('admin')) {
            return true;
        }

        // Agent can update their own profile
        if ($user->id === $agent->user_id) {
            return true;
        }

        // Company owner can update agents in their company
        if ($user->company_id && $user->company->agents()->where('id', $agent->id)->exists()) {
            return true;
        }

        // Company managers can update agents in their company
        if ($user->company_id && $user->companyMembers()->where('role', 'manager')->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Agent $agent): bool
    {
        // Admin can delete any agent
        if ($user->hasRole('admin')) {
            return true;
        }

        // Company owner can delete agents in their company
        if ($user->company_id && $user->company->agents()->where('id', $agent->id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can manage clients.
     */
    public function manageClients(User $user, Agent $agent): bool
    {
        // Admin can manage any clients
        if ($user->hasRole('admin')) {
            return true;
        }

        // Agent can manage their own clients
        if ($user->id === $agent->user_id) {
            return true;
        }

        // Company owner can manage all clients in their company
        if ($user->company_id && $user->company->agents()->where('id', $agent->id)->exists()) {
            return true;
        }

        // Company managers can manage clients in their company
        if ($user->company_id && $user->companyMembers()->where('role', 'manager')->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view leads.
     */
    public function viewLeads(User $user, Agent $agent): bool
    {
        // Admin can view any leads
        if ($user->hasRole('admin')) {
            return true;
        }

        // Agent can view their own leads
        if ($user->id === $agent->user_id) {
            return true;
        }

        // Company owner can view all leads in their company
        if ($user->company_id && $user->company->agents()->where('id', $agent->id)->exists()) {
            return true;
        }

        // Company managers can view leads in their company
        if ($user->company_id && $user->companyMembers()->where('role', 'manager')->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can manage commissions.
     */
    public function manageCommissions(User $user, Agent $agent): bool
    {
        // Admin can manage any commissions
        if ($user->hasRole('admin')) {
            return true;
        }

        // Agent can view their own commissions
        if ($user->id === $agent->user_id) {
            return true;
        }

        // Company owner can manage all commissions in their company
        if ($user->company_id && $user->company->agents()->where('id', $agent->id)->exists()) {
            return true;
        }

        // Company managers can manage commissions in their company
        if ($user->company_id && $user->companyMembers()->where('role', 'manager')->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can manage appointments.
     */
    public function manageAppointments(User $user, Agent $agent): bool
    {
        // Admin can manage any appointments
        if ($user->hasRole('admin')) {
            return true;
        }

        // Agent can manage their own appointments
        if ($user->id === $agent->user_id) {
            return true;
        }

        // Company owner can manage all appointments in their company
        if ($user->company_id && $user->company->agents()->where('id', $agent->id)->exists()) {
            return true;
        }

        // Company managers can manage appointments in their company
        if ($user->company_id && $user->companyMembers()->where('role', 'manager')->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view performance.
     */
    public function viewPerformance(User $user, Agent $agent): bool
    {
        // Admin can view any performance
        if ($user->hasRole('admin')) {
            return true;
        }

        // Agent can view their own performance
        if ($user->id === $agent->user_id) {
            return true;
        }

        // Company owner can view all performance in their company
        if ($user->company_id && $user->company->agents()->where('id', $agent->id)->exists()) {
            return true;
        }

        // Company managers can view performance in their company
        if ($user->company_id && $user->companyMembers()->where('role', 'manager')->exists()) {
            return true;
        }

        return false;
    }
}
