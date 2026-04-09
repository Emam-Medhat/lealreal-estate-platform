<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = UserNotification::where('user_id', $user->id);
        
        // Filter
        if ($request->filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($request->filter === 'messages') {
            $query->where('type', 'message');
        } elseif ($request->filter === 'appointments') {
            $query->where('type', 'appointment');
        } elseif ($request->filter === 'system') {
            $query->where('type', 'system');
        } elseif ($request->filter === 'payments') {
            $query->where('type', 'payment');
        }
        
        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Add type color and icon for each notification
        $notifications->getCollection()->transform(function ($notification) {
            $notification->type_color = $this->getTypeColor($notification->type);
            $notification->icon = $this->getTypeIcon($notification->type);
            return $notification;
        });
        
        $stats = [
            'total' => UserNotification::where('user_id', $user->id)->count(),
            'unread' => UserNotification::where('user_id', $user->id)->whereNull('read_at')->count(),
            'read' => UserNotification::where('user_id', $user->id)->whereNotNull('read_at')->count(),
            'today' => UserNotification::where('user_id', $user->id)->whereDate('created_at', today())->count(),
            'messages' => UserNotification::where('user_id', $user->id)->where('type', 'message')->count(),
            'appointments' => UserNotification::where('user_id', $user->id)->where('type', 'appointment')->count(),
            'system' => UserNotification::where('user_id', $user->id)->where('type', 'system')->count(),
            'payments' => UserNotification::where('user_id', $user->id)->where('type', 'payment')->count()
        ];
        
        $settings = [
            'email' => $user->notification_settings['email'] ?? true,
            'push' => $user->notification_settings['push'] ?? true,
            'sms' => $user->notification_settings['sms'] ?? false,
            'whatsapp' => $user->notification_settings['whatsapp'] ?? false
        ];
        
        return view('messages.notifications', compact('notifications', 'stats', 'settings'));
    }
    
    public function markAllRead()
    {
        $user = Auth::user();
        
        UserNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return response()->json(['success' => true]);
    }
    
    public function clearAll()
    {
        $user = Auth::user();
        
        UserNotification::where('user_id', $user->id)->delete();
        
        return response()->json(['success' => true]);
    }
    
    public function markAsRead($id)
    {
        $notification = UserNotification::where('user_id', Auth::id())
            ->findOrFail($id);
        
        $notification->update(['read_at' => now()]);
        
        return response()->json(['success' => true]);
    }
    
    public function delete($id)
    {
        $notification = UserNotification::where('user_id', Auth::id())
            ->findOrFail($id);
        
        $notification->delete();
        
        return response()->json(['success' => true]);
    }
    
    public function updateSettings(Request $request)
    {
        $request->validate([
            'type' => 'required|in:email,push,sms,whatsapp',
            'value' => 'required|boolean'
        ]);
        
        $user = Auth::user();
        $settings = $user->notification_settings ?? [];
        $settings[$request->type] = $request->value;
        
        $user->update(['notification_settings' => $settings]);
        
        return response()->json(['success' => true]);
    }
    
    public function getUnreadCount()
    {
        $count = UserNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();
        
        return response()->json(['count' => $count]);
    }
    
    public function getRecentNotifications()
    {
        $notifications = UserNotification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'read_at' => $notification->read_at,
                    'action_url' => $notification->action_url,
                    'action_text' => $notification->action_text
                ];
            });
        
        return response()->json(['notifications' => $notifications]);
    }
    
    public function create(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:message,appointment,system,payment',
            'action_url' => 'nullable|url',
            'action_text' => 'nullable|string|max:100',
            'data' => 'nullable|array'
        ]);
        
        $notification = UserNotification::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'action_url' => $request->action_url,
            'action_text' => $request->action_text,
            'data' => $request->data ? json_encode($request->data) : null
        ]);
        
        // Send push notification if enabled
        $user = User::find($request->user_id);
        if ($user->notification_settings['push'] ?? true) {
            // Implement push notification logic here
        }
        
        // Send email notification if enabled
        if ($user->notification_settings['email'] ?? true) {
            // Implement email notification logic here
        }
        
        return response()->json(['success' => true, 'notification' => $notification]);
    }
    
    private function getTypeColor($type)
    {
        $colors = [
            'message' => 'blue',
            'appointment' => 'purple',
            'system' => 'gray',
            'payment' => 'green',
            'property' => 'orange',
            'alert' => 'red',
            'success' => 'green',
            'warning' => 'yellow',
            'info' => 'blue'
        ];
        
        return $colors[$type] ?? 'gray';
    }
    
    private function getTypeIcon($type)
    {
        $icons = [
            'message' => 'envelope',
            'appointment' => 'calendar',
            'system' => 'cog',
            'payment' => 'credit-card',
            'property' => 'home',
            'alert' => 'exclamation-triangle',
            'success' => 'check-circle',
            'warning' => 'exclamation-triangle',
            'info' => 'info-circle'
        ];
        
        return $icons[$type] ?? 'bell';
    }
}
