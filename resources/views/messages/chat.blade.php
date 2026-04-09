@extends('layouts.app')

@section('title', 'الدردشة')

@section('content')
<div class="h-[calc(100vh-64px)] bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-100 flex flex-col">
    <!-- Animated Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-20 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
        <div class="absolute top-40 right-20 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse animation-delay-2000"></div>
        <div class="absolute bottom-20 left-1/2 w-80 h-80 bg-indigo-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse animation-delay-4000"></div>
    </div>

    <div class="relative z-10 container mx-auto px-4 py-4 flex-1 flex flex-col min-h-0">
        <!-- Header -->
        <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-md p-4 mb-4 border border-white/20 flex justify-between items-center shrink-0">
            <div>
                <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent flex items-center gap-2">
                    <i class="fas fa-comments"></i>
                    الدردشة
                </h1>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="createChatRoom()" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-4 py-2 rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all duration-300 shadow-md text-sm">
                    <i class="fas fa-plus ml-1"></i>
                    إنشاء غرفة
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 flex-1 min-h-0">
            <!-- Chat Rooms Sidebar -->
            <div class="lg:col-span-1 flex flex-col min-h-0">
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 flex flex-col h-full overflow-hidden">
                    <div class="p-4 border-b border-gray-200/50 shrink-0">
                        <div class="flex space-x-2 overflow-x-auto pb-2 scrollbar-hide">
                            <button onclick="filterItems('all')" class="whitespace-nowrap px-3 py-1.5 rounded-lg text-xs font-medium bg-purple-100 text-purple-700 hover:bg-purple-200 transition-colors" id="filter-all">
                                الكل
                            </button>
                            <button onclick="filterItems('group')" class="whitespace-nowrap px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors" id="filter-group">
                                المجموعات
                            </button>
                            <button onclick="filterItems('private')" class="whitespace-nowrap px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors" id="filter-private">
                                الخاص
                            </button>
                        </div>
                    </div>
                        
                    <div class="flex-1 overflow-y-auto p-2 space-y-2">
                        @forelse ($chatRooms as $room)
                            <div onclick="openRoom({{ $room->id }})" data-type="group" class="chat-item group p-3 rounded-xl hover:bg-purple-50 cursor-pointer transition-all duration-200 {{ isset($activeRoom) && $room->id == $activeRoom ? 'bg-purple-50 border border-purple-100 shadow-sm' : 'border border-transparent' }}">
                                <div class="flex items-center space-x-3">
                                    <div class="relative shrink-0">
                                        <div class="w-10 h-10 bg-gradient-to-br from-{{ $room->type === 'public' ? 'green' : 'purple' }}-400 to-{{ $room->type === 'public' ? 'green' : 'purple' }}-600 rounded-lg flex items-center justify-center shadow-sm text-white">
                                            <i class="fas fa-{{ $room->type === 'public' ? 'globe' : 'lock' }} text-sm"></i>
                                        </div>
                                        @if($room->unread_count > 0)
                                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full border-2 border-white"></div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-gray-800 truncate text-sm group-hover:text-purple-700 transition-colors">{{ $room->name }}</h4>
                                        <p class="text-xs text-gray-500 flex items-center gap-1">
                                            <i class="fas fa-users text-[10px]"></i>
                                            {{ $room->participants_count }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                        @endforelse

                        @forelse ($conversations as $conversation)
                            @php
                                $otherUser = $conversation->sender_id == auth()->id() ? $conversation->receiver : $conversation->sender;
                            @endphp
                            <div onclick="openConversation({{ $conversation->id }})" data-type="private" class="chat-item group p-3 rounded-xl hover:bg-purple-50 cursor-pointer transition-all duration-200 {{ isset($activeConversation) && $conversation->id == $activeConversation ? 'bg-purple-50 border border-purple-100 shadow-sm' : 'border border-transparent' }}">
                                <div class="flex items-center space-x-3">
                                    <div class="relative shrink-0">
                                        <img src="{{ $otherUser->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($otherUser->full_name ?? $otherUser->name) . '&color=7F9CF5&background=EBF4FF' }}" class="w-10 h-10 rounded-lg border border-gray-200">
                                        @if($otherUser->is_online)
                                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-gray-800 truncate text-sm group-hover:text-purple-700 transition-colors">{{ $otherUser->full_name ?? $otherUser->name }}</h4>
                                        <p class="text-xs text-gray-500 flex items-center gap-1">
                                            @if($conversation->lastMessage)
                                                <span class="truncate">{{ Str::limit($conversation->lastMessage->content, 20) }}</span>
                                            @else
                                                <span>لا توجد رسائل</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                        @endforelse
                        
                        @if($chatRooms->isEmpty() && $conversations->isEmpty())
                            <div class="p-8 text-center">
                                <p class="text-gray-500 text-sm">لا توجد محادثات</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="lg:col-span-3 flex flex-col min-h-0">
                @if($activeRoom || $activeConversation)
                    <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 flex flex-col h-full overflow-hidden">
                        <!-- Room Header -->
                        <div class="p-4 border-b border-gray-200/50 bg-white/50 shrink-0 flex justify-between items-center">
                            <div class="flex items-center space-x-3">
                                @if($activeRoomData)
                                    <div class="w-10 h-10 bg-gradient-to-br from-{{ $activeRoomData->type === 'public' ? 'green' : 'purple' }}-400 to-{{ $activeRoomData->type === 'public' ? 'green' : 'purple' }}-600 rounded-lg flex items-center justify-center shadow-md text-white">
                                        <i class="fas fa-{{ $activeRoomData->type === 'public' ? 'globe' : 'lock' }}"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900">{{ $activeRoomData->name }}</h3>
                                        <p class="text-xs text-gray-500 flex items-center gap-1">
                                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                            {{ $activeRoomData->participants_count }} متصل
                                        </p>
                                    </div>
                                @elseif($activeConversationData)
                                    @php
                                        $otherUser = $activeConversationData->sender_id == auth()->id() ? $activeConversationData->receiver : $activeConversationData->sender;
                                    @endphp
                                    <div class="w-10 h-10 rounded-lg overflow-hidden border border-gray-200">
                                        <img src="{{ $otherUser->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($otherUser->full_name ?? $otherUser->name) . '&color=7F9CF5&background=EBF4FF' }}" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900">{{ $otherUser->full_name ?? $otherUser->name }}</h3>
                                        <p class="text-xs text-gray-500 flex items-center gap-1">
                                            @if($otherUser->is_online)
                                                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                                متصل الآن
                                            @else
                                                <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                                غير متصل
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                @if($activeRoomData)
                                    <button onclick="showParticipants()" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors" title="المشاركين">
                                        <i class="fas fa-users"></i>
                                    </button>
                                    <button onclick="roomSettings()" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors" title="الإعدادات">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <button onclick="leaveRoom({{ $activeRoomData->id }})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="مغادرة">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Messages -->
                        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50/50" id="chatMessages">
                            @foreach ($chatMessages as $message)
                                <div class="flex {{ $message->user_id == auth()->id() ? 'justify-end' : 'justify-start' }} group">
                                    <div class="max-w-[80%] lg:max-w-[70%]">
                                        <div class="flex items-end gap-2 {{ $message->user_id == auth()->id() ? 'flex-row-reverse' : 'flex-row' }}">
                                            @if($message->user_id != auth()->id())
                                                <img src="{{ $message->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($message->user->full_name ?? $message->user->name ?? $message->user->first_name) . '&color=7F9CF5&background=EBF4FF' }}" class="w-8 h-8 rounded-full border border-white shadow-sm mb-1">
                                            @endif
                                            
                                            <div>
                                                @if($message->user_id != auth()->id())
                                                    <span class="text-[10px] text-gray-500 px-1 mb-0.5 block">{{ $message->user->full_name ?? $message->user->first_name }}</span>
                                                @endif
                                                
                                                <div class="{{ $message->user_id == auth()->id() ? 'bg-purple-600 text-white rounded-l-2xl rounded-tr-2xl' : 'bg-white text-gray-800 rounded-r-2xl rounded-tl-2xl shadow-sm border border-gray-100' }} px-4 py-2.5 text-sm leading-relaxed">
                                                    <p>{{ $message->content }}</p>
                                                    @if($message->attachment)
                                                        <div class="mt-2">
                                                            @if(str_contains($message->attachment->mime_type, 'image'))
                                                                <img src="{{ $message->attachment->url }}" class="rounded-lg max-w-full shadow-sm cursor-pointer hover:opacity-90 transition-opacity">
                                                            @else
                                                                <div class="flex items-center gap-2 text-xs bg-black/10 rounded p-2">
                                                                    <i class="fas fa-file"></i>
                                                                    <span>{{ $message->attachment->name }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                                <p class="text-[10px] text-gray-400 mt-1 px-1 {{ $message->user_id == auth()->id() ? 'text-left' : 'text-right' }}">
                                                    {{ $message->created_at->format('h:i A') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Message Input -->
                        <div class="p-4 bg-white border-t border-gray-200/50 shrink-0">
                            <form onsubmit="sendChatMessage(event)" class="flex items-center gap-2">
                                <button type="button" onclick="attachChatFile()" class="p-3 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-xl transition-all relative">
                                    <i class="fas fa-paperclip"></i>
                                    <span id="fileCountBadge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">0</span>
                                </button>
                                <input type="file" id="chatAttachment" class="hidden" multiple onchange="handleFileSelect(event)">
                                
                                <div class="flex-1 bg-gray-100 rounded-xl flex items-center px-4 py-2 border border-transparent focus-within:border-purple-300 focus-within:bg-white focus-within:ring-2 focus-within:ring-purple-100 transition-all">
                                    <input type="text" id="chatMessageInput" placeholder="اكتب رسالتك هنا..." class="flex-1 bg-transparent border-none outline-none text-sm text-gray-700 placeholder-gray-400 h-10">
                                    <button type="button" onclick="insertChatEmoji()" class="text-gray-400 hover:text-yellow-500 transition-colors">
                                        <i class="far fa-smile"></i>
                                    </button>
                                </div>
                                
                                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white p-3 rounded-xl shadow-lg shadow-purple-200 transition-all transform hover:scale-105 active:scale-95">
                                    <i class="fas fa-paper-plane rtl:rotate-180"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 h-full flex items-center justify-center">
                        <div class="text-center p-8">
                            <div class="w-20 h-20 bg-purple-50 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
                                <i class="fas fa-comments text-3xl text-purple-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">مرحباً بك في الدردشة</h3>
                            <p class="text-gray-500 text-sm max-w-xs mx-auto">اختر غرفة من القائمة الجانبية لبدء المحادثة أو قم بإنشاء غرفة جديدة</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Create Chat Room Modal -->
<div id="createRoomModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-8 max-w-md mx-4 w-full shadow-2xl transform transition-all duration-300">
        <h3 class="text-xl font-bold text-gray-800 mb-6">
            <i class="fas fa-plus-circle text-purple-600 ml-2"></i>
            إنشاء غرفة دردشة
        </h3>
        
        <form onsubmit="createNewRoom(event)">
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">اسم الغرفة</label>
                    <input type="text" id="roomName" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300">
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">نوع الغرفة</label>
                    <select id="roomType" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300">
                        <option value="public">عامة</option>
                        <option value="private">خاصة</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">الوصف</label>
                    <textarea id="roomDescription" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">دعوة مستخدمين</label>
                    <select id="inviteUsers" multiple class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300">
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->full_name ?? $user->first_name . ' ' . $user->last_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-8">
                <button type="button" onclick="closeCreateRoomModal()" class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all duration-300">
                    إلغاء
                </button>
                <button type="submit" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    إنشاء غرفة
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Participants Modal -->
<div id="participantsModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-8 max-w-md mx-4 w-full shadow-2xl transform transition-all duration-300">
        <h3 class="text-xl font-bold text-gray-800 mb-6">
            <i class="fas fa-users text-purple-600 ml-2"></i>
            المشاركين
        </h3>
        
        <div id="participantsList" class="space-y-4 max-h-96 overflow-y-auto">
            <!-- Participants will be loaded here -->
        </div>
        
        <div class="flex justify-end mt-8">
            <button type="button" onclick="closeParticipantsModal()" class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all duration-300">
                إغلاق
            </button>
        </div>
    </div>
</div>

<!-- Room Settings Modal -->
<div id="roomSettingsModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-8 max-w-md mx-4 w-full shadow-2xl transform transition-all duration-300">
        <h3 class="text-xl font-bold text-gray-800 mb-6">
            <i class="fas fa-cog text-purple-600 ml-2"></i>
            إعدادات الغرفة
        </h3>
        
        <form onsubmit="saveRoomSettings(event)">
            <input type="hidden" id="editRoomId">
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">اسم الغرفة</label>
                    <input type="text" id="editRoomName" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300">
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">نوع الغرفة</label>
                    <select id="editRoomType" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300">
                        <option value="public">عامة</option>
                        <option value="private">خاصة</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">الوصف</label>
                    <textarea id="editRoomDescription" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"></textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-8">
                <button type="button" onclick="closeRoomSettingsModal()" class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all duration-300">
                    إلغاء
                </button>
                <button type="submit" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Define current chat context
    let chatContext = {
        type: '{{ $activeRoom ? "room" : ($activeConversation ? "conversation" : "none") }}',
        id: {{ $activeRoom ?? $activeConversation ?? 'null' }}
    };

function filterItems(type) {
    const items = document.querySelectorAll('.chat-item');
    
    // Update active button state
    document.querySelectorAll('[id^="filter-"]').forEach(btn => {
        if (btn.id === 'filter-' + type) {
            btn.classList.remove('bg-gray-100', 'text-gray-600');
            btn.classList.add('bg-purple-100', 'text-purple-700');
        } else {
            btn.classList.add('bg-gray-100', 'text-gray-600');
            btn.classList.remove('bg-purple-100', 'text-purple-700');
        }
    });

    items.forEach(item => {
        if (type === 'all') {
            item.classList.remove('hidden');
        } else {
            if (item.dataset.type === type) {
                item.classList.remove('hidden');
            } else if (type === 'group' && item.dataset.type === 'group') {
                 item.classList.remove('hidden');
            } else if (type === 'private' && item.dataset.type === 'private') {
                 item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        }
    });
}

function openRoom(roomId) {
    // Update active state in sidebar
    document.querySelectorAll('.chat-item').forEach(el => {
        el.classList.remove('bg-purple-50', 'border-purple-100', 'shadow-sm');
        el.classList.add('border-transparent');
    });
    
    // Highlight selected room (need to find the element by onclick attribute or ID)
    // For now, let's just fetch data
    
    fetch('/messages/chat/json/room/' + roomId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateChatUI(data);
                // Update URL without reload
                const url = new URL(window.location);
                url.searchParams.set('room_id', roomId);
                url.searchParams.delete('conversation_id');
                window.history.pushState({}, '', url);
            } else {
                alert(data.error || 'Failed to load room');
            }
        })
        .catch(error => console.error('Error loading room:', error));
}

function openConversation(conversationId) {
    fetch('/messages/chat/json/conversation/' + conversationId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateChatUI(data);
                // Update URL without reload
                const url = new URL(window.location);
                url.searchParams.set('conversation_id', conversationId);
                url.searchParams.delete('room_id');
                window.history.pushState({}, '', url);
            } else {
                alert(data.error || 'Failed to load conversation');
            }
        })
        .catch(error => console.error('Error loading conversation:', error));
}

function updateChatUI(data) {
    // Update Chat Context
    chatContext = {
        type: data.type,
        id: data.data.id
    };
    
    // Update Header
    const headerTitle = document.querySelector('.bg-white\\/90 .font-bold.text-gray-900'); // Need better selector
    // Actually, let's assume we replace the content of the chat area or specific elements.
    // Since the structure is complex, it's better to update specific IDs if they exist.
    // Looking at the HTML, the header is dynamic.
    
    // Let's reload the page content wrapper if possible, or update specific elements.
    // Since the HTML structure is deeply nested and conditional (@if($activeRoomData)), 
    // it might be hard to update DOM elements one by one if they don't exist (e.g. switching from "Welcome" screen to Chat).
    
    // Strategy: If we are in "Welcome" state (no chat), we might need to reload or have a hidden template.
    // However, the user wants "no page reload".
    // The easiest way for complex UI is to have the backend return the partial HTML view for the chat area.
    // But I created JSON endpoints.
    
    // I will implement a client-side render for the chat area.
    // This requires having the chat area container always present or creating it.
    
    // Check if chat area exists
    const chatArea = document.querySelector('.lg\\:col-span-3');
    if (!chatArea) return; // Should not happen
    
    // Build HTML for Header
    let headerHtml = '';
    if (data.type === 'room') {
        const room = data.data;
        headerHtml = `
            <div class="p-4 border-b border-gray-200/50 bg-white/50 shrink-0 flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-${room.type === 'public' ? 'green' : 'purple'}-400 to-${room.type === 'public' ? 'green' : 'purple'}-600 rounded-lg flex items-center justify-center shadow-md text-white">
                        <i class="fas fa-${room.type === 'public' ? 'globe' : 'lock'}"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">${room.name}</h3>
                        <p class="text-xs text-gray-500 flex items-center gap-1">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            ${room.participants_count || 0} متصل
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="showParticipants()" class="p-2 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="المشاركين">
                        <i class="fas fa-users"></i>
                    </button>
                    <button onclick="roomSettings()" class="p-2 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="الإعدادات">
                        <i class="fas fa-cog"></i>
                    </button>
                    <button onclick="leaveRoom(${room.id})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="مغادرة">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </div>
            </div>
        `;
    } else {
        const conversation = data.data;
        const otherUser = conversation.other_user;
        headerHtml = `
            <div class="p-4 border-b border-gray-200/50 bg-white/50 shrink-0 flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <img src="${otherUser.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(otherUser.name || otherUser.first_name || 'User') + '&color=7F9CF5&background=EBF4FF'}" class="w-10 h-10 rounded-lg border border-gray-200 shadow-sm">
                        ${otherUser.is_online ? '<div class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>' : ''}
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">${otherUser.name || otherUser.first_name + ' ' + otherUser.last_name}</h3>
                        <p class="text-xs text-gray-500 flex items-center gap-1">
                            ${otherUser.is_online ? '<span class="text-green-500">متصل الآن</span>' : '<span>غير متصل</span>'}
                        </p>
                    </div>
                </div>
            </div>
        `;
    }

    // Build Messages HTML
    const messagesContainer = document.getElementById('chatMessages');
    // If chatMessages doesn't exist (because we were in welcome screen), we need to render the full container
    
    if (!messagesContainer) {
        // Render full chat layout
        chatArea.innerHTML = `
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 flex flex-col h-full overflow-hidden">
                ${headerHtml}
                <div id="chatMessages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50/50 scroll-smooth"></div>
                <div class="p-4 bg-white border-t border-gray-200/50 shrink-0">
                    <form onsubmit="sendChatMessage(event)" class="flex items-center gap-2">
                        <button type="button" onclick="attachChatFile()" class="p-3 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-xl transition-all relative">
                            <i class="fas fa-paperclip"></i>
                            <span id="fileCountBadge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">0</span>
                        </button>
                        <input type="file" id="chatAttachment" class="hidden" multiple onchange="handleFileSelect(event)">
                        
                        <div class="flex-1 bg-gray-100 rounded-xl flex items-center px-4 py-2 border border-transparent focus-within:border-purple-300 focus-within:bg-white focus-within:ring-2 focus-within:ring-purple-100 transition-all">
                            <input type="text" id="chatMessageInput" placeholder="اكتب رسالتك هنا..." class="flex-1 bg-transparent border-none outline-none text-sm text-gray-700 placeholder-gray-400 h-10">
                            <button type="button" onclick="insertChatEmoji()" class="text-gray-400 hover:text-yellow-500 transition-colors">
                                <i class="far fa-smile"></i>
                            </button>
                        </div>
                        
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white p-3 rounded-xl shadow-lg shadow-purple-200 transition-all transform hover:scale-105 active:scale-95">
                            <i class="fas fa-paper-plane rtl:rotate-180"></i>
                        </button>
                    </form>
                </div>
            </div>
        `;
    } else {
        // Just update header and messages
        // Update Header
        const headerContainer = chatArea.querySelector('.border-b'); // Assuming first border-b is header
        if (headerContainer) headerContainer.outerHTML = headerHtml;
        else chatArea.querySelector('.bg-white\\/90').insertAdjacentHTML('afterbegin', headerHtml); // Fallback
    }

    // Populate Messages
    const container = document.getElementById('chatMessages');
    container.innerHTML = '';
    
    if (data.messages && data.messages.length > 0) {
        data.messages.forEach(msg => appendMessage(msg));
        lastMessageId = data.messages[data.messages.length - 1].id;
    } else {
        lastMessageId = 0;
        container.innerHTML = '<div class="text-center text-gray-500 mt-10">لا توجد رسائل بعد</div>';
    }
    
    // Scroll to bottom
    container.scrollTop = container.scrollHeight;
}

function handleFileSelect(event) {
    const files = event.target.files;
    const badge = document.getElementById('fileCountBadge');
    
    if (files.length > 0) {
        badge.textContent = files.length;
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }
}

function attachChatFile() {
    document.getElementById('chatAttachment').click();
}

function sendChatMessage(event) {
    event.preventDefault();
    
    const input = document.getElementById('chatMessageInput');
    const content = input.value.trim();
    const fileInput = document.getElementById('chatAttachment');
    const files = fileInput.files;
    
    if (!content && files.length === 0) return;
    
    if (chatContext.type === 'none' || !chatContext.id) return;

    const formData = new FormData();
    formData.append('content', content);
    formData.append('type', files.length > 0 ? 'file' : 'text'); // Simplification
    
    if (chatContext.type === 'room') {
        formData.append('chat_room_id', chatContext.id);
    } else if (chatContext.type === 'conversation') {
        formData.append('conversation_id', chatContext.id);
    }
    
    for (let i = 0; i < files.length; i++) {
        formData.append('attachments[]', files[i]);
    }

    fetch('/messages/chat/send', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            fileInput.value = ''; // Clear files
            document.getElementById('fileCountBadge').classList.add('hidden');
            
            // Append message to chat
            appendMessage(data.message);
        } else {
            console.error('Failed to send message:', data.message);
            alert('Failed to send message: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while sending the message');
    });
}

