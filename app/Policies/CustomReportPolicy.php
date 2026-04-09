<?php

namespace App\Policies;

use App\Models\CustomReport;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomReportPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, CustomReport $customReport)
    {
        return $customReport->canBeUsedBy($user);
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, CustomReport $customReport)
    {
        return $customReport->canBeEditedBy($user);
    }

    public function delete(User $user, CustomReport $customReport)
    {
        return $customReport->canBeDeletedBy($user);
    }

    public function restore(User $user, CustomReport $customReport)
    {
        return $customReport->canBeEditedBy($user);
    }

    public function forceDelete(User $user, CustomReport $customReport)
    {
        return $customReport->canBeDeletedBy($user);
    }
}
