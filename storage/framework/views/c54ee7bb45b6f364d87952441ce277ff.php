

<?php $__env->startSection('title', 'Messages Inbox'); ?>

<?php $__env->startSection('content'); ?>
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
                        <?php $__empty_1 = true; $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conversation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div onclick="openConversation(<?php echo e($conversation->id); ?>)" class="p-4 hover:bg-gray-50 cursor-pointer">
                                <div class="flex items-start space-x-3">
                                    <div class="relative">
                                        <?php if($conversation->sender_id == auth()->id() && $conversation->receiver->avatar): ?>
                                            <img src="<?php echo e($conversation->receiver->avatar); ?>" alt="" class="w-10 h-10 rounded-full">
                                        <?php elseif($conversation->receiver_id == auth()->id() && $conversation->sender->avatar): ?>
                                            <img src="<?php echo e($conversation->sender->avatar); ?>" alt="" class="w-10 h-10 rounded-full">
                                        <?php else: ?>
                                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user text-gray-500 text-sm"></i>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($conversation->hasUnreadMessagesForUser(auth()->id())): ?>
                                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-blue-600 rounded-full"></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-start">
                                            <h4 class="font-medium text-gray-900 truncate">
                                                <?php echo e($conversation->sender_id == auth()->id() ? $conversation->receiver->full_name : $conversation->sender->full_name); ?>

                                            </h4>
                                            <span class="text-xs text-gray-500">
                                                <?php echo e($conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : 'Never'); ?>

                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 truncate">
                                            <?php echo e($conversation->last_message_preview ?? 'No messages yet'); ?>

                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="p-8 text-center">
                                <i class="fas fa-comments text-4xl text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No conversations yet</h3>
                                <p class="text-gray-500 mb-4">Start messaging other users</p>
                                <button onclick="composeMessage()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    Start Conversation
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Conversation View -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm h-96 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-comments text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Select a conversation</h3>
                        <p class="text-gray-500">Choose a conversation from the list to start messaging</p>
                    </div>
                </div>
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
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>"><?php echo e($user->full_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
    
    // This function is for the conversation view, not inbox
    alert('Please select a conversation first to send messages');
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/messages/inbox.blade.php ENDPATH**/ ?>