function joinRoom(roomId) {
    // Try to join the room first
    fetch('/messages/chat/room/' + roomId + '/join', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || data.error === 'Already a participant') {
            // Redirect to the chat view with this room active
            window.location.href = '/messages/chat?room_id=' + roomId;
        } else {
            alert(data.error || 'Failed to join room');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Fallback redirect
        window.location.href = '/messages/chat?room_id=' + roomId;
    });
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
            closeCreateRoomModal();
            // Redirect to the new room
            window.location.href = '/messages/chat?room_id=' + data.room_id;
        } else {
            alert(data.message || 'Failed to create room');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the room');
    });
}


function appendMessage(message) {
    const chatMessages = document.getElementById('chatMessages');
    const isCurrentUser = message.user_id == {{ auth()->id() }};
    
    let attachmentsHtml = '';
    if (message.attachments && message.attachments.length > 0) {
        attachmentsHtml = '<div class="mt-2 space-y-2">';
        message.attachments.forEach(att => {
            const ext = att.file_path.split('.').pop().toLowerCase();
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                attachmentsHtml += `
                    <a href="/storage/${att.file_path}" target="_blank" class="block">
                        <img src="/storage/${att.file_path}" class="max-w-xs rounded-lg shadow-sm hover:opacity-90 transition-opacity">
                    </a>
                `;
            } else if (['mp4', 'webm', 'ogg'].includes(ext)) {
                 attachmentsHtml += `
                    <video controls class="max-w-xs rounded-lg shadow-sm">
                        <source src="/storage/${att.file_path}" type="video/${ext}">
                        Your browser does not support the video tag.
                    </video>
                `;
            } else {
                attachmentsHtml += `
                    <a href="/storage/${att.file_path}" target="_blank" class="flex items-center space-x-2 p-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors">
                        <i class="fas fa-file text-white"></i>
                        <span class="text-sm underline truncate max-w-[200px]">${att.file_name || 'File'}</span>
                    </a>
                `;
            }
        });
        attachmentsHtml += '</div>';
    }

    const messageHtml = `
        <div class="flex ${isCurrentUser ? 'justify-end' : 'justify-start'} animate-fade-in mb-4">
            <div class="max-w-xs lg:max-w-md">
                <div class="flex items-center space-x-2 mb-1 ${isCurrentUser ? 'justify-end' : 'justify-start'}">
                    ${!isCurrentUser ? `
                        <img src="${message.user.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(message.user.name || message.user.first_name || 'User') + '&color=7F9CF5&background=EBF4FF'}" alt="" class="w-8 h-8 rounded-full border-2 border-white shadow-md">
                        <span class="text-xs text-gray-600 font-medium">${message.user.name || message.user.first_name}</span>
                    ` : ''}
                </div>
                <div class="${isCurrentUser ? 'bg-gradient-to-r from-purple-600 to-blue-600 text-white' : 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800'} rounded-2xl px-4 py-3 shadow-lg">
                    <p class="text-sm break-words whitespace-pre-wrap">${message.content || ''}</p>
                    ${attachmentsHtml}
                </div>
                <p class="text-xs text-gray-500 mt-2 ${isCurrentUser ? 'text-right' : 'text-left'}">
                    ${new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                </p>
            </div>
        </div>
    `;
    
    chatMessages.insertAdjacentHTML('beforeend', messageHtml);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function showParticipants() {
    const roomId = {{ $activeRoomData->id ?? 'null' }};
    if (!roomId) return;

    fetch('/messages/chat/room/' + roomId + '/participants')
        .then(response => response.json())
        .then(data => {
            if (data.participants) {
                const list = document.getElementById('participantsList');
                list.innerHTML = '';
                
                data.participants.forEach(p => {
                    const user = p.user;
                    const isOnline = p.is_online; // Backend should provide this or we use user.is_online
                    const html = `
                        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50">
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <img src="${user.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name || user.first_name || 'User') + '&color=7F9CF5&background=EBF4FF'}" class="w-10 h-10 rounded-full border border-gray-200">
                                    <div class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white ${user.is_online ? 'bg-green-500' : 'bg-gray-400'}"></div>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">${user.name || user.first_name + ' ' + user.last_name}</h4>
                                    <span class="text-xs text-purple-600 font-medium bg-purple-100 px-2 py-0.5 rounded-lg">${p.role}</span>
                                </div>
                            </div>
                        </div>
                    `;
                    list.insertAdjacentHTML('beforeend', html);
                });
                
                document.getElementById('participantsModal').classList.remove('hidden');
            }
        });
}

