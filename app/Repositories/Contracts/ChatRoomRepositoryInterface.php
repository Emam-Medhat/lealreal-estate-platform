<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface ChatRoomRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get chat rooms for a specific user.
     *
     * @param User $user
     * @return Collection
     */
    public function getForUser(User $user): Collection;

    /**
     * Get chat room with participants and messages.
     *
     * @param int $id
     * @return mixed
     */
    public function getWithDetails(int $id);
}
