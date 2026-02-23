@extends('admin.layouts.admin')

@section('title', 'تفاصيل التقييم')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تفاصيل التقييم</h1>
            <p class="text-gray-600 mt-1">تفاصيل تقييم العميل {{ $leadScore->lead->first_name }} {{ $leadScore->lead->last_name }}</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('lead-scoring.edit', $leadScore->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                <i class="fas fa-edit ml-2"></i>
                تعديل التقييم
            </a>
            <a href="{{ route('lead-scoring.index') }}" class="text-gray-600 hover:text-gray-900 font-medium ml-4">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للقائمة
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <!-- Score Details -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">معلومات التقييم</h2>
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-gray-600 font-medium">الدرجة النهائية:</span>
                <span class="text-2xl font-bold {{ $leadScore->score >= 70 ? 'text-green-600' : ($leadScore->score >= 40 ? 'text-orange-600' : 'text-red-600') }}">
                    {{ $leadScore->score }} / 100
                </span>
            </div>
            
            <div>
                <span class="block text-gray-600 font-medium mb-2">عوامل التقييم:</span>
                @if(!empty($leadScore->factors))
                    <ul class="list-disc list-inside space-y-1 text-gray-700">
                        @foreach($leadScore->factors as $factor)
                            <li>{{ $factor }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-400 text-sm">لا توجد عوامل مسجلة</p>
                @endif
            </div>

            @if($leadScore->notes)
            <div>
                <span class="block text-gray-600 font-medium mb-2">ملاحظات:</span>
                <p class="text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-100">{{ $leadScore->notes }}</p>
            </div>
            @endif

            <div class="text-sm text-gray-500 mt-4 pt-4 border-t border-gray-100 flex justify-between">
                <span>تم التقييم بواسطة: {{ $leadScore->calculatedBy->name ?? 'غير معروف' }}</span>
                <span>{{ $leadScore->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>
    </div>

    <!-- Lead Details -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">معلومات العميل</h2>
        <div class="space-y-4">
            <div class="flex items-center space-x-3 space-x-reverse">
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-user text-purple-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">{{ $leadScore->lead->first_name }} {{ $leadScore->lead->last_name }}</h3>
                    <p class="text-sm text-gray-500">{{ $leadScore->lead->email }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <div class="bg-gray-50 p-3 rounded-lg">
                    <span class="block text-xs text-gray-500 mb-1">المصدر</span>
                    <span class="font-medium text-gray-900">{{ $leadScore->lead->source->name ?? 'غير محدد' }}</span>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <span class="block text-xs text-gray-500 mb-1">الحالة الحالية</span>
                    <span class="font-medium text-gray-900">{{ $leadScore->lead->status->name ?? 'غير محدد' }}</span>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <span class="block text-xs text-gray-500 mb-1">الموظف المسؤول</span>
                    <span class="font-medium text-gray-900">{{ $leadScore->lead->assignedUser->name ?? 'غير معين' }}</span>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <span class="block text-xs text-gray-500 mb-1">تاريخ التسجيل</span>
                    <span class="font-medium text-gray-900">{{ $leadScore->lead->created_at->format('Y-m-d') }}</span>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('leads.show', $leadScore->lead_id) }}" class="block w-full text-center py-2 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 font-medium transition-colors">
                    عرض الملف الكامل للعميل
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
