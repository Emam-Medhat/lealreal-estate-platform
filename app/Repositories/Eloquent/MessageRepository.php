<?php

namespace App\Repositories\Eloquent;

use App\Models\Message;
use App\Repositories\Contracts\MessageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MessageRepository extends BaseRepository implements MessageRepositoryInterface
{
    /**
     * MessageRepository constructor.
     *
     * @param Message $model
     */
    public function __construct(Message $model)
    {
        parent::__construct($model);
    }

    /**
     * Get messages for a specific room.
     *
     * @param int $roomId
     * @param int|null $afterId
     * @return Collection
     */
    public function getForRoom(int $roomId, ?int $afterId = null): Collection
    {
        $query = $this->model->where('chat_room_id', $roomId)
            ->with(['user.profile', 'attachments'])
            ->orderBy('created_at', 'asc');

        if ($afterId) {
            $query->where('id', '>', $afterId);
        }

        return $query->get();
    }

    /**
     * Get messages for a specific conversation.
     *
     * @param int $conversationId
     * @param int|null $afterId
     * @return Collection
     */
    public function getForConversation(int $conversationId, ?int $afterId = null): Collection
    {
        $query = $this->model->where('conversation_id', $conversationId)
            ->with(['user.profile', 'attachments'])
            ->orderBy('created_at', 'asc');

        if ($afterId) {
            $query->where('id', '>', $afterId);
        }

        return $query->get();
    }
}
