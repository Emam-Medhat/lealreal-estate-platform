@extends('layouts.app')

@section('title', 'Messages Inbox')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Messages</h1>
                    <p class="text-gray-600">Manage your conversations</p>
                </div>
                <button onclick="composeMessage()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    New Message
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Conversations List -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-4 border-b">
                        <div class="flex items-center space-x-2">
                            <input type="text" placeholder="Search conversations..." class="flex-1 px-3 py-2 border rounded-lg text-sm">
                            <button class="text-gray-600 hover:text-gray-800">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="divide-y">
                        @forelse ($conversations as $conversation)
                            <div onclick="openConversation({{ $conversation->id }})" class="p-4 hover:bg-gray-50 cursor-pointer {{ $conversation->id == $activeConversation ? 'bg-blue-50' : '' }}">
                                <div class="flex items-start space-x-3">
                                    <div class="relative">
                                        @if($conversation->other_user->avatar)
                                            <img src="{{ $conversation->other_user->avatar }}" alt="" class="w-10 h-10 rounded-full">
                                        @else
                                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user text-gray-500 text-sm"></i>
                                            </div>
                                        @endif
                                        @if($conversation->unread_count > 0)
                                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-blue-600 rounded-full"></div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-start">
                                            <h4 class="font-medium text-gray-900 truncate">{{ $conversation->other_user->name }}</h4>
                                            <span class="text-xs text-gray-500">{{ $conversation->last_message_time }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600 truncate">{{ $conversation->last_message }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <i class="fas fa-comments text-4xl text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No conversations yet</h3>
                                <p class="text-gray-500 mb-4">Start messaging other users</p>
                                <button onclick="composeMessage()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    Start Conversation
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Conversation View -->
            <div class="lg:col-span-2">
                @if($activeConversation)
                    <div class="bg-white rounded-lg shadow-sm h-96 flex flex-col">
                        <!-- Conversation Header -->
                        <div class="p-4 border-b flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                @if($activeConversation->other_user->avatar)
                                    <img src="{{ $activeConversation->other_user->avatar }}" alt="" class="w-8 h-8 rounded-full">
                                @else
                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-gray-500 text-xs"></i>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $activeConversation->other_user->name }}</h3>
                                    <p class="text-xs text-gray-500">{{ $activeConversation->other_user->status ?? 'Active' }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <button onclick="startVideoCall({{ $activeConversation->id }})" class="text-gray-600 hover:text-gray-800">
                                    <i class="fas fa-video"></i>
                                </button>
                                <button onclick="startVoiceCall({{ $activeConversation->id }})" class="text-gray-600 hover:text-gray-800">
                                    <i class="fas fa-phone"></i>
                                </button>
                                <button onclick="deleteConversation({{ $activeConversation->id }})" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messagesContainer">
                            @foreach ($messages as $message)
                                <div class="flex {{ $message->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-xs lg:max-w-md">
                                        <div class="{{ $message->sender_id == auth()->id() ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800' } rounded-lg px-4 py-2">
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
                                        <p class="text-xs text-gray-500 mt-1 {{ $message->sender_id == auth()->id() ? 'text-right' : 'text-left' }}">
                                            {{ $message->created_at->format('h:i A') }}
                                            @if($message->sender_id == auth()->id())
                                                <span class="ml-2">
                                                    @if($message->read_at) <i class="fas fa-check-double text-blue-500"></i>
                                                    @else <i class="fas fa-check text-gray-400"></i>
                                                    @endif
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Message Input -->
                        <div class="p-4 border-t">
                            <form onsubmit="sendMessage(event)" class="flex items-center space-x-2">
                                <div class="flex-1 flex items-center space-x-2">
                                    <button type="button" onclick="attachFile()" class="text-gray-600 hover:text-gray-800">
                                        <i class="fas fa-paperclip"></i>
                                    </button>
                                    <input type="text" id="messageInput" placeholder="Type a message..." class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <button type="button" onclick="insertEmoji()" class="text-gray-600 hover:text-gray-800">
                                        <i class="fas fa-smile"></i>
                                    </button>
                                </div>
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow-sm h-96 flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-comments text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Select a conversation</h3>
                            <p class="text-gray-500">Choose a conversation from the list to start messaging</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Compose Message Modal -->
<div id="composeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">New Message</h3>
        
        <form onsubmit="createConversation(event)">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">To</label>
                    <select id="recipientSelect" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select recipient</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                    <textarea id="newMessageContent" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeComposeModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Send Message
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openConversation(conversationId) {
    window.location.href = '/messages/conversation/' + conversationId;
}

function composeMessage() {
    document.getElementById('composeModal').classList.remove('hidden');
}

function closeComposeModal() {
    document.getElementById('composeModal').classList.add('hidden');
}

function createConversation(event) {
    event.preventDefault();
    
    const recipientId = document.getElementById('recipientSelect').value;
    const content = document.getElementById('newMessageContent').value;
    
    fetch('/messages/conversations', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            recipient_id: recipientId,
            content: content
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/messages/conversation/' + data.conversation_id;
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function sendMessage(event) {
    event.preventDefault();
    
    const input = document.getElementById('messageInput');
    const content = input.value.trim();
    
    if (!content) return;
    
    const conversationId = {{ $activeConversation->id ?? 'null' }};
    
    fetch('/messages/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            conversation_id: conversationId,
            content: content
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            // Refresh messages
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function startVideoCall(conversationId) {
    window.location.href = '/messages/video-call/' + conversationId;
}

function startVoiceCall(conversationId) {
    window.location.href = '/messages/voice-call/' + conversationId;
}

function deleteConversation(conversationId) {
    if (confirm('Are you sure you want to delete this conversation?')) {
        fetch('/messages/conversation/' + conversationId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/messages';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

function attachFile() {
    // Implement file attachment
    alert('File attachment feature coming soon!');
}

function insertEmoji() {
    // Implement emoji picker
    alert('Emoji picker coming soon!');
}

// Auto-scroll to bottom of messages
window.addEventListener('load', function() {
    const container = document.getElementById('messagesContainer');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
});
</script>
@endsection
