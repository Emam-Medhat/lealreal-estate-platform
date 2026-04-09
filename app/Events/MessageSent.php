<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $user;

    /**
     * Create a new event instance.
     *
     * @param Message $message
     * @param User $user
     */
    public function __construct(Message $message, User $user)
    {
        $this->message = $message;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        if ($this->message->chat_room_id) {
            return new PrivateChannel('chat.room.' . $this->message->chat_room_id);
        }
        
        if ($this->message->conversation_id) {
            return new PrivateChannel('chat.conversation.' . $this->message->conversation_id);
        }

        return [];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'message.sent';
    }
    
    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'content' => $this->message->content,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name ?? $this->user->full_name,
                'avatar' => $this->user->avatar_url ?? $this->user->avatar,
            ],
            'created_at' => $this->message->created_at->toIso8601String(),
            'type' => $this->message->type,
            'attachments' => $this->message->attachments ?? [],
        ];
    }
}
