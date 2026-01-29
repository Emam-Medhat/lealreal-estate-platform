@extends('layouts.app')

@section('title', 'إضافة نوع عقاري جديد')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-100 rounded-lg p-3">
                        <i class="fas fa-plus text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">إضافة نوع عقاري جديد</h1>
                        <p class="text-gray-600 mt-1">قم بإنشاء نوع عقاري جديد للمنصة</p>
                    </div>
                </div>
                <a href="{{ route('property-types.index') }}" 
                   class="inline-flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 font-medium transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i>
                    رجوع
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <form action="{{ route('property-types.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        اسم النوع العقاري <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                           placeholder="مثال: شقة، فيلا، منزل، أرض، تجاري"
                           value="{{ old('name') }}">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                        الوصف
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                              placeholder="اكتب وصفاً مفصلاً لهذا النوع العقاري...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Features -->
                <div>
                    <label for="features" class="block text-sm font-semibold text-gray-700 mb-2">
                        المميزات
                    </label>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-4">
                            <input type="text" 
                                   name="features[]" 
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   placeholder="ميزة 1"
                                   value="{{ old('features.0') }}">
                            <button type="button" 
                                    onclick="addFeatureField()"
                                    class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div id="additional-features"></div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">أضف المميزات الرئيسية لهذا النوع العقاري</p>
                </div>

                <!-- Sort Order -->
                <div>
                    <label for="sort_order" class="block text-sm font-semibold text-gray-700 mb-2">
                        ترتيب العرض
                    </label>
                    <input type="number" 
                           id="sort_order" 
                           name="sort_order" 
                           min="0"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                           placeholder="0 (الأول)"
                           value="{{ old('sort_order', 0) }}">
                    @error('sort_order')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        الحالة
                    </label>
                    <div class="flex items-center space-x-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', 1) == 1 ? 'checked' : '' }}
                                   class="ml-2 text-blue-600 focus:ring-blue-500">
                            <span class="text-gray-700">نشط</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" 
                                   name="is_active" 
                                   value="0"
                                   {{ old('is_active', 1) == 0 ? 'checked' : '' }}
                                   class="ml-2 text-blue-600 focus:ring-blue-500">
                            <span class="text-gray-700">غير نشط</span>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <a href="{{ route('property-types.index') }}" 
                       class="inline-flex items-center px-6 py-3 text-gray-600 hover:text-gray-800 font-medium transition-colors">
                        <i class="fas fa-times ml-2"></i>
                        إلغاء
                    </a>
                    
                    <div class="space-x-3">
                        <button type="reset" 
                                class="inline-flex items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                            <i class="fas fa-undo ml-2"></i>
                            إعادة تعيين
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                            <i class="fas fa-save ml-2"></i>
                            حفظ النوع العقاري
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addFeatureField() {
    const container = document.getElementById('additional-features');
    const featureCount = container.children.length;
    
    const newField = document.createElement('div');
    newField.className = 'flex items-center space-x-4';
    newField.innerHTML = `
        <input type="text" 
               name="features[]" 
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
               placeholder="ميزة ${featureCount + 2}">
        <button type="button" 
                onclick="removeFeatureField(this)"
                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
            <i class="fas fa-minus"></i>
        </button>
    `;
    
    container.appendChild(newField);
}

function removeFeatureField(button) {
    button.parentElement.remove();
}

// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function(e) {
    const name = e.target.value;
    const slug = name.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-');
    
    // You can use this slug if needed
    console.log('Generated slug:', slug);
});
</script>

<style>
/* Custom animations */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.bg-white {
    animation: slideIn 0.5s ease-out;
}

/* Smooth transitions */
* {
    transition: all 0.2s ease;
}
</style>
@endsection
