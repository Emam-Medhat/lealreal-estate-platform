{{--
Floating AI Chatbot Widget
Premium Design with Glassmorphism and Smooth Animations
--}}

<div id="ai-chatbot-widget" class="ai-closed">
    <!-- Chat Toggle Button -->
    <button id="ai-chat-toggle" class="ai-pulse-animation">
        <i class="fas fa-comment-dots"></i>
        <div class="ai-button-ripple"></div>
    </button>

    <!-- Chat Window -->
    <div id="ai-chat-window">
        <!-- Header -->
        <div class="ai-chat-header">
            <div class="ai-bot-info">
                <div class="ai-avatar">
                    <i class="fas fa-robot"></i>
                    <span class="ai-online-status"></span>
                </div>
                <div>
                    <h4>مستشار العقارات الذكي</h4>
                    <p>متصل الآن - جاهز للمساعدة</p>
                </div>
            </div>
            <button id="ai-close-chat" title="إغلاق">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Message List -->
        <div id="ai-message-list">
            <div class="ai-message ai-bot-message ai-fade-in">
                <div class="ai-message-content">
                    أهلاً بك! أنا مستشارك العقاري الذكي. كيف يمكنني مساعدتك في العثور على منزلك المثالي اليوم؟
                </div>
            </div>
        </div>

        <!-- Typing Indicator (hidden by default) -->
        <div id="ai-typing-indicator" class="ai-hidden">
            <div class="ai-typing-dots">
                <span></span><span></span><span></span>
            </div>
        </div>

        <!-- Input Area -->
        <div class="ai-chat-input-area">
            <form id="ai-chat-form">
                <input type="text" id="ai-user-input" placeholder="اكتب سؤالك هنا..." autocomplete="off">
                <button type="submit" id="ai-send-btn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    /* ─────────────── CSS VARIABLES ─────────────── */
    :root {
        --ai-primary: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        --ai-accent: #a855f7;
        --ai-bg-glass: rgba(255, 255, 255, 0.85);
        --ai-text-dark: #1e293b;
        --ai-text-light: #64748b;
        --ai-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.2);
        --ai-radius: 20px;
    }

    /* ─────────────── CONTAINER & TOGGLE ─────────────── */
    #ai-chatbot-widget {
        position: fixed !important;
        bottom: 20px !important;
        right: 20px !important;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
        direction: rtl;
        top: auto !important;
        left: auto !important;
    }

    #ai-chat-toggle {
        width: 65px;
        height: 65px;
        border-radius: 50%;
        background: var(--ai-primary);
        border: none;
        color: white;
        font-size: 28px;
        cursor: pointer;
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
    }

    #ai-chat-toggle:hover {
        transform: scale(1.1) translateY(-5px);
        box-shadow: 0 15px 30px rgba(99, 102, 241, 0.6);
    }

    .ai-pulse-animation {
        animation: ai-pulse 2s infinite;
    }

    @keyframes ai-pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.7);
        }

        70% {
            box-shadow: 0 0 0 15px rgba(99, 102, 241, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(99, 102, 241, 0);
        }
    }

    /* ─────────────── CHAT WINDOW ─────────────── */
    #ai-chat-window {
        width: 380px;
        max-width: calc(100vw - 40px);
        height: 550px;
        max-height: calc(100vh - 120px);
        background: var(--ai-bg-glass);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: var(--ai-radius);
        box-shadow: var(--ai-shadow);
        margin-bottom: 80px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transform-origin: bottom right;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        opacity: 0;
        pointer-events: none;
        transform: scale(0.8) translateY(20px);
        position: absolute;
        bottom: 0;
        right: 0;
    }

    .ai-open #ai-chat-window {
        opacity: 1;
        pointer-events: auto;
        transform: scale(1) translateY(0);
    }

    .ai-open #ai-chat-toggle {
        transform: scale(0);
        opacity: 0;
    }

    /* ─────────────── HEADER ─────────────── */
    .ai-chat-header {
        background: var(--ai-primary);
        padding: 20px 25px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .ai-bot-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .ai-avatar {
        width: 45px;
        height: 45px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        position: relative;
    }

    .ai-online-status {
        position: absolute;
        top: -2px;
        left: -2px;
        width: 12px;
        height: 12px;
        background: #10b981;
        border: 2px solid white;
        border-radius: 50%;
    }

    .ai-bot-info h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
    }

    .ai-bot-info p {
        margin: 2px 0 0;
        font-size: 11px;
        opacity: 0.8;
    }

    #ai-close-chat {
        background: transparent;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.2s;
    }

    #ai-close-chat:hover {
        opacity: 1;
    }

    /* ─────────────── MESSAGES ─────────────── */
    #ai-message-list {
        flex: 1;
        padding: 25px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 15px;
        background: rgba(255, 255, 255, 0.3);
    }

    .ai-message {
        max-width: 85%;
        padding: 12px 18px;
        border-radius: 18px;
        font-size: 14px;
        line-height: 1.6;
        position: relative;
        word-wrap: break-word;
    }

    .ai-bot-message {
        align-self: flex-start;
        background: white;
        color: var(--ai-text-dark);
        border-bottom-right-radius: 4px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .ai-user-message {
        align-self: flex-end;
        background: var(--ai-primary);
        color: white;
        border-bottom-left-radius: 4px;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.2);
    }

    /* ─────────────── TYPING INDICATOR ─────────────── */
    #ai-typing-indicator {
        padding: 10px 25px;
        display: flex;
    }

    .ai-typing-dots {
        display: flex;
        gap: 4px;
        background: white;
        padding: 10px 15px;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .ai-typing-dots span {
        width: 6px;
        height: 6px;
        background: #cbd5e1;
        border-radius: 50%;
        animation: ai-typing-bounce 1.4s infinite ease-in-out;
    }

    .ai-typing-dots span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .ai-typing-dots span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes ai-typing-bounce {

        0%,
        80%,
        100% {
            transform: scale(0);
        }

        40% {
            transform: scale(1);
        }
    }

    .ai-hidden {
        display: none !important;
    }

    /* ─────────────── INPUT AREA ─────────────── */
    .ai-chat-input-area {
        padding: 20px;
        background: white;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    #ai-chat-form {
        display: flex;
        gap: 10px;
    }

    #ai-user-input {
        flex: 1;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        padding: 12px 15px;
        border-radius: 12px;
        font-size: 14px;
        outline: none;
        transition: all 0.2s;
    }

    #ai-user-input:focus {
        background: white;
        border-color: var(--ai-accent);
        box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1);
    }

    #ai-send-btn {
        width: 45px;
        height: 45px;
        background: var(--ai-primary);
        border: none;
        color: white;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #ai-send-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
    }

    /* ─────────────── ANIMATIONS ─────────────── */
    .ai-fade-in {
        animation: ai-fadeIn 0.3s ease-out forwards;
    }

    @keyframes ai-fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Scrollbar Styling */
    #ai-message-list::-webkit-scrollbar {
        width: 5px;
    }

    #ai-message-list::-webkit-scrollbar-track {
        background: transparent;
    }

    #ai-message-list::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const aiWidget = document.getElementById('ai-chatbot-widget');
        const aiToggle = document.getElementById('ai-chat-toggle');
        const aiChatWindow = document.getElementById('ai-chat-window');
        const aiCloseBtn = document.getElementById('ai-close-chat');
        const aiMessageList = document.getElementById('ai-message-list');
        const aiChatForm = document.getElementById('ai-chat-form');
        const aiUserInput = document.getElementById('ai-user-input');
        const aiTypingIndicator = document.getElementById('ai-typing-indicator');

        let sessionId = localStorage.getItem('ai_chat_sid') || null;

        // Toggle Chat
        aiToggle.addEventListener('click', () => {
            aiWidget.classList.add('ai-open');
            aiUserInput.focus();
        });

        aiCloseBtn.addEventListener('click', () => {
            aiWidget.classList.remove('ai-open');
        });

        // Handle Form Submit
        aiChatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = aiUserInput.value.trim();
            if (!message) return;

            // Add user message to UI
            appendAiMessage('user', message);
            aiUserInput.value = '';

            // Show typing indicator
            showAiTyping(true);
            scrollToBottom();

            try {
                const response = await fetch('/api/ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        message: message,
                        session_id: sessionId
                    })
                });

                const data = await response.json();
                showAiTyping(false);

                if (data.success) {
                    if (data.session_id) {
                        sessionId = data.session_id;
                        localStorage.setItem('ai_chat_sid', sessionId);
                    }

                    // Simulate thinking time for "natural" feel
                    setTimeout(() => {
                        appendAiMessage('bot', data.reply);
                        scrollToBottom();
                    }, 400);
                } else {
                    appendAiMessage('bot', data.reply || 'عذراً، حدث خطأ ما.');
                    console.error('AI Error:', data.debug_error);
                }
            } catch (error) {
                showAiTyping(false);
                appendAiMessage('bot', 'حدث خطأ في الاتصال بالخادم.');
                console.error('Fetch Error:', error);
            }
        });

        function appendAiMessage(type, content) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `ai-message ai-${type}-message ai-fade-in`;

            const contentDiv = document.createElement('div');
            contentDiv.className = 'ai-message-content';
            contentDiv.textContent = content;

            messageDiv.appendChild(contentDiv);
            aiMessageList.appendChild(messageDiv);
            scrollToBottom();
        }

        function showAiTyping(show) {
            if (show) {
                aiTypingIndicator.classList.remove('ai-hidden');
                aiMessageList.appendChild(aiTypingIndicator); // Move to end
            } else {
                aiTypingIndicator.classList.add('ai-hidden');
            }
        }

        function scrollToBottom() {
            aiMessageList.scrollTop = aiMessageList.scrollHeight;
        }
    });
</script>