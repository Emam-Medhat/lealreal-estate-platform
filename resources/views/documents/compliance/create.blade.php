@extends('layouts.app')

@section('title', 'فحص امتثال جديد')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">فحص امتثال جديد</h1>
        <a href="{{ route('documents.show', $document) }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
            <i class="fas fa-arrow-right ml-2"></i>عودة للوثيقة
        </a>
    </div>

    <!-- Document Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">معلومات الوثيقة</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">العنوان</label>
                <p class="text-gray-900">{{ $document->title }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">النوع</label>
                <p class="text-gray-900">{{ $document->type }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">الفئة</label>
                <p class="text-gray-900">{{ $document->category->name ?? 'غير محدد' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">تاريخ الإنشاء</label>
                <p class="text-gray-900">{{ $document->created_at->format('Y-m-d') }}</p>
            </div>
        </div>
    </div>

    <!-- Compliance Form -->
    <form method="POST" action="{{ route('documents.compliance.store', $document) }}" class="space-y-6">
        @csrf
        
        <!-- Compliance Checks -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">فحص متطلبات الامتثال</h2>
            
            @foreach($requirements as $requirement)
                <div class="border border-gray-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900">
                                {{ $requirement['title'] }}
                                @if($requirement['mandatory'] ?? false)
                                    <span class="text-red-500 text-sm mr-2">*</span>
                                @endif
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $requirement['description'] }}</p>
                        </div>
                        <div class="ml-4">
                            <select name="compliance_checks[{{ $loop->index }}][requirement_id]" class="hidden" value="{{ $requirement['id'] }}">
                                <option value="{{ $requirement['id'] }}" selected>{{ $requirement['id'] }}</option>
                            </select>
                            <select name="compliance_checks[{{ $loop->index }}][status]" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">اختر الحالة</option>
                                <option value="compliant">ممتثل</option>
                                <option value="non_compliant">غير ممتثل</option>
                                <option value="not_applicable">غير مطبق</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                        <textarea name="compliance_checks[{{ $loop->index }}][notes]" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="أدخل ملاحظاتك حول هذا المتطلب..."></textarea>
                    </div>
                    
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">الأدلة (اختياري)</label>
                        <input type="text" name="compliance_checks[{{ $loop->index }}][evidence][]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="أدخل رابط أو وصف الدليل...">
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Overall Assessment -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">التقييم العام</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الحالة العامة <span class="text-red-500">*</span></label>
                    <select name="overall_status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">اختر الحالة</option>
                        <option value="compliant">ممتثل</option>
                        <option value="non_compliant">غير ممتثل</option>
                        <option value="needs_review">يحتاج مراجعة</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ المراجعة التالية</label>
                    <input type="date" name="next_review_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات الامتثال <span class="text-red-500">*</span></label>
                <textarea name="compliance_notes" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="أدخل ملاحظاتك العامة حول امتثال الوثيقة..." required></textarea>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4 space-x-reverse">
            <a href="{{ route('documents.show', $document) }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">
                إلغاء
            </a>
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                <i class="fas fa-save ml-2"></i>حفظ فحص الامتثال
            </button>
        </div>
    </form>
</div>
@endsection
