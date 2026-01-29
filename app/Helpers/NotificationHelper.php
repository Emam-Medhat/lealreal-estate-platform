<?php

namespace App\Helpers;

use App\Models\UserPreference;
use Illuminate\Support\Facades\Auth;

class NotificationHelper
{
    /**
     * Check if user has enabled email notifications
     */
    public static function canSendEmail($userId = null): bool
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();
        if (!$user) return false;

        $preference = $user->preference;
        return $preference ? $preference->email_notifications : true;
    }

    /**
     * Check if user has enabled SMS notifications
     */
    public static function canSendSMS($userId = null): bool
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();
        if (!$user) return false;

        $preference = $user->preference;
        return $preference ? $preference->sms_notifications : false;
    }

    /**
     * Check if user has enabled push notifications
     */
    public static function canSendPush($userId = null): bool
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();
        if (!$user) return false;

        $preference = $user->preference;
        return $preference ? $preference->push_notifications : true;
    }

    /**
     * Check if user has enabled property alerts
     */
    public static function canSendPropertyAlerts($userId = null): bool
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();
        if (!$user) return false;

        $preference = $user->preference;
        return $preference ? $preference->property_alerts : true;
    }

    /**
     * Check if user has enabled price drop alerts
     */
    public static function canSendPriceDropAlerts($userId = null): bool
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();
        if (!$user) return false;

        $preference = $user->preference;
        return $preference ? $preference->price_drop_alerts : true;
    }

    /**
     * Check if user has enabled new listing alerts
     */
    public static function canSendNewListingAlerts($userId = null): bool
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();
        if (!$user) return false;

        $preference = $user->preference;
        return $preference ? $preference->new_listing_alerts : true;
    }

    /**
     * Send notification based on user preferences
     */
    public static function sendNotification($userId, $title, $message, $type = 'info', $data = [])
    {
        $user = \App\Models\User::find($userId);
        if (!$user) return false;

        $preference = $user->preference;
        if (!$preference) {
            // Create default preference if not exists
            $preference = UserPreference::create([
                'user_id' => $userId,
                'email_notifications' => true,
                'sms_notifications' => false,
                'push_notifications' => true,
            ]);
        }

        $sent = [];

        // Send email notification if enabled
        if ($preference->email_notifications) {
            try {
                // \Mail::to($user->email)->send(new \App\Mail\UserNotification($title, $message, $data));
                $sent['email'] = true;
            } catch (\Exception $e) {
                $sent['email'] = false;
            }
        }

        // Send SMS notification if enabled
        if ($preference->sms_notifications && $user->phone) {
            try {
                // SMS logic here
                $sent['sms'] = true;
            } catch (\Exception $e) {
                $sent['sms'] = false;
            }
        }

        // Send push notification if enabled
        if ($preference->push_notifications) {
            try {
                // Push notification logic here
                $sent['push'] = true;
            } catch (\Exception $e) {
                $sent['push'] = false;
            }
        }

        // Store notification in database
        \App\Models\Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => json_encode($data),
            'sent_via' => json_encode(array_keys($sent)),
        ]);

        return $sent;
    }
}
