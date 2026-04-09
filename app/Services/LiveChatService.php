<?php

namespace App\Services;

use App\Models\User;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\ChatParticipant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Carbon\Carbon;

class LiveChatService
{
    private const MESSAGE_TYPES = [
        'text' => ['max_length' => 1000, 'supports_formatting' => true],
        'image' => ['max_size' => 10485760, 'formats' => ['jpg', 'png', 'gif', 'webp']],
        'file' => ['max_size' => 52428800, 'formats' => ['pdf', 'doc', 'docx', 'txt']],
        'voice' => ['max_duration' => 300, 'formats' => ['mp3', 'wav', 'ogg']],
        'video' => ['max_duration' => 600, 'formats' => ['mp4', 'webm', 'mov']],
        'location' => ['required_fields' => ['latitude', 'longitude']],
        'contact' => ['required_fields' => ['name', 'phone']],
        'system' => ['restricted' => true]
    ];

    private const ROOM_TYPES = [
        'public' => ['max_participants' => 1000, 'requires_approval' => false],
        'private' => ['max_participants' => 50, 'requires_approval' => true],
        'direct' => ['max_participants' => 2, 'requires_approval' => false],
        'group' => ['max_participants' => 100, 'requires_approval' => true],
        'broadcast' => ['max_participants' => 10000, 'requires_approval' => false]
    ];

    private $webSocketService;
    private $notificationService;

    public function __construct(
        WebSocketService $webSocketService = null,
        RealTimeNotificationService $notificationService = null
    ) {
        $this->webSocketService = $webSocketService;
        $this->notificationService = $notificationService;
    }

