@extends('layouts.app')

@section('title', 'إنشاء قالب عقد جديد')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">إنشاء قالب عقد جديد</h1>
        <a href="{{ route('contract-templates.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
            <i class="fas fa-arrow-right ml-2"></i>عودة
        </a>
    </div>

    <form method="POST" action="{{ route('contract-templates.store') }}" class="space-y-6">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">المعلومات الأساسية</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">اسم القالب <span class="text-red-500">*</span></label>
                    <input type="text" name="name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الفئة <span class="text-red-500">*</span></label>
                    <select name="category_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">اختر الفئة</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                    <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
            </div>
        </div>

        <!-- Contract Terms -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">بنود العقد</h2>
            
            <div id="terms-container">
                <div class="term-item border border-gray-200 rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">عنوان البند <span class="text-red-500">*</span></label>
                            <input type="text" name="terms[0][title]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="مثال: شروط الدفع" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الترتيب</label>
                            <input type="number" name="terms[0][order]" value="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" min="0">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">المحتوى <span class="text-red-500">*</span></label>
                            <textarea name="terms[0][content]" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="أدخل محتوى البند هنا..." required></textarea>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="terms[0][is_required]" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <label class="mr-2 text-sm text-gray-700">بند إلزامي</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="button" id="add-term" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                <i class="fas fa-plus ml-2"></i>إضافة بند
            </button>
        </div>

        <!-- Standard Clauses -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">البنود القياسية</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center">
                    <input type="checkbox" name="include_force_majeure" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <label class="mr-2 text-sm text-gray-700">بند القوة القاهرة</label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="include_confidentiality" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <label class="mr-2 text-sm text-gray-700">بند السرية</label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="include_termination" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <label class="mr-2 text-sm text-gray-700">بند الإنهاء</label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="include_dispute_resolution" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <label class="mr-2 text-sm text-gray-700">بند حل النزاعات</label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="include_governing_law" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <label class="mr-2 text-sm text-gray-700">بند القانون الحاكم</label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="include_liability" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <label class="mr-2 text-sm text-gray-700">بند المسؤولية</label>
                </div>
            </div>
        </div>

        <!-- Settings -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">الإعدادات</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">نوع العقد</label>
                    <select name="contract_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="sale">عقد بيع</option>
                        <option value="rental">عقد إيجار</option>
                        <option value="service">عقد خدمات</option>
                        <option value="partnership">عقد شراكة</option>
                        <option value="employment">عقد عمل</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">المدة الافتراضية (بالأيام)</label>
                    <input type="number" name="default_duration" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="365">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">اللغة</label>
                    <select name="language" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="ar">العربية</option>
                        <option value="en">English</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الاتجاه</label>
                    <select name="direction" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="rtl">من اليمين إلى اليسار</option>
                        <option value="ltr">من اليسار إلى اليمين</option>
                    </select>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <label class="mr-2 text-sm text-gray-700">نشط</label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="requires_signature" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <label class="mr-2 text-sm text-gray-700">يتطلب توقيع</label>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4 space-x-reverse">
            <a href="{{ route('contract-templates.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">
                إلغاء
            </a>
            <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600">
                <i class="fas fa-save ml-2"></i>حفظ القالب
            </button>
        </div>
    </form>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let termCount = 1;
    
    document.getElementById('add-term').addEventListener('click', function() {
        const container = document.getElementById('terms-container');
        const termDiv = document.createElement('div');
        termDiv.className = 'term-item border border-gray-200 rounded-lg p-4 mb-4';
        termDiv.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <h3 class="font-medium text-gray-900">بند ${termCount + 1}</h3>
                <button type="button" class="text-red-500 hover:text-red-700 remove-term">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">عنوان البند <span class="text-red-500">*</span></label>
                    <input type="text" name="terms[${termCount}][title]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="مثال: شروط الدفع" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الترتيب</label>
                    <input type="number" name="terms[${termCount}][order]" value="${termCount + 1}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" min="0">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">المحتوى <span class="text-red-500">*</span></label>
                    <textarea name="terms[${termCount}][content]" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="أدخل محتوى البند هنا..." required></textarea>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="terms[${termCount}][is_required]" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <label class="mr-2 text-sm text-gray-700">بند إلزامي</label>
                </div>
            </div>
        `;
        container.appendChild(termDiv);
        termCount++;
    });
    
    // Remove term functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-term')) {
            e.target.closest('.term-item').remove();
        }
    });
});
</script>
@endsection
