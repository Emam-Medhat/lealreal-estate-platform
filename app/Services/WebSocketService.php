<?php

namespace App\Services;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WebSocketService implements MessageComponentInterface
{
    protected $clients;
    protected $subscriptions;
    protected $userConnections;
    protected $rooms;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->subscriptions = [];
        $this->userConnections = [];
        $this->rooms = [];
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        
        // Extract user info from query parameters or headers
        $queryParams = $conn->httpRequest->getUri()->getQuery();
        parse_str($queryParams, $params);
        
        $userId = $params['user_id'] ?? null;
        $token = $params['token'] ?? null;
        
        // Authenticate connection
        if (!$this->authenticateConnection($userId, $token)) {
            $conn->close();
            return;
        }

        // Store user connection
        if ($userId) {
            $this->userConnections[$userId] = $conn;
            
            // Send welcome message
            $this->sendToClient($conn, [
                'type' => 'connection_established',
                'user_id' => $userId,
                'timestamp' => now()->toISOString(),
                'server_time' => time()
            ]);

            // Notify other clients about new connection
            $this->broadcastToOthers($conn, [
                'type' => 'user_online',
                'user_id' => $userId,
                'timestamp' => now()->toISOString()
            ], $userId);

            // Update user online status
            $this->updateUserOnlineStatus($userId, true);
        }

        Log::info('WebSocket connection opened', [
            'connection_id' => $conn->resourceId,
            'user_id' => $userId,
            'ip_address' => $conn->remoteAddress
        ]);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            $data = json_decode($msg, true);
            
            if (!$data || !isset($data['type'])) {
                $this->sendError($from, 'Invalid message format');
                return;
            }

            $userId = $this->getUserIdFromConnection($from);
            
