<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface MessageRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get messages for a specific room.
     *
     * @param int $roomId
     * @param int|null $afterId
     * @return Collection
     */
    public function getForRoom(int $roomId, ?int $afterId = null): Collection;

    /**
     * Get messages for a specific conversation.
     *
     * @param int $conversationId
     * @param int|null $afterId
     * @return Collection
     */
    public function getForConversation(int $conversationId, ?int $afterId = null): Collection;
}