    public function createRoom(array $roomData): array
    {
        try {
            // Validate room data
            $validatedData = $this->validateRoomData($roomData);
            
            // Create chat room
            $room = ChatRoom::create([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'] ?? null,
                'type' => $validatedData['type'],
                'created_by' => $validatedData['created_by'],
                'max_participants' => $validatedData['max_participants'] ?? self::ROOM_TYPES[$validatedData['type']]['max_participants'],
                'requires_approval' => $validatedData['requires_approval'] ?? self::ROOM_TYPES[$validatedData['type']]['requires_approval'],
                'is_active' => true,
                'settings' => $validatedData['settings'] ?? [],
                'metadata' => $validatedData['metadata'] ?? [],
                'created_at' => now()
            ]);

            // Add creator as participant
            $this->addParticipant($room->id, $validatedData['created_by'], 'admin');

            // Send real-time notification
            $this->broadcastRoomEvent($room->id, 'room_created', [
                'room_id' => $room->id,
                'room_name' => $room->name,
                'created_by' => $validatedData['created_by']
            ]);

            return [
                'success' => true,
                'room' => $room->load(['participants.user', 'creator']),
                'message' => 'Chat room created successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to create chat room', [
                'error' => $e->getMessage(),
                'room_data' => $roomData
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create chat room',
                'error' => $e->getMessage()
            ];
        }
    }

    public function joinRoom(int $roomId, int $userId, ?string $password = null): array
    {
        try {
            $room = ChatRoom::findOrFail($roomId);
            
            // Check if room is active
            if (!$room->is_active) {
                return [
                    'success' => false,
                    'message' => 'Room is not active'
                ];
            }

            // Check if user is already a participant
            if ($this->isParticipant($roomId, $userId)) {
                return [
                    'success' => false,
                    'message' => 'Already a participant in this room'
                ];
            }

            // Check room capacity
            if ($this->getParticipantCount($roomId) >= $room->max_participants) {
                return [
                    'success' => false,
                    'message' => 'Room is full'
                ];
            }

            // Check approval requirement
            if ($room->requires_approval) {
                return $this->requestJoinApproval($roomId, $userId);
            }

            // Check password for private rooms
            if ($room->type === 'private' && !$this->validateRoomPassword($room, $password)) {
                return [
                    'success' => false,
                    'message' => 'Invalid password'
                ];
            }

            // Add participant
            $participant = $this->addParticipant($roomId, $userId, 'member');

            // Send real-time notifications
            $this->broadcastRoomEvent($roomId, 'user_joined', [
                'user_id' => $userId,
                'participant_id' => $participant->id,
                'joined_at' => now()->toISOString()
            ]);

            // Send notification to room participants
            $this->notifyRoomParticipants($roomId, 'user_joined_room', [
                'room_id' => $roomId,
                'user_id' => $userId
            ], [$userId]);

            return [
                'success' => true,
                'participant' => $participant->load('user'),
                'room' => $room->load(['participants.user']),
                'message' => 'Joined room successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to join room', [
                'error' => $e->getMessage(),
                'room_id' => $roomId,
                'user_id' => $userId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to join room',
                'error' => $e->getMessage()
            ];
        }
    }

    public function leaveRoom(int $roomId, int $userId): array
    {
        try {
            $participant = $this->getParticipant($roomId, $userId);
            
            if (!$participant) {
                return [
                    'success' => false,
                    'message' => 'Not a participant in this room'
                ];
            }

            // Remove participant
            $participant->delete();

            // Send real-time notifications
            $this->broadcastRoomEvent($roomId, 'user_left', [
                'user_id' => $userId,
                'left_at' => now()->toISOString()
            ]);

            // Notify remaining participants
            $this->notifyRoomParticipants($roomId, 'user_left_room', [
                'room_id' => $roomId,
                'user_id' => $userId
            ]);

            // Check if room should be deactivated (no participants)
            if ($this->getParticipantCount($roomId) === 0) {
                $this->deactivateRoom($roomId);
            }

            return [
                'success' => true,
                'message' => 'Left room successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to leave room', [
                'error' => $e->getMessage(),
                'room_id' => $roomId,
                'user_id' => $userId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to leave room',
                'error' => $e->getMessage()
            ];
        }
    }

    public function sendMessage(array $messageData): array
    {
        try {
            // Validate message data
            $validatedData = $this->validateMessageData($messageData);
            
            // Check if user is participant
            if (!$this->isParticipant($validatedData['room_id'], $validatedData['user_id'])) {
                return [
                    'success' => false,
                    'message' => 'Not a participant in this room'
                ];
            }

            // Check room permissions
            $room = ChatRoom::findOrFail($validatedData['room_id']);
            if (!$room->is_active) {
                return [
                    'success' => false,
                    'message' => 'Room is not active'
                ];
            }

            // Check if user is muted
            if ($this->isUserMuted($validatedData['room_id'], $validatedData['user_id'])) {
                return [
                    'success' => false,
                    'message' => 'User is muted in this room'
                ];
            }

            // Process message content
            $processedContent = $this->processMessageContent($validatedData);

            // Create message
            $message = ChatMessage::create([
                'room_id' => $validatedData['room_id'],
                'user_id' => $validatedData['user_id'],
                'content' => $processedContent['content'],
                'type' => $validatedData['type'],
                'metadata' => array_merge($processedContent['metadata'], $validatedData['metadata'] ?? []),
                'reply_to' => $validatedData['reply_to'] ?? null,
                'is_edited' => false,
                'created_at' => now()
            ]);

            // Update participant last activity
            $this->updateParticipantActivity($validatedData['room_id'], $validatedData['user_id']);

            // Send real-time message
            $this->broadcastMessage($message);

            // Send notifications for mentions
            if (!empty($processedContent['mentions'])) {
                $this->sendMentionNotifications($message, $processedContent['mentions']);
            }

            // Update room statistics
            $this->updateRoomStats($validatedData['room_id']);

            return [
                'success' => true,
                'message' => $message->load(['user', 'replyTo.user']),
                'processed_content' => $processedContent,
                'sent_at' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send message', [
                'error' => $e->getMessage(),
                'message_data' => $messageData
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send message',
                'error' => $e->getMessage()
            ];
        }
    }

    public function editMessage(int $messageId, int $userId, string $newContent): array
    {
        try {
            $message = ChatMessage::findOrFail($messageId);
            
            // Check if user is the message author
            if ($message->user_id !== $userId) {
                return [
                    'success' => false,
                    'message' => 'Can only edit your own messages'
                ];
            }

            // Check if message is too old to edit (e.g., 15 minutes)
            if ($message->created_at->diffInMinutes(now()) > 15) {
                return [
                    'success' => false,
                    'message' => 'Message is too old to edit'
                ];
            }

            // Process new content
            $processedContent = $this->processMessageContent([
                'content' => $newContent,
                'type' => $message->type
            ]);

            // Update message
            $message->update([
                'content' => $processedContent['content'],
                'metadata' => array_merge($message->metadata ?? [], $processedContent['metadata']),
                'is_edited' => true,
                'edited_at' => now()
            ]);

            // Broadcast edit notification
            $this->broadcastMessageEdit($message);

            return [
                'success' => true,
                'message' => $message->load('user'),
                'processed_content' => $processedContent,
                'edited_at' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('Failed to edit message', [
                'error' => $e->getMessage(),
                'message_id' => $messageId,
                'user_id' => $userId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to edit message',
                'error' => $e->getMessage()
            ];
        }
    }

    public function deleteMessage(int $messageId, int $userId): array
    {
        try {
            $message = ChatMessage::findOrFail($messageId);
            
            // Check permissions (author or room admin)
            if (!$this->canDeleteMessage($message, $userId)) {
                return [
                    'success' => false,
                    'message' => 'Permission denied'
                ];
            }

            // Soft delete message
            $message->delete();

            // Broadcast deletion notification
            $this->broadcastMessageDelete($messageId, $message->room_id);

            return [
                'success' => true,
                'message' => 'Message deleted successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to delete message', [
                'error' => $e->getMessage(),
                'message_id' => $messageId,
                'user_id' => $userId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to delete message',
                'error' => $e->getMessage()
            ];
        }
    }

    public function getRoomMessages(int $roomId, int $userId, array $options = []): array
    {
        try {
            // Check if user is participant
            if (!$this->isParticipant($roomId, $userId)) {
                return [
                    'success' => false,
                    'message' => 'Not a participant in this room'
                ];
            }

            $query = ChatMessage::where('room_id', $roomId)
                ->with(['user', 'replyTo.user'])
                ->orderBy('created_at', 'desc');

            // Apply filters
            if (isset($options['before'])) {
                $query->where('created_at', '<', $options['before']);
            }

            if (isset($options['after'])) {
                $query->where('created_at', '>', $options['after']);
            }

            if (isset($options['type'])) {
                $query->where('type', $options['type']);
            }

            if (isset($options['user_id'])) {
                $query->where('user_id', $options['user_id']);
            }

            // Pagination
            $limit = $options['limit'] ?? 50;
            $messages = $query->limit($limit)->get()->reverse(); // Reverse for chronological order

            return [
                'success' => true,
                'messages' => $messages->map(function($message) {
                    return [
                        'id' => $message->id,
                        'content' => $message->content,
                        'type' => $message->type,
                        'user' => [
                            'id' => $message->user->id,
                            'name' => $message->user->full_name,
                            'avatar' => $message->user->avatar_url
                        ],
                        'reply_to' => $message->replyTo ? [
                            'id' => $message->replyTo->id,
                            'content' => substr($message->replyTo->content, 0, 100),
                            'user' => $message->replyTo->user->full_name
                        ] : null,
                        'metadata' => $message->metadata,
                        'is_edited' => $message->is_edited,
                        'edited_at' => $message->edited_at?->toISOString(),
                        'created_at' => $message->created_at->toISOString(),
                        'timestamp' => $message->created_at->timestamp
                    ];
                }),
                'has_more' => $messages->count() === $limit,
                'room_id' => $roomId
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get room messages', [
                'error' => $e->getMessage(),
                'room_id' => $roomId,
                'user_id' => $userId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get messages',
                'error' => $e->getMessage()
            ];
        }
    }

    public function getUserRooms(int $userId, array $options = []): array
    {
        try {
            $query = ChatParticipant::where('user_id', $userId)
                ->with(['room.participants.user', 'room.creator'])
                ->whereHas('room', function($q) {
                    $q->where('is_active', true);
                });

            // Apply filters
            if (isset($options['room_type'])) {
                $query->whereHas('room', function($q) use ($options) {
                    $q->where('type', $options['room_type']);
                });
            }

            $participants = $query->orderBy('last_activity_at', 'desc')->get();

            return [
                'success' => true,
                'rooms' => $participants->map(function($participant) {
                    $room = $participant->room;
                    return [
                        'id' => $room->id,
                        'name' => $room->name,
                        'description' => $room->description,
                        'type' => $room->type,
                        'participant_count' => $this->getParticipantCount($room->id),
                        'max_participants' => $room->max_participants,
                        'created_by' => $room->created_by,
                        'creator' => $room->creator ? [
                            'id' => $room->creator->id,
                            'name' => $room->creator->full_name
                        ] : null,
                        'role' => $participant->role,
                        'joined_at' => $participant->created_at->toISOString(),
                        'last_activity_at' => $participant->last_activity_at->toISOString(),
                        'unread_count' => $this->getUnreadCount($room->id, $participant->user_id),
                        'last_message' => $this->getLastMessage($room->id),
                        'is_muted' => $participant->is_muted,
                        'notifications_enabled' => $participant->notifications_enabled
                    ];
                })
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get user rooms', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get rooms',
                'error' => $e->getMessage()
            ];
        }
    }

    public function searchMessages(int $roomId, int $userId, string $query, array $options = []): array
    {
        try {
            // Check if user is participant
            if (!$this->isParticipant($roomId, $userId)) {
                return [
                    'success' => false,
                    'message' => 'Not a participant in this room'
                ];
            }

            $messages = ChatMessage::where('room_id', $roomId)
                ->where('content', 'LIKE', "%{$query}%")
                ->with(['user'])
                ->orderBy('created_at', 'desc')
                ->limit($options['limit'] ?? 20)
                ->get();

            return [
                'success' => true,
                'query' => $query,
                'results' => $messages->map(function($message) {
                    return [
                        'id' => $message->id,
                        'content' => $message->content,
                        'type' => $message->type,
                        'user' => [
                            'id' => $message->user->id,
                            'name' => $message->user->full_name
                        ],
                        'created_at' => $message->created_at->toISOString(),
                        'highlights' => $this->highlightSearchTerms($message->content, $query)
                    ];
                })
            ];
        } catch (\Exception $e) {
            Log::error('Failed to search messages', [
                'error' => $e->getMessage(),
                'room_id' => $roomId,
                'user_id' => $userId,
                'query' => $query
            ]);

            return [
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ];
        }
    }

    // Private helper methods
    private function validateRoomData(array $data): array
    {
        $required = ['name', 'type', 'created_by'];
        
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        if (!isset(self::ROOM_TYPES[$data['type']])) {
            throw new \InvalidArgumentException("Invalid room type: {$data['type']}");
        }

        return $data;
    }

    private function validateMessageData(array $data): array
    {
        $required = ['room_id', 'user_id', 'content', 'type'];
        
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        if (!isset(self::MESSAGE_TYPES[$data['type']])) {
            throw new \InvalidArgumentException("Invalid message type: {$data['type']}");
        }

        // Validate message length
        $maxLength = self::MESSAGE_TYPES[$data['type']]['max_length'] ?? 1000;
        if (strlen($data['content']) > $maxLength) {
            throw new \InvalidArgumentException("Message too long");
        }

        return $data;
    }

    private function processMessageContent(array $data): array
    {
        $content = $data['content'];
        $type = $data['type'];
        $mentions = [];
        $hashtags = [];
        $links = [];

        if ($type === 'text' && self::MESSAGE_TYPES[$type]['supports_formatting']) {
            // Extract mentions (@username)
            preg_match_all('/@(\w+)/', $content, $mentionMatches);
            $mentions = $mentionMatches[1];

            // Extract hashtags (#tag)
            preg_match_all('/#(\w+)/', $content, $hashtagMatches);
            $hashtags = $hashtagMatches[1];

            // Extract and convert links
            $content = preg_replace(
                '/(https?:\/\/[^\s]+)/',
                '<a href="$1" target="_blank">$1</a>',
                $content
            );
        }

        return [
            'content' => $content,
            'mentions' => $mentions,
            'hashtags' => $hashtags,
            'links' => $links,
            'metadata' => [
                'mentions' => $mentions,
                'hashtags' => $hashtags,
                'links' => $links
            ]
        ];
    }

    private function addParticipant(int $roomId, int $userId, string $role): ChatParticipant
    {
        return ChatParticipant::firstOrCreate([
            'room_id' => $roomId,
            'user_id' => $userId
        ], [
            'role' => $role,
            'joined_at' => now(),
            'last_activity_at' => now(),
            'is_muted' => false,
            'notifications_enabled' => true
        ]);
    }

    private function isParticipant(int $roomId, int $userId): bool
    {
        return ChatParticipant::where('room_id', $roomId)
            ->where('user_id', $userId)
            ->exists();
    }

    private function getParticipant(int $roomId, int $userId): ?ChatParticipant
    {
        return ChatParticipant::where('room_id', $roomId)
            ->where('user_id', $userId)
            ->first();
    }

    private function getParticipantCount(int $roomId): int
    {
        return ChatParticipant::where('room_id', $roomId)->count();
    }

    private function isUserMuted(int $roomId, int $userId): bool
    {
        $participant = $this->getParticipant($roomId, $userId);
        return $participant ? $participant->is_muted : false;
    }

    private function updateParticipantActivity(int $roomId, int $userId): void
    {
        ChatParticipant::where('room_id', $roomId)
            ->where('user_id', $userId)
            ->update(['last_activity_at' => now()]);
    }

    private function broadcastMessage(ChatMessage $message): void
    {
        $messageData = [
            'type' => 'new_message',
            'room_id' => $message->room_id,
            'message' => [
                'id' => $message->id,
                'content' => $message->content,
                'type' => $message->type,
                'user_id' => $message->user_id,
                'user_name' => $message->user->full_name,
                'user_avatar' => $message->user->avatar_url,
                'reply_to' => $message->reply_to_id,
                'metadata' => $message->metadata,
                'is_edited' => $message->is_edited,
                'created_at' => $message->created_at->toISOString(),
                'timestamp' => $message->created_at->timestamp
            ]
        ];

        if ($this->webSocketService) {
            $this->webSocketService->broadcastToRoom("chat_{$message->room_id}", $messageData);
        }
    }

    private function broadcastMessageEdit(ChatMessage $message): void
    {
        $editData = [
            'type' => 'message_edited',
            'room_id' => $message->room_id,
            'message_id' => $message->id,
            'content' => $message->content,
            'edited_at' => $message->edited_at->toISOString()
        ];

        if ($this->webSocketService) {
            $this->webSocketService->broadcastToRoom("chat_{$message->room_id}", $editData);
        }
    }

    private function broadcastMessageDelete(int $messageId, int $roomId): void
    {
        $deleteData = [
            'type' => 'message_deleted',
            'room_id' => $roomId,
            'message_id' => $messageId,
            'deleted_at' => now()->toISOString()
        ];

        if ($this->webSocketService) {
            $this->webSocketService->broadcastToRoom("chat_{$roomId}", $deleteData);
        }
    }

    private function broadcastRoomEvent(int $roomId, string $event, array $data): void
    {
        $eventData = array_merge([
            'type' => $event,
            'room_id' => $roomId,
            'timestamp' => now()->toISOString()
        ], $data);

        if ($this->webSocketService) {
            $this->webSocketService->broadcastToRoom("chat_{$roomId}", $eventData);
        }
    }

    private function notifyRoomParticipants(int $roomId, string $notificationType, array $data, array $excludeUsers = []): void
    {
        if (!$this->notificationService) {
            return;
        }

        $participants = ChatParticipant::where('room_id', $roomId)
            ->whereNotIn('user_id', $excludeUsers)
            ->where('notifications_enabled', true)
            ->pluck('user_id');

        foreach ($participants as $userId) {
            $this->notificationService->sendNotification([
                'user_id' => $userId,
                'type' => 'info',
                'title' => 'Chat Notification',
                'message' => $this->getChatNotificationMessage($notificationType, $data),
                'data' => array_merge(['room_id' => $roomId], $data),
                'channels' => ['websocket', 'in_app']
            ]);
        }
    }

    private function sendMentionNotifications(ChatMessage $message, array $mentions): void
    {
        if (!$this->notificationService) {
            return;
        }

        foreach ($mentions as $username) {
            $user = User::where('username', $username)->first();
            if ($user && $this->isParticipant($message->room_id, $user->id)) {
                $this->notificationService->sendNotification([
                    'user_id' => $user->id,
                    'type' => 'info',
                    'title' => 'You were mentioned',
                    'message' => "{$message->user->full_name} mentioned you in chat",
                    'data' => [
                        'room_id' => $message->room_id,
                        'message_id' => $message->id,
                        'mention_by' => $message->user_id
                    ],
                    'channels' => ['websocket', 'in_app', 'push']
                ]);
            }
        }
    }

    private function updateRoomStats(int $roomId): void
    {
        Cache::increment("room_messages_{$roomId}");
        Cache::put("room_last_activity_{$roomId}", now(), 3600);
    }

    private function getUnreadCount(int $roomId, int $userId): int
    {
        $participant = $this->getParticipant($roomId, $userId);
        if (!$participant) {
            return 0;
        }

        return ChatMessage::where('room_id', $roomId)
            ->where('created_at', '>', $participant->last_read_at ?? $participant->created_at)
            ->where('user_id', '!=', $userId)
            ->count();
    }

    private function getLastMessage(int $roomId): ?array
    {
        $message = ChatMessage::where('room_id', $roomId)
            ->with('user')
            ->latest()
            ->first();

        if (!$message) {
            return null;
        }

        return [
            'id' => $message->id,
            'content' => substr($message->content, 0, 100),
            'type' => $message->type,
            'user_name' => $message->user->full_name,
            'created_at' => $message->created_at->toISOString()
        ];
    }

    private function highlightSearchTerms(string $content, string $query): string
    {
        return preg_replace("/({$query})/i", '<mark>$1</mark>', $content);
    }

    private function getChatNotificationMessage(string $type, array $data): string
    {
        return match($type) {
            'user_joined_room' => 'A new user joined the room',
            'user_left_room' => 'A user left the room',
            default => 'Chat room update'
        };
    }

    private function canDeleteMessage(ChatMessage $message, int $userId): bool
    {
        // User can delete their own message
        if ($message->user_id === $userId) {
            return true;
        }

        // Check if user is room admin
        $participant = $this->getParticipant($message->room_id, $userId);
        return $participant && in_array($participant->role, ['admin', 'moderator']);
    }

    private function validateRoomPassword(ChatRoom $room, ?string $password): bool
    {
        if (!$room->password) {
            return true;
        }

        return $password && hash_equals($room->password, $password);
    }

    private function requestJoinApproval(int $roomId, int $userId): array
    {
        // Implementation for join approval request
        return [
            'success' => false,
            'message' => 'Join request sent for approval',
            'requires_approval' => true
        ];
    }

    private function deactivateRoom(int $roomId): void
    {
        ChatRoom::where('id', $roomId)->update(['is_active' => false]);
    }
}
