@extends('admin.layouts.admin')

@section('title', 'إنشاء سؤال شائع')
@section('page-title', 'إنشاء سؤال شائع')

@section('content')
    <!-- Page Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 rounded-3xl shadow-2xl p-8 text-white relative overflow-hidden">
            <!-- Animated Background Pattern -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full -mr-48 -mt-48 animate-pulse"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-white rounded-full -ml-32 -mb-32 animate-pulse" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 left-1/2 w-48 h-48 bg-yellow-300 rounded-full -ml-24 -mt-24 animate-pulse" style="animation-delay: 2s;"></div>
            </div>
            
            <!-- Grid Pattern Overlay -->
            <div class="absolute inset-0 opacity-10">
                <div class="h-full w-full" style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>
            </div>
            
            <div class="relative z-10">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="bg-white bg-opacity-25 rounded-2xl p-4 ml-4 backdrop-blur-sm border border-white border-opacity-40 shadow-xl transform hover:scale-105 transition-transform duration-300">
                                <i class="fas fa-plus text-white text-3xl"></i>
                            </div>
                            <div>
                                <h1 class="text-5xl font-black text-white mb-2 bg-clip-text text-transparent bg-gradient-to-r from-white to-yellow-200">إنشاء سؤال شائع</h1>
                                <p class="text-white text-xl font-medium">إضافة سؤال جديد للأسئلة الشائعة</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-reverse space-x-4">
                        <a href="{{ route('admin.faqs.index') }}" class="inline-flex items-center px-8 py-4 bg-white text-indigo-600 rounded-2xl text-sm font-bold hover:bg-yellow-50 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-white transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-yellow-300/50">
                            <i class="fas fa-arrow-right ml-2"></i>
                            العودة للأسئلة
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Form -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-edit ml-3 text-indigo-600"></i>
                معلومات السؤال
            </h3>
        </div>
        
        <div class="p-8">
            <form action="{{ route('admin.faqs.store') }}" method="POST" class="space-y-8">
                @csrf
                
                <!-- Enhanced Basic Information -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200">
                    <h3 class="text-lg font-bold text-blue-900 mb-6 flex items-center">
                        <i class="fas fa-info-circle ml-3 text-blue-600"></i>
                        المعلومات الأساسية
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- Enhanced Question -->
                        <div>
                            <label for="question" class="block text-sm font-bold text-blue-700 mb-3">
                                السؤال <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="question" 
                                   name="question" 
                                   value="{{ old('question') }}"
                                   class="w-full px-6 py-4 border border-blue-300 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-500 focus:border-transparent text-lg font-medium"
                                   placeholder="أدخل السؤال الشائع"
                                   required>
                            @error('question')
                                <p class="mt-2 text-sm text-red-600 font-medium flex items-center">
                                    <i class="fas fa-exclamation-circle ml-2"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Enhanced Answer -->
                        <div>
                            <label for="answer" class="block text-sm font-bold text-blue-700 mb-3">
                                الإجابة <span class="text-red-500">*</span>
                            </label>
                            <textarea id="answer" 
                                      name="answer" 
                                      rows="8"
                                      class="w-full px-6 py-4 border border-blue-300 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-500 focus:border-transparent text-lg font-medium"
                                      placeholder="أدخل إجابة مفصلة"
                                      required>{{ old('answer') }}</textarea>
                            @error('answer')
                                <p class="mt-2 text-sm text-red-600 font-medium flex items-center">
                                    <i class="fas fa-exclamation-circle ml-2"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Enhanced Category -->
                        <div>
                            <label for="category" class="block text-sm font-bold text-blue-700 mb-3">
                                التصنيف <span class="text-red-500">*</span>
                            </label>
                            <div class="flex space-x-reverse space-x-4">
                                <select id="category" 
                                        name="category" 
                                        class="flex-1 px-6 py-4 border border-blue-300 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-500 focus:border-transparent text-lg font-medium"
                                        required>
                                    <option value="">اختر تصنيف</option>
                                    @foreach ($categories ?? [] as $category)
                                        <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="text" 
                                       id="new_category" 
                                       name="new_category" 
                                       value="{{ old('new_category') }}"
                                       class="flex-1 px-6 py-4 border border-blue-300 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-500 focus:border-transparent text-lg font-medium"
                                       placeholder="أو إنشاء تصنيف جديد">
                            </div>
                            <p class="mt-3 text-sm text-blue-600 font-medium flex items-center">
                                <i class="fas fa-info-circle ml-2"></i>
                                اختر تصنيف موجود أو أنشئ تصنيف جديد
                            </p>
                            @error('category')
                                <p class="mt-2 text-sm text-red-600 font-medium flex items-center">
                                    <i class="fas fa-exclamation-circle ml-2"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Enhanced Additional Settings -->
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-200">
                    <h3 class="text-lg font-bold text-purple-900 mb-6 flex items-center">
                        <i class="fas fa-cog ml-3 text-purple-600"></i>
                        إعدادات إضافية
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Enhanced Order -->
                        <div>
                            <label for="order" class="block text-sm font-bold text-purple-700 mb-3">
                                ترتيب العرض
                            </label>
                            <input type="number" 
                                   id="order" 
                                   name="order" 
                                   value="{{ old('order', 0) }}"
                                   class="w-full px-6 py-4 border border-purple-300 rounded-2xl focus:outline-none focus:ring-4 focus:ring-purple-500 focus:border-transparent text-lg font-medium"
                                   min="0"
                                   placeholder="0">
                            <p class="mt-3 text-sm text-purple-600 font-medium flex items-center">
                                <i class="fas fa-sort-numeric-up ml-2"></i>
                                الأرقام الأقل تظهر أولاً
                            </p>
                            @error('order')
                                <p class="mt-2 text-sm text-red-600 font-medium flex items-center">
                                    <i class="fas fa-exclamation-circle ml-2"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Enhanced Status Options -->
                        <div>
                            <label class="block text-sm font-bold text-purple-700 mb-3">خيارات الحالة</label>
                            <div class="space-y-4">
                                <div class="flex items-center bg-white p-4 rounded-xl border border-purple-200">
                                    <input type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1"
                                           {{ old('is_active', '1') ? 'checked' : '' }}
                                           class="h-5 w-5 text-purple-600 focus:ring-purple-500 border-purple-300 rounded">
                                    <label for="is_active" class="mr-3 text-sm font-medium text-gray-700">
                                        نشط
                                    </label>
                                </div>
                                <div class="flex items-center bg-white p-4 rounded-xl border border-purple-200">
                                    <input type="checkbox" 
                                           id="is_featured" 
                                           name="is_featured" 
                                           value="1"
                                           {{ old('is_featured') ? 'checked' : '' }}
                                           class="h-5 w-5 text-purple-600 focus:ring-purple-500 border-purple-300 rounded">
                                    <label for="is_featured" class="mr-3 text-sm font-medium text-gray-700">
                                        مميز
                                    </label>
                                </div>
                            </div>
                            <p class="mt-3 text-sm text-purple-600 font-medium flex items-center">
                                <i class="fas fa-eye ml-2"></i>
                                التحكم في الرؤية والإبراز
                            </p>
                            @error('is_active')
                                <p class="mt-2 text-sm text-red-600 font-medium flex items-center">
                                    <i class="fas fa-exclamation-circle ml-2"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                            @error('is_featured')
                                <p class="mt-2 text-sm text-red-600 font-medium flex items-center">
                                    <i class="fas fa-exclamation-circle ml-2"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Enhanced Submit Buttons -->
                <div class="flex justify-between items-center bg-gradient-to-r from-gray-50 to-gray-100 p-6 rounded-2xl border border-gray-200">
                    <a href="{{ route('admin.faqs.index') }}" 
                       class="inline-flex items-center px-8 py-4 bg-gray-500 text-white rounded-2xl text-sm font-bold hover:bg-gray-600 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-times ml-2"></i>
                        إلغاء
                    </a>
                    
                    <div class="flex space-x-reverse space-x-4">
                        <button type="submit" 
                                name="save_and_continue"
                                value="1"
                                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-2xl text-sm font-bold hover:from-gray-700 hover:to-gray-800 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-gray-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-save ml-2"></i>
                            حفظ ومتابعة
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl text-sm font-bold hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-indigo-500/50">
                            <i class="fas fa-plus ml-2"></i>
                            إنشاء السؤال
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<script>
// Handle category selection
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category');
    const newCategoryInput = document.getElementById('new_category');
    
    categorySelect.addEventListener('change', function() {
        if (this.value) {
            newCategoryInput.value = '';
            newCategoryInput.disabled = true;
        } else {
            newCategoryInput.disabled = false;
        }
    });
    
    newCategoryInput.addEventListener('input', function() {
        if (this.value) {
            categorySelect.value = '';
        }
    });
    
    // Initialize state
    if (categorySelect.value) {
        newCategoryInput.disabled = true;
    }
});

