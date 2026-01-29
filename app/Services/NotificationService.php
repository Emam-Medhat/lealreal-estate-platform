<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

class NotificationService
{
    /**
     * Send notification to user
     */
    public static function send(User $user, string $type, array $data, string $title = null, string $message = null)
    {
        $notification = [
            'type' => $type,
            'data' => array_merge($data, [
                'title' => $title,
                'message' => $message,
                'timestamp' => now()->toISOString(),
            ]),
        ];

        return $user->notify(new \App\Notifications\GenericNotification($notification));
    }

    /**
     * Send notification to multiple users
     */
    public static function sendToUsers(array $users, string $type, array $data, string $title = null, string $message = null)
    {
        foreach ($users as $user) {
            self::send($user, $type, $data, $title, $message);
        }
    }

    /**
     * Send property notification
     */
    public static function propertyNotification(User $user, string $action, $property, array $extraData = [])
    {
        $data = array_merge([
            'property_id' => $property->id,
            'property_title' => $property->title ?? 'Property',
            'action' => $action,
        ], $extraData);

        $title = self::getPropertyNotificationTitle($action, $property);
        $message = self::getPropertyNotificationMessage($action, $property);

        return self::send($user, 'property_' . $action, $data, $title, $message);
    }

    /**
     * Send wallet notification
     */
    public static function walletNotification(User $user, string $action, array $transactionData = [])
    {
        $data = array_merge([
            'action' => $action,
            'amount' => $transactionData['amount'] ?? 0,
            'currency' => $transactionData['currency'] ?? 'USD',
        ], $transactionData);

        $title = self::getWalletNotificationTitle($action, $transactionData);
        $message = self::getWalletNotificationMessage($action, $transactionData);

        return self::send($user, 'wallet_' . $action, $data, $title, $message);
    }

    /**
     * Send system notification
     */
    public static function systemNotification(User $user, string $title, string $message, array $data = [])
    {
        return self::send($user, 'system', $data, $title, $message);
    }

    /**
     * Get unread notifications count for user
     */
    public static function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    /**
     * Mark notification as read
     */
    public static function markAsRead(DatabaseNotification $notification): bool
    {
        return $notification->markAsRead();
    }

    /**
     * Mark all notifications as read for user
     */
    public static function markAllAsRead(User $user): int
    {
        return $user->notifications()->whereNull('read_at')->update(['read_at' => now()]);
    }

    /**
     * Get recent notifications for user
     */
    public static function getRecent(User $user, int $limit = 10)
    {
        return $user->notifications()->latest()->limit($limit)->get();
    }

    /**
     * Get property notification title
     */
    private static function getPropertyNotificationTitle(string $action, $property): string
    {
        $titles = [
            'favorited' => 'Property Added to Favorites',
            'viewed' => 'Property Viewed',
            'inquiry' => 'New Property Inquiry',
            'comparison' => 'Property Compared',
            'price_change' => 'Property Price Changed',
            'status_change' => 'Property Status Updated',
            'new_property' => 'New Property Listed',
        ];

        return $titles[$action] ?? 'Property Notification';
    }

    /**
     * Get property notification message
     */
    private static function getPropertyNotificationMessage(string $action, $property): string
    {
        $propertyTitle = $property->title ?? 'Property';
        
        $messages = [
            'favorited' => "You added {$propertyTitle} to your favorites",
            'viewed' => "You viewed {$propertyTitle}",
            'inquiry' => "New inquiry received for {$propertyTitle}",
            'comparison' => "{$propertyTitle} added to comparison",
            'price_change' => "Price updated for {$propertyTitle}",
            'status_change' => "Status changed for {$propertyTitle}",
            'new_property' => "New property: {$propertyTitle}",
        ];

        return $messages[$action] ?? "Property notification for {$propertyTitle}";
    }

    /**
     * Get wallet notification title
     */
    private static function getWalletNotificationTitle(string $action, array $data): string
    {
        $titles = [
            'deposit' => 'Deposit Successful',
            'withdrawal' => 'Withdrawal Processed',
            'transfer_received' => 'Transfer Received',
            'transfer_sent' => 'Transfer Sent',
            'balance_low' => 'Low Balance Warning',
        ];

        return $titles[$action] ?? 'Wallet Notification';
    }

    /**
     * Get wallet notification message
     */
    private static function getWalletNotificationMessage(string $action, array $data): string
    {
        $amount = $data['amount'] ?? 0;
        $currency = $data['currency'] ?? 'USD';
        
        $messages = [
            'deposit' => "Deposited {$amount} {$currency} to your wallet",
            'withdrawal' => "Withdrew {$amount} {$currency} from your wallet",
            'transfer_received' => "Received {$amount} {$currency} transfer",
            'transfer_sent' => "Sent {$amount} {$currency} transfer",
            'balance_low' => "Your wallet balance is running low",
        ];

        return $messages[$action] ?? "Wallet notification";
    }
}
