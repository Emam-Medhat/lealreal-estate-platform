<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Property $property)
    {
        // Allow if user is the agent who owns the property
        if ($user->agent && $user->agent->id === $property->agent_id) {
            return true;
        }
        
        // Allow if user is admin
        if ($user->is_admin) {
            return true;
        }
        
        // Allow if property is active (public viewing)
        if ($property->status === 'active') {
            return true;
        }
        
        return false;
    }

    public function create(User $user)
    {
        return $user->agent !== null;
    }

    public function update(User $user, Property $property)
    {
        return ($user->agent && $user->agent->id === $property->agent_id) || $user->is_admin;
    }

    public function delete(User $user, Property $property)
    {
        return ($user->agent && $user->agent->id === $property->agent_id) || $user->is_admin;
    }

    public function publish(User $user, Property $property)
    {
        return ($user->agent && $user->agent->id === $property->agent_id) || $user->is_admin;
    }

    public function feature(User $user, Property $property)
    {
        return $user->is_admin;
    }

    public function moderate(User $user, Property $property)
    {
        return $user->is_admin;
    }
}
