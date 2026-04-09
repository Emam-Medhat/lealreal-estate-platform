<?php

namespace App\Services;

use App\Models\User;
use App\Models\ChatRoom;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ChatParticipant;
use App\Models\MessageAttachment;
use App\Models\UserNotification;
use App\Models\UserActivityLog;
use App\Events\MessageSent;
use App\Repositories\Contracts\ChatRoomRepositoryInterface;
use App\Repositories\Contracts\ConversationRepositoryInterface;
use App\Repositories\Contracts\MessageRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatService
{
    protected $chatRoomRepo;
    protected $conversationRepo;
    protected $messageRepo;

    public function __construct(
        ChatRoomRepositoryInterface $chatRoomRepo,
        ConversationRepositoryInterface $conversationRepo,
        MessageRepositoryInterface $messageRepo
    ) {
        $this->chatRoomRepo = $chatRoomRepo;
        $this->conversationRepo = $conversationRepo;
        $this->messageRepo = $messageRepo;
    }

    public function getChatData(User $user)
    {
        return [
            'chatRooms' => $this->chatRoomRepo->getForUser($user),
            'conversations' => $this->conversationRepo->getForUser($user),
        ];
    }

    public function createRoom(array $data, User $creator)
    {
        return DB::transaction(function () use ($data, $creator) {
            $room = $this->chatRoomRepo->create([
                'name' => $data['name'],
                'type' => $data['type'],
                'description' => $data['description'] ?? null,
                'created_by' => $creator->id,
                'participants_count' => 1,
                'is_active' => true,
                'settings' => ['allow_invites' => true]
            ]);

            ChatParticipant::create([
                'chat_room_id' => $room->id,
                'user_id' => $creator->id,
                'role' => 'admin',
                'joined_at' => now()
            ]);

            if (!empty($data['invite_users'])) {
                foreach ($data['invite_users'] as $userId) {
                    if ($userId != $creator->id) {
                        ChatParticipant::create([
                            'chat_room_id' => $room->id,
                            'user_id' => $userId,
                            'role' => 'member',
                            'joined_at' => now()
                        ]);
                        $room->increment('participants_count');
                    }
                }
            }

            return $room;
        });
    }

    public function sendMessage(User $sender, array $data, $files = [])
    {
        return DB::transaction(function () use ($sender, $data, $files) {
            $messageData = [
                'sender_id' => $sender->id,
                'content' => $data['content'] ?? null,
                'type' => $data['type'] ?? 'text',
                'chat_room_id' => $data['chat_room_id'] ?? null,
                'conversation_id' => $data['conversation_id'] ?? null,
                'receiver_id' => $data['receiver_id'] ?? null,
            ];

            // If it's a new conversation (no conversation_id but has receiver_id)
            if (empty($messageData['conversation_id']) && !empty($messageData['receiver_id']) && empty($messageData['chat_room_id'])) {
                // Check if conversation exists
                $conversation = $this->conversationRepo->findBetweenUsers($sender->id, $messageData['receiver_id']);
                
                if (!$conversation) {
                    $conversation = $this->conversationRepo->create([
                        'sender_id' => $sender->id,
                        'receiver_id' => $messageData['receiver_id'],
                        'status' => 'active'
                    ]);
                }
                $messageData['conversation_id'] = $conversation->id;
            }

            $message = $this->messageRepo->create($messageData);

            // Handle attachments
            if (!empty($files)) {
                foreach ($files as $file) {
                    $path = $file->store('message-attachments', 'public');
                    MessageAttachment::create([
                        'message_id' => $message->id,
                        'filename' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ]);
                }
            }

            // Update parent timestamps and notifications
            if ($message->chat_room_id) {
                $room = $this->chatRoomRepo->findById($message->chat_room_id);
                $room->touch();
                // Logic for room notifications could go here
            } elseif ($message->conversation_id) {
                $conversation = $this->conversationRepo->findById($message->conversation_id);
                $conversation->touch();
                
                $recipientId = ($conversation->sender_id == $sender->id) ? $conversation->receiver_id : $conversation->sender_id;
                
                UserNotification::create([
                    'user_id' => $recipientId,
                    'title' => 'New Message',
                    'message' => "You have a new message from " . $sender->name, // Assuming name exists
                    'type' => 'message',
                    'action_url' => route('messages.chat', ['conversation_id' => $conversation->id]),
                    'action_text' => 'View Message',
                    'is_read' => false,
                ]);
            }

            broadcast(new MessageSent($message, $sender))->toOthers();

            return $message;
        });
    }
}