            // Route message based on type
            switch ($data['type']) {
                case 'subscribe':
                    $this->handleSubscription($from, $data, $userId);
                    break;
                    
                case 'unsubscribe':
                    $this->handleUnsubscription($from, $data, $userId);
                    break;
                    
                case 'join_room':
                    $this->handleJoinRoom($from, $data, $userId);
                    break;
                    
                case 'leave_room':
                    $this->handleLeaveRoom($from, $data, $userId);
                    break;
                    
                case 'chat_message':
                    $this->handleChatMessage($from, $data, $userId);
                    break;
                    
                case 'property_update':
                    $this->handlePropertyUpdate($from, $data, $userId);
                    break;
                    
                case 'auction_bid':
                    $this->handleAuctionBid($from, $data, $userId);
                    break;
                    
                case 'heartbeat':
                    $this->handleHeartbeat($from, $userId);
                    break;
                    
                default:
                    $this->sendError($from, 'Unknown message type');
            }
        } catch (\Exception $e) {
            Log::error('Error processing WebSocket message', [
                'error' => $e->getMessage(),
                'message' => $msg
            ]);
            $this->sendError($from, 'Internal server error');
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        
        $userId = $this->getUserIdFromConnection($conn);
        
        if ($userId) {
            // Remove from user connections
            unset($this->userConnections[$userId]);
            
            // Remove subscriptions
            $this->removeAllUserSubscriptions($userId);
            
            // Remove from all rooms
            $this->removeUserFromAllRooms($userId);
            
            // Notify other clients
            $this->broadcast([
                'type' => 'user_offline',
                'user_id' => $userId,
                'timestamp' => now()->toISOString()
            ]);
            
            // Update user offline status
            $this->updateUserOnlineStatus($userId, false);
        }

        Log::info('WebSocket connection closed', [
            'connection_id' => $conn->resourceId,
            'user_id' => $userId
        ]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        Log::error('WebSocket error', [
            'error' => $e->getMessage(),
            'connection_id' => $conn->resourceId
        ]);
        
        $conn->close();
    }

    // Message Handlers
    private function handleSubscription(ConnectionInterface $conn, array $data, ?int $userId): void
    {
        if (!$userId || !isset($data['channel'])) {
            $this->sendError($conn, 'Invalid subscription request');
            return;
        }

        $channel = $data['channel'];
        $connectionId = $conn->resourceId;

        // Add subscription
        if (!isset($this->subscriptions[$channel])) {
            $this->subscriptions[$channel] = [];
        }

        $this->subscriptions[$channel][$connectionId] = $conn;

        // Send confirmation
        $this->sendToClient($conn, [
            'type' => 'subscription_confirmed',
            'channel' => $channel,
            'timestamp' => now()->toISOString()
        ]);

        Log::info('User subscribed to channel', [
            'user_id' => $userId,
            'channel' => $channel,
            'connection_id' => $connectionId
        ]);
    }

    private function handleUnsubscription(ConnectionInterface $conn, array $data, ?int $userId): void
    {
        if (!isset($data['channel'])) {
            $this->sendError($conn, 'Invalid unsubscription request');
            return;
        }

        $channel = $data['channel'];
        $connectionId = $conn->resourceId;

        // Remove subscription
        if (isset($this->subscriptions[$channel][$connectionId])) {
            unset($this->subscriptions[$channel][$connectionId]);
            
            // Clean up empty channels
            if (empty($this->subscriptions[$channel])) {
                unset($this->subscriptions[$channel]);
            }
        }

        // Send confirmation
        $this->sendToClient($conn, [
            'type' => 'unsubscription_confirmed',
            'channel' => $channel,
            'timestamp' => now()->toISOString()
        ]);
    }

    private function handleJoinRoom(ConnectionInterface $conn, array $data, ?int $userId): void
    {
        if (!$userId || !isset($data['room'])) {
            $this->sendError($conn, 'Invalid room join request');
            return;
        }

        $room = $data['room'];
        $connectionId = $conn->resourceId;

        // Add to room
        if (!isset($this->rooms[$room])) {
            $this->rooms[$room] = [];
        }

        $this->rooms[$room][$connectionId] = [
            'user_id' => $userId,
            'connection' => $conn,
            'joined_at' => now()
        ];

        // Send confirmation
        $this->sendToClient($conn, [
            'type' => 'room_joined',
            'room' => $room,
            'user_count' => count($this->rooms[$room]),
            'timestamp' => now()->toISOString()
        ]);

        // Notify room members
        $this->broadcastToRoom($room, [
            'type' => 'user_joined_room',
            'room' => $room,
            'user_id' => $userId,
            'user_count' => count($this->rooms[$room]),
            'timestamp' => now()->toISOString()
        ], $connectionId);

        Log::info('User joined room', [
            'user_id' => $userId,
            'room' => $room,
            'connection_id' => $connectionId
        ]);
    }

    private function handleLeaveRoom(ConnectionInterface $conn, array $data, ?int $userId): void
    {
        if (!isset($data['room'])) {
            $this->sendError($conn, 'Invalid room leave request');
            return;
        }

        $room = $data['room'];
        $connectionId = $conn->resourceId;

        // Remove from room
        if (isset($this->rooms[$room][$connectionId])) {
            unset($this->rooms[$room][$connectionId]);
            
            // Clean up empty rooms
            if (empty($this->rooms[$room])) {
                unset($this->rooms[$room]);
            }
        }

        // Send confirmation
        $this->sendToClient($conn, [
            'type' => 'room_left',
            'room' => $room,
            'timestamp' => now()->toISOString()
        ]);

        // Notify room members
        $this->broadcastToRoom($room, [
            'type' => 'user_left_room',
            'room' => $room,
            'user_id' => $userId,
            'user_count' => isset($this->rooms[$room]) ? count($this->rooms[$room]) : 0,
            'timestamp' => now()->toISOString()
        ]);

        Log::info('User left room', [
            'user_id' => $userId,
            'room' => $room,
            'connection_id' => $connectionId
        ]);
    }

    private function handleChatMessage(ConnectionInterface $conn, array $data, ?int $userId): void
    {
        if (!$userId || !isset($data['room']) || !isset($data['message'])) {
            $this->sendError($conn, 'Invalid chat message');
            return;
        }

        $room = $data['room'];
        $message = $data['message'];
        $messageType = $data['message_type'] ?? 'text';

        // Validate message
        if (strlen($message) > 1000) {
            $this->sendError($conn, 'Message too long');
            return;
        }

        // Create message object
        $chatMessage = [
            'type' => 'chat_message',
            'room' => $room,
            'user_id' => $userId,
            'message' => $message,
            'message_type' => $messageType,
            'timestamp' => now()->toISOString(),
            'message_id' => uniqid('msg_')
        ];

        // Broadcast to room
        $this->broadcastToRoom($room, $chatMessage);

        // Store message in database
        $this->storeChatMessage($chatMessage);

        Log::info('Chat message sent', [
            'user_id' => $userId,
            'room' => $room,
            'message_id' => $chatMessage['message_id']
        ]);
    }

    private function handlePropertyUpdate(ConnectionInterface $conn, array $data, ?int $userId): void
    {
        if (!$userId || !isset($data['property_id']) || !isset($data['update_data'])) {
            $this->sendError($conn, 'Invalid property update');
            return;
        }

        $propertyId = $data['property_id'];
        $updateData = $data['update_data'];

        // Validate user has permission to update property
        if (!$this->canUserUpdateProperty($userId, $propertyId)) {
            $this->sendError($conn, 'Permission denied');
            return;
        }

        // Broadcast property update
        $updateMessage = [
            'type' => 'property_update',
            'property_id' => $propertyId,
            'update_data' => $updateData,
            'updated_by' => $userId,
            'timestamp' => now()->toISOString()
        ];

        // Broadcast to property subscribers
        $this->broadcastToChannel("property_{$propertyId}", $updateMessage);

        // Also broadcast to general property updates channel
        $this->broadcastToChannel('property_updates', $updateMessage);

        Log::info('Property update broadcast', [
            'user_id' => $userId,
            'property_id' => $propertyId,
            'update_type' => array_keys($updateData)
        ]);
    }

    private function handleAuctionBid(ConnectionInterface $conn, array $data, ?int $userId): void
    {
        if (!$userId || !isset($data['auction_id']) || !isset($data['bid_amount'])) {
            $this->sendError($conn, 'Invalid auction bid');
            return;
        }

        $auctionId = $data['auction_id'];
        $bidAmount = $data['bid_amount'];

        // Validate bid
        if (!$this->validateAuctionBid($auctionId, $userId, $bidAmount)) {
            $this->sendError($conn, 'Invalid bid');
            return;
        }

        // Create bid message
        $bidMessage = [
            'type' => 'auction_bid',
            'auction_id' => $auctionId,
            'user_id' => $userId,
            'bid_amount' => $bidAmount,
            'timestamp' => now()->toISOString(),
            'bid_id' => uniqid('bid_')
        ];

        // Broadcast to auction room
        $this->broadcastToRoom("auction_{$auctionId}", $bidMessage);

        // Also broadcast to general auction updates
        $this->broadcastToChannel('auction_updates', $bidMessage);

        // Store bid
        $this->storeAuctionBid($bidMessage);

        Log::info('Auction bid placed', [
            'user_id' => $userId,
            'auction_id' => $auctionId,
            'bid_amount' => $bidAmount,
            'bid_id' => $bidMessage['bid_id']
        ]);
    }

    private function handleHeartbeat(ConnectionInterface $conn, ?int $userId): void
    {
        if ($userId) {
            // Update user last seen
            Cache::put("user_heartbeat_{$userId}", now(), 300); // 5 minutes
            
            $this->sendToClient($conn, [
                'type' => 'heartbeat_response',
                'timestamp' => now()->toISOString(),
                'server_time' => time()
            ]);
        }
    }

    // Broadcasting Methods
    public function broadcastToChannel(string $channel, array $data): void
    {
        if (!isset($this->subscriptions[$channel])) {
            return;
        }

        foreach ($this->subscriptions[$channel] as $conn) {
            $this->sendToClient($conn, $data);
        }
    }

    public function broadcastToRoom(string $room, array $data, $excludeConnectionId = null): void
    {
        if (!isset($this->rooms[$room])) {
            return;
        }

        foreach ($this->rooms[$room] as $connectionId => $roomData) {
            if ($connectionId !== $excludeConnectionId) {
                $this->sendToClient($roomData['connection'], $data);
            }
        }
    }

    public function broadcastToOthers(ConnectionInterface $conn, array $data, $excludeUserId = null): void
    {
        foreach ($this->clients as $client) {
            $userId = $this->getUserIdFromConnection($client);
            
            if ($client !== $conn && $userId !== $excludeUserId) {
                $this->sendToClient($client, $data);
            }
        }
    }

    public function broadcast(array $data): void
    {
        foreach ($this->clients as $client) {
            $this->sendToClient($client, $data);
        }
    }

    // Utility Methods
    private function sendToClient(ConnectionInterface $conn, array $data): void
    {
        try {
            $conn->send(json_encode($data));
        } catch (\Exception $e) {
            Log::error('Failed to send message to client', [
                'error' => $e->getMessage(),
                'connection_id' => $conn->resourceId
            ]);
        }
    }

    private function sendError(ConnectionInterface $conn, string $message): void
    {
        $this->sendToClient($conn, [
            'type' => 'error',
            'message' => $message,
            'timestamp' => now()->toISOString()
        ]);
    }

    private function getUserIdFromConnection(ConnectionInterface $conn): ?int
    {
        foreach ($this->userConnections as $userId => $connection) {
            if ($connection === $conn) {
                return $userId;
            }
        }
        return null;
    }

    private function authenticateConnection(?int $userId, ?string $token): bool
    {
        if (!$userId || !$token) {
            return false;
        }

        // Verify token (implement your token validation logic)
        return $this->validateWebSocketToken($userId, $token);
    }

    private function validateWebSocketToken(int $userId, string $token): bool
    {
        // Check if token exists in cache
        $cachedToken = Cache::get("ws_token_{$userId}");
        
        return $cachedToken && hash_equals($cachedToken, $token);
    }

    private function updateUserOnlineStatus(int $userId, bool $online): void
    {
        Cache::put("user_online_{$userId}", $online, 300);
        
        if ($online) {
            Cache::put("user_last_seen_{$userId}", now(), 3600);
        }

        // Update database
        DB::table('users')
            ->where('id', $userId)
            ->update(['online' => $online, 'last_seen_at' => now()]);
    }

    private function removeAllUserSubscriptions(int $userId): void
    {
        $connectionId = null;
        
        foreach ($this->userConnections as $uid => $conn) {
            if ($uid === $userId) {
                $connectionId = $conn->resourceId;
                break;
            }
        }

        if ($connectionId) {
            foreach ($this->subscriptions as $channel => $connections) {
                if (isset($connections[$connectionId])) {
                    unset($this->subscriptions[$channel][$connectionId]);
                    
                    if (empty($this->subscriptions[$channel])) {
                        unset($this->subscriptions[$channel]);
                    }
                }
            }
        }
    }

    private function removeUserFromAllRooms(int $userId): void
    {
        foreach ($this->rooms as $room => $members) {
            foreach ($members as $connectionId => $memberData) {
                if ($memberData['user_id'] === $userId) {
                    unset($this->rooms[$room][$connectionId]);
                    
                    if (empty($this->rooms[$room])) {
                        unset($this->rooms[$room]);
                    }
                    break;
                }
            }
        }
    }

    private function canUserUpdateProperty(int $userId, int $propertyId): bool
    {
        // Implement property permission check
        return true; // Simplified for demo
    }

    private function validateAuctionBid(int $auctionId, int $userId, float $bidAmount): bool
    {
        // Implement auction bid validation
        return true; // Simplified for demo
    }

    private function storeChatMessage(array $message): void
    {
        // Store chat message in database
        // Implementation would go here
    }

    private function storeAuctionBid(array $bid): void
    {
        // Store auction bid in database
        // Implementation would go here
    }

    // Static method to start the server
    public static function startServer(int $port = 8080): void
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new self()
                )
            ),
            $port
        );

        Log::info("WebSocket server started on port {$port}");
        $server->run();
    }

    // Get server statistics
    public function getServerStats(): array
    {
        return [
            'total_connections' => count($this->clients),
            'total_subscriptions' => array_sum(array_map('count', $this->subscriptions)),
            'total_rooms' => count($this->rooms),
            'room_members' => array_map('count', $this->rooms),
            'online_users' => count($this->userConnections),
            'subscriptions_by_channel' => array_map('count', $this->subscriptions)
        ];
    }
}
