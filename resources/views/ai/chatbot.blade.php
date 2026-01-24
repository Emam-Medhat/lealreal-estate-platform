@extends('layouts.app')

@section('title', 'المساعد الذكي للعقارات')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-robot me-2"></i>
            المساعد الذكي للعقارات
        </h1>
        <div>
            <button type="button" class="btn btn-primary" onclick="startNewConversation()">
                <i class="fas fa-plus me-2"></i>محادثة جديدة
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['total_conversations'] ?? 0 }}</h4>
                            <p class="card-text">إجمالي المحادثات</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-comments fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['active_conversations'] ?? 0 }}</h4>
                            <p class="card-text">محادثات نشطة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['resolved_conversations'] ?? 0 }}</h4>
                            <p class="card-text">محادثات محلولة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['avg_satisfaction'] ?? 0 }}%</h4>
                            <p class="card-text">متوسط الرضا</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-smile fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Chat Interface -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-comments me-2"></i>
                        واجهة الدردشة
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div id="chatMessages" class="chat-messages" style="height: 500px; overflow-y: auto; padding: 20px; background-color: #f8f9fa;">
                        <!-- Welcome Message -->
                        <div class="text-center mb-4">
                            <i class="fas fa-robot fa-3x text-primary mb-3"></i>
                            <h5>مرحباً بك في المساعد الذكي للعقارات</h5>
                            <p class="text-muted">كيف يمكنني مساعدتك اليوم؟</p>
                        </div>
                    </div>
                    <div class="chat-input p-3 border-top">
                        <form id="chatForm">
                            @csrf
                            <div class="input-group">
                                <input type="text" id="messageInput" class="form-control" placeholder="اكتب رسالتك هنا..." required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversations List -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>
                        المحادثات السابقة
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 500px; overflow-y: auto;">
                        @forelse ($conversations ?? [] as $conversation)
                            <a href="#" class="list-group-item list-group-item-action" onclick="loadConversation({{ $conversation->id }})">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $conversation->conversation_type_label }}</h6>
                                        <p class="mb-1 text-truncate">{{ $conversation->last_message['text'] ?? 'لا توجد رسائل' }}</p>
                                        <small class="text-muted">{{ $conversation->created_at->format('Y-m-d H:i') }}</small>
                                    </div>
                                    <div class="ms-2">
                                        @if ($conversation->status == 'active')
                                            <span class="badge bg-success">نشط</span>
                                        @elseif ($conversation->resolution_status == 'resolved')
                                            <span class="badge bg-primary">محلول</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $conversation->status_label }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center p-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">لا توجد محادثات سابقة</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Conversation Details Modal -->
<div class="modal fade" id="conversationDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل المحادثة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="conversationDetailsContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" onclick="exportConversation()">
                    <i class="fas fa-download me-2"></i>تصدير المحادثة
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentConversationId = null;
let isTyping = false;

// Chat Form
document.getElementById('chatForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    
    if (!message) return;
    
    // Add user message
    addMessage('user', message);
    messageInput.value = '';
    
    // Show typing indicator
    showTypingIndicator();
    
    // Send to AI
    sendMessageToAI(message);
});

