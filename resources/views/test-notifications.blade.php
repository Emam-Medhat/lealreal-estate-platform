@extends('layouts.app')

@section('title', 'Test Notifications')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Test Notification System</h1>
        
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Send Test Notification</h2>
            
            <form id="test-notification-form" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input type="text" id="test-title" name="title" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                           value="Test Notification" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                    <textarea id="test-message" name="message" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                              required>This is a test notification from the system.</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Icon</label>
                    <select id="test-icon" name="icon" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="bell">Bell</option>
                        <option value="home">Home</option>
                        <option value="user">User</option>
                        <option value="building">Building</option>
                        <option value="envelope">Envelope</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                    <select id="test-color" name="color" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="blue">Blue</option>
                        <option value="green">Green</option>
                        <option value="red">Red</option>
                        <option value="amber">Amber</option>
                        <option value="purple">Purple</option>
                    </select>
                </div>
                
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Send Test Notification
                </button>
            </form>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Current User Info</h2>
            <div class="space-y-2">
                <p><strong>ID:</strong> {{ Auth::id() }}</p>
                <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
                <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                <p><strong>User Type:</strong> {{ Auth::user()->user_type }}</p>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold mb-4">Recent Notifications</h2>
            <div id="recent-notifications">
                @php
                    $notifications = Auth::user()->notifications()->latest()->take(5)->get();
                @endphp
                @if($notifications->count() > 0)
                    <div class="space-y-3">
                        @foreach($notifications as $notification)
                            <div class="p-3 border rounded-lg {{ $notification->read_at ? 'bg-gray-50' : 'bg-blue-50' }}">
                                <p class="font-semibold">{{ $notification->data['title'] ?? 'No Title' }}</p>
                                <p class="text-sm text-gray-600">{{ $notification->data['message'] ?? 'No Message' }}</p>
                                <p class="text-xs text-gray-500">{{ $notification->created_at->format('Y-m-d H:i:s') }}</p>
                                <p class="text-xs text-gray-500">Read: {{ $notification->read_at ? 'Yes' : 'No' }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No notifications found</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('test-notification-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/api/notifications/create', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            title: formData.get('title'),
            message: formData.get('message'),
            icon: formData.get('icon'),
            color: formData.get('color')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Test notification sent successfully!');
            // Refresh notifications list
            location.reload();
        } else {
            alert('Failed to send notification: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending notification: ' + error.message);
    });
});
</script>
@endsection
