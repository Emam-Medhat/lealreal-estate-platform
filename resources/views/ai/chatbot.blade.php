@extends('layouts.app')

@section('title', 'المستشار الذكي للعقارات')

@push('styles')
<style>
/* Enhanced Chat Page Styles */
.chat-container {
    min-height: calc(100vh - 130px);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    overflow: hidden;
}

.chat-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.05"><circle cx="7" cy="7" r="7"/></g></g></svg>');
    pointer-events: none;
}

.chat-main {
    max-width: 1400px;
    margin: 0 auto;
    height: 85vh;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 30px;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    display: flex;
    border: 1px solid rgba(255, 255, 255, 0.2);
    position: relative;
}

/* Sidebar Styles */
.chat-sidebar {
    width: 400px;
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
    color: white;
    padding: 3rem;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}

.chat-sidebar::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: float 20s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    33% { transform: translate(30px, -30px) rotate(120deg); }
    66% { transform: translate(-20px, 20px) rotate(240deg); }
}

.bot-avatar {
    width: 120px;
    height: 120px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
    border: 2px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    position: relative;
}

.bot-avatar:hover {
    transform: scale(1.05) rotate(5deg);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
}

.bot-avatar i {
    font-size: 3.5rem;
    color: white;
}

.status-indicator {
    position: absolute;
    bottom: 8px;
    right: 8px;
    width: 24px;
    height: 24px;
    background: #10b981;
    border: 4px solid #1e3a8a;
    border-radius: 50%;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
}

.bot-title {
    font-size: 2.5rem;
    font-weight: 800;
    text-align: center;
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, #ffffff 0%, #e0e7ff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.bot-subtitle {
    text-align: center;
    color: rgba(255, 255, 255, 0.7);
    font-size: 1rem;
    margin-bottom: 3rem;
}

.services-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    flex: 1;
}

.services-card:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
}

.services-title {
    font-size: 0.8rem;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.8);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 1.5rem;
}

.services-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.services-list li {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.95rem;
    transition: all 0.2s ease;
}

.services-list li:hover {
    transform: translateX(5px);
    color: white;
}

.service-bullet {
    width: 8px;
    height: 8px;
    background: #60a5fa;
    border-radius: 50%;
    flex-shrink: 0;
}

.clear-chat-btn {
    width: 100%;
    padding: 1.25rem;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    transition: all 0.3s ease;
    cursor: pointer;
    margin-top: 2rem;
}

.clear-chat-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.clear-chat-btn i {
    transition: all 0.3s ease;
}

.clear-chat-btn:hover i {
    transform: rotate(15deg);
}

/* Chat Area Styles */
.chat-area {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: white;
    position: relative;
}

.chat-header-mobile {
    display: none;
    padding: 1.5rem;
    background: white;
    border-bottom: 1px solid #e5e7eb;
    align-items: center;
    gap: 1rem;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 3rem;
    background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
    position: relative;
}

.chat-messages::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23e5e7eb" fill-opacity="0.3"><circle cx="5" cy="5" r="5"/></g></g></svg>');
    pointer-events: none;
}

.welcome-message {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    color: #475569;
    border-radius: 25px;
    padding: 1.5rem 2rem;
    max-width: 85%;
    font-size: 1.1rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.5);
    line-height: 1.6;
    animation: slideInRight 0.6s ease-out;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Input Area Styles */
.chat-input-area {
    padding: 2.5rem;
    background: white;
    border-top: 1px solid #e5e7eb;
    position: relative;
}

.quick-actions {
    display: flex;
    gap: 0.75rem;
    overflow-x: auto;
    padding-bottom: 1rem;
    margin-bottom: 1rem;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.quick-actions::-webkit-scrollbar {
    display: none;
}

.quick-action-btn {
    white-space: nowrap;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    color: #0369a1;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 700;
    border: 1px solid #bae6fd;
    transition: all 0.3s ease;
    cursor: pointer;
}

.quick-action-btn:hover {
    background: linear-gradient(135deg, #0369a1 0%, #0284c7 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(3, 105, 161, 0.3);
}

.input-container {
    position: relative;
    max-width: 800px;
    margin: 0 auto;
}

.chat-input {
    width: 100%;
    padding: 1.5rem 5rem 1.5rem 2rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 2px solid #e2e8f0;
    border-radius: 30px;
    font-size: 1.1rem;
    outline: none;
    transition: all 0.3s ease;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
}

.chat-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1), inset 0 2px 4px rgba(0, 0, 0, 0.06);
    background: white;
}

.chat-input::placeholder {
    color: #94a3b8;
}

.send-btn {
    position: absolute;
    left: 8px;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border: none;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
}

.send-btn:hover {
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 15px 35px rgba(59, 130, 246, 0.4);
}

.send-btn:active {
    transform: translateY(-50%) scale(0.95);
}

.send-btn i {
    font-size: 1.2rem;
    transform: rotate(-45deg);
}

