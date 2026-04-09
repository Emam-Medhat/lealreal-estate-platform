@extends('layouts.dashboard')

@section('title', 'إنشاء تصنيف جديد')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-100 via-orange-100 to-yellow-100">
    <div class="p-8">
        <div class="max-w-4xl mx-auto">
            <!-- Animated Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-amber-600 via-orange-600 to-yellow-600 rounded-3xl shadow-2xl p-8 mb-8 transform hover:scale-[1.02] transition-all duration-500">
                <div class="absolute inset-0 bg-gradient-to-r from-orange-500/30 to-yellow-500/30 animate-pulse"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 space-x-reverse">
                            <a href="{{ route('admin.blog.categories.index') }}" class="group bg-white/20 backdrop-blur text-white px-4 py-3 rounded-xl hover:bg-white/30 transition-all duration-300 border border-white/20 hover:border-white/40">
                                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                <span class="font-medium">العودة</span>
                            </a>
                            <div>
                                <h1 class="text-3xl font-bold text-white tracking-tight">إنشاء تصنيف جديد</h1>
                                <p class="text-amber-100 text-lg font-medium">أضف تصنيفًا جديدًا للمقالات</p>
                            </div>
                        </div>
                        <div class="flex space-x-3 space-x-reverse">
                            <button type="button" onclick="window.history.back()" class="group bg-white/20 backdrop-blur text-white px-6 py-3 rounded-xl hover:bg-white/30 transition-all duration-300 border border-white/20 hover:border-white/40">
                                <i class="fas fa-times ml-2 group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">إلغاء</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Decorative elements -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-amber-400/30 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-yellow-400/30 rounded-full blur-3xl"></div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-r-4 border-green-500 p-6 mb-8 rounded-xl shadow-lg">
                    <div class="flex items-center">
                        <div class="bg-green-100 text-green-600 p-3 rounded-xl ml-4">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-green-800 font-bold text-lg">✅ تم بنجاح:</p>
                            <p class="text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-gradient-to-r from-red-50 to-rose-50 border-r-4 border-red-500 p-6 mb-8 rounded-xl shadow-lg">
                    <div class="flex items-center">
                        <div class="bg-red-100 text-red-600 p-3 rounded-xl ml-4">
                            <i class="fas fa-exclamation-circle text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-red-800 font-bold text-lg">❌ خطأ:</p>
                            <p class="text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-gradient-to-r from-orange-50 to-amber-50 border-r-4 border-orange-500 p-6 mb-8 rounded-xl shadow-lg">
                    <div class="flex items-start">
                        <div class="bg-orange-100 text-orange-600 p-3 rounded-xl ml-4">
                            <i class="fas fa-exclamation-triangle text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-orange-800 font-bold text-lg mb-3">⚠️ يرجى تصحيح الأخطاء التالية:</p>
                            <ul class="list-disc list-inside space-y-2 text-orange-700">
                                @foreach($errors->all() as $error)
                                    <li class="bg-orange-100/50 p-2 rounded-lg">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form Content -->
            <form action="{{ route('admin.blog.categories.store') }}" method="POST" id="category-form">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Main Information -->
                    <div class="space-y-6">
                        <!-- Name -->
                        <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl shadow-xl p-8 border border-amber-200">
                            <label class="block text-gray-900 font-bold text-lg mb-4 flex items-center">
                                <div class="bg-gradient-to-r from-amber-500 to-orange-500 text-white p-2 rounded-lg ml-3">
                                    <i class="fas fa-tag text-sm"></i>
                                </div>
                                اسم التصنيف
                            </label>
                            <input type="text" name="name" id="name" 
                                   class="w-full px-6 py-4 text-xl border-2 border-gray-200 rounded-xl focus:border-amber-500 focus:ring-4 focus:ring-amber-100 transition-all duration-300"
                                   placeholder="أدخل اسم التصنيف..." value="{{ old('name') }}">
                            @error('name')
                                <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div class="bg-gradient-to-r from-orange-50 to-yellow-50 rounded-2xl shadow-xl p-8 border border-orange-200">
                            <label class="block text-gray-900 font-bold text-lg mb-4 flex items-center">
                                <div class="bg-gradient-to-r from-orange-500 to-yellow-500 text-white p-2 rounded-lg ml-3">
                                    <i class="fas fa-link text-sm"></i>
                                </div>
                                الرابط المختصر (Slug)
                            </label>
                            <input type="text" name="slug" id="slug" 
                                   class="w-full px-6 py-4 text-xl border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all duration-300"
                                   placeholder="سيتم إنشاؤه تلقائياً..." value="{{ old('slug') }}">
                            <p class="text-sm text-gray-500 mt-2">سيتم استخدامه في رابط التصنيف</p>
                            @error('slug')
                                <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-2xl shadow-xl p-8 border border-yellow-200">
                            <label class="block text-gray-900 font-bold text-lg mb-4 flex items-center">
                                <div class="bg-gradient-to-r from-yellow-500 to-amber-500 text-white p-2 rounded-lg ml-3">
                                    <i class="fas fa-align-left text-sm"></i>
                                </div>
                                الوصف
                            </label>
                            <textarea name="description" id="description" rows="4"
                                      class="w-full px-6 py-4 text-lg border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-4 focus:ring-yellow-100 transition-all duration-300"
                                      placeholder="اكتب وصفًا للتصنيف...">{{ old('description') }}</textarea>
                            <p class="text-sm text-gray-500 mt-2">وصف قصير للتصنيف يظهر في صفحة التصنيف</p>
                            @error('description')
                                <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Additional Settings -->
                    <div class="space-y-6">
                        <!-- Parent Category -->
                        <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-2xl shadow-xl p-8 border border-orange-200">
                            <label class="block text-gray-900 font-bold text-lg mb-4 flex items-center">
                                <div class="bg-gradient-to-r from-orange-500 to-amber-500 text-white p-2 rounded-lg ml-3">
                                    <i class="fas fa-sitemap text-sm"></i>
                                </div>
                                التصنيف الأب
                            </label>
                            <select name="parent_id" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all duration-300">
                                <option value="">لا يوجد (تصنيف رئيسي)</option>
                                @if(isset($categories) && $categories->count() > 0)
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('parent_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <p class="text-sm text-gray-500 mt-2">اختر تصنيفًا أبًا إذا كان هذا تصنيفًا فرعيًا</p>
                            @error('parent_id')
                                <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sort Order -->
                        <div class="bg-gradient-to-r from-amber-50 to-yellow-50 rounded-2xl shadow-xl p-8 border border-amber-200">
                            <label class="block text-gray-900 font-bold text-lg mb-4 flex items-center">
                                <div class="bg-gradient-to-r from-amber-500 to-yellow-500 text-white p-2 rounded-lg ml-3">
                                    <i class="fas fa-sort-numeric-down text-sm"></i>
                                </div>
                                ترتيب الفرز
                            </label>
                            <input type="number" name="sort_order" id="sort_order" 
                                   class="w-full px-6 py-4 text-xl border-2 border-gray-200 rounded-xl focus:border-amber-500 focus:ring-4 focus:ring-amber-100 transition-all duration-300"
                                   placeholder="0" value="{{ old('sort_order', 0) }}" min="0">
                            <p class="text-sm text-gray-500 mt-2">رقم يحدد ترتيب ظهور التصنيف</p>
                            @error('sort_order')
                                <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Color -->
                        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-2xl shadow-xl p-8 border border-yellow-200">
                            <label class="block text-gray-900 font-bold text-lg mb-4 flex items-center">
                                <div class="bg-gradient-to-r from-yellow-500 to-orange-500 text-white p-2 rounded-lg ml-3">
                                    <i class="fas fa-palette text-sm"></i>
                                </div>
                                اللون
                            </label>
                            <div class="flex items-center space-x-4 space-x-reverse">
                                <input type="color" name="color" id="color" 
                                       class="w-20 h-20 border-2 border-gray-200 rounded-xl cursor-pointer"
                                       value="{{ old('color', '#f59e0b') }}">
                                <input type="text" name="color_text" id="color_text" 
                                       class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-4 focus:ring-yellow-100 transition-all duration-300"
                                       placeholder="#f59e0b" value="{{ old('color', '#f59e0b') }}">
                            </div>
                            <p class="text-sm text-gray-500 mt-2">اختر لونًا للتصنيف</p>
                            @error('color')
                                <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Icon -->
                        <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-2xl shadow-xl p-8 border border-orange-200">
                            <label class="block text-gray-900 font-bold text-lg mb-4 flex items-center">
                                <div class="bg-gradient-to-r from-orange-500 to-amber-500 text-white p-2 rounded-lg ml-3">
                                    <i class="fas fa-icons text-sm"></i>
                                </div>
                                الأيقونة
                            </label>
                            <input type="text" name="icon" id="icon" 
                                   class="w-full px-6 py-4 text-xl border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all duration-300"
                                   placeholder="fas fa-folder" value="{{ old('icon') }}">
                            <p class="text-sm text-gray-500 mt-2">اسم أيقونة Font Awesome (مثال: fas fa-folder)</p>
                            @error('icon')
                                <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div class="bg-gradient-to-r from-amber-50 to-yellow-50 rounded-2xl shadow-xl p-8 border border-amber-200">
                            <label class="block text-gray-900 font-bold text-lg mb-4 flex items-center">
                                <div class="bg-gradient-to-r from-amber-500 to-yellow-500 text-white p-2 rounded-lg ml-3">
                                    <i class="fas fa-toggle-on text-sm"></i>
                                </div>
                                الحالة
                            </label>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" {{ old('is_active', true) ? 'checked' : '' }} class="w-6 h-6 text-amber-600 border-2 border-gray-300 rounded focus:ring-amber-500">
                                <label for="is_active" class="mr-3 text-gray-700 font-semibold">نشط</label>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">تفعيل التصنيف للاستخدام في المقالات</p>
                            @error('is_active')
                                <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-center space-x-4 space-x-reverse mt-8">
                    <button type="button" onclick="window.history.back()" class="group bg-gradient-to-r from-gray-500 to-slate-500 text-white px-8 py-4 rounded-xl hover:from-gray-600 hover:to-slate-600 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
                        <i class="fas fa-times ml-2 group-hover:scale-110 transition-transform"></i>
                        <span class="font-semibold">إلغاء</span>
                    </button>
                    <button type="submit" class="group bg-gradient-to-r from-amber-600 to-orange-600 text-white px-8 py-4 rounded-xl hover:from-amber-700 hover:to-orange-700 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
                        <i class="fas fa-save ml-2 group-hover:scale-110 transition-transform"></i>
                        <span class="font-semibold">حفظ التصنيف</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slug = name.toLowerCase()
        .replace(/[^a-z0-9\u0621-\u064A\s-]/g, '') // Keep Arabic characters, English letters, numbers, spaces, and hyphens
        .replace(/\s+/g, '-') // Replace spaces with hyphens
        .replace(/-+/g, '-') // Replace multiple hyphens with single hyphen
        .replace(/^-|-$/g, ''); // Remove leading and trailing hyphens
    
    document.getElementById('slug').value = slug;
});

// Sync color inputs
document.getElementById('color').addEventListener('input', function() {
    document.getElementById('color_text').value = this.value;
});

document.getElementById('color_text').addEventListener('input', function() {
    if (/^#[0-9A-F]{6}$/i.test(this.value)) {
        document.getElementById('color').value = this.value;
    }
});
</script>
@endsection
