@extends('layouts.app')

@section('content')
    <div class="min-h-[calc(100vh-130px)] bg-[#f8fafc] flex items-center justify-center p-4 md:p-10 font-sans rtl"
        dir="rtl">
        <div
            class="max-w-7xl w-full h-[85vh] bg-white rounded-3xl shadow-[0_20px_60px_-15px_rgba(37,99,235,0.15)] overflow-hidden flex flex-col md:flex-row border border-blue-50">

            <!-- Sidebar: Advisor Profile -->
            <div
                class="w-full md:w-96 bg-gradient-to-b from-[#1e3a8a] via-[#2563eb] to-[#3b82f6] text-white p-10 flex flex-col relative overflow-hidden">
                <!-- Background Decoration -->
                <div class="absolute inset-0 opacity-10 pointer-events-none">
                    <div class="absolute -top-24 -left-24 w-64 h-64 bg-white rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-white rounded-full blur-3xl"></div>
                </div>

                <div class="relative z-10 flex flex-col h-full">
                    <div class="flex flex-col items-center text-center mb-10">
                        <div
                            class="w-28 h-28 bg-white/10 backdrop-blur-md rounded-3xl flex items-center justify-center mb-6 border border-white/20 shadow-2xl relative group transition-transform hover:scale-105 duration-300">
                            <i class="fas fa-robot text-5xl"></i>
                            <div
                                class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 border-4 border-[#1e3a8a] rounded-full">
                            </div>
                        </div>
                        <h2 class="text-3xl font-extrabold tracking-tight mb-2">المستشار الذكي</h2>
                        <p class="text-blue-100/70 text-sm font-medium">مدعوم بالذكاء الاصطناعي لخدمتك</p>
                    </div>

                    <div class="space-y-6 flex-1">
                        <div
                            class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10 group hover:bg-white/10 transition-colors duration-300">
                            <h3 class="text-xs font-bold text-blue-200 uppercase tracking-[0.2em] mb-4">خدمات استشارية فورية
                            </h3>
                            <ul class="text-sm space-y-4 text-blue-50/90">
                                <li class="flex items-center gap-3">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full"></span>
                                    تحليل الأسعار ومقارنة السوق
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full"></span>
                                    ترشيح أفضل المناطق السكنية
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full"></span>
                                    تحليل العائد على الاستثمار
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-auto pt-8 border-t border-white/10">
                        <button onclick="clearChat()"
                            class="w-full py-4 px-6 bg-white/10 hover:bg-white/20 rounded-2xl text-blue-100 font-bold transition-all flex items-center justify-center gap-3 group">
                            <i class="fas fa-trash-alt group-hover:rotate-12 transition-transform"></i>
                            بدء محادثة جديدة
                        </button>
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="flex-1 flex flex-col bg-white overflow-hidden relative">
                <!-- Chat Header (Mobile Only) -->
                <div class="md:hidden p-4 bg-white border-b flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h4 class="font-bold text-gray-800">المستشار الذكي</h4>
                </div>

                <!-- Messages Container -->
                <div id="chatBox"
                    class="flex-1 overflow-y-auto p-10 space-y-8 scroll-smooth bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] bg-fixed opacity-95">
                    <!-- Welcome Message -->
                    <div class="flex justify-start animate-fade-in">
                        <div
                            class="bg-slate-100 text-slate-800 rounded-3xl rounded-tr-none px-6 py-4 max-w-[85%] text-base shadow-sm border border-slate-200 leading-relaxed">
                            أهلاً بك في منصتنا العقارية المتطورة! أنا هنا لمساعدتك في اتخاذ أفضل قرار عقاري. ما الذي يمكنني
                            تقديمه لك اليوم؟
                        </div>
                    </div>
                </div>

                <!-- Input Area -->
                <div class="p-8 bg-white border-t border-slate-100">
                    <div class="max-w-4xl mx-auto">
                        <!-- Quick Actions -->
                        <div class="flex gap-2 overflow-x-auto no-scrollbar pb-4 mb-2">
                            <button onclick="quickAsk('أفضل عقارات للاستثمار حالياً')"
                                class="whitespace-nowrap px-4 py-2 bg-blue-50 text-blue-600 rounded-full text-xs font-bold border border-blue-100 hover:bg-blue-600 hover:text-white transition-all">
                                💰 أفضل استثمار
                            </button>
                            <button onclick="quickAsk('قارن بين أسعار التجمع وزايد')"
                                class="whitespace-nowrap px-4 py-2 bg-slate-50 text-slate-600 rounded-full text-xs font-bold border border-slate-100 hover:bg-blue-600 hover:text-white transition-all">
                                📊 مقارنة الأسعار
                            </button>
                            <button onclick="quickAsk('شراء شقة بالتقسيط')"
                                class="whitespace-nowrap px-4 py-2 bg-slate-50 text-slate-600 rounded-full text-xs font-bold border border-slate-100 hover:bg-blue-600 hover:text-white transition-all">
                                💳 شقق بالتقسيط
                            </button>
                        </div>

                        <div class="relative flex items-center gap-4 group">
                            <input type="text" id="chatInput" placeholder="اسألني عن أي منطقة، ميزانية، أو نوع عقار..."
                                class="flex-1 bg-slate-50 border border-slate-200 rounded-[2rem] px-8 py-5 text-base focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none shadow-inner transition-all">

                            <button id="sendBtn"
                                class="absolute left-3 p-4 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg shadow-blue-300/30 transition-all hover:scale-105 active:scale-95">
                                <i class="fas fa-paper-plane fa-lg -rotate-45"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom Scrollbar for Chat */
        #chatBox::-webkit-scrollbar {
            width: 6px;
        }

        #chatBox::-webkit-scrollbar-track {
            background: transparent;
        }

        #chatBox::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        @keyframes fadeIn {
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
            animation: fadeIn 0.4s ease-out forwards;
        }

        .user-bubble {
            background: linear-gradient(135deg, #2563eb 0%, #1e4ed8 100%);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2);
        }

        .bot-bubble {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
    </style>

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
            typingDiv.className = 'flex justify-start animate-fade-in';
            typingDiv.innerHTML = `
                    <div class="bot-bubble rounded-3xl rounded-tr-none px-6 py-4 max-w-[85%] text-slate-400 font-medium flex items-center gap-2">
                        <span class="flex gap-1">
                            <span class="w-1.5 h-1.5 bg-slate-300 rounded-full animate-bounce"></span>
                            <span class="w-1.5 h-1.5 bg-slate-300 rounded-full animate-bounce [animation-delay:0.2s]"></span>
                            <span class="w-1.5 h-1.5 bg-slate-300 rounded-full animate-bounce [animation-delay:0.4s]"></span>
                        </span>
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
            div.className = `flex flex-col ${type === 'user' ? 'items-end' : 'items-start'} animate-fade-in`;

            let contentClass = '';
            if (type === 'user') contentClass = 'user-bubble text-white rounded-tl-none';
            else if (type === 'error') contentClass = 'bg-red-50 text-red-600 border border-red-100 rounded-tr-none';
            else contentClass = 'bot-bubble text-slate-800 rounded-tr-none';

            let propertyHtml = '';
            if (properties && properties.length > 0) {
                propertyHtml = `
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 w-full max-w-2xl">
                            ${properties.map(p => `
                                <div class="bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-md hover:shadow-xl transition-all group flex flex-col">
                                    <div class="relative h-32 overflow-hidden">
                                        <img src="${p.image}" alt="${p.title}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        <div class="absolute top-2 right-2 bg-blue-600 text-white px-2 py-1 rounded-lg text-[10px] font-bold">
                                            ${new Intl.NumberFormat().format(p.price)} ${p.currency}
                                        </div>
                                    </div>
                                    <div class="p-4 flex-1 flex flex-col">
                                        <h3 class="text-sm font-bold text-gray-800 mb-1 line-clamp-1">${p.title}</h3>
                                        <p class="text-[10px] text-gray-500 mb-3 flex items-center gap-1">
                                            <i class="fas fa-map-marker-alt text-blue-500"></i> ${p.location}
                                        </p>
                                        <a href="${p.url}" target="_blank" class="mt-auto block w-full py-2 bg-slate-50 hover:bg-blue-600 hover:text-white text-blue-600 text-center text-xs font-bold rounded-xl transition-colors">
                                            عرض التفاصيل
                                        </a>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    `;
            }

            div.innerHTML = `
                    <div class="${contentClass} rounded-3xl px-6 py-4 max-w-[85%] shadow-sm leading-relaxed text-base">
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