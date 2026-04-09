@extends('layouts.app')

@section('title', 'تفاصيل العرض المضاد')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- رأس الصفحة -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">عرض مضاد رقم: {{ $counterOffer->id }}</h1>
                <div class="flex items-center space-x-4 space-x-reverse">
                    <span class="px-3 py-1 text-sm rounded-full {{ getStatusClass($counterOffer->status) }}">
                        {{ getStatusText($counterOffer->status) }}
                    </span>
                    <span class="text-gray-600">
                        <i class="fas fa-calendar"></i>
                        {{ $counterOffer->created_at->format('Y-m-d H:i') }}
                    </span>
                    @if($counterOffer->expiration_date)
                        <span class="text-gray-600">
                            <i class="fas fa-clock"></i>
                            ينتهي: {{ $counterOffer->expiration_date->format('Y-m-d') }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex space-x-2 space-x-reverse">
                @if($counterOffer->status === 'pending' && $counterOffer->countered_to_id === auth()->user()->id && !$counterOffer->isExpired())
                    <button onclick="acceptCounterOffer()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                        <i class="fas fa-check ml-2"></i>
                        قبول
                    </button>
                    <button onclick="rejectCounterOffer()" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                        <i class="fas fa-times ml-2"></i>
                        رفض
                    </button>
                @endif
                @if($counterOffer->status === 'pending' && $counterOffer->countered_by_id === auth()->user()->id && !$counterOffer->isExpired())
                    <button onclick="withdrawCounterOffer()" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                        <i class="fas fa-undo ml-2"></i>
                        سحب
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- المحتوى الرئيسي -->
        <div class="lg:col-span-2">
            <!-- العرض الأصلي -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4 text-gray-900">
                    <i class="fas fa-file-contract ml-2 text-blue-500"></i>
                    العرض الأصلي
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">رقم العرض</p>
                        <p class="font-semibold">
                            <a href="/offers/{{ $counterOffer->offer_id }}" class="text-blue-600 hover:text-blue-900">
                                #{{ $counterOffer->offer->offer_number }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">حالة العرض</p>
                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                            {{ $counterOffer->offer->status }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">المبلغ الأصلي</p>
                        <p class="font-semibold text-green-600">{{ number_format($counterOffer->offer->offer_amount, 2) }} ريال</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">المبلغ المقترح</p>
                        <p class="font-semibold text-blue-600">{{ number_format($counterOffer->counter_amount, 2) }} ريال</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">التغيير</p>
                        <p class="font-semibold {{ getPriceChangeClass($counterOffer->counter_amount, $counterOffer->offer->offer_amount) }}">
                            {{ getPriceChangeText($counterOffer->counter_amount, $counterOffer->offer->offer_amount) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">تاريخ انتهاء العرض الأصلي</p>
                        <p class="font-semibold">{{ $counterOffer->offer->offer_expiration_date->format('Y-m-d') }}</p>
                    </div>
                </div>

                @if($counterOffer->offer->message)
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 mb-2">رسالة العرض الأصلي</p>
                        <div class="bg-gray-50 p-3 rounded">
                            <p class="text-gray-700">{{ $counterOffer->offer->message }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- تفاصيل العرض المضاد -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4 text-gray-900">
                    <i class="fas fa-exchange-alt ml-2 text-blue-500"></i>
                    تفاصيل العرض المضاد
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">المبلغ المقترح</p>
                        <p class="font-semibold text-blue-600 text-lg">{{ number_format($counterOffer->counter_amount, 2) }} ريال</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">تاريخ الإنهاء</p>
                        <p class="font-semibold">{{ $counterOffer->expiration_date->format('Y-m-d') }}</p>
                        @if($counterOffer->isExpired())
                            <span class="text-red-500 text-sm">(منتهي)</span>
                        @endif
                    </div>
                    @if($counterOffer->proposed_closing_date)
                        <div>
                            <p class="text-sm text-gray-600">تاريخ الإغلاق المقترح</p>
                            <p class="font-semibold">{{ $counterOffer->proposed_closing_date->format('Y-m-d') }}</p>
                        </div>
                    @endif
                    @if($counterOffer->earnest_money)
                        <div>
                            <p class="text-sm text-gray-600">العربون المقترح</p>
                            <p class="font-semibold">{{ number_format($counterOffer->earnest_money, 2) }} ريال</p>
                        </div>
                    @endif
                </div>

                @if($counterOffer->counter_message)
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 mb-2">رسالة العرض المضاد</p>
                        <div class="bg-blue-50 p-3 rounded">
                            <p class="text-gray-700">{{ $counterOffer->counter_message }}</p>
                        </div>
                    </div>
                @endif

                <!-- الشروط المعدلة -->
                @if($counterOffer->counter_terms)
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 mb-2">الشروط المقترحة</p>
                        <div class="bg-gray-50 p-3 rounded">
                            @if(is_array($counterOffer->counter_terms))
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($counterOffer->counter_terms as $term)
                                        <li class="text-gray-700">{{ $term }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-gray-700">{{ $counterOffer->counter_terms }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- الشروط الاحتياطية المعدلة -->
                @if($counterOffer->modified_contingencies)
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 mb-2">الشروط الاحتياطية المعدلة</p>
                        <div class="bg-yellow-50 p-3 rounded">
                            @if(is_array($counterOffer->modified_contingencies))
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($counterOffer->modified_contingencies as $contingency)
                                        <li class="text-gray-700">{{ $contingency }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-gray-700">{{ $counterOffer->modified_contingencies }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- سجل النشاط -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-900">
                    <i class="fas fa-history ml-2 text-blue-500"></i>
                    سجل النشاط
                </h2>
                
                <div class="space-y-4">
                    <!-- إنشاء العرض المضاد -->
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center ml-3">
                            <i class="fas fa-plus text-blue-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-sm">تم إنشاء العرض المضاد</p>
                            <p class="text-sm text-gray-600">
                                بواسطة {{ $counterOffer->counteredBy->name }} - 
                                {{ $counterOffer->created_at->format('Y-m-d H:i') }}
                            </p>
                        </div>
                    </div>

                    <!-- الرد على العرض -->
                    @if($counterOffer->responded_at)
                        <div class="flex items-start">
                            <div class="w-8 h-8 {{ getResponseIconClass($counterOffer->status) }} rounded-full flex items-center justify-center ml-3">
                                <i class="fas {{ getResponseIcon($counterOffer->status) }} {{ getResponseIconColor($counterOffer->status) }} text-xs"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-sm">{{ getResponseText($counterOffer->status) }}</p>
                                <p class="text-sm text-gray-600">
                                    بواسطة {{ $counterOffer->counteredTo->name }} - 
                                    {{ $counterOffer->responded_at->format('Y-m-d H:i') }}
                                </p>
                                @if($counterOffer->rejection_reason)
                                    <p class="text-sm text-red-600 mt-1">السبب: {{ $counterOffer->rejection_reason }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- الجانب الأيمن -->
        <div class="lg:col-span-1">
            <!-- الأطراف -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4 text-gray-900">
                    <i class="fas fa-users ml-2 text-blue-500"></i>
                    الأطراف
                </div>
                
                <!-- من قبل -->
                <div class="mb-4 pb-4 border-b">
                    <p class="text-sm text-gray-600 mb-2">من قبل</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center ml-3">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold">{{ $counterOffer->counteredBy->name }}</p>
                            <p class="text-sm text-gray-600">{{ $counterOffer->counteredBy->email }}</p>
                            <p class="text-sm text-gray-600">{{ $counterOffer->counteredBy->phone ?? 'غير محدد' }}</p>
                        </div>
                    </div>
                </div>

                <!-- إلى -->
                <div>
                    <p class="text-sm text-gray-600 mb-2">إلى</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center ml-3">
                            <i class="fas fa-user text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold">{{ $counterOffer->counteredTo->name }}</p>
                            <p class="text-sm text-gray-600">{{ $counterOffer->counteredTo->email }}</p>
                            <p class="text-sm text-gray-600">{{ $counterOffer->counteredTo->phone ?? 'غير محدد' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات العقار -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4 text-gray-900">
                    <i class="fas fa-home ml-2 text-blue-500"></i>
                    معلومات العقار
                </h2>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">العنوان</p>
                        <p class="font-semibold">{{ $counterOffer->offer->property->title }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">الموقع</p>
                        <p class="font-semibold">{{ $counterOffer->offer->property->location }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">نوع العقار</p>
                        <p class="font-semibold">{{ $counterOffer->offer->property->type }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">المساحة</p>
                        <p class="font-semibold">{{ $counterOffer->offer->property->area }} م²</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">السعر الأساسي</p>
                        <p class="font-semibold">{{ number_format($counterOffer->offer->property->price, 2) }} ريال</p>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t">
                    <a href="/properties/{{ $counterOffer->offer->property_id }}" class="text-blue-600 hover:text-blue-900 text-sm">
                        <i class="fas fa-external-link-alt ml-1"></i>
                        عرض صفحة العقار
                    </a>
                </div>
            </div>

            <!-- الإجراءات -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-900">
                    <i class="fas fa-cog ml-2 text-blue-500"></i>
                    الإجراءات
                </h2>
                
                <div class="space-y-3">
                    <a href="/offers/{{ $counterOffer->offer_id }}" class="block w-full bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 text-center">
                        <i class="fas fa-file-contract ml-2"></i>
                        عرض العرض الأصلي
                    </a>
                    
                    <a href="/properties/{{ $counterOffer->offer->property_id }}" class="block w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 text-center">
                        <i class="fas fa-home ml-2"></i>
                        عرض العقار
                    </a>
                    
                    @if($counterOffer->offer->property->images && $counterOffer->offer->property->images->isNotEmpty())
                        <button onclick="viewPropertyImages()" class="block w-full bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600">
                            <i class="fas fa-images ml-2"></i>
                            صور العقار
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function acceptCounterOffer() {
    if (confirm('هل أنت متأكد من قبول هذا العرض المضاد؟ سيتم تحديث العرض الأصلي بهذه الشروط.')) {
        fetch(`/counter-offers/{{ $counterOffer->id }}/accept`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم قبول العرض المضاد بنجاح');
                location.reload();
            } else {
                alert(data.message || 'حدث خطأ ما');
            }
        });
    }
}

function rejectCounterOffer() {
    const reason = prompt('يرجى إدخال سبب الرفض (اختياري):');
    
    fetch(`/counter-offers/{{ $counterOffer->id }}/reject`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ reason: reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('تم رفض العرض المضاد بنجاح');
            location.reload();
        } else {
            alert(data.message || 'حدث خطأ ما');
        }
    });
}

function withdrawCounterOffer() {
    if (confirm('هل أنت متأكد من سحب هذا العرض المضاد؟')) {
        fetch(`/counter-offers/{{ $counterOffer->id }}/withdraw`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم سحب العرض المضاد بنجاح');
                location.reload();
            } else {
                alert(data.message || 'حدث خطأ ما');
            }
        });
    }
}

function viewPropertyImages() {
    // يمكن فتح معرض الصور هنا
    var images = window.propertyImages || [];
    if (images.length > 0) {
        // فتح الصورة الأولى في نافذة جديدة
        window.open(images[0], '_blank');
    }
}

</script>
@endpush

@endsection

<div id="js-data" 
     data-user-id="{{ auth()->user()->id }}" 
     data-property-images="{{ json_encode($counterOffer->offer->property->images->pluck('url') ?? []) }}" 
     style="display: none;"></div>

<script>
window.currentUserId = parseInt(document.getElementById('js-data').getAttribute('data-user-id'));
window.propertyImages = JSON.parse(document.getElementById('js-data').getAttribute('data-property-images'));
</script>
