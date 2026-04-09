<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface ConversationRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get conversations for a specific user.
     *
     * @param User $user
     * @return Collection
     */
    public function getForUser(User $user): Collection;

    /**
     * Find a conversation between two users.
     *
     * @param int $userOneId
     * @param int $userTwoId
     * @return mixed
     */
    public function findBetweenUsers(int $userOneId, int $userTwoId);

    /**
     * Get conversation with details.
     *
     * @param int $id
     * @return mixed
     */
    public function getWithDetails(int $id);
}
