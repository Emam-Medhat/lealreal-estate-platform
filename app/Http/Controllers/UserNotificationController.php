<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserNotification;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserNotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $notifications = UserNotification::where('user_id', $user->id)
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->read !== null, function ($query) use ($request) {
                $query->where('is_read', $request->boolean('read'));
            })
            ->latest()
            ->paginate(20);

        $unreadCount = UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return view('user.notifications', compact('notifications', 'unreadCount'));
    }

    public function show(UserNotification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        // Mark as read if not already read
        if (!$notification->is_read) {
            $notification->update(['is_read' => true]);
            
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'read_notification',
                'details' => "Read notification: {$notification->title}",
                'ip_address' => request()->ip(),
            ]);
        }

        return view('user.notification-detail', compact('notification'));
    }

    public function markAsRead(UserNotification $notification): JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $notification->update(['is_read' => true]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'marked_notification_read',
            'details' => "Marked notification as read: {$notification->title}",
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    public function markAsUnread(UserNotification $notification): JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $notification->update(['is_read' => false]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'marked_notification_unread',
            'details' => "Marked notification as unread: {$notification->title}",
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as unread'
        ]);
    }

    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();
        
        $updated = UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'marked_all_notifications_read',
            'details' => "Marked {$updated} notifications as read",
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Marked {$updated} notifications as read"
        ]);
    }

    public function delete(UserNotification $notification): JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $notification->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_notification',
            'details' => "Deleted notification: {$notification->title}",
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully'
        ]);
    }

    public function deleteRead(): JsonResponse
    {
        $user = Auth::user();
        
        $deleted = UserNotification::where('user_id', $user->id)
            ->where('is_read', true)
            ->delete();

        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'deleted_read_notifications',
            'details' => "Deleted {$deleted} read notifications",
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Deleted {$deleted} read notifications"
        ]);
    }

    public function deleteAll(): JsonResponse
    {
        $user = Auth::user();
        
        $deleted = UserNotification::where('user_id', $user->id)->delete();

        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'deleted_all_notifications',
            'details' => "Deleted {$deleted} notifications",
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Deleted {$deleted} notifications"
        ]);
    }

    public function getUnreadCount(): JsonResponse
    {
        $count = UserNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    public function getRecentNotifications(Request $request): JsonResponse
    {
        $notifications = UserNotification::where('user_id', Auth::id())
            ->latest()
            ->limit($request->limit ?? 5)
            ->get(['id', 'title', 'message', 'type', 'is_read', 'created_at']);

        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    public function getNotificationSettings(): JsonResponse
    {
        $user = Auth::user();
        $preferences = $user->preference;

        $settings = [
            'email_notifications' => $preferences->email_notifications ?? true,
            'sms_notifications' => $preferences->sms_notifications ?? false,
            'push_notifications' => $preferences->push_notifications ?? true,
            'marketing_emails' => $preferences->marketing_emails ?? false,
            'newsletter_subscription' => $preferences->newsletter_subscription ?? true,
            'property_alerts' => $preferences->property_alerts ?? true,
            'price_drop_alerts' => $preferences->price_drop_alerts ?? true,
            'new_listing_alerts' => $preferences->new_listing_alerts ?? true,
        ];

        return response()->json([
            'success' => true,
            'settings' => $settings
        ]);
    }

    public function createNotification(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,error,property,system',
            'action_url' => 'nullable|url',
            'action_text' => 'nullable|string|max:50',
        ]);

        $notification = UserNotification::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'action_url' => $request->action_url,
            'action_text' => $request->action_text,
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'notification' => $notification,
            'message' => 'Notification created successfully'
        ]);
    }

    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'exists:user_notifications,id',
            'action' => 'required|in:mark_read,mark_unread,delete',
        ]);

        $user = Auth::user();
        $notificationIds = $request->notification_ids;
        $action = $request->action;

        // Ensure notifications belong to the user
        $notifications = UserNotification::where('user_id', $user->id)
            ->whereIn('id', $notificationIds)
            ->get();

        $count = 0;

        foreach ($notifications as $notification) {
            switch ($action) {
                case 'mark_read':
                    $notification->update(['is_read' => true]);
                    $count++;
                    break;
                case 'mark_unread':
                    $notification->update(['is_read' => false]);
                    $count++;
                    break;
                case 'delete':
                    $notification->delete();
                    $count++;
                    break;
            }
        }

        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => "bulk_notification_{$action}",
            'details' => "Bulk {$action} on {$count} notifications",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Successfully performed {$action} on {$count} notifications"
        ]);
    }
}
