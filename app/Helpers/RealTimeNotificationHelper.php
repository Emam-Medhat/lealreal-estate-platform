<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class RealTimeNotificationHelper
{
    /**
     * Send a real-time notification to a user
     */
    public static function notify($userId, $title, $message, $icon = 'bell', $color = 'blue')
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        // Create database notification using Laravel's notification system
        $notificationData = [
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'color' => $color,
            'timestamp' => now()->toISOString()
        ];

        // Create notification manually in database
        $notification = DatabaseNotification::create([
            'id' => \Str::uuid(),
            'type' => 'App\\Notifications\\RealTimeNotification',
            'notifiable_type' => get_class($user),
            'notifiable_id' => $user->id,
            'data' => $notificationData,
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Trigger real-time update via localStorage (WebSocket simulation)
        self::triggerRealTimeUpdate($userId, $notificationData);

        return $notification;
    }

    /**
     * Notify multiple users
     */
    public static function notifyUsers($userIds, $title, $message, $icon = 'bell', $color = 'blue')
    {
        $notifications = [];
        foreach ($userIds as $userId) {
            $notifications[] = self::notify($userId, $title, $message, $icon, $color);
        }
        return $notifications;
    }

    /**
     * Notify all admin users
     */
    public static function notifyAdmins($title, $message, $icon = 'bell', $color = 'red')
    {
        $adminUsers = User::where('user_type', 'admin')->pluck('id');
        return self::notifyUsers($adminUsers, $title, $message, $icon, $color);
    }

    /**
     * Notify current authenticated user
     */
    public static function notifyCurrentUser($title, $message, $icon = 'bell', $color = 'blue')
    {
        if (Auth::check()) {
            return self::notify(Auth::id(), $title, $message, $icon, $color);
        }
        return false;
    }

    /**
     * Property created notification
     */
    public static function propertyCreated($propertyId, $propertyTitle, $agentId)
    {
        // Notify the agent who created the property
        self::notify($agentId, 
            'عقار جديد تم إضافته', 
            "تم إضافة العقار '{$propertyTitle}' بنجاح",
            'home',
            'green'
        );

        // Notify all admin users
        self::notifyAdmins(
            'عقار جديد في النظام',
            "قام الوكيل بإضافة عقار جديد: {$propertyTitle}",
            'building',
            'blue'
        );
    }

    /**
     * Property updated notification
     */
    public static function propertyUpdated($propertyId, $propertyTitle, $agentId)
    {
        self::notify($agentId,
            'تم تحديث العقار',
            "تم تحديث معلومات العقار '{$propertyTitle}'",
            'edit',
            'amber'
        );
    }

    /**
     * New inquiry notification
     */
    public static function newInquiry($propertyId, $propertyTitle, $agentId, $customerName)
    {
        self::notify($agentId,
            'استفسار جديد',
            "قام {$customerName} بالاستفسار عن العقار '{$propertyTitle}'",
            'envelope',
            'purple'
        );
    }

    /**
     * User registration notification
     */
    public static function userRegistered($userName, $userId)
    {
        self::notify($userId,
            'مرحباً بك في منصة عقاري',
            'تم تسجيل حسابك بنجاح. ابدأ باستكشاف المنصة الآن!',
            'user',
            'green'
        );

        self::notifyAdmins(
            'مستخدم جديد',
            "قام {$userName} بالتسجيل في المنصة",
            'user-plus',
            'blue'
        );
    }

    /**
     * System maintenance notification
     */
    public static function systemMaintenance($message)
    {
        self::notifyAdmins(
            'صيانة النظام',
            $message,
            'tools',
            'orange'
        );
    }

    /**
     * Trigger real-time update simulation
     */
    private static function triggerRealTimeUpdate($userId, $notificationData)
    {
        // In a real WebSocket implementation, this would push through WebSocket
        // For now, we simulate with localStorage events
        $eventData = [
            'user_id' => $userId,
            'notification' => $notificationData,
            'timestamp' => now()->toISOString()
        ];

        // Store in cache for real-time polling
        \Cache::put("realtime_notification_{$userId}", $eventData, 60); // 1 minute
    }

    /**
     * Get real-time notifications for a user
     */
    public static function getRealTimeNotifications($userId)
    {
        return \Cache::get("realtime_notification_{$userId}");
    }

    /**
     * Clear real-time notifications for a user
     */
    public static function clearRealTimeNotifications($userId)
    {
        \Cache::forget("realtime_notification_{$userId}");
    }
}
