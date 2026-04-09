<?php

namespace App\Repositories\Eloquent;

use App\Models\ChatRoom;
use App\Models\User;
use App\Repositories\Contracts\ChatRoomRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ChatRoomRepository extends BaseRepository implements ChatRoomRepositoryInterface
{
    /**
     * ChatRoomRepository constructor.
     *
     * @param ChatRoom $model
     */
    public function __construct(ChatRoom $model)
    {
        parent::__construct($model);
    }

    /**
     * Get chat rooms for a specific user.
     *
     * @param User $user
     * @return Collection
     */
    public function getForUser(User $user): Collection
    {
        return $this->model->whereHas('participants', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })->with(['participants', 'latestMessage.user'])
          ->withCount('messages')
          ->orderBy('updated_at', 'desc')
          ->get();
    }

    /**
     * Get chat room with participants and messages.
     *
     * @param int $id
     * @return mixed
     */
    public function getWithDetails(int $id)
    {
        return $this->model->with(['participants', 'messages.user', 'messages.attachments'])
            ->withCount('messages')
            ->findOrFail($id);
    }
}
