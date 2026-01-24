@extends('layouts.app')

@section('title', 'Chat')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Chat</h1>
                    <p class="text-gray-600">Real-time messaging</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="createChatRoom()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-users mr-2"></i>
                        Create Room
                    </button>
                    <a href="{{ route('messages.inbox') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Messages
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Chat Rooms -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-4 border-b">
                        <h3 class="font-medium text-gray-800 mb-3">Chat Rooms</h3>
                        <div class="space-y-2">
                            <button onclick="filterRooms('all')" class="w-full text-left px-3 py-2 rounded-lg text-sm bg-blue-50 text-blue-600">
                                All Rooms
                            </button>
                            <button onclick="filterRooms('joined')" class="w-full text-left px-3 py-2 rounded-lg text-sm hover:bg-gray-50">
                                Joined
                            </button>
                            <button onclick="filterRooms('public')" class="w-full text-left px-3 py-2 rounded-lg text-sm hover:bg-gray-50">
                                Public
                            </button>
                            <button onclick="filterRooms('private')" class="w-full text-left px-3 py-2 rounded-lg text-sm hover:bg-gray-50">
                                Private
                            </button>
                        </div>
                    </div>
                    
                    <div class="divide-y max-h-96 overflow-y-auto">
                        @forelse ($chatRooms as $room)
                            <div onclick="joinRoom({{ $room->id }})" class="p-4 hover:bg-gray-50 cursor-pointer {{ $room->id == $activeRoom ? 'bg-blue-50' : '' }}">
                                <div class="flex items-center space-x-3">
                                    <div class="relative">
                                        <div class="w-10 h-10 bg-{{ $room->type === 'public' ? 'green' : 'purple' }}-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-{{ $room->type === 'public' ? 'globe' : 'lock' }} text-{{ $room->type === 'public' ? 'green' : 'purple' }}-600"></i>
                                        </div>
                                        @if($room->unread_count > 0)
                                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-blue-600 rounded-full"></div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-medium text-gray-900 truncate">{{ $room->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $room->participants_count }} members</p>
                                    </div>
                                    
                                    @if($room->is_active)
                                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <i class="fas fa-comments text-4xl text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No chat rooms</h3>
                                <p class="text-gray-500 mb-4">Create or join a chat room</p>
                                <button onclick="createChatRoom()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                                    Create Room
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="lg:col-span-3">
                @if($activeRoom)
                    <div class="bg-white rounded-lg shadow-sm h-96 flex flex-col">
                        <!-- Room Header -->
                        <div class="p-4 border-b flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-{{ $activeRoom->type === 'public' ? 'green' : 'purple' }}-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-{{ $activeRoom->type === 'public' ? 'globe' : 'lock' }} text-{{ $activeRoom->type === 'public' ? 'green' : 'purple' }}-600 text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $activeRoom->name }}</h3>
                                    <p class="text-xs text-gray-500">{{ $activeRoom->participants_count }} members online</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <button onclick="showParticipants()" class="text-gray-600 hover:text-gray-800">
                                    <i class="fas fa-users"></i>
                                </button>
                                <button onclick="roomSettings()" class="text-gray-600 hover:text-gray-800">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <button onclick="leaveRoom({{ $activeRoom->id }})" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-sign-out-alt"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div class="flex-1 overflow-y-auto p-4 space-y-4" id="chatMessages">
                            @foreach ($chatMessages as $message)
                                <div class="flex {{ $message->user_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-xs lg:max-w-md">
                                        <div class="flex items-center space-x-2 mb-1 {{ $message->user_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                                            @if($message->user_id != auth()->id())
                                                <img src="{{ $message->user->avatar ?? asset('images/default-avatar.png') }}" alt="" class="w-6 h-6 rounded-full">
                                                <span class="text-xs text-gray-500">{{ $message->user->name }}</span>
                                            @endif
                                        </div>
                                        <div class="{{ $message->user_id == auth()->id() ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800' } rounded-lg px-4 py-2">
                                            <p>{{ $message->content }}</p>
                                            @if($message->attachment)
                                                <div class="mt-2">
                                                    @if(str_contains($message->attachment->mime_type, 'image'))
                                                        <img src="{{ $message->attachment->url }}" alt="" class="rounded max-w-full">
                                                    @else
                                                        <div class="flex items-center space-x-2 text-sm">
                                                            <i class="fas fa-file"></i>
                                                            <span>{{ $message->attachment->name }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1 {{ $message->user_id == auth()->id() ? 'text-right' : 'text-left' }}">
                                            {{ $message->created_at->format('h:i A') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Message Input -->
                        <div class="p-4 border-t">
                            <form onsubmit="sendChatMessage(event)" class="flex items-center space-x-2">
                                <div class="flex-1 flex items-center space-x-2">
                                    <button type="button" onclick="attachChatFile()" class="text-gray-600 hover:text-gray-800">
                                        <i class="fas fa-paperclip"></i>
                                    </button>
                                    <input type="text" id="chatMessageInput" placeholder="Type a message..." class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <button type="button" onclick="insertChatEmoji()" class="text-gray-600 hover:text-gray-800">
                                        <i class="fas fa-smile"></i>
                                    </button>
                                </div>
                                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow-sm h-96 flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-comments text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Join a chat room</h3>
                            <p class="text-gray-500">Select a room from the list to start chatting</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Online Users -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h3 class="font-medium text-gray-800 mb-4">Online Users</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach ($onlineUsers as $user)
                    <div class="text-center">
                        <div class="relative inline-block">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" alt="" class="w-12 h-12 rounded-full">
                            @else
                                <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-gray-500 text-sm"></i>
                                </div>
                            @endif
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">{{ $user->name }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Create Chat Room Modal -->
<div id="createRoomModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Create Chat Room</h3>
        
        <form onsubmit="createNewRoom(event)">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Room Name</label>
                    <input type="text" id="roomName" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Room Type</label>
                    <select id="roomType" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="public">Public</option>
                        <option value="private">Private</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="roomDescription" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Invite Users</label>
                    <select id="inviteUsers" multiple class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeCreateRoomModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    Create Room
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function filterRooms(type) {
    // Implement room filtering
    console.log('Filter rooms:', type);
}

function joinRoom(roomId) {
    window.location.href = '/messages/chat/room/' + roomId;
}

function createChatRoom() {
    document.getElementById('createRoomModal').classList.remove('hidden');
}

function closeCreateRoomModal() {
    document.getElementById('createRoomModal').classList.add('hidden');
}

function createNewRoom(event) {
    event.preventDefault();
    
    const name = document.getElementById('roomName').value;
    const type = document.getElementById('roomType').value;
    const description = document.getElementById('roomDescription').value;
    const inviteUsers = Array.from(document.getElementById('inviteUsers').selectedOptions).map(option => option.value);
    
    fetch('/messages/chat/rooms', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            name: name,
            type: type,
            description: description,
            invite_users: inviteUsers
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/messages/chat/room/' + data.room_id;
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function sendChatMessage(event) {
    event.preventDefault();
    
    const input = document.getElementById('chatMessageInput');
    const content = input.value.trim();
    
    if (!content) return;
    
    const roomId = {{ $activeRoom->id ?? 'null' }};
    
    fetch('/messages/chat/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            room_id: roomId,
            content: content
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function showParticipants() {
    // Show participants modal
    alert('Participants feature coming soon!');
}

function roomSettings() {
    // Show room settings
    alert('Room settings feature coming soon!');
}

function leaveRoom(roomId) {
    if (confirm('Are you sure you want to leave this room?')) {
        fetch('/messages/chat/room/' + roomId + '/leave', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/messages/chat';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

function attachChatFile() {
    alert('File attachment feature coming soon!');
}

function insertChatEmoji() {
    alert('Emoji picker feature coming soon!');
}

// Auto-scroll to bottom of chat
window.addEventListener('load', function() {
    const container = document.getElementById('chatMessages');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
});

// WebSocket connection for real-time messages
const socket = new WebSocket('ws://localhost:6001');

socket.onmessage = function(event) {
    const data = JSON.parse(event.data);
    if (data.type === 'chat_message') {
        // Handle incoming chat message
        console.log('New chat message:', data);
    }
};
</script>
@endsection
