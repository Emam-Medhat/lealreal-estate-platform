@extends('layouts.app')

@section('title', 'تفاصيل العقد')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- رأس الصفحة -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">عقد رقم: {{ $contract->contract_number }}</h1>
                <div class="flex items-center space-x-4 space-x-reverse">
                    <span class="px-3 py-1 text-sm rounded-full {{ getStatusClass($contract->status) }}">
                        {{ getStatusText($contract->status) }}
                    </span>
                    <span class="text-gray-600">
                        <i class="fas fa-calendar"></i>
                        {{ $contract->created_at->format('Y-m-d') }}
                    </span>
                </div>
            </div>
            <div class="flex space-x-2 space-x-reverse">
                @if($contract->status === 'awaiting_signature' && auth()->user()->id === $contract->buyer_id || auth()->user()->id === $contract->seller_id)
                    <button onclick="signContract()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                        <i class="fas fa-signature ml-2"></i>
                        توقيع العقد
                    </button>
                @endif
                @if(in_array($contract->status, ['draft', 'pending_review']))
                    <a href="/contracts/{{ $contract->id }}/edit" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                        <i class="fas fa-edit ml-2"></i>
                        تعديل
                    </a>
                @endif
                <button onclick="downloadContract()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                    <i class="fas fa-download ml-2"></i>
                    تحميل
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- المعلومات الرئيسية -->
        <div class="lg:col-span-2">
            <!-- تفاصيل العقار -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4 text-gray-900">
                    <i class="fas fa-home ml-2 text-blue-500"></i>
                    تفاصيل العقار
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">عنوان العقار</p>
                        <p class="font-semibold">{{ $contract->property->title ?? 'غير محدد' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">نوع العقار</p>
                        <p class="font-semibold">{{ $contract->property->type ?? 'غير محدد' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">المساحة</p>
                        <p class="font-semibold">{{ $contract->property->area ?? '0' }} م²</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">الموقع</p>
                        <p class="font-semibold">{{ $contract->property->location ?? 'غير محدد' }}</p>
                    </div>
                </div>
            </div>

            <!-- تفاصيل العقد -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4 text-gray-900">
                    <i class="fas fa-file-contract ml-2 text-blue-500"></i>
                    تفاصيل العقد
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">نوع العقد</p>
                        <p class="font-semibold">{{ getContractTypeText($contract->contract_type) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">سعر الشراء</p>
                        <p class="font-semibold text-green-600">{{ number_format($contract->purchase_price, 2) }} ريال</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">العربون</p>
                        <p class="font-semibold">{{ number_format($contract-> arrangements, 2) ?? '0.00' }} ريال</p>
                    </div>
                    <集>
                        <p class="text-sm text-gray-600">تاريخ العقد</p>
                        note class="font-semibold">{{ $contract->contract_date->format('Y-m-d') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">تاريخ الإغلاق</p>
                        <p class="font-semibold">{{ $contract->closing_date->formatpaired('Y-m-d') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">تاريخ التسليم</p>
                        <p class="font-semibold">{{ $contract->possession_date集?->format('Y-m-d') ?? 'غير محدد' }}</p>
                    </div>
                </div>

                @if($contract->special_provisions)
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 mb-2">أحكام خاصة念念</p>
                        <div class="bg-gray-50 p-3 rounded">
                            <p class="text-gray-700">{{ $contract->special_provisions }}</p>
                        </div>
                    </div>
                @endif

                @if($contract->notes)
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 mb-2">ملاحظات</p>
                        <div class="bg-gray-50 p-3 rounded">
                            <p class="text-gray-700">{{ $contract->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- الشروط والبنود -->
            -->
            @if($contract->contract_terms)
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-900">
                        <i class="fas fa-list-check ml-2 text-blue-500"></i>
                        الشروط والبنود
                    </h2>
                    <div class="space-y-3">
                        @if(is_array($contract->contract_terms))
                            @foreach($contract->contract_terms as $term)
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 ml-2 mt-1"></i>
                                    <p class="text-gray-700">{{ $term }}</p>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-700">{{ $contract->contract_terms }}</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- الشروط الاحتياطية -->
            @if($contract->contingencies)
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-900">
                        <i class="fas fa-exclamation-triangle ml-2 text-yellow-500"></i>
                        الشروط الاحتياطية
                    </h2>
                    <div class="space-y-3">
                        @if(is_array($contract->contingencies))
                            @foreach($contract->contingencies as $contingency)
                                <div class="flex items-start">
                                    <i class="fas fa-shield-alt text-yellow-500 ml-2 mt-1"></i>
                                    <p class="text-gray-700">{{ $contingency }}</p>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-700">{{ $contract->contingencies }}</p>
                        @endif
                    </div>
                </div>
            @endif
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
                            <p class="font-semibold">{{ $contract->buyer->name }}</p>
                            <p class="text-sm text-gray-600">{{ $contract->buyer->email }}</p>
                            <p class="text-sm text-gray-600">{{ $contract->buyer->phone ?? 'غير محدد' }}</p>
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
                            <p class="font-semibold">{{ $contract->seller->name }}</p>
                            <p class="text-sm text-gray-600">{{ $contract->seller->email }}</p>
                            <p class="text-sm text-gray-600">{{ $contract->seller->phone ?? 'غير محدد' }}</p>
                        </div>
                    </div>
                </div>

                <!-- الوكيل (إذا وجد) -->
                @if($contract->agent)
                    <div class="mt-4 pt-4 border-t">
                        <p class="text-sm text-gray-600 mb-2">الوكيل</p>
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center ml-3">
                                <i class="fas fa-user-tie text-purple-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold">{{ $contract->agent->name }}</p>
                                <p class="text-sm text-gray-600">{{ $contract->agent->email }}</p>
                                <p class="text-sm text-gray-600">{{ $contract->agent->phone ?? 'غير محدد' }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- التوقيعات -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4 text-gray-900">
                    <i class="fas fa-signature ml-2 text-blue-500"></i>
                    التوقيعات
                </h2>
                
                @foreach($contract->signatures ?? [] as $signature)
                    <div class="mb-3 pb-3 border-b last:border-b-0">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold">{{ getPartyTypeText($signature->party_type) }}</span>
                            <span class="px-2 py-1 text-xs rounded-full {{ getSignatureStatusClass($signature->status) }}">
                                {{ getSignatureStatusText($signature->status) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">{{ $signature->user->name }}</p>
                        @if($signature->signed_at)
                            <p class="text-xs text-gray-500">تم التوقيع: {{ $signature->signed_at->format('Y-m-d H:i') }}</p>
                        @endif
                    </div>
                @endforeach
                
                @if(!$contract->signatures || $contract->signatures->isEmpty())
                    <p class="text-gray-500 text-center">لا توجد توقيعات بعد</p>
                @endif
            </div>

            <!-- التعديلات -->
            @if($contract->amendments && $contract->amendments->isNotEmpty())
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-900">
                        <i class="fas fa-edit ml-2 text-blue-500"></i>
                        التعديلات
                    </h2>
                    
                    @foreach($contract->amendments as $amendment)
                        <div class="mb-3 pb-3 border-b last:border-b-0">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-semibold">تعديل رقم {{ $amendment->amendment_number }}</span>
                                <span class="px-2 py-1 text-xs rounded-full {{ getAmendmentStatusClass($amendment->status) }}">
                                    {{ getAmendmentStatusText($amendment->status) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-700">{{ $amendment->title }}</p>
                            <p class="text-xs text-gray-500">{{ $amendment->proposed_at->format('Y-m-d') }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function signContract() {
    if (confirm('هل أنت متأكد من توقيع هذا العقد؟ هذا الإجراء لا يمكن التراجع عنه.')) {
        fetch(`/contracts/{{ $contract->id }}/sign`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم توقيع العقد بنجاح');
                location.reload();
            } else {
                alert(data.message || 'حدث خطأ ما');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ ما');
        });
    }
}

function downloadContract() {
    window.open(`/contracts/{{ $contract->id }}/download`, '_blank');
}
</script>

@php
function getStatusClass($status) {
    $classes = [
        'draft' => 'bg-gray-100 text-gray-800',
        'pending_review' => 'bg-yellow-100 text-yellow-800',
        'awaiting_signature' => 'bg-blue-100 text-blue-800',
        'signed' => 'bg-green-100 text-green-800',
        'executed' => 'bg-purple-100 text-purple-800',
        'completed' => 'bg-green-100 text-green-800',
        'terminated' => 'bg-red-100 text-red-800',
        'cancelled' => 'bg-gray-100 text-gray-800',
        'expired' => 'bg-red-100 text-red-800'
    ];
    return $classes[$status] ?? 'bg-gray-100 text-gray-800';
}

function getStatusText($status) {
    $texts = [
        'draft' => 'مسودة',
        'pending_review' => 'قيد المراجعة',
        'awaiting_signature' => 'في انتظار التوقيع',
        'signed' => 'موقع',
        'executed' => 'منفذ',
        'completed' => 'مكتمل',
        'terminated' => 'ملغي',
        'cancelled' => 'ملغى',
        'expired' => 'منتهي'
    ];
    return $texts[$status] ?? $status;
}

function getContractTypeText($type) {
    $types = [
        'purchase' => 'عقد شراء',
        'rent' => 'عقد إيجار',
        'lease_option' => 'عقد إيجار مع خيار شراء',
        'rental_agreement' => 'اتفاقية إيجار'
    ];
    return $types[$type] ?? $type;
}

function getPartyTypeText($type) {
    $types = [
        'buyer' => 'المشتري',
        'seller' => 'البائع',
        'agent' => 'الوكيل',
        'witness' => 'شاهد',
        'notary' => 'كاتب العدل',
        'other' => 'آخر'
    ];
    return $types[$type] ?? $type;
}

function getSignatureStatusClass($status) {
    $classes = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'signed' => 'bg-green-100 text-green-800',
        'declined' => 'bg-red-100 text-red-800',
        'expired' => 'bg-gray-100 text-gray-800'
    ];
    return $classes[$status] ?? 'bg-gray-100 text-gray-800';
}

function getSignatureStatusText($status) {
    $texts = [
        'pending' => 'في انتظار التوقيع',
        'signed' => 'موقع',
        'declined' => 'مرفوض',
        'expired' => 'منتهي'
    ];
    return $texts[$status] ?? $status;
}

function getAmendmentStatusClass($status) {
    $classes = [
        'draft' => 'bg-gray-100 text-gray-800',
        'proposed' => 'bg-blue-100 text-blue-800',
        'accepted' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
        'withdrawn' => 'bg-gray-100 text-gray-800'
    ];
    return $classes[$status] ?? 'bg-gray-100 text-gray-800';
}

function getAmendmentStatusText($status) {
    $texts = [
        'draft' => 'مسودة',
        'proposed' => 'مقترح',
        'accepted' => 'مقبول',
        'rejected' => 'مرفوض',
        'withdrawn' => 'مسحوب'
    ];
    return $texts[$status] ?? $status;
}
@endphp
@endpush
