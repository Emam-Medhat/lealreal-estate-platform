@extends('layouts.app')

@section('title', 'تفاصيل طلب التحقق')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">تفاصيل طلب التحقق</h1>
        <div class="flex space-x-2 space-x-reverse">
            @if($verification->status === 'verified')
                <a href="{{ route('notary.certificate', $verification) }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                    <i class="fas fa-certificate ml-2"></i>شهادة التوثيق
                </a>
            @endif
            @if($verification->status === 'requires_info')
                <a href="{{ route('notary.info', $verification) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                    <i class="fas fa-info ml-2"></i>تقديم معلومات
                </a>
            @endif
            <a href="{{ route('notary.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                <i class="fas fa-arrow-right ml-2"></i>عودة
            </a>
        </div>
    </div>

    <!-- Verification Status -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">الحالة</p>
                    <p class="text-lg font-semibold">
                        @switch($verification->status)
                            @case('pending')
                                <span class="text-yellow-600">في الانتظار</span>
                                @break
                            @case('verified')
                                <span class="text-green-600">موثق</span>
                                @break
                            @case('rejected')
                                <span class="text-red-600">مرفوض</span>
                                @break
                            @case('requires_info')
                                <span class="text-blue-600">مطلوب معلومات</span>
                                @break
                        @endswitch
                    </p>
                </div>
                <div class="text-3xl">
                    @switch($verification->status)
                        @case('pending')
                            <i class="fas fa-clock text-yellow-500"></i>
                            @break
                        @case('verified')
                            <i class="fas fa-check-circle text-green-500"></i>
                            @break
                        @case('rejected')
                            <i class="fas fa-times-circle text-red-500"></i>
                            @break
                        @case('requires_info')
                            <i class="fas fa-info-circle text-blue-500"></i>
                            @break
                    @endswitch
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">كود التحقق</p>
                    <p class="text-lg font-mono">{{ $verification->verification_code }}</p>
                </div>
                <div class="text-3xl">
                    <i class="fas fa-barcode text-purple-500"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">النوع</p>
                    <p class="text-lg font-semibold">
                        @switch($verification->verification_type)
                            @case('standard')
                                <span class="text-gray-600">قياسي</span>
                                @break
                            @case('expedited')
                                <span class="text-blue-600">معجل</span>
                                @break
                            @case('priority')
                                <span class="text-purple-600">أولوية</span>
                                @break
                        @endswitch
                    </p>
                </div>
                <div class="text-3xl">
                    <i class="fas fa-tachometer-alt text-indigo-500"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">الإكمال المتوقع</p>
                    <p class="text-lg font-semibold {{ $verification->estimated_completion && $verification->estimated_completion->isPast() ? 'text-red-600' : '' }}">
                        {{ $verification->estimated_completion ? $verification->estimated_completion->format('Y-m-d') : 'غير محدد' }}
                    </p>
                </div>
                <div class="text-3xl">
                    <i class="fas fa-calendar-alt text-orange-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Contract Information -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">معلومات العقد</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">عنوان العقد</label>
                <p class="text-gray-900">{{ $verification->contract->title }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">رقم العقد</label>
                <p class="text-gray-900">{{ $verification->contract->contract_number }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">تاريخ الطلب</label>
                <p class="text-gray-900">{{ $verification->requested_at->format('Y-m-d H:i') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">طالب التحقق</label>
                <p class="text-gray-900">{{ $verification->requestedBy->name }}</p>
            </div>
        </div>
    </div>

    <!-- Witnesses -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">الشهود</h2>
        
        @if($verification->witnesses)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($verification->witnesses as $index => $witness)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-900 mb-2">شاهد {{ $index + 1 }}</h3>
                        <div class="space-y-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">الاسم</label>
                                <p class="text-gray-900">{{ $witness['name'] ?? 'غير محدد' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">رقم الهوية</label>
                                <p class="text-gray-900">{{ $witness['national_id'] ?? 'غير محدد' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">رقم الهاتف</label>
                                <p class="text-gray-900">{{ $witness['phone'] ?? 'غير محدد' }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">لا يوجد شهود مسجلون</p>
        @endif
    </div>

    <!-- Documents -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">المستندات</h2>
        
        @if($verification->documents)
            <div class="space-y-3">
                @foreach($verification->documents as $document)
                    <div class="border border-gray-200 rounded-lg p-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $document['name'] ?? 'مستند' }}</h3>
                                <p class="text-sm text-gray-600">{{ $document['type'] ?? 'غير محدد' }}</p>
                            </div>
                            @if(isset($document['path']))
                                <a href="{{ asset('storage/' . $document['path']) }}" target="_blank" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-download"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">لا توجد مستندات مسجلة</p>
        @endif
    </div>

    <!-- Verification Details -->
    @if($verification->status === 'verified' && $verification->verification_details)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">تفاصيل التحقق</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">الموثق</label>
                    <p class="text-gray-900">{{ $verification->notary->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">تاريخ التحقق</label>
                    <p class="text-gray-900">{{ $verification->verified_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>
            
            @if($verification->verification_notes)
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات التحقق</label>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-gray-700">{{ $verification->verification_notes }}</p>
                    </div>
                </div>
            @endif
            
            @if($verification->notary_seal_path)
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">ختم التوثيق</label>
                    <img src="{{ asset('storage/' . $verification->notary_seal_path) }}" alt="ختم التوثيق" class="w-32 h-32 border border-gray-300 rounded">
                </div>
            @endif
        </div>
    @endif

    <!-- Additional Requirements -->
    @if($verification->status === 'requires_info' && $verification->additional_requirements)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4 text-yellow-800">متطلبات إضافية</h2>
            
            <div class="space-y-3">
                @foreach($verification->additional_requirements as $requirement)
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 ml-3"></i>
                        <div>
                            <p class="text-gray-900">{{ $requirement['description'] ?? 'متطلب إضافي' }}</p>
                            @if(isset($requirement['deadline']))
                                <p class="text-sm text-gray-600">الموعد النهائي: {{ $requirement['deadline'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($verification->additional_notes)
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات إضافية</label>
                    <div class="bg-yellow-100 rounded-lg p-3">
                        <p class="text-gray-700">{{ $verification->additional_notes }}</p>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Notes -->
    @if($verification->notes)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">ملاحظات الطلب</h2>
            <div class="prose max-w-none">
                <p class="text-gray-700">{{ $verification->notes }}</p>
            </div>
        </div>
    @endif
</div>
@endsection
