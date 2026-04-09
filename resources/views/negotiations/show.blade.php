@extends('layouts.app')

@section('title', 'تفاصيل المفاوضات')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- رأس الصفحة -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">مفاوضة رقم: {{ $negotiation->negotiation_number }}</h1>
                <div class="flex items-center space-x-4 space-x-reverse">
                    <span class="px-3 py-1 text-sm rounded-full {{ getStatusClass($negotiation->status) }}">
                        {{ getStatusText($negotiation->status) }}
                    </span>
                    <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">
                        {{ getTypeText($negotiation->type) }}
                    </span>
                    <span class="text-gray-600">
                        <i class="fas fa-calendar"></i>
                        بدأت: {{ $negotiation->started_at->format('Y-m-d') }}
                    </span>
                </div>
            </div>
            <div class="flex space-x-2 space-x-reverse">
                @if($negotiation->status === 'active')
                    <button onclick="pauseNegotiation()" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                        <i class="fas fa-pause ml-2"></i>
                        إيقاف
                    </button>
                @endif
                @if($negotiation->status === 'paused')
                    <button onclick="resumeNegotiation()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                        <i class="fas fa-play ml-2"></i>
                        استئناف
                    </button>
                @endif
                @if(in_array($negotiation->status, ['active', 'paused']))
                    <button onclick="terminateNegotiation()" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                        <i class="fas fa-stop ml-2"></i>
                        إنهاء
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- المحتوى الرئيسي -->
        <div class="lg:col-span-2">
            <!-- معلومات العقار -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4 text-gray-900">
                    <i class="fas fa-home ml-2 text-blue-500"></i>
                    العقار
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">العنوان</p>
                        <p class="font-semibold">{{ $negotiation->property->title }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">الموقع</p>
                        <p class="font-semibold">{{ $negotiation->property->location }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">السعر الحالي</p>
                        <p class="font-semibold text-green-600">{{ number_format($negotiation->current_price ?? $negotiation->property->price, 2) }} ريال</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">السعر المستهدف</p>
                        <p class="font-semibold text-blue-600">{{ number_format($negotiation->target_price ?? 0, 2) }} ريال</p>
                    </div>
                </div>
            </div>

            <!-- الرسائل -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4 text-gray-900">
                    <i class="fas fa-comments ml-2 text-blue-500"></i>
                    الرسائل والمقترحات
                </h2>
                
                <!-- إرسال رسالة جديدة -->
                @if($negotiation->status === 'active')
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <div class="mb-3">
                            <textarea id="messageInput" placeholder="اكتب رسالتك أو مقترحك..." 
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                rows="3"></textarea>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="flex space-x-2 space-x-reverse">
                                <select id="proposalType" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">نوع المقترح</option>
                                    <option value="price">السعر</option>
                                    <option value="terms">الشروط</option>
                                    <option value="contingencies">الشروط الاحتياطية</option>
                                    <option value="closing">تاريخ الإغلاق</option>
                                </select>
                                <input type="number" id="proposalValue" placeholder="القيمة (إذا كانت مالية)" 
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <button onclick="sendMessage()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                <i class="fas fa-paper-plane ml-2"></i>
                                إرسال
                            </button>
                        </div>
                    </div>
                @endif

                <!-- قائمة الرسائل -->
                <div id="messagesContainer" class="space-y-4 max-h-96 overflow-y-auto">
                    <!-- سيتم تحميل الرسائل هنا -->
                </div>
            </div>

            <!-- نقاط الاتفاق والخلاف -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4 text-gray-900">
                    <i class="fas fa-balance-scale ml-2 text-blue-500"></i>
                    نقاط الاتفاق والخلاف
                </h2>
                
                <!-- نقاط الاتفاق -->
                @if($negotiation->agreement_points)
                    <div class="mb-4">
                        <h3 class="font-semibold text-green-600 mb-2">نقاط الاتفاق</h3>
                        <div class="space-y-2">
                            @if(is_array($negotiation->agreement_points))
                                @foreach($negotiation->agreement_points as $point)
                                    <div class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 ml-2 mt-1"></i>
                                        <p class="text-gray-700">{{ $point }}</p>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-gray-700">{{ $negotiation->agreement_points }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- نقاط الخلاف -->
                @if($negotiation->disputed_points)
                    <div>
                        <h3 class="font-semibold text-red-600 mb-2">نقاط الخلاف</h3>
                        <div class="space-y-2">
                            @if(is_array($negotiation->disputed_points))
                                @foreach($negotiation->disputed_points as $point)
                                    <div class="flex items-start">
                                        <i class="fas fa-times-circle text-red-500 ml-2 mt-1"></i>
                                        <p class="text-gray-700">{{ $point }}</p>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-gray-700">{{ $negotiation->disputed_points }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- الجانب الأيمن -->
        <div class="lg:col-span-1">
            <!-- الأطراف -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4 text-gray-900">
                    <i class="fas fa-users ml-2 text-blue-500"></i>
                    الأطراف
                </h2>
                
                <!-- المشتري -->
                <div class="mb-4 pb-4 border-b">
                    <p class="text-sm text-gray-600 mb-2">المشتري</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center ml-3">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold">{{ $negotiation->buyer->name }}</p>
                            <p class="text-sm text-gray-600">{{ $negotiation->buyer->email }}</p>
                            <p class="text-sm text-gray-600">{{ $negotiation->buyer->phone ?? 'غير محدد' }}</p>
                        </div>
                    </div>
                </div>

                <!-- البائع -->
                <div>
                    <p class="text-sm text-gray-600 mb-2">البائع</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center ml-3">
                            <i class="fas fa-user text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold">{{ $negotiation->seller->name }}</p>
                            <p class="text-sm text-gray-600">{{ $negotiation->seller->email }}</p>
                            <p class="text-sm text-gray-600">{{ $negotiation->seller->phone ?? 'غير محدد' }}</p>
                        </div>
                    </div>
                </div>

                <!-- الوسيط (إذا وجد) -->
                @if($negotiation->mediator)
                    <div class="mt-4 pt-4 border-t">
                        <p class="text-sm text-gray-600 mb-2">الوسيط</p>
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center ml-3">
                                <i class="fas fa-user-tie text-purple-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold">{{ $negotiation->mediator->name }}</p>
                                <p class="text-sm text-gray-600">{{ $negotiation->mediator->email }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- معلومات المفاوضة -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4 text-gray-900">
                    <i class="fas fa-info-circle ml-2 text-blue-500"></i>
                    معلومات المفاوضة
                </h2>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">الموضوع</p>
                        <p class="font-semibold">{{ $negotiation->subject ?? 'عام' }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">السعر الأولي</p>
                        <p class="font-semibold">{{ number_format($negotiation->initial_price ?? 0, 2) }} ريال</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">السعر الحالي</p>
                        <p class="font-semibold text-green-600">{{ number_format($negotiation->current_price ?? 0, 2) }} ريال</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">السعر المستهدف</p>
                        <p class="font-semibold text-blue-600">{{ number_format($negotiation->target_price ?? 0, 2) }} ريال</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">بدأت بواسطة</p>
                        <p class="font-semibold">{{ $negotiation->initiator->name }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">آخر نشاط</p>
                        <p class="font-semibold">{{ $negotiation->last_activity_at->format('Y-m-d H:i') }}</p>
                    </div>
                    
                    @if($negotiation->completed_at)
                        <div>
                            <p class="text-sm text-gray-600">تاريخ الإنجاز</p>
                            <p class="font-semibold">{{ $negotiation->completed_at->format('Y-m-d H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- العرض الأصلي (إذا وجد) -->
            @if($negotiation->offer)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-900">
                        <i class="fas fa-file-contract ml-2 text-blue-500"></i>
                        العرض الأصلي
                    </h2>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">رقم العرض</p>
                            <p class="font-semibold">{{ $negotiation->offer->offer_number }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">مبلغ العرض</p>
                            <p class="font-semibold text-green-600">{{ number_format($negotiation->offer->offer_amount, 2) }} ريال</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">حالة العرض</p>
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                {{ $negotiation->offer->status }}
                            </span>
                        </div>
                        
                        <div class="pt-3 border-t">
                            <a href="/offers/{{ $negotiation->offer->id }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                <i class="fas fa-external-link-alt ml-1"></i>
                                عرض التفاصيل الكاملة
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var currentUserId = window.currentUserId;

document.addEventListener('DOMContentLoaded', function() {
    loadMessages();
    
    // تحديث الرسائل كل 30 ثانية
    setInterval(loadMessages, 30000);
});

function loadMessages() {
    fetch(`/negotiations/{{ $negotiation->id }}/messages`)
        .then(response => response.json())
        .then(data => {
            renderMessages(data);
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function renderMessages(messages) {
    var container = document.getElementById('messagesContainer');
    container.innerHTML = '';
    
    messages.forEach(function(message) {
        var messageDiv = document.createElement('div');
        var isMyMessage = message.user_id == currentUserId;
        
        messageDiv.className = 'flex ' + (isMyMessage ? 'justify-start' : 'justify-end');
        messageDiv.innerHTML = 
            '<div class="max-w-xs lg:max-w-md">' +
                '<div class="bg-' + (isMyMessage ? 'blue' : 'gray') + '-100 rounded-lg p-3">' +
                    '<div class="flex justify-between items-start mb-1">' +
                        '<span class="font-semibold text-sm">' + message.user.name + '</span>' +
                        '<span class="text-xs text-gray-500">' + formatTime(message.created_at) + '</span>' +
                    '</div>' +
                    '<p class="text-gray-700">' + message.message + '</p>' +
                    (message.proposal_type && message.proposal_value ? 
                        '<div class="mt-2 p-2 bg-white rounded text-sm">' +
                            '<span class="font-semibold">مقترح:</span> ' + 
                            getProposalTypeText(message.proposal_type) + ' - ' + 
                            message.proposal_value + ' ريال' +
                        '</div>' : ''
                    ) +
                '</div>' +
            '</div>';
        container.appendChild(messageDiv);
    });
    
    // التمرير لآخر رسالة
    container.scrollTop = container.scrollHeight;
}

function sendMessage() {
    const messageInput = document.getElementById('messageInput');
    const proposalType = document.getElementById('proposalType').value;
    const proposalValue = document.getElementById('proposalValue').value;
    
    if (!messageInput.value.trim()) {
        alert('الرجاء كتابة رسالة');
        return;
    }
    
    const data = {
        message: messageInput.value,
        proposal_type: proposalType || null,
        proposal_value: proposalValue || null
    };
    
    fetch(`/negotiations/{{ $negotiation->id }}/message`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageInput.value = '';
            document.getElementById('proposalType').value = '';
            document.getElementById('proposalValue').value = '';
            loadMessages();
        } else {
            alert(data.message || 'حدث خطأ ما');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ ما');
    });
}

function pauseNegotiation() {
    if (confirm('هل أنت متأكد من إيقاف هذه المفاوضات؟')) {
        fetch(`/negotiations/{{ $negotiation->id }}/pause`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'حدث خطأ ما');
            }
        });
    }
}

function resumeNegotiation() {
    if (confirm('هل أنت متأكد من استئناف هذه المفاوضات؟')) {
        fetch(`/negotiations/{{ $negotiation->id }}/resume`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'حدث خطأ ما');
            }
        });
    }
}

function terminateNegotiation() {
    if (confirm('هل أنت متأكد من إنهاء هذه المفاوضات؟ هذا الإجراء لا يمكن التراجع عنه.')) {
        fetch(`/negotiations/{{ $negotiation->id }}/terminate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'حدث خطأ ما');
            }
        });
    }
}

function getProposalTypeText(type) {
    const types = {
        'price': 'السعر',
        'terms': 'الشروط',
        'contingencies': 'الشروط الاحتياطية',
        'closing': 'تاريخ الإغلاق'
    };
    return types[type] || type;
}

function formatTime(dateString) {
    return new Date(dateString).toLocaleTimeString('ar-SA', {
        hour: '2-digit',
        minute: '2-digit'
    });

}

</script>
@endpush

@endsection

<div id="js-data" data-user-id="{{ auth()->user()->id }}" style="display: none;"></div>

<script>
window.currentUserId = parseInt(document.getElementById('js-data').getAttribute('data-user-id'));
</script>