/* Message Bubble Styles */
.message-bubble {
    border-radius: 25px;
    padding: 1.25rem 1.75rem;
    max-width: 85%;
    font-size: 1rem;
    line-height: 1.6;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    animation: messageSlide 0.4s ease-out;
}

@keyframes messageSlide {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.user-message {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    border-bottom-right-radius: 8px;
}

.bot-message {
    background: white;
    color: #374151;
    border: 1px solid #e5e7eb;
    border-bottom-left-radius: 8px;
}

.error-message {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    color: #dc2626;
    border: 1px solid #fecaca;
    border-bottom-left-radius: 8px;
}

.typing-indicator {
    background: white;
    color: #94a3b8;
    border: 1px solid #e5e7eb;
    border-bottom-left-radius: 8px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.typing-dots {
    display: flex;
    gap: 0.25rem;
}

.typing-dot {
    width: 8px;
    height: 8px;
    background: #94a3b8;
    border-radius: 50%;
    animation: typingBounce 1.4s ease-in-out infinite;
}

.typing-dot:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-dot:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typingBounce {
    0%, 60%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-10px);
    }
}

/* Property Cards */
.property-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
    width: 100%;
    max-width: 100%;
}

.property-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.property-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
}

.property-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.property-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.property-card:hover .property-image img {
    transform: scale(1.1);
}

.property-price {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 700;
    box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
}