// Auto-save draft functionality
let autoSaveTimer;
const form = document.querySelector('form');
const originalFormData = new FormData(form);

function autoSave() {
    const currentFormData = new FormData(form);
    const hasChanges = Array.from(currentFormData.entries()).some(([key, value]) => {
        return originalFormData.get(key) !== value;
    });
    
    if (hasChanges) {
        // Save to localStorage
        const formData = Object.fromEntries(currentFormData.entries());
        localStorage.setItem('faq_draft', JSON.stringify(formData));
        
        // Show indicator
        const indicator = document.createElement('div');
        indicator.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-3 py-1 rounded-md text-sm';
        indicator.textContent = 'Draft saved';
        document.body.appendChild(indicator);
        
        setTimeout(() => {
            indicator.remove();
        }, 2000);
    }
}

// Auto-save every 30 seconds
form.addEventListener('input', function() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(autoSave, 30000);
});

// Load draft on page load
window.addEventListener('load', function() {
    const draft = localStorage.getItem('faq_draft');
    if (draft) {
        const formData = JSON.parse(draft);
        Object.entries(formData).forEach(([key, value]) => {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'checkbox') {
                    input.checked = value === '1';
                } else {
                    input.value = value;
                }
            }
        });
    }
});

// Clear draft on successful submission
form.addEventListener('submit', function() {
    localStorage.removeItem('faq_draft');
});
</script>
@endsection
