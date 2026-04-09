<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Notifications API Routes
Route::middleware(['auth'])->prefix('api')->group(function () {
    
    // Get unread notifications for a user
    Route::get('/notifications/unread/{userId}', function (Request $request, $userId) {
        if ($request->user()->id != $userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $notifications = $request->user()->notifications()
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($notification) {
                $data = $notification->data;
                return [
                    'id' => $notification->id,
                    'title' => $data['title'] ?? 'تنبيه جديد',
                    'message' => $data['message'] ?? 'لديك تنبيه جديد',
                    'icon' => $data['icon'] ?? 'bell',
                    'color' => $data['color'] ?? 'blue',
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->toISOString(),
                ];
            });
        
        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $request->user()->unreadNotifications()->count()
        ]);
    });

    // Check for new notifications
    Route::get('/notifications/check-new/{userId}', function (Request $request, $userId) {
        if ($request->user()->id != $userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $lastCheck = $request->get('last_check', now()->subMinutes(5)->toISOString());
        $newNotifications = $request->user()->notifications()
            ->whereNull('read_at')
            ->where('created_at', '>', $lastCheck)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) {
                $data = $notification->data;
                return [
                    'id' => $notification->id,
                    'title' => $data['title'] ?? 'تنبيه جديد',
                    'message' => $data['message'] ?? 'لديك تنبيه جديد',
                    'icon' => $data['icon'] ?? 'bell',
                    'color' => $data['color'] ?? 'blue',
                    'created_at' => $notification->created_at->toISOString(),
                ];
            });
        
        return response()->json([
            'success' => true,
            'has_new' => $newNotifications->count() > 0,
            'new_notifications' => $newNotifications
        ]);
    });

    // Get all notifications
    Route::get('/notifications', function (Request $request) {
        $notifications = $request->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get()
            ->map(function ($notification) {
                $data = $notification->data;
                return [
                    'id' => $notification->id,
                    'title' => $data['title'] ?? 'تنبيه جديد',
                    'message' => $data['message'] ?? 'لديك تنبيه جديد',
                    'icon' => $data['icon'] ?? 'bell',
                    'color' => $data['color'] ?? 'blue',
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->toISOString(),
                ];
            });
        
        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $request->user()->unreadNotifications()->count()
        ]);
    });

    // Mark notification as read
    Route::post('/notifications/{notification}/read', function (Request $request, $notification) {
        $notification = $request->user()->notifications()->findOrFail($notification);
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    });

    // Mark all notifications as read for a user
    Route::post('/notifications/mark-all-read/{userId}', function (Request $request, $userId) {
        if ($request->user()->id != $userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $request->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);
        
        return response()->json(['success' => true]);
    });

    // Delete notification
    Route::delete('/notifications/{notification}', function (Request $request, $notification) {
        $notification = $request->user()->notifications()->findOrFail($notification);
        $notification->delete();
        
        return response()->json(['success' => true]);
    });

    // Create a new notification (for testing)
    Route::post('/notifications/create', function (Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:500',
            'icon' => 'string|max:50',
            'color' => 'string|max:20'
        ]);

        $notification = $request->user()->notify(
            new \Illuminate\Notifications\DatabaseNotification([
                'id' => \Str::uuid(),
                'type' => 'App\\Notifications\\GeneralNotification',
                'notifiable_type' => get_class($request->user()),
                'notifiable_id' => $request->user()->id,
                'data' => [
                    'title' => $request->title,
                    'message' => $request->message,
                    'icon' => $request->icon ?? 'bell',
                    'color' => $request->color ?? 'blue'
                ],
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ])
        );

        return response()->json(['success' => true, 'notification_id' => $notification->id]);
    });
});