function closeParticipantsModal() {
    document.getElementById('participantsModal').classList.add('hidden');
}

function roomSettings() {
    const roomId = {{ $activeRoomData->id ?? 'null' }};
    if (!roomId) return;
    
    // Fill current values
    document.getElementById('editRoomId').value = roomId;
    document.getElementById('editRoomName').value = "{{ $activeRoomData->name ?? '' }}";
    document.getElementById('editRoomType').value = "{{ $activeRoomData->type ?? 'public' }}";
    document.getElementById('editRoomDescription').value = "{{ $activeRoomData->description ?? '' }}";
    
    document.getElementById('roomSettingsModal').classList.remove('hidden');
}

function closeRoomSettingsModal() {
    document.getElementById('roomSettingsModal').classList.add('hidden');
}

function saveRoomSettings(event) {
    event.preventDefault();
    
    const roomId = document.getElementById('editRoomId').value;
    const name = document.getElementById('editRoomName').value;
    const type = document.getElementById('editRoomType').value;
    const description = document.getElementById('editRoomDescription').value;
    
    fetch('/messages/chat/room/' + roomId + '/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            name: name,
            type: type,
            description: description
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeRoomSettingsModal();
            // Reload page to show changes
            window.location.reload();
        } else {
            alert(data.error || 'Failed to update room');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the room');
    });
}

