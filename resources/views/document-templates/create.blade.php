@extends('admin.layouts.admin')

@section('title', 'إنشاء قالب جديد')

@section('content')
<div class="min-h-screen bg-gray-50" dir="rtl">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-file-alt text-blue-600 ml-3"></i>
                    إنشاء قالب جديد
                </h1>
                <p class="text-gray-600">إدارة وإنشاء قوالب المستندات والعقود</p>
            </div>
            <a href="{{ route('document-templates.index') }}" 
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                <i class="fas fa-arrow-right ml-2"></i>
                عودة
            </a>
        </div>

        <form method="POST" action="{{ route('document-templates.store') }}" class="space-y-8" id="template-form">
            @csrf

            <!-- Basic Information -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center border-b pb-3">
                    <i class="fas fa-info-circle text-blue-500 ml-2"></i>
                    المعلومات الأساسية
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم القالب <span class="text-red-500">*</span></label>
                        <input type="text" name="name" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="مثال: عقد بيع عقار"
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">التصنيف <span class="text-red-500">*</span></label>
                        <select name="category" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                required>
                            <option value="">اختر التصنيف</option>
                            <option value="contract">عقود (Contract)</option>
                            <option value="legal">قانوني (Legal)</option>
                            <option value="financial">مالي (Financial)</option>
                            <option value="administrative">إداري (Administrative)</option>
                            <option value="technical">فني (Technical)</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                    <textarea name="description" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="وصف مختصر للقالب..."></textarea>
                </div>

                <div class="mt-6 flex items-center">
                    <input type="checkbox" name="is_active" value="1" checked id="is_active" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_active" class="mr-2 block text-sm text-gray-900">
                        تفعيل القالب
                    </label>
                </div>
            </div>

            <!-- Variables Section -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-cube text-purple-500 ml-2"></i>
                        المتغيرات (Variables)
                    </h2>
                    <button type="button" id="add-variable" class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-md hover:bg-blue-100 transition-colors text-sm font-medium">
                        <i class="fas fa-plus ml-1"></i> إضافة متغير
                    </button>
                </div>

                <div id="variables-container" class="space-y-4">
                    <!-- Variables will be added here -->
                    <div class="text-center py-8 text-gray-400" id="no-variables-msg">
                        <i class="fas fa-code mb-2 text-2xl"></i>
                        <p>لم يتم إضافة متغيرات بعد. اضغط على "إضافة متغير" للبدء.</p>
                    </div>
                </div>
            </div>

            <!-- Template Content -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center border-b pb-3">
                    <i class="fas fa-file-code text-green-500 ml-2"></i>
                    محتوى القالب
                </h2>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المحتوى <span class="text-red-500">*</span></label>
                    <div class="mb-2 text-xs text-gray-500 bg-blue-50 p-2 rounded border border-blue-100">
                        <i class="fas fa-lightbulb text-blue-400 ml-1"></i>
                        استخدم المتغيرات التي عرفتها أعلاه داخل المحتوى بالشكل التالي: <code>{variable_name}</code>
                    </div>
                    <textarea name="content" rows="20" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm" 
                              required placeholder="اكتب محتوى القالب هنا..."></textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-4 sticky bottom-4 bg-white p-4 border rounded-lg shadow-lg z-10">
                <a href="{{ route('document-templates.index') }}" 
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-times ml-2"></i>
                    إلغاء
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg">
                    <i class="fas fa-save ml-2"></i>
                    حفظ القالب
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let variableIndex = 0;
    const container = document.getElementById('variables-container');
    const noVarsMsg = document.getElementById('no-variables-msg');
    
    document.getElementById('add-variable').addEventListener('click', function() {
        if(noVarsMsg) noVarsMsg.style.display = 'none';
        
        const variableDiv = document.createElement('div');
        variableDiv.className = 'variable-item border border-gray-200 rounded-lg p-4 bg-gray-50 relative hover:shadow-md transition-shadow';
        variableDiv.dataset.index = variableIndex;
        
        variableDiv.innerHTML = `
            <button type="button" class="absolute top-4 left-4 text-red-500 hover:text-red-700 remove-variable bg-white p-1 rounded-full shadow-sm" title="حذف المتغير">
                <i class="fas fa-trash"></i>
            </button>
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-700 mb-1">اسم المتغير (Example: client_name) <span class="text-red-500">*</span></label>
                    <input type="text" name="variables[${variableIndex}][name]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm dir-ltr" placeholder="client_name" required>
                </div>
                
                <div class="md:col-span-4">
                    <label class="block text-xs font-medium text-gray-700 mb-1">وصف المتغير (للمستخدم)</label>
                    <input type="text" name="variables[${variableIndex}][description]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="مثال: اسم العميل">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">النوع <span class="text-red-500">*</span></label>
                    <select name="variables[${variableIndex}][type]" class="variable-type-select w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" onchange="toggleOptions(this)">
                        <option value="text">نص (Text)</option>
                        <option value="date">تاريخ (Date)</option>
                        <option value="number">رقم (Number)</option>
                        <option value="currency">عملة (Currency)</option>
                        <option value="email">بريد إلكتروني (Email)</option>
                        <option value="phone">هاتف (Phone)</option>
                        <option value="select">قائمة (Select)</option>
                    </select>
                </div>

                <div class="md:col-span-3 options-container hidden">
                    <label class="block text-xs font-medium text-gray-700 mb-1">الخيارات (افصل بينها بفاصلة)</label>
                    <input type="text" name="variables[${variableIndex}][options_text]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="خيار 1, خيار 2, خيار 3">
                </div>
            </div>
        `;
        
        container.appendChild(variableDiv);
        variableIndex++;
    });
    
    // Remove variable functionality
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-variable')) {
            const item = e.target.closest('.variable-item');
            item.remove();
            
            if (container.children.length === 1 && noVarsMsg) { // 1 because noVarsMsg is a child
                 noVarsMsg.style.display = 'block';
            } else if (container.children.length === 0 && !noVarsMsg) {
                 // Should not happen if noVarsMsg is kept
            }
        }
    });

    // Initial check
    if (container.children.length <= 1) {
        if(noVarsMsg) noVarsMsg.style.display = 'block';
    }
});

function toggleOptions(selectElement) {
    const container = selectElement.closest('.grid').querySelector('.options-container');
    if (selectElement.value === 'select') {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }
}
</script>
@endpush
@endsection