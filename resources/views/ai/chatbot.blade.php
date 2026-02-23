@extends('layouts.app')

@section('content')
    <div class="h-screen bg-slate-50 flex items-center justify-center p-4 md:p-8 font-sans">
        <div class="max-w-5xl w-full h-[85vh] bg-white rounded-3xl shadow-2xl shadow-blue-100 overflow-hidden flex flex-col md:flex-row rtl"
            dir="rtl">

            <!-- Sidebar: Advisor Profile & Stats -->
            <div class="w-full md:w-80 bg-gradient-to-br from-blue-700 to-blue-900 text-white p-8 flex flex-col">
                <div class="flex flex-col items-center text-center mb-8">
                    <div
                        class="w-24 h-24 bg-white/10 rounded-full flex items-center justify-center mb-4 border border-white/20 relative">
                        <span class="text-4xl">🤖</span>
                        <div class="absolute bottom-1 right-1 w-5 h-5 bg-green-400 border-4 border-blue-800 rounded-full">
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold">المستشار الذكي</h2>
                    <p class="text-blue-100/70 text-sm">متاح دائماً لمساعدتك</p>
                </div>

                <div class="space-y-6 flex-1">
                    <div class="bg-white/10 rounded-2xl p-4 border border-white/10">
                        <h3 class="text-xs font-bold text-blue-200 uppercase tracking-widest mb-2">كيف يمكنني مساعدتك؟</h3>
                        <ul class="text-sm space-y-3 text-blue-50">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 mt-0.5 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                البحث عن عقارات تناسب ميزانيتك
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 mt-0.5 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                مقارنة المناطق والأحياء
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 mt-0.5 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                تحليل أسعار السوق الحالية
                            </li>
                        </ul>
                    </div>
                </div>

                <button onclick="clearChat()"
                    class="text-sm text-blue-300 hover:text-white transition-colors flex items-center justify-center gap-2 mt-8">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                    بدء محادثة جديدة
                </button>
            </div>

            <!-- Chat Area -->
            <div class="flex-1 flex flex-col bg-white overflow-hidden">
                <!-- Messages Container -->
                <div id="chatBox" class="flex-1 overflow-y-auto p-6 space-y-6 scroll-smooth">
                    <!-- Welcome Message -->
                    <div class="flex justify-start">
                        <div
                            class="bg-gray-100 text-gray-800 rounded-2xl rounded-tr-none p-4 max-w-[85%] text-sm shadow-sm">
                            أهلاً بك! أنا مستشارك العقاري الذكي. كيف يمكنني مساعدتك في العثور على منزلك المثالي اليوم؟
                        </div>
                    </div>
                </div>

                <!-- Input Area -->
                <div class="p-6 bg-slate-50 border-t border-gray-100">
                    <div class="relative flex items-center gap-4">
                        <input type="text" id="chatInput" placeholder="اكتب سؤالك هنا..."
                            class="flex-1 bg-white border border-gray-200 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none shadow-sm transition-all pr-12">
                        <button id="sendBtn"
                            class="bg-blue-600 hover:bg-blue-700 text-white p-3.5 rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-95 group">
                            <svg class="w-6 h-6 transform rotate-180 group-hover:translate-x-1 duration-300" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="mt-3 flex gap-2 overflow-x-auto no-scrollbar pb-1">
                        <button onclick="quickAsk('عقارات رخيصة في التجمع')"
                            class="text-[11px] bg-white border border-gray-200 text-gray-500 px-3 py-1.5 rounded-full whitespace-nowrap hover:bg-blue-50 hover:text-blue-600 transition-colors">عقارات
                            رخيصة في التجمع</button>
                        <button onclick="quickAsk('أسعار الشقق في زايد')"
                            class="text-[11px] bg-white border border-gray-200 text-gray-500 px-3 py-1.5 rounded-full whitespace-nowrap hover:bg-blue-50 hover:text-blue-600 transition-colors">أسعار
                            الشقق في زايد</button>
                        <button onclick="quickAsk('فلل للايجار بالساحل')"
                            class="text-[11px] bg-white border border-gray-200 text-gray-500 px-3 py-1.5 rounded-full whitespace-nowrap hover:bg-blue-50 hover:text-blue-600 transition-colors">فلل
                            للايجار بالساحل</button>
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
                            <div class="bg-gray-100 text-gray-400 rounded-2xl rounded-tr-none p-4 max-w-[85%] text-sm italic animate-pulse">
                                جاري التفكير...
                            </div>
                        `;
            box.appendChild(typingDiv);
            box.scrollTop = box.scrollHeight;

            try {
                console.log('Sending message:', { message: text, session_id: sessionId });
                const response = await fetch('/api/ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        message: text,
                        session_id: sessionId
                    })
                });

                const responseText = await response.text();
                console.log('Raw server response:', responseText);

                let res;
                try {
                    res = JSON.parse(responseText);
                } catch (parseErr) {
                    console.error('Failed to parse JSON:', parseErr, 'Raw:', responseText.substring(0, 500));
                    document.getElementById(typingId).remove();
                    appendMessage('error', 'عذراً، السيرفر رجّع رد غير صالح. شوف الكونسول.');
                    return;
                }

                console.log('Server response:', res);
                document.getElementById(typingId).remove();

                if (!response.ok) {
                    console.error('HTTP Error:', response.status, res);
                    if (res.debug_error) console.error('🔴 Backend Error Detail:', res.debug_error);
                    appendMessage('error', res.message || res.reply || 'عذراً، حدث خطأ في النظام.');
                    return;
                }

                if (res.success) {
                    sessionId = res.session_id;
                    localStorage.setItem('ai_chat_sid', sessionId);
                    appendMessage('ai', res.reply);
                } else {
                    console.warn('AI Service Error:', res);
                    if (res.debug_error) console.error('🔴 Backend Error Detail:', res.debug_error);
                    appendMessage('error', res.reply || 'حدث خطأ في خدمة الذكاء الاصطناعي.');
                }
            } catch (e) {
                console.error('Chat Script Exception:', e);
                document.getElementById(typingId).remove();
                appendMessage('error', 'عذراً، حدث خطأ ما. حاول مرة أخرى.');
            }
        }

        function appendMessage(type, text) {
            const box = document.getElementById('chatBox');
            const div = document.createElement('div');
            div.className = `flex ${type === 'user' ? 'justify-end' : 'justify-start'}`;

            const bgColor = type === 'user' ? 'bg-blue-600 text-white' : (type === 'error' ? 'bg-red-50 text-red-600' : 'bg-gray-100 text-gray-800');
            const rounded = type === 'user' ? 'rounded-tl-none' : 'rounded-tr-none';

            div.innerHTML = `
                            <div class="${bgColor} ${rounded} rounded-2xl p-4 max-w-[85%] text-sm shadow-sm leading-relaxed">
                                ${text.replace(/\n/g, '<br>')}
                            </div>
                        `;

            box.appendChild(div);
            box.scrollTop = box.scrollHeight;
        }

        async function clearChat() {
            if (!sessionId) return;
            await fetch(`/api/ai/chat/${sessionId}`, { method: 'DELETE' });
            localStorage.removeItem('ai_chat_sid');
            sessionId = '';
            window.location.reload();
        }

        document.getElementById('sendBtn').onclick = sendMessage;
        document.getElementById('chatInput').onkeypress = (e) => { if (e.key === 'Enter') sendMessage(); };
    </script>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endsection