.property-details {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.property-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

.property-location {
    font-size: 0.9rem;
    color: #6b7280;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.property-location i {
    color: #3b82f6;
}

.property-link {
    margin-top: auto;
    display: block;
    width: 100%;
    padding: 0.75rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    color: #3b82f6;
    text-align: center;
    font-size: 0.9rem;
    font-weight: 700;
    border-radius: 15px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid #e2e8f0;
}

.property-link:hover {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .chat-sidebar {
        width: 350px;
        padding: 2rem;
    }
    
    .bot-avatar {
        width: 100px;
        height: 100px;
    }
    
    .bot-avatar i {
        font-size: 3rem;
    }
    
    .bot-title {
        font-size: 2rem;
    }
}

@media (max-width: 768px) {
    .chat-container {
        padding: 1rem;
    }
    
    .chat-main {
        height: 90vh;
        border-radius: 20px;
        flex-direction: column;
    }
    
    .chat-sidebar {
        width: 100%;
        padding: 1.5rem;
        min-height: auto;
    }
    
    .chat-header-mobile {
        display: flex;
    }
    
    .chat-messages {
        padding: 1.5rem;
    }
    
    .chat-input-area {
        padding: 1.5rem;
    }
    
    .property-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions {
        gap: 0.5rem;
    }
    
    .quick-action-btn {
        font-size: 0.8rem;
        padding: 0.5rem 1rem;
    }
}

/* Custom Scrollbar */
.chat-messages::-webkit-scrollbar {
    width: 8px;
}

.chat-messages::-webkit-scrollbar-track {
    background: transparent;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
    border-radius: 10px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #cbd5e1 0%, #94a3b8 100%);
}
</style>
@endpush

@section('content')
<div class="chat-container" dir="rtl">
    <div class="chat-main">
        <!-- Sidebar: Advisor Profile -->
        <div class="chat-sidebar">
            <div class="bot-avatar">
                <i class="fas fa-robot"></i>
                <div class="status-indicator"></div>
            </div>
            
            <h2 class="bot-title">المستشار الذكي</h2>
            <p class="bot-subtitle">مدعوم بالذكاء الاصطناعي لخدمتك</p>
            
            <div class="services-card">
                <h3 class="services-title">خدمات استشارية فورية</h3>
                <ul class="services-list">
                    <li>
                        <span class="service-bullet"></span>
                        تحليل الأسعار ومقارنة السوق
                    </li>
                    <li>
                        <span class="service-bullet"></span>
                        ترشيح أفضل المناطق السكنية
                    </li>
                    <li>
                        <span class="service-bullet"></span>
                        تحليل العائد على الاستثمار
                    </li>
                    <li>
                        <span class="service-bullet"></span>
                        تقييم العقارات بالذكاء الاصطناعي
                    </li>
                    <li>
                        <span class="service-bullet"></span>
                        استشارات قانونية عقارية
                    </li>
                </ul>
            </div>
            
            <button class="clear-chat-btn" onclick="clearChat()">
                <i class="fas fa-trash-alt"></i>
                بدء محادثة جديدة
            </button>
        </div>

        <!-- Chat Area -->
        <div class="chat-area">
            <!-- Chat Header (Mobile Only) -->
            <div class="chat-header-mobile">
                <div class="bot-avatar" style="width: 40px; height: 40px; margin: 0;">
                    <i class="fas fa-robot" style="font-size: 1.2rem;"></i>
                    <div class="status-indicator" style="width: 12px; height: 12px; bottom: 4px; right: 4px;"></div>
                </div>
                <h4 class="font-bold text-gray-800">المستشار الذكي</h4>
            </div>

            <!-- Messages Container -->
            <div id="chatBox" class="chat-messages">
                <!-- Welcome Message -->
                <div class="flex justify-start">
                    <div class="message-bubble bot-message">
                        أهلاً بك في منصتنا العقارية المتطورة! أنا هنا لمساعدتك في اتخاذ أفضل قرار عقاري. ما الذي يمكنني تقديمه لك اليوم؟
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="chat-input-area">
                <div class="quick-actions">
                    <button class="quick-action-btn" onclick="quickAsk('أفضل عقارات للاستثمار حالياً')">
                        💰 أفضل استثمار
                    </button>
                    <button class="quick-action-btn" onclick="quickAsk('قارن بين أسعار التجمع وزايد')">
                        📊 مقارنة الأسعار
                    </button>
                    <button class="quick-action-btn" onclick="quickAsk('شراء شقة بالتقسيط')">
                        💳 شقق بالتقسيط
                    </button>
                    <button class="quick-action-btn" onclick="quickAsk('أفضل الأحياء السكنية في الرياض')">
                        🏘️ أفضل الأحياء
                    </button>
                    <button class="quick-action-btn" onclick="quickAsk('نصائح للمستثمرين الجدد')">
                        🎯 نصائح استثمارية
                    </button>
                </div>

                <div class="input-container">
                    <input type="text" id="chatInput" class="chat-input" placeholder="اسألني عن أي منطقة، ميزانية، أو نوع عقار...">
                    <button id="sendBtn" class="send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

    <script>
        let sessionId = localStorage.getItem('ai_chat_sid') || '';

        function quickAsk(text) {
            document.getElementById('chatInput').value = text;
            sendMessage();
        }

        async function sendMessage() {
            const input = document.getElementById('chatInput');
            const box = document.getElementById('chatBox');
            const text = input.value.trim();

            if (!text) return;

            // Add user message
            appendMessage('user', text);
            input.value = '';

            // Add typing indicator
            const typingId = 'typing_' + Date.now();
            const typingDiv = document.createElement('div');
            typingDiv.id = typingId;
            typingDiv.className = 'flex justify-start';
            typingDiv.innerHTML = `
                <div class="message-bubble typing-indicator">
                    <div class="typing-dots">
                        <span class="typing-dot"></span>
                        <span class="typing-dot"></span>
                        <span class="typing-dot"></span>
                    </div>
                    جاري تحليل طلبك...
                </div>
            `;
            box.appendChild(typingDiv);
            box.scrollTop = box.scrollHeight;

            try {
                const response = await fetch('/api/ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ message: text, session_id: sessionId })
                });

                const res = await response.json();
                document.getElementById(typingId).remove();

                if (res.success) {
                    sessionId = res.session_id;
                    localStorage.setItem('ai_chat_sid', sessionId);
                    appendMessage('ai', res.reply, res.suggested_properties || []);
                } else {
                    appendMessage('error', res.reply || 'حدث خطأ غير متوقع.');
                }
            } catch (e) {
                document.getElementById(typingId).remove();
                appendMessage('error', 'عذراً، تعذر الاتصال بالسيرفر.');
            }
        }

        function appendMessage(type, text, properties = []) {
            const box = document.getElementById('chatBox');
            const div = document.createElement('div');
            div.className = `flex flex-col ${type === 'user' ? 'items-end' : 'items-start'}`;

            let contentClass = '';
            if (type === 'user') contentClass = 'message-bubble user-message';
            else if (type === 'error') contentClass = 'message-bubble error-message';
            else contentClass = 'message-bubble bot-message';

            let propertyHtml = '';
            if (properties && properties.length > 0) {
                propertyHtml = `
                    <div class="property-grid">
                        ${properties.map(p => `
                            <div class="property-card">
                                <div class="property-image">
                                    <img src="${p.image}" alt="${p.title}">
                                    <div class="property-price">
                                        ${new Intl.NumberFormat().format(p.price)} ${p.currency}
                                    </div>
                                </div>
                                <div class="property-details">
                                    <h3 class="property-title">${p.title}</h3>
                                    <p class="property-location">
                                        <i class="fas fa-map-marker-alt"></i> ${p.location}
                                    </p>
                                    <a href="${p.url}" target="_blank" class="property-link">
                                        عرض التفاصيل
                                    </a>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
            }

            div.innerHTML = `
                <div class="${contentClass}">
                    ${text.replace(/\n/g, '<br>')}
                </div>
                ${propertyHtml}
            `;

            box.appendChild(div);
            box.scrollTop = box.scrollHeight;
        }

        async function clearChat() {
            if (!sessionId) return;
            if (!confirm('هل أنت متأكد من حذف تاريخ المحادثة؟')) return;

            await fetch(`/api/ai/chat/${sessionId}`, { method: 'DELETE' });
            localStorage.removeItem('ai_chat_sid');
            window.location.reload();
        }

        document.getElementById('sendBtn').onclick = sendMessage;
        document.getElementById('chatInput').onkeypress = (e) => { if (e.key === 'Enter') sendMessage(); };
    </script>
@endsection