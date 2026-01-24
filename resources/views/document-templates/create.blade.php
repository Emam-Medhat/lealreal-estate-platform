@extends('layouts.app')

@section('title', 'إنشاء قالب جديد')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">إنشاء قالب جديد</h1>
        <a href="{{ route('document-templates.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
            <i class="fas fa-arrow-right ml-2"></i>عودة
        </a>
    </div>

    <form method="POST" action="{{ route('document-templates.store') }}" class="space-y-6">
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
                    <select name="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">اختر الفئة</option>
                        <option value="contract">عقود</option>
                        <option value="legal">قانوني</option>
                        <option value="financial">مالي</option>
                        <option value="administrative">إداري</option>
                        <option value="technical">فني</option>
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                    <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
            </div>
        </div>

        <!-- Template Content -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">محتوى القالب</h2>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">المحتوى <span class="text-red-500">*</span></label>
                <textarea name="content" rows="15" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required placeholder="أدخل محتوى القالب هنا..."></textarea>
                <p class="text-sm text-gray-500 mt-1">استخدم المتغيرات مثل {{name}}, {{date}}, {{amount}} للإشارة إلى البيانات الديناميكية</p>
            </div>
        </div>

        <!-- Variables -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">متغيرات القالب</h2>
            
            <div id="variables-container">
                <div class="variable-item border border-gray-200 rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">اسم المتغير</label>
                            <input type="text" name="variables[0][name]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="مثال: client_name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                            <input type="text" name="variables[0][description]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="مثال: اسم العميل">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">النوع</label>
                            <select name="variables[0][type]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="text">نص</option>
                                <option value="date">تاريخ</option>
                                <option value="number">رقم</option>
                                <option value="currency">عملة</option>
                                <option value="email">بريد إلكتروني</option>
                                <option value="phone">هاتف</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="button" id="add-variable" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                <i class="fas fa-plus ml-2"></i>إضافة متغير
            </button>
        </div>

        <!-- Settings -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">الإعدادات</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                    <input type="checkbox" name="requires_signature" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <label class="mr-2 text-sm text-gray-700">يتطلب توقيع</label>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4 space-x-reverse">
            <a href="{{ route('document-templates.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">
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
    let variableCount = 1;
    
    document.getElementById('add-variable').addEventListener('click', function() {
        const container = document.getElementById('variables-container');
        const variableDiv = document.createElement('div');
        variableDiv.className = 'variable-item border border-gray-200 rounded-lg p-4 mb-4';
        variableDiv.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <h3 class="font-medium text-gray-900">متغير ${variableCount + 1}</h3>
                <button type="button" class="text-red-500 hover:text-red-700 remove-variable">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">اسم المتغير</label>
                    <input type="text" name="variables[${variableCount}][name]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="مثال: client_name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                    <input type="text" name="variables[${variableCount}][description]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="مثال: اسم العميل">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">النوع</label>
                    <select name="variables[${variableCount}][type]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="text">نص</option>
                        <option value="date">تاريخ</option>
                        <option value="number">رقم</option>
                        <option value="currency">عملة</option>
                        <option value="email">بريد إلكتروني</option>
                        <option value="phone">هاتف</option>
                    </select>
                </div>
            </div>
        `;
        container.appendChild(variableDiv);
        variableCount++;
    });
    
    // Remove variable functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-variable')) {
            e.target.closest('.variable-item').remove();
        }
    });
});
</script>
@endsection
