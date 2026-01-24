@extends('layouts.app')

@section('title', 'Conversation')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('messages.inbox') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ $otherUser->name }}</h1>
                        <p class="text-gray-600">{{ $otherUser->status ?? 'Active' }}</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button onclick="startVideoCall()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-video mr-2"></i>
                        Video Call
                    </button>
                    <button onclick="startVoiceCall()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-phone mr-2"></i>
                        Voice Call
                    </button>
                    <button onclick="scheduleAppointment()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-calendar mr-2"></i>
                        Schedule
                    </button>
                </div>
            </div>
        </div>

        <!-- Conversation Area -->
        <div class="bg-white rounded-lg shadow-sm">
            <!-- Messages Container -->
            <div class="h-96 overflow-y-auto p-6 space-y-4" id="messagesContainer">
                @foreach ($messages as $message)
                    <div class="flex {{ $message->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="flex items-start space-x-3 max-w-lg">
                            @if($message->sender_id != auth()->id())
                                <div class="flex-shrink-0">
                                    @if($message->sender->avatar)
                                        <img src="{{ $message->sender->avatar }}" alt="" class="w-8 h-8 rounded-full">
                                    @else
                                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-gray-500 text-sm"></i>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            
                            <div class="flex-1">
                                <div class="{{ $message->sender_id == auth()->id() ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800' } rounded-lg px-4 py-3">
                                    <p class="text-sm">{{ $message->content }}</p>
                                    
                                    @if($message->attachment)
                                        <div class="mt-2">
                                            @if(str_contains($message->attachment->mime_type, 'image'))
                                                <img src="{{ $message->attachment->url }}" alt="" class="rounded max-w-full cursor-pointer" onclick="viewImage('{{ $message->attachment->url }}')">
                                            @else
                                                <div class="flex items-center space-x-2 text-sm">
                                                    <i class="fas fa-file"></i>
                                                    <a href="{{ $message->attachment->url }}" download class="hover:underline">
                                                        {{ $message->attachment->name }}
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex items-center space-x-2 mt-1 {{ $message->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                                    <span class="text-xs text-gray-500">{{ $message->created_at->format('M j, h:i A') }}</span>
                                    
                                    @if($message->sender_id == auth()->id())
                                        <span class="text-xs">
                                            @if($message->read_at) 
                                                <i class="fas fa-check-double text-blue-500"></i>
                                            @else 
                                                <i class="fas fa-check text-gray-400"></i>
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            @if($message->sender_id == auth()->id())
                                <div class="flex-shrink-0">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ auth()->user()->avatar }}" alt="" class="w-8 h-8 rounded-full">
                                    @else
                                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-gray-500 text-sm"></i>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Message Input -->
            <div class="border-t p-4">
                <form onsubmit="sendMessage(event)" class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="attachFile()" class="text-gray-600 hover:text-gray-800 p-2">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <button type="button" onclick="insertEmoji()" class="text-gray-600 hover:text-gray-800 p-2">
                            <i class="fas fa-smile"></i>
                        </button>
                        <input type="text" id="messageInput" placeholder="Type your message..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Shared Files -->
        @if($sharedFiles->isNotEmpty())
            <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                <h3 class="font-medium text-gray-800 mb-4">Shared Files</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($sharedFiles as $file)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center space-x-3">
                                <div class="bg-gray-100 rounded-lg p-2">
                                    @if(str_contains($file->mime_type, 'image'))
                                        <i class="fas fa-image text-gray-600"></i>
                                    @elseif(str_contains($file->mime_type, 'pdf'))
                                        <i class="fas fa-file-pdf text-red-600"></i>
                                    @else
                                        <i class="fas fa-file text-gray-600"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $file->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $file->size_formatted }}</p>
                                </div>
                            </div>
                            <div class="flex justify-end mt-3">
                                <a href="{{ $file->url }}" download class="text-blue-600 hover:text-blue-800 text-sm">
                                    Download
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Conversation Info -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h3 class="font-medium text-gray-800 mb-4">Conversation Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Participant</h4>
                    <div class="flex items-center space-x-3">
                        @if($otherUser->avatar)
                            <img src="{{ $otherUser->avatar }}" alt="" class="w-12 h-12 rounded-full">
                        @else
                            <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-500"></i>
                            </div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-900">{{ $otherUser->name }}</p>
                            <p class="text-sm text-gray-600">{{ $otherUser->email }}</p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Statistics</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Messages</span>
                            <span class="font-medium text-gray-900">{{ $messageCount }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Started</span>
                            <span class="font-medium text-gray-900">{{ $conversation->created_at->format('M j, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Active</span>
                            <span class="font-medium text-gray-900">{{ $conversation->updated_at->format('M j, h:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Viewer Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="relative max-w-4xl max-h-screen">
        <img id="modalImage" src="" alt="" class="max-w-full max-h-full rounded-lg">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-75">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<!-- Appointment Modal -->
<div id="appointmentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Schedule Appointment</h3>
        
        <form onsubmit="createAppointment(event)">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input type="text" id="appointmentTitle" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date" id="appointmentDate" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Time</label>
                    <input type="time" id="appointmentTime" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duration</label>
                    <select id="appointmentDuration" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="30">30 minutes</option>
                        <option value="60">1 hour</option>
                        <option value="120">2 hours</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea id="appointmentNotes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeAppointmentModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    Schedule
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function sendMessage(event) {
    event.preventDefault();
    
    const input = document.getElementById('messageInput');
    const content = input.value.trim();
    
    if (!content) return;
    
    fetch('/messages/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            conversation_id: {{ $conversation->id }},
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

function attachFile() {
    const input = document.createElement('input');
    input.type = 'file';
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            uploadFile(file);
        }
    };
    input.click();
}

function uploadFile(file) {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('conversation_id', {{ $conversation->id }});
    
    fetch('/messages/upload', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
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

function insertEmoji() {
    alert('Emoji picker coming soon!');
}

function viewImage(url) {
    document.getElementById('modalImage').src = url;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

function startVideoCall() {
    window.location.href = '/messages/video-call/{{ $conversation->id }}';
}

function startVoiceCall() {
    window.location.href = '/messages/voice-call/{{ $conversation->id }}';
}

function scheduleAppointment() {
    document.getElementById('appointmentModal').classList.remove('hidden');
}

function closeAppointmentModal() {
    document.getElementById('appointmentModal').classList.add('hidden');
}

function createAppointment(event) {
    event.preventDefault();
    
    const title = document.getElementById('appointmentTitle').value;
    const date = document.getElementById('appointmentDate').value;
    const time = document.getElementById('appointmentTime').value;
    const duration = document.getElementById('appointmentDuration').value;
    const notes = document.getElementById('appointmentNotes').value;
    
    fetch('/messages/appointments', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            conversation_id: {{ $conversation->id }},
            title: title,
            date: date,
            time: time,
            duration: duration,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeAppointmentModal();
            alert('Appointment scheduled successfully!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Auto-scroll to bottom
window.addEventListener('load', function() {
    const container = document.getElementById('messagesContainer');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
});

// Real-time updates with WebSocket
const socket = new WebSocket('ws://localhost:6001');

socket.onmessage = function(event) {
    const data = JSON.parse(event.data);
    if (data.type === 'new_message' && data.conversation_id == {{ $conversation->id }}) {
        location.reload();
    }
};
</script>
@endsection