function leaveRoom(roomId) {
    if (confirm('هل أنت متأكد من مغادرة هذه الغرفة؟')) {
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


function insertChatEmoji() {
    alert('ميزة الرموز التعبيرية قريباً!');
}

// Auto-scroll to bottom of chat
window.addEventListener('load', function() {
    const container = document.getElementById('chatMessages');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
    
    // Start polling for new messages
    if (typeof startMessagePolling === 'function') {
        startMessagePolling();
    }
});

// WebSocket connection for real-time messages
const socket = new WebSocket('ws://localhost:6001');

socket.onmessage = function(event) {
    const data = JSON.parse(event.data);
    if (data.type === 'chat_message') {
        console.log('New chat message:', data);
        appendMessage(data.message);
    }
};

// Message Polling System
let lastMessageId = 0;
@if(isset($chatMessages) && count($chatMessages) > 0)
    lastMessageId = {{ $chatMessages->last()->id }};
@endif

function startMessagePolling() {
    if (chatContext.type === 'none' || !chatContext.id) return;
    
    // Initial fetch to get any messages since page load
    fetchMessages();

    setInterval(() => {
        fetchMessages();
    }, 3000); // Poll every 3 seconds
}

function fetchMessages() {
    let url = '';
    if (chatContext.type === 'room') {
        url = '/messages/chat/room/' + chatContext.id + '/messages?after_id=' + lastMessageId;
    } else if (chatContext.type === 'conversation') {
        url = '/messages/chat/conversation/' + chatContext.id + '/messages?after_id=' + lastMessageId;
    }

    if (!url) return;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(message => {
                    if (message.id > lastMessageId) {
                        appendMessage(message);
                        lastMessageId = message.id;
                    }
                });
            }
        })
        .catch(error => console.error('Polling error:', error));
}
</script>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.backdrop-blur-lg {
    backdrop-filter: blur(16px);
}

.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}
</style>
@endsection
