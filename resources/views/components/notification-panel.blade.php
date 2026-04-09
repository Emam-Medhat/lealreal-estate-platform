@php
    $user = Auth::user();
    $unreadNotifications = \App\Models\Notification::forUser($user->id)->unread()->latest()->limit(5)->get();
    $unreadCount = \App\Models\Notification::forUser($user->id)->unread()->count();
@endphp

<!-- Notification Bell -->
<div class="relative">
    <button onclick="toggleNotifications()" class="relative p-2 text-gray-600 hover:text-gray-800 transition-colors">
        <i class="fas fa-bell text-xl"></i>
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Notification Dropdown -->
    <div id="notificationDropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50">
        <div class="p-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">الإشعارات</h3>
                <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    عرض الكل
                </a>
            </div>
        </div>

        <div class="max-h-96 overflow-y-auto">
            @if($unreadNotifications->count() > 0)
                @foreach($unreadNotifications as $notification)
                    <div class="p-4 hover:bg-gray-50 border-b border-gray-100 cursor-pointer notification-item" data-id="{{ $notification->id }}">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                @switch($notification->type)
                                    @case('success')
                                        <div class="bg-green-100 rounded-full p-2">
                                            <i class="fas fa-check text-green-600 text-sm"></i>
                                        </div>
                                        @break
                                    @case('warning')
                                        <div class="bg-yellow-100 rounded-full p-2">
                                            <i class="fas fa-exclamation text-yellow-600 text-sm"></i>
                                        </div>
                                        @break
                                    @case('error')
                                        <div class="bg-red-100 rounded-full p-2">
                                            <i class="fas fa-times text-red-600 text-sm"></i>
                                        </div>
                                        @break
                                    @default
                                        <div class="bg-blue-100 rounded-full p-2">
                                            <i class="fas fa-info text-blue-600 text-sm"></i>
                                        </div>
                                @endswitch
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                                <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="p-8 text-center">
                    <i class="fas fa-bell-slash text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500">لا توجد إشعارات جديدة</p>
                </div>
            @endif
        </div>

        @if($unreadNotifications->count() > 0)
            <div class="p-3 border-t border-gray-200">
                <button onclick="markAllAsRead()" class="w-full text-center text-sm text-blue-600 hover:text-blue-800">
                    تعيين الكل كمقروء
                </button>
            </div>
        @endif
    </div>
</div>

<style>
.notification-item {
    transition: background-color 0.2s ease;
}

.notification-item:hover {
    background-color: #f9fafb;
}
</style>

<script>
function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.toggle('hidden');
    
    // Close dropdown when clicking outside
    if (!dropdown.classList.contains('hidden')) {
        setTimeout(() => {
            document.addEventListener('click', closeNotificationsOutside);
        }, 100);
    }
}

function closeNotificationsOutside(event) {
    const dropdown = document.getElementById('notificationDropdown');
    if (!dropdown.contains(event.target) && !event.target.closest('button[onclick="toggleNotifications()"]')) {
        dropdown.classList.add('hidden');
        document.removeEventListener('click', closeNotificationsOutside);
    }
}

function markAllAsRead() {
    fetch('{{ route("notifications.mark-all-read") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
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

// Mark notification as read when clicked
document.addEventListener('DOMContentLoaded', function() {
    const notificationItems = document.querySelectorAll('.notification-item');
    notificationItems.forEach(item => {
        item.addEventListener('click', function() {
            const notificationId = this.dataset.id;
            fetch(`{{ route("notifications.mark-read", ":id") }}`.replace(':id', notificationId), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.style.opacity = '0.5';
                    this.style.pointerEvents = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
});
</script>
