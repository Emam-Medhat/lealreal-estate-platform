<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RealTimeNotificationService
{
    private const NOTIFICATION_TYPES = [
        'info' => ['priority' => 1, 'color' => 'blue', 'icon' => 'info'],
        'success' => ['priority' => 2, 'color' => 'green', 'icon' => 'check'],
        'warning' => ['priority' => 3, 'color' => 'yellow', 'icon' => 'warning'],
        'error' => ['priority' => 4, 'color' => 'red', 'icon' => 'error'],
        'critical' => ['priority' => 5, 'color' => 'purple', 'icon' => 'critical'],
    ];

    private const DELIVERY_CHANNELS = [
        'websocket' => 'real-time',
        'push' => 'mobile_push',
        'email' => 'email',
        'sms' => 'sms',
        'in_app' => 'in_app'
    ];

    private $webSocketService;

    public function __construct(WebSocketService $webSocketService = null)
    {
        $this->webSocketService = $webSocketService;
    }

    public function sendNotification(array $notificationData): array
    {
        try {
            // Validate notification data
            $validatedData = $this->validateNotificationData($notificationData);
            
            // Create notification record
            $notification = $this->createNotification($validatedData);
            
            // Determine delivery channels
            $channels = $this->determineDeliveryChannels($validatedData);
            
            // Send through each channel
            $results = [];
            foreach ($channels as $channel) {
                $results[$channel] = $this->sendThroughChannel($channel, $notification, $validatedData);
            }
            
            // Update notification status
            $this->updateNotificationStatus($notification, $results);
            
            return [
                'success' => true,
                'notification_id' => $notification->id,
                'delivery_results' => $results,
                'channels_used' => $channels,
                'sent_at' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send notification', [
                'error' => $e->getMessage(),
                'notification_data' => $notificationData
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send notification',
                'error' => $e->getMessage()
            ];
        }
    }

    public function sendToMultipleUsers(array $userIds, array $notificationData): array
    {
        $results = [];
        $batchId = uniqid('batch_');
        
        foreach ($userIds as $userId) {
            $userNotificationData = array_merge($notificationData, [
                'user_id' => $userId,
                'batch_id' => $batchId
            ]);
            
            $results[$userId] = $this->sendNotification($userNotificationData);
        }
        
        return [
            'success' => true,
            'batch_id' => $batchId,
            'total_users' => count($userIds),
            'results' => $results,
            'summary' => $this->generateBatchSummary($results)
        ];
    }

    public function sendBroadcast(array $notificationData, array $filters = []): array
    {
        try {
            // Get target users based on filters
            $targetUsers = $this->getTargetUsers($filters);
            
            if ($targetUsers->isEmpty()) {
                return [
                    'success' => true,
                    'message' => 'No users found for broadcast',
                    'target_count' => 0
                ];
            }

            $userIds = $targetUsers->pluck('id')->toArray();
            
            // Send to all target users
            $result = $this->sendToMultipleUsers($userIds, $notificationData);
            
            return array_merge($result, [
                'broadcast_type' => 'filtered',
                'filters_applied' => $filters,
                'target_count' => count($userIds)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send broadcast', [
                'error' => $e->getMessage(),
                'filters' => $filters
            ]);

            return [
                'success' => false,
                'message' => 'Broadcast failed',
                'error' => $e->getMessage()
            ];
        }
    }

    public function sendScheduledNotification(array $notificationData, Carbon $scheduleAt): array
    {
        try {
            // Create scheduled notification
            $scheduledNotification = $this->createScheduledNotification($notificationData, $scheduleAt);
            
            // Queue the notification
            Queue::later($scheduleAt, new \App\Jobs\SendScheduledNotification($scheduledNotification));
            
            return [
                'success' => true,
                'scheduled_notification_id' => $scheduledNotification->id,
                'scheduled_at' => $scheduleAt->toISOString(),
                'message' => 'Notification scheduled successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to schedule notification', [
                'error' => $e->getMessage(),
                'notification_data' => $notificationData,
                'schedule_at' => $scheduleAt
            ]);

            return [
                'success' => false,
                'message' => 'Failed to schedule notification',
                'error' => $e->getMessage()
            ];
        }
    }

    public function sendRealTimeUpdate(array $updateData): array
    {
        try {
            // Real-time updates are sent immediately via WebSocket
            $channel = $updateData['channel'] ?? 'global';
            $event = $updateData['event'] ?? 'update';
            $data = $updateData['data'] ?? [];
            
            $realTimeMessage = [
                'type' => 'real_time_update',
                'channel' => $channel,
                'event' => $event,
                'data' => $data,
                'timestamp' => now()->toISOString(),
                'update_id' => uniqid('update_')
            ];

            // Send via WebSocket
            if ($this->webSocketService) {
                $this->webSocketService->broadcastToChannel($channel, $realTimeMessage);
            }

            // Also send to specific users if specified
            if (isset($updateData['target_users'])) {
                foreach ($updateData['target_users'] as $userId) {
                    $this->sendToUserWebSocket($userId, $realTimeMessage);
                }
            }

            return [
                'success' => true,
                'update_id' => $realTimeMessage['update_id'],
                'channel' => $channel,
                'event' => $event,
                'sent_at' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send real-time update', [
                'error' => $e->getMessage(),
                'update_data' => $updateData
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send real-time update',
                'error' => $e->getMessage()
            ];
        }
    }

    public function sendPropertyUpdateNotification(int $propertyId, array $updateData, ?int $updatedBy = null): array
    {
        $notificationData = [
            'type' => 'property_update',
            'title' => 'Property Updated',
            'message' => $this->generatePropertyUpdateMessage($updateData),
            'data' => [
                'property_id' => $propertyId,
                'updates' => $updateData,
                'updated_by' => $updatedBy
            ],
            'priority' => 'info',
            'channels' => ['websocket', 'in_app']
        ];

        // Get users interested in this property
        $interestedUsers = $this->getPropertyInterestedUsers($propertyId);
        
        if ($interestedUsers->isNotEmpty()) {
            return $this->sendToMultipleUsers(
                $interestedUsers->pluck('id')->toArray(),
                $notificationData
            );
        }

        // Send to general property updates channel
        return $this->sendRealTimeUpdate([
            'channel' => "property_{$propertyId}",
            'event' => 'property_updated',
            'data' => $notificationData
        ]);
    }

    public function sendAuctionUpdateNotification(int $auctionId, array $auctionData): array
    {
        $notificationData = [
            'type' => 'auction_update',
            'title' => 'Auction Update',
            'message' => $this->generateAuctionUpdateMessage($auctionData),
            'data' => [
                'auction_id' => $auctionId,
                'auction_data' => $auctionData
            ],
            'priority' => 'high',
            'channels' => ['websocket', 'in_app', 'push']
        ];

        // Get users participating in auction
        $participants = $this->getAuctionParticipants($auctionId);
        
        if ($participants->isNotEmpty()) {
            return $this->sendToMultipleUsers(
                $participants->pluck('id')->toArray(),
                $notificationData
            );
        }

        // Send to general auction updates channel
        return $this->sendRealTimeUpdate([
            'channel' => "auction_{$auctionId}",
            'event' => 'auction_updated',
            'data' => $notificationData
        ]);
    }

    public function sendSystemNotification(array $systemData): array
    {
        $notificationData = [
            'type' => 'system',
            'title' => $systemData['title'] ?? 'System Notification',
            'message' => $systemData['message'] ?? 'System update',
            'data' => $systemData,
            'priority' => $systemData['priority'] ?? 'info',
            'channels' => ['websocket', 'in_app', 'email']
        ];

        // Send to all active users
        return $this->sendBroadcast($notificationData, [
            'status' => 'active',
            'notifications_enabled' => true
        ]);
    }

    public function getUserNotifications(int $userId, array $options = []): array
    {
        try {
            $query = UserNotification::where('user_id', $userId)
                ->with('notification')
                ->orderBy('created_at', 'desc');

            // Apply filters
            if (isset($options['type'])) {
                $query->whereHas('notification', function($q) use ($options) {
                    $q->where('type', $options['type']);
                });
            }

            if (isset($options['read'])) {
                $query->where('read_at', $options['read'] ? '!=' : '=', null);
            }

            if (isset($options['priority'])) {
                $query->whereHas('notification', function($q) use ($options) {
                    $q->where('priority', $options['priority']);
                });
            }

            // Pagination
            $limit = $options['limit'] ?? 20;
            $offset = $options['offset'] ?? 0;

            $notifications = $query->limit($limit)->offset($offset)->get();
            $unreadCount = UserNotification::where('user_id', $userId)->whereNull('read_at')->count();

            return [
                'success' => true,
                'notifications' => $notifications->map(function($userNotif) {
                    return [
                        'id' => $userNotif->id,
                        'notification_id' => $userNotif->notification_id,
                        'type' => $userNotif->notification->type,
                        'title' => $userNotif->notification->title,
                        'message' => $userNotif->notification->message,
                        'data' => $userNotif->notification->data,
                        'priority' => $userNotif->notification->priority,
                        'read_at' => $userNotif->read_at,
                        'created_at' => $userNotif->created_at->toISOString(),
                        'meta' => $this->getNotificationMeta($userNotif->notification)
                    ];
                }),
                'unread_count' => $unreadCount,
                'total_count' => $query->count(),
                'has_more' => ($offset + $limit) < $query->count()
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get user notifications', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to retrieve notifications',
                'error' => $e->getMessage()
            ];
        }
    }

    public function markAsRead(int $userId, array $notificationIds): array
    {
        try {
            $updated = UserNotification::where('user_id', $userId)
                ->whereIn('id', $notificationIds)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return [
                'success' => true,
                'marked_as_read' => $updated,
                'notification_ids' => $notificationIds
            ];
        } catch (\Exception $e) {
            Log::error('Failed to mark notifications as read', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'notification_ids' => $notificationIds
            ]);

            return [
                'success' => false,
                'message' => 'Failed to mark notifications as read',
                'error' => $e->getMessage()
            ];
        }
    }

    public function markAllAsRead(int $userId): array
    {
        try {
            $updated = UserNotification::where('user_id', $userId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return [
                'success' => true,
                'marked_as_read' => $updated
            ];
        } catch (\Exception $e) {
            Log::error('Failed to mark all notifications as read', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to mark all notifications as read',
                'error' => $e->getMessage()
            ];
        }
    }

    public function getNotificationSettings(int $userId): array
    {
        try {
            $user = User::findOrFail($userId);
            
            return [
                'success' => true,
                'settings' => [
                    'email_notifications' => $user->email_notifications ?? true,
                    'push_notifications' => $user->push_notifications ?? true,
                    'sms_notifications' => $user->sms_notifications ?? false,
                    'in_app_notifications' => $user->in_app_notifications ?? true,
                    'real_time_updates' => $user->real_time_updates ?? true,
                    'notification_types' => $this->getUserNotificationTypeSettings($userId),
                    'quiet_hours' => $this->getUserQuietHours($userId),
                    'frequency_limits' => $this->getUserFrequencyLimits($userId)
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get notification settings',
                'error' => $e->getMessage()
            ];
        }
    }

    public function updateNotificationSettings(int $userId, array $settings): array
    {
        try {
            $user = User::findOrFail($userId);
            
            $user->update([
                'email_notifications' => $settings['email_notifications'] ?? $user->email_notifications,
                'push_notifications' => $settings['push_notifications'] ?? $user->push_notifications,
                'sms_notifications' => $settings['sms_notifications'] ?? $user->sms_notifications,
                'in_app_notifications' => $settings['in_app_notifications'] ?? $user->in_app_notifications,
                'real_time_updates' => $settings['real_time_updates'] ?? $user->real_time_updates,
            ]);

            // Update additional settings
            $this->updateUserNotificationTypeSettings($userId, $settings['notification_types'] ?? []);
            $this->updateUserQuietHours($userId, $settings['quiet_hours'] ?? []);
            $this->updateUserFrequencyLimits($userId, $settings['frequency_limits'] ?? []);

            return [
                'success' => true,
                'message' => 'Notification settings updated successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to update notification settings', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'settings' => $settings
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update notification settings',
                'error' => $e->getMessage()
            ];
        }
    }

    // Private helper methods
    private function validateNotificationData(array $data): array
    {
        $required = ['title', 'message', 'type'];
        
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        if (!isset(self::NOTIFICATION_TYPES[$data['type']])) {
            throw new \InvalidArgumentException("Invalid notification type: {$data['type']}");
        }

        return $data;
    }

    private function createNotification(array $data): Notification
    {
        return Notification::create([
            'title' => $data['title'],
            'message' => $data['message'],
            'type' => $data['type'],
            'priority' => $data['priority'] ?? 'info',
            'data' => $data['data'] ?? [],
            'channels' => $data['channels'] ?? ['in_app'],
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'created_at' => now()
        ]);
    }

    private function determineDeliveryChannels(array $data): array
    {
        $channels = $data['channels'] ?? ['in_app'];
        
        // Add WebSocket for real-time notifications
        if (in_array('real_time', $channels) || $data['real_time'] ?? false) {
            $channels[] = 'websocket';
        }

        return array_unique($channels);
    }

    private function sendThroughChannel(string $channel, Notification $notification, array $data): array
    {
        try {
            switch ($channel) {
                case 'websocket':
                    return $this->sendWebSocketNotification($notification, $data);
                case 'push':
                    return $this->sendPushNotification($notification, $data);
                case 'email':
                    return $this->sendEmailNotification($notification, $data);
                case 'sms':
                    return $this->sendSMSNotification($notification, $data);
                case 'in_app':
                    return $this->sendInAppNotification($notification, $data);
                default:
                    return ['success' => false, 'message' => "Unknown channel: {$channel}"];
            }
        } catch (\Exception $e) {
            Log::error("Failed to send notification via {$channel}", [
                'error' => $e->getMessage(),
                'notification_id' => $notification->id
            ]);

            return [
                'success' => false,
                'message' => "Failed to send via {$channel}",
                'error' => $e->getMessage()
            ];
        }
    }

    private function sendWebSocketNotification(Notification $notification, array $data): array
    {
        if (!$this->webSocketService) {
            return ['success' => false, 'message' => 'WebSocket service not available'];
        }

        $message = [
            'type' => 'notification',
            'notification_id' => $notification->id,
            'title' => $notification->title,
            'message' => $notification->message,
            'data' => $notification->data,
            'priority' => $notification->priority,
            'timestamp' => $notification->created_at->toISOString()
        ];

        // Send to specific user if user_id is provided
        if (isset($data['user_id'])) {
            $this->sendToUserWebSocket($data['user_id'], $message);
        } else {
            // Broadcast to general notifications channel
            $this->webSocketService->broadcastToChannel('notifications', $message);
        }

        return ['success' => true, 'sent_via' => 'websocket'];
    }

    private function sendPushNotification(Notification $notification, array $data): array
    {
        // Implementation for push notifications
        return ['success' => true, 'sent_via' => 'push'];
    }

    private function sendEmailNotification(Notification $notification, array $data): array
    {
        // Implementation for email notifications
        return ['success' => true, 'sent_via' => 'email'];
    }

    private function sendSMSNotification(Notification $notification, array $data): array
    {
        // Implementation for SMS notifications
        return ['success' => true, 'sent_via' => 'sms'];
    }

    private function sendInAppNotification(Notification $notification, array $data): array
    {
        if (!isset($data['user_id'])) {
            return ['success' => false, 'message' => 'User ID required for in-app notifications'];
        }

        UserNotification::create([
            'user_id' => $data['user_id'],
            'notification_id' => $notification->id,
            'created_at' => now()
        ]);

        return ['success' => true, 'sent_via' => 'in_app'];
    }

    private function updateNotificationStatus(Notification $notification, array $results): void
    {
        $successfulChannels = array_keys(array_filter($results, fn($r) => $r['success']));
        
        $notification->update([
            'status' => !empty($successfulChannels) ? 'sent' : 'failed',
            'sent_channels' => $successfulChannels,
            'sent_at' => !empty($successfulChannels) ? now() : null
        ]);
    }

    private function sendToUserWebSocket(int $userId, array $message): void
    {
        if ($this->webSocketService) {
            $this->webSocketService->broadcastToChannel("user_{$userId}", $message);
        }
    }

    private function getTargetUsers(array $filters): \Illuminate\Database\Eloquent\Collection
    {
        $query = User::where('status', 'active');

        // Apply filters
        if (isset($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (isset($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        if (isset($filters['notifications_enabled'])) {
            $query->where('notifications_enabled', $filters['notifications_enabled']);
        }

        return $query->get();
    }

    private function generateBatchSummary(array $results): array
    {
        $total = count($results);
        $successful = count(array_filter($results, fn($r) => $r['success']));
        
        return [
            'total' => $total,
            'successful' => $successful,
            'failed' => $total - $successful,
            'success_rate' => $total > 0 ? ($successful / $total) * 100 : 0
        ];
    }

    private function createScheduledNotification(array $data, Carbon $scheduleAt): Notification
    {
        return Notification::create([
            'title' => $data['title'],
            'message' => $data['message'],
            'type' => $data['type'],
            'priority' => $data['priority'] ?? 'info',
            'data' => $data['data'] ?? [],
            'channels' => $data['channels'] ?? ['in_app'],
            'scheduled_at' => $scheduleAt,
            'status' => 'scheduled',
            'created_at' => now()
        ]);
    }

    private function generatePropertyUpdateMessage(array $updateData): string
    {
        $updates = array_keys($updateData);
        return 'Property updated: ' . implode(', ', $updates);
    }

    private function generateAuctionUpdateMessage(array $auctionData): string
    {
        if (isset($auctionData['new_bid'])) {
            return "New bid placed: {$auctionData['new_bid']}";
        }
        
        return 'Auction status updated';
    }

    private function getPropertyInterestedUsers(int $propertyId): \Illuminate\Database\Eloquent\Collection
    {
        // Get users who have shown interest in this property
        return User::whereHas('savedProperties', function($query) use ($propertyId) {
            $query->where('property_id', $propertyId);
        })->get();
    }

    private function getAuctionParticipants(int $auctionId): \Illuminate\Database\Eloquent\Collection
    {
        // Get users participating in this auction
        return User::whereHas('auctionBids', function($query) use ($auctionId) {
            $query->where('auction_id', $auctionId);
        })->get();
    }

    private function getNotificationMeta(Notification $notification): array
    {
        $typeConfig = self::NOTIFICATION_TYPES[$notification->type] ?? [];
        
        return [
            'color' => $typeConfig['color'] ?? 'gray',
            'icon' => $typeConfig['icon'] ?? 'notification',
            'priority' => $typeConfig['priority'] ?? 1
        ];
    }

    // Additional helper methods for user settings
    private function getUserNotificationTypeSettings(int $userId): array
    {
        // Implementation for user notification type preferences
        return [];
    }

    private function getUserQuietHours(int $userId): array
    {
        // Implementation for user quiet hours
        return [];
    }

    private function getUserFrequencyLimits(int $userId): array
    {
        // Implementation for user frequency limits
        return [];
    }

    private function updateUserNotificationTypeSettings(int $userId, array $settings): void
    {
        // Implementation for updating user notification type settings
    }

    private function updateUserQuietHours(int $userId, array $settings): void
    {
        // Implementation for updating user quiet hours
    }

    private function updateUserFrequencyLimits(int $userId, array $settings): void
    {
        // Implementation for updating user frequency limits
    }
}
