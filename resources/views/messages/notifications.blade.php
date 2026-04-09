@extends('layouts.app')

@section('title', 'Notifications')

@push('styles')
<style>
    .page-shell {
        animation: fadeIn 0.45s ease-in;
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.10);
    }

    .header-actions .btn {
        border-radius: 999px;
        padding: 0.75rem 1.25rem;
        font-weight: 700;
        transition: all 0.25s ease;
    }

    .btn-soft {
        background: rgba(255,255,255,0.18);
        border: 1px solid rgba(255,255,255,0.24);
        color: #fff;
        backdrop-filter: blur(10px);
    }

    .btn-soft:hover {
        background: rgba(255,255,255,0.28);
        transform: translateY(-2px);
    }

    .btn-danger-soft {
        background: rgba(239, 68, 68, 0.18);
        border: 1px solid rgba(239, 68, 68, 0.25);
        color: #fff;
        backdrop-filter: blur(10px);
    }

    .btn-danger-soft:hover {
        background: rgba(239, 68, 68, 0.28);
        transform: translateY(-2px);
    }

    .card-modern {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.04);
        overflow: hidden;
    }

    .card-modern .card-head {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .pill {
        border-radius: 999px;
        padding: 0.5rem 0.9rem;
        font-size: 0.875rem;
        font-weight: 700;
        transition: all 0.2s ease;
    }

    .pill:hover {
        transform: translateY(-1px);
    }

    .notif-item {
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .notif-item:hover {
        background-color: #f8f9ff;
    }

    .notif-unread {
        background: linear-gradient(135deg, #eef2ff 0%, #f0f9ff 100%);
        border-right: 4px solid #667eea;
    }

    .empty-state {
        padding: 3rem;
        text-align: center;
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
        border-radius: 16px;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-8 page-shell">
    <div class="max-w-5xl mx-auto">
        <div class="page-header mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold mb-2">
                        <i class="fas fa-bell ml-2"></i>
                        الإشعارات
                    </h1>
                    <p class="opacity-90">إدارة الإشعارات والتنبيهات والإعدادات</p>
                </div>
                <div class="header-actions flex flex-wrap items-center gap-2">
                    <button onclick="markAllAsRead()" class="btn btn-soft">
                        <i class="fas fa-check-double ml-2"></i>
                        تعليم الكل كمقروء
                    </button>
                    <button onclick="clearAll()" class="btn btn-danger-soft">
                        <i class="fas fa-trash ml-2"></i>
                        مسح الكل
                    </button>
                    <a href="{{ route('messages.inbox') }}" class="btn btn-soft">
                        <i class="fas fa-arrow-right ml-2"></i>
                        الرجوع للرسائل
                    </a>
                </div>
            </div>
        </div>

        <div class="card-modern mb-6">
            <div class="card-head">
                <div class="font-bold text-gray-900">
                    <i class="fas fa-sliders-h ml-2 text-indigo-600"></i>
                    إعدادات الإشعارات
                </div>
                <button onclick="toggleSettings()" class="text-gray-600 hover:text-gray-800" title="Settings">
                    <i class="fas fa-cog"></i>
                </button>
            </div>
            <div class="p-6">
            
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

        <div class="card-modern mb-6">
            <div class="card-head">
                <div class="font-bold text-gray-900">
                    <i class="fas fa-filter ml-2 text-indigo-600"></i>
                    التصفية
                </div>
                <div class="text-sm text-gray-600">اختار نوع الإشعارات</div>
            </div>
            <div class="p-6">
                <div class="flex flex-wrap items-center gap-2">
                    <button onclick="filterNotifications('all')" class="pill bg-indigo-100 text-indigo-700">
                        الكل ({{ $stats['total'] }})
                    </button>
                    <button onclick="filterNotifications('unread')" class="pill bg-yellow-100 text-yellow-700">
                        غير مقروء ({{ $stats['unread'] }})
                    </button>
                    <button onclick="filterNotifications('messages')" class="pill bg-blue-100 text-blue-700">
                        رسائل ({{ $stats['messages'] }})
                    </button>
                    <button onclick="filterNotifications('appointments')" class="pill bg-purple-100 text-purple-700">
                        مواعيد ({{ $stats['appointments'] }})
                    </button>
                    <button onclick="filterNotifications('system')" class="pill bg-gray-100 text-gray-700">
                        نظام ({{ $stats['system'] }})
                    </button>
                    <button onclick="filterNotifications('payments')" class="pill bg-green-100 text-green-700">
                        مدفوعات ({{ $stats['payments'] }})
                    </button>
                </div>
            </div>
        </div>

        <div class="card-modern">
            <div class="card-head">
                <div class="font-bold text-gray-900">
                    <i class="fas fa-inbox ml-2 text-indigo-600"></i>
                    قائمة الإشعارات
                </div>
                <div class="text-sm text-gray-600">{{ $notifications->total() }} إشعار</div>
            </div>
            <div class="divide-y">
                @forelse ($notifications as $notification)
                    <div class="p-6 notif-item {{ !$notification->read_at ? 'notif-unread' : '' }}">
                        <div class="flex items-start space-x-4 space-x-reverse">
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
                                        
                                        <div class="flex flex-wrap items-center gap-3 mt-3">
                                            <span class="text-sm text-gray-500">
                                                <i class="fas fa-clock ml-1"></i>{{ $notification->created_at->diffForHumans() }}
                                            </span>
                                            
                                            @if($notification->action_url)
                                                <a href="{{ $notification->action_url }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                                    {{ $notification->action_text ?? 'View Details' }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-2">
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
                    <div class="p-6">
                        <div class="empty-state">
                            <i class="fas fa-bell fa-4x mb-3"></i>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد إشعارات</h3>
                            <p class="text-gray-600">أنت على اطلاع كامل — لا يوجد إشعارات جديدة حالياً.</p>
                        </div>
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

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
            <div class="card-modern p-6">
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
            
            <div class="card-modern p-6">
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
            
            <div class="card-modern p-6">
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
            
            <div class="card-modern p-6">
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
