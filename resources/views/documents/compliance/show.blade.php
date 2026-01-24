@extends('layouts.app')

@section('title', 'تفاصيل فحص الامتثال')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">تفاصيل فحص الامتثال</h1>
        <div class="flex space-x-2 space-x-reverse">
            <a href="{{ route('documents.compliance.edit', $compliance) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                <i class="fas fa-edit ml-2"></i>تعديل
            </a>
            <a href="{{ route('documents.show', $compliance->document) }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                <i class="fas fa-arrow-right ml-2"></i>الوثيقة
            </a>
        </div>
    </div>

    <!-- Compliance Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">الحالة العامة</p>
                    <p class="text-lg font-semibold">
                        @switch($compliance->overall_status)
                            @case('compliant')
                                <span class="text-green-600">ممتثل</span>
                                @break
                            @case('non_compliant')
                                <span class="text-red-600">غير ممتثل</span>
                                @break
                            @case('needs_review')
                                <span class="text-yellow-600">يحتاج مراجعة</span>
                                @break
                        @endswitch
                    </p>
                </div>
                <div class="text-3xl">
                    @switch($compliance->overall_status)
                        @case('compliant')
                            <i class="fas fa-check-circle text-green-500"></i>
                            @break
                        @case('non_compliant')
                            <i class="fas fa-times-circle text-red-500"></i>
                            @break
                        @case('needs_review')
                            <i class="fas fa-exclamation-circle text-yellow-500"></i>
                            @break
                    @endswitch
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">درجة الامتثال</p>
                    <p class="text-lg font-semibold">{{ number_format($compliance->compliance_score, 1) }}%</p>
                </div>
                <div class="text-3xl">
                    <i class="fas fa-chart-pie text-blue-500"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">الفاحص</p>
                    <p class="text-lg font-semibold">{{ $compliance->checkedBy->name }}</p>
                </div>
                <div class="text-3xl">
                    <i class="fas fa-user-check text-purple-500"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">تاريخ الفحص</p>
                    <p class="text-lg font-semibold">{{ $compliance->checked_at->format('Y-m-d') }}</p>
                </div>
                <div class="text-3xl">
                    <i class="fas fa-calendar text-indigo-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">معلومات الوثيقة</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">العنوان</label>
                <p class="text-gray-900">{{ $compliance->document->title }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">النوع</label>
                <p class="text-gray-900">{{ $compliance->document->type }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">الفئة</label>
                <p class="text-gray-900">{{ $compliance->document->category->name ?? 'غير محدد' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">المراجعة التالية</label>
                <p class="text-gray-900 {{ $compliance->next_review_date && $compliance->next_review_date->isPast() ? 'text-red-600 font-semibold' : '' }}">
                    {{ $compliance->next_review_date ? $compliance->next_review_date->format('Y-m-d') : 'غير محدد' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Compliance Checks -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">فحص المتطلبات</h2>
        
        @if($compliance->compliance_checks)
            @foreach($compliance->compliance_checks as $index => $check)
                <div class="border border-gray-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900">{{ $check['requirement_id'] ?? 'متطلب ' . ($index + 1) }}</h3>
                            @if(isset($check['notes']) && !empty($check['notes']))
                                <p class="text-sm text-gray-600 mt-1">{{ $check['notes'] }}</p>
                            @endif
                        </div>
                        <div class="ml-4">
                            @switch($check['status'] ?? '')
                                @case('compliant')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        ممتثل
                                    </span>
                                    @break
                                @case('non_compliant')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        غير ممتثل
                                    </span>
                                    @break
                                @case('not_applicable')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        غير مطبق
                                    </span>
                                    @break
                            @endswitch
                        </div>
                    </div>
                    
                    @if(isset($check['evidence']) && !empty($check['evidence']))
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">الأدلة</label>
                            <div class="text-sm text-gray-600">
                                @if(is_array($check['evidence']))
                                    @foreach($check['evidence'] as $evidence)
                                        <div class="mb-1">• {{ $evidence }}</div>
                                    @endforeach
                                @else
                                    <div>• {{ $check['evidence'] }}</div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <p class="text-gray-500 text-center py-4">لا توجد فحوصات مسجلة</p>
        @endif
    </div>

    <!-- Compliance Notes -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold mb-4">ملاحظات الامتثال</h2>
        <div class="prose max-w-none">
            <p class="text-gray-700">{{ $compliance->compliance_notes }}</p>
        </div>
    </div>
</div>
@endsection
