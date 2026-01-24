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
                    <p class="text-gray-600">Stay updated with your latest activities</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="markAllAsRead()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-check-double mr-2"></i>
                        Mark All Read
                    </button>
                    <button onclick="clearNotifications()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        Clear All
                    </button>
                </div>
            </div>
        </div>

        <!-- Notification Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-bell text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Notifications</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $notifications->count() }}</p>
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
                        <p class="text-2xl font-bold text-gray-800">{{ $unreadCount }}</p>
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
                        <p class="text-2xl font-bold text-gray-800">{{ $notifications->count() - $unreadCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex space-x-1">
                <button onclick="filterNotifications('all')" class="filter-btn px-4 py-2 rounded-lg bg-blue-600 text-white">
                    All ({{ $notifications->count() }})
                </button>
                <button onclick="filterNotifications('unread')" class="filter-btn px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">
                    Unread ({{ $unreadCount }})
                </button>
                <button onclick="filterNotifications('property')" class="filter-btn px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">
                    Properties
                </button>
                <button onclick="filterNotifications('message')" class="filter-btn px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">
                    Messages
                </button>
                <button onclick="filterNotifications('system')" class="filter-btn px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">
                    System
                </button>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            @forelse ($notifications as $notification)
                <div class="notification-item border-b hover:bg-gray-50 transition-colors {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}" data-type="{{ $notification->type }}">
                    <div class="p-6">
                        <div class="flex items-start">
                            <!-- Icon -->
                            <div class="flex-shrink-0 mr-4">
                                <div class="bg-{{ $notification->type === 'property' ? 'green' : ($notification->type === 'message' ? 'blue' : ($notification->type === 'system' ? 'yellow' : 'gray')) }}-100 rounded-full p-3">
                                    <i class="fas fa-{{ $notification->type === 'property' ? 'home' : ($notification->type === 'message' ? 'envelope' : ($notification->type === 'system' ? 'cog' : 'bell')) }} text-{{ $notification->type === 'property' ? 'green' : ($notification->type === 'message' ? 'blue' : ($notification->type === 'system' ? 'yellow' : 'gray')) }}-600"></i>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-medium text-gray-800 mb-1">
                                            {{ $notification->title }}
                                        </h3>
                                        <p class="text-gray-600 mb-2">
                                            {{ $notification->message }}
                                        </p>
                                        
                                        @if($notification->data)
                                            <div class="bg-gray-100 rounded-lg p-3 mb-2">
                                                @if($notification->type === 'property' && isset($notification->data['property']))
                                                    <div class="flex items-center">
                                                        <div class="bg-gray-200 rounded w-12 h-12 mr-3">
                                                            @if(isset($notification->data['property']['image']))
                                                                <img src="{{ $notification->data['property']['image'] }}" alt="" class="w-12 h-12 rounded object-cover">
                                                            @else
                                                                <div class="w-12 h-12 bg-gray-200 flex items-center justify-center">
                                                                    <i class="fas fa-home text-gray-400 text-sm"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="flex-1">
                                                            <p class="font-medium text-gray-800">{{ $notification->data['property']['title'] }}</p>
                                                            <p class="text-sm text-gray-600">{{ $notification->data['property']['price'] }}</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span>{{ $notification->created_at->diffForHumans() }}</span>
                                            @if($notification->read_at)
                                                <span>Read {{ $notification->read_at->diffForHumans() }}</span>
                                            @else
                                                <span class="text-blue-600 font-medium">Unread</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="flex items-center space-x-2 ml-4">
                                        @if($notification->action_url)
                                            <a href="{{ $notification->action_url }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                                View
                                            </a>
                                        @endif
                                        
                                        @if(!$notification->read_at)
                                            <button onclick="markAsRead({{ $notification->id }})" class="text-gray-600 hover:text-gray-800" title="Mark as read">
                                                <i class="fas fa-envelope-open"></i>
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
                </div>
            @empty
                <div class="text-center py-12">
                    <i class="fas fa-bell-slash text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications</h3>
                    <p class="text-gray-500">You're all caught up! No new notifications.</p>
                </div>
            @endforelse
        </div>

        <!-- Load More -->
        @if($notifications->hasPages())
            <div class="mt-6 text-center">
                <button onclick="loadMore()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    Load More
                </button>
            </div>
        @endif
    </div>
</div>

<script>
function filterNotifications(type) {
    // Update button styles
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    event.target.classList.remove('bg-gray-200', 'text-gray-700');
    event.target.classList.add('bg-blue-600', 'text-white');
    
    // Filter notifications
    const items = document.querySelectorAll('.notification-item');
    items.forEach(item => {
        if (type === 'all') {
            item.style.display = 'block';
        } else if (type === 'unread') {
            item.style.display = item.classList.contains('bg-blue-50') ? 'block' : 'none';
        } else {
            item.style.display = item.dataset.type === type ? 'block' : 'none';
        }
    });
}

function markAsRead(notificationId) {
    fetch('/notifications/' + notificationId + '/read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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

function markAllAsRead() {
    if (!confirm('Mark all notifications as read?')) {
        return;
    }
    
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
    if (!confirm('Delete this notification?')) {
        return;
    }
    
    fetch('/notifications/' + notificationId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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

function clearNotifications() {
    if (!confirm('Clear all notifications? This cannot be undone.')) {
        return;
    }
    
    fetch('/notifications/clear', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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

function loadMore() {
    // Implement pagination loading
    window.location.href = '?page=' + (parseInt('{{ request('page', 1) }}') + 1);
}
</script>
@endsection
