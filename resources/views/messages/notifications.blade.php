@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Notifications</h1>
                    <p class="text-gray-600">Manage your notifications and alerts</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="markAllAsRead()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-check-double mr-2"></i>
                        Mark All Read
                    </button>
                    <button onclick="clearAll()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        Clear All
                    </button>
                    <a href="{{ route('messages.inbox') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Messages
                    </a>
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Notification Settings</h3>
                <button onclick="toggleSettings()" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-cog"></i>
                </button>
            </div>
            
            <div id="notificationSettings" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium text-gray-800">Email Notifications</h4>
                        <p class="text-sm text-gray-600">Receive notifications via email</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" {{ $settings['email'] ? 'checked' : '' }} onchange="updateSetting('email', this.checked)" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium text-gray-800">Push Notifications</h4>
                        <p class="text-sm text-gray-600">Browser push notifications</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" {{ $settings['push'] ? 'checked' : '' }} onchange="updateSetting('push', this.checked)" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium text-gray-800">SMS Notifications</h4>
                        <p class="text-sm text-gray-600">Text message alerts</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" {{ $settings['sms'] ? 'checked' : '' }} onchange="updateSetting('sms', this.checked)" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium text-gray-800">WhatsApp Notifications</h4>
                        <p class="text-sm text-gray-600">WhatsApp message alerts</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" {{ $settings['whatsapp'] ? 'checked' : '' }} onchange="updateSetting('whatsapp', this.checked)" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Notification Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-wrap items-center gap-3">
                <button onclick="filterNotifications('all')" class="px-3 py-1 rounded-lg text-sm bg-blue-100 text-blue-600">
                    All ({{ $stats['total'] }})
                </button>
                <button onclick="filterNotifications('unread')" class="px-3 py-1 rounded-lg text-sm hover:bg-gray-100">
                    Unread ({{ $stats['unread'] }})
                </button>
                <button onclick="filterNotifications('messages')" class="px-3 py-1 rounded-lg text-sm hover:bg-gray-100">
                    Messages ({{ $stats['messages'] }})
                </button>
                <button onclick="filterNotifications('appointments')" class="px-3 py-1 rounded-lg text-sm hover:bg-gray-100">
                    Appointments ({{ $stats['appointments'] }})
                </button>
                <button onclick="filterNotifications('system')" class="px-3 py-1 rounded-lg text-sm hover:bg-gray-100">
                    System ({{ $stats['system'] }})
                </button>
                <button onclick="filterNotifications('payments')" class="px-3 py-1 rounded-lg text-sm hover:bg-gray-100">
                    Payments ({{ $stats['payments'] }})
                </button>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="divide-y">
                @forelse ($notifications as $notification)
                    <div class="p-6 hover:bg-gray-50 {{ !$notification->read_at ? 'bg-blue-50 border-l-4 border-blue-500' : '' }}">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="bg-{{ $notification->type_color }}-100 rounded-full p-3">
                                    <i class="fas fa-{{ $notification->icon }} text-{{ $notification->type_color }}-600"></i>
                                </div>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $notification->title }}</h4>
                                        <p class="text-gray-600 mt-1">{{ $notification->message }}</p>
                                        
                                        @if($notification->data)
                                            <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                                @foreach ($notification->data as $key => $value)
                                                    <div class="flex justify-between text-sm">
                                                        <span class="text-gray-600">{{ ucfirst($key) }}:</span>
                                                        <span class="font-medium text-gray-800">{{ $value }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        
                                        <div class="flex items-center space-x-4 mt-3">
                                            <span class="text-sm text-gray-500">
                                                <i class="fas fa-clock mr-1"></i>{{ $notification->created_at->diffForHumans() }}
                                            </span>
                                            
                                            @if($notification->action_url)
                                                <a href="{{ $notification->action_url }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                                    {{ $notification->action_text ?? 'View Details' }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2">
                                        @if(!$notification->read_at)
                                            <button onclick="markAsRead({{ $notification->id }})" class="text-blue-600 hover:text-blue-800" title="Mark as read">
                                                <i class="fas fa-envelope"></i>
                                            </button>
                                        @endif
                                        
                                        <button onclick="deleteNotification({{ $notification->id }})" class="text-red-600 hover:text-red-800" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <i class="fas fa-bell text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications</h3>
                        <p class="text-gray-500">You're all caught up! No new notifications.</p>
                    </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="p-4 border-t">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>

        <!-- Notification Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-bell text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-envelope text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Unread</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['unread'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Read</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['read'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-clock text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Today</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['today'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function markAllAsRead() {
    fetch('/messages/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function clearAll() {
    if (confirm('Are you sure you want to clear all notifications?')) {
        fetch('/messages/notifications/clear-all', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

function markAsRead(notificationId) {
    fetch('/messages/notifications/' + notificationId + '/read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function deleteNotification(notificationId) {
    if (confirm('Are you sure you want to delete this notification?')) {
        fetch('/messages/notifications/' + notificationId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

function filterNotifications(type) {
    window.location.href = '?filter=' + type;
}

function updateSetting(type, value) {
    fetch('/messages/notifications/settings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            type: type,
            value: value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Setting updated');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function toggleSettings() {
    const settings = document.getElementById('notificationSettings');
    settings.classList.toggle('hidden');
}

// Request notification permission
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}

// WebSocket for real-time notifications
const socket = new WebSocket('ws://localhost:6001');

socket.onmessage = function(event) {
    const data = JSON.parse(event.data);
    if (data.type === 'notification') {
        // Show browser notification
        if (Notification.permission === 'granted') {
            new Notification(data.title, {
                body: data.message,
                icon: '/favicon.ico'
            });
        }
        
        // Update UI
        location.reload();
    }
};
</script>
@endsection