// Add message to chat
function addMessage(sender, message, timestamp = null) {
    const chatMessages = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `d-flex mb-3 ${sender === 'user' ? 'justify-content-end' : 'justify-content-start'}`;
    
    const time = timestamp || new Date().toLocaleTimeString('ar-SA');
    
    messageDiv.innerHTML = `
        <div class="max-width-70">
            <div class="card ${sender === 'user' ? 'bg-primary text-white' : 'bg-light'}">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>${sender === 'user' ? 'أنت' : 'المساعد الذكي'}</strong>
                        <small class="${sender === 'user' ? 'text-white' : 'text-muted'}">${time}</small>
                    </div>
                    <p class="mb-0">${message}</p>
                </div>
            </div>
        </div>
    `;
    
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Show typing indicator
function showTypingIndicator() {
    const chatMessages = document.getElementById('chatMessages');
    const typingDiv = document.createElement('div');
    typingDiv.id = 'typingIndicator';
    typingDiv.className = 'd-flex justify-content-start mb-3';
    typingDiv.innerHTML = `
        <div class="max-width-70">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center">
                        <strong>المساعد الذكي</strong>
                        <div class="ms-2">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">جاري الكتابة...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    chatMessages.appendChild(typingDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Hide typing indicator
function hideTypingIndicator() {
    const typingIndicator = document.getElementById('typingIndicator');
    if (typingIndicator) {
        typingIndicator.remove();
    }
}

// Send message to AI
function sendMessageToAI(message) {
    const formData = new FormData();
    formData.append('message', message);
    formData.append('conversation_id', currentConversationId || '');
    
    fetch('{{ route("ai.chatbot.send") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideTypingIndicator();
        
        if (data.success) {
            addMessage('ai', data.response.text);
            
            // Update conversation ID if new
            if (data.conversation_id && !currentConversationId) {
                currentConversationId = data.conversation_id;
            }
            
            // Show suggestions if available
            if (data.response.suggestions && data.response.suggestions.length > 0) {
                showSuggestions(data.response.suggestions);
            }
        } else {
            addMessage('ai', 'عذراً، حدث خطأ. يرجى المحاولة مرة أخرى.');
        }
    })
    .catch(error => {
        hideTypingIndicator();
        console.error('Error:', error);
        addMessage('ai', 'عذراً، حدث خطأ في الاتصال بالخادم.');
    });
}

// Show suggestions
function showSuggestions(suggestions) {
    const chatMessages = document.getElementById('chatMessages');
    const suggestionsDiv = document.createElement('div');
    suggestionsDiv.className = 'd-flex justify-content-start mb-3';
    suggestionsDiv.innerHTML = `
        <div class="max-width-70">
            <div class="card bg-info text-white">
                <div class="card-body py-2">
                    <small class="d-block mb-2">اقتراحات:</small>
                    <div class="d-flex flex-wrap gap-2">
                        ${suggestions.map(suggestion => `<button type="button" class="btn btn-sm btn-light" onclick="sendSuggestion('${suggestion}')">${suggestion}</button>`).join('')}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    chatMessages.appendChild(suggestionsDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Send suggestion
function sendSuggestion(suggestion) {
    document.getElementById('messageInput').value = suggestion;
    document.getElementById('chatForm').dispatchEvent(new Event('submit'));
}

// Start new conversation
function startNewConversation() {
    currentConversationId = null;
    document.getElementById('chatMessages').innerHTML = `
        <div class="text-center mb-4">
            <i class="fas fa-robot fa-3x text-primary mb-3"></i>
            <h5>بدأت محادثة جديدة</h5>
            <p class="text-muted">كيف يمكنني مساعدتك اليوم؟</p>
        </div>
    `;
}

// Load conversation
function loadConversation(conversationId) {
    fetch(`/ai/chatbot/conversation/${conversationId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentConversationId = conversationId;
            displayConversation(data.conversation);
            new bootstrap.Modal(document.getElementById('conversationDetailsModal')).show();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'خطأ!',
            text: 'حدث خطأ أثناء تحميل المحادثة',
            confirmButtonText: 'موافق'
        });
    });
}

// Display conversation
function displayConversation(conversation) {
    const content = document.getElementById('conversationDetailsContent');
    const messages = conversation.messages || [];
    
    let html = `
        <div class="mb-3">
            <h6>تفاصيل المحادثة</h6>
            <div class="row">
                <div class="col-md-6">
                    <strong>النوع:</strong> ${conversation.conversation_type_label}<br>
                    <strong>الحالة:</strong> ${conversation.status_label}<br>
                    <strong>التاريخ:</strong> ${conversation.created_at}
                </div>
                <div class="col-md-6">
                    <strong>مستوى الرضا:</strong> ${conversation.satisfaction_level || 'غير محدد'}<br>
                    <strong>المدة:</strong> ${conversation.formatted_duration}<br>
                    <strong>عدد الرسائل:</strong> ${conversation.message_count}
                </div>
            </div>
        </div>
        <div class="chat-messages" style="max-height: 400px; overflow-y: auto; padding: 20px; background-color: #f8f9fa;">
    `;
    
    messages.forEach(msg => {
        const time = new Date(msg.timestamp).toLocaleTimeString('ar-SA');
        html += `
            <div class="d-flex mb-3 ${msg.sender === 'user' ? 'justify-content-end' : 'justify-content-start'}">
                <div class="max-width-70">
                    <div class="card ${msg.sender === 'user' ? 'bg-primary text-white' : 'bg-light'}">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>${msg.sender === 'user' ? 'المستخدم' : 'المساعد الذكي'}</strong>
                                <small class="${msg.sender === 'user' ? 'text-white' : 'text-muted'}">${time}</small>
                            </div>
                            <p class="mb-0">${msg.text}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    content.innerHTML = html;
}

// Export conversation
function exportConversation() {
    if (!currentConversationId) {
        Swal.fire({
            icon: 'warning',
            title: 'تنبيه!',
            text: 'يرجى اختيار محادثة أولاً',
            confirmButtonText: 'موافق'
        });
        return;
    }
    
    window.open(`/ai/chatbot/conversation/${currentConversationId}/export`, '_blank');
}

// Auto-scroll to bottom on load
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
});
</script>
@endpush
