<?php

namespace App\Repositories\Eloquent;

use App\Models\Conversation;
use App\Models\User;
use App\Repositories\Contracts\ConversationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ConversationRepository extends BaseRepository implements ConversationRepositoryInterface
{
    /**
     * ConversationRepository constructor.
     *
     * @param Conversation $model
     */
    public function __construct(Conversation $model)
    {
        parent::__construct($model);
    }

    /**
     * Get conversations for a specific user.
     *
     * @param User $user
     * @return Collection
     */
    public function getForUser(User $user): Collection
    {
        return $this->model->where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
        })->with(['sender.profile', 'receiver.profile', 'lastMessage'])
          ->withCount('messages')
          ->orderBy('updated_at', 'desc')
          ->get();
    }

    /**
     * Find a conversation between two users.
     *
     * @param int $userOneId
     * @param int $userTwoId
     * @return mixed
     */
    public function findBetweenUsers(int $userOneId, int $userTwoId)
    {
        return $this->model->where(function ($query) use ($userOneId, $userTwoId) {
            $query->where('sender_id', $userOneId)
                  ->where('receiver_id', $userTwoId);
        })->orWhere(function ($query) use ($userOneId, $userTwoId) {
            $query->where('sender_id', $userTwoId)
                  ->where('receiver_id', $userOneId);
        })->first();
    }

    /**
     * Get conversation with details.
     *
     * @param int $id
     * @return mixed
     */
    public function getWithDetails(int $id)
    {
        return $this->model->with(['sender.profile', 'receiver.profile', 'messages.user', 'messages.attachments'])
            ->withCount('messages')
            ->findOrFail($id);
    }
}
