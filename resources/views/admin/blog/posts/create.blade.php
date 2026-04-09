@extends('layouts.dashboard')

@section('title', 'إنشاء مقال جديد')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-rose-100 via-pink-100 to-purple-100">
    <div class="p-8">
        <div class="max-w-6xl mx-auto">
            <!-- Animated Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-rose-600 via-pink-600 to-purple-600 rounded-3xl shadow-2xl p-8 mb-8 transform hover:scale-[1.02] transition-all duration-500">
                <div class="absolute inset-0 bg-gradient-to-r from-pink-500/30 to-purple-500/30 animate-pulse"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 space-x-reverse">
                            <a href="{{ route('admin.blog.posts.index') }}" class="group bg-white/20 backdrop-blur text-white px-4 py-3 rounded-xl hover:bg-white/30 transition-all duration-300 border border-white/20 hover:border-white/40">
                                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                <span class="font-medium">العودة</span>
                            </a>
                            <div>
                                <h1 class="text-3xl font-bold text-white tracking-tight">إنشاء مقال جديد</h1>
                                <p class="text-indigo-100 text-lg font-medium">اكتب وانشر مقالًا جديدًا</p>
                            </div>
                        </div>
                        <div class="flex space-x-3 space-x-reverse">
                            <button type="button" onclick="saveDraft()" class="group bg-white/20 backdrop-blur text-white px-6 py-3 rounded-xl hover:bg-white/30 transition-all duration-300 border border-white/20 hover:border-white/40">
                                <i class="fas fa-save ml-2 group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">حفظ مسودة</span>
                            </button>
                            <button type="button" onclick="previewPost()" class="group bg-white text-indigo-700 px-6 py-3 rounded-xl hover:bg-indigo-50 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
                                <i class="fas fa-eye ml-2 group-hover:scale-110 transition-transform"></i>
                                <span class="font-semibold">معاينة</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Decorative elements -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-rose-400/30 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-400/30 rounded-full blur-3xl"></div>
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
            <form action="{{ route('admin.blog.posts.store') }}" method="POST" enctype="multipart/form-data" id="blog-post-form">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Title Input -->
                        <div class="bg-gradient-to-r from-rose-50 to-pink-50 rounded-2xl shadow-xl p-8 border border-rose-200">
                            <label class="block text-gray-900 font-bold text-lg mb-4 flex items-center">
                                <div class="bg-gradient-to-r from-rose-500 to-pink-500 text-white p-2 rounded-lg ml-3">
                                    <i class="fas fa-heading text-sm"></i>
                                </div>
                                عنوان المقال
                            </label>
                            <input type="text" name="title" id="title" 
                                   class="w-full px-6 py-4 text-xl border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-300"
                                   placeholder="أدخل عنوان المقال..." value="{{ old('title') }}">
                            @error('title')
                                <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Content Editor -->
                        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-2xl shadow-xl p-8 border border-purple-200">
                            <label class="block text-gray-900 font-bold text-lg mb-4 flex items-center">
                                <div class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white p-2 rounded-lg ml-3">
                                    <i class="fas fa-pen text-sm"></i>
                                </div>
                                محتوى المقال
                            </label>
                            <textarea name="content" id="content" rows="20"
                                      class="w-full px-6 py-4 text-lg border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-300"
                                      placeholder="اكتب محتوى المقال هنا...">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Excerpt -->
                        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl shadow-xl p-8 border border-emerald-200">
                            <label class="block text-gray-900 font-bold text-lg mb-4 flex items-center">
                                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white p-2 rounded-lg ml-3">
                                    <i class="fas fa-align-left text-sm"></i>
                                </div>
                                ملخص المقال
                            </label>
                            <textarea name="excerpt" id="excerpt" rows="4"
                                      class="w-full px-6 py-4 text-lg border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all duration-300"
                                      placeholder="اكتب ملخص قصير للمقال...">{{ old('excerpt') }}</textarea>
                            <p class="text-sm text-gray-500 mt-2">سيظهر في نتائج البحث ومشاركات وسائل الإعلام</p>
                            @error('excerpt')
                                <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Publish Settings -->
                        <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl shadow-xl p-8 border border-amber-200">
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <div class="bg-gradient-to-r from-amber-500 to-orange-500 text-white p-2 rounded-lg ml-3">
                                    <i class="fas fa-cog text-sm"></i>
                                </div>
                                إعدادات النشر
                            </h3>
                            
                            <div class="space-y-4">
                                <!-- Status -->
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">حالة المقال</label>
                                    <select name="status" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-4 focus:ring-yellow-100 transition-all duration-300">
                                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>منشور</option>
                                        <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>مجدول</option>
                                    </select>
                                    @error('status')
                                        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Featured -->
                                <div class="flex items-center">
                                    <input type="checkbox" name="featured" id="featured" {{ old('featured') ? 'checked' : '' }} class="w-5 h-5 text-yellow-600 border-2 border-gray-300 rounded focus:ring-yellow-500">
                                    <label for="featured" class="mr-3 text-gray-700 font-semibold">مقال مميز</label>
                                </div>

                                <!-- Category -->
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">التصنيف</label>
                                    <select name="category_id" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-4 focus:ring-yellow-100 transition-all duration-300">
                                        <option value="">اختر تصنيف...</option>
                                        @if(isset($categories) && $categories->count() > 0)
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>لا توجد تصنيفات متاحة</option>
                                        @endif
                                    </select>
                                    <div class="mt-2 flex items-center justify-between">
                                        @error('category_id')
                                            <p class="text-red-600 text-sm">{{ $message }}</p>
                                        @else
                                            <p class="text-gray-500 text-sm">اختر تصنيفًا للمقال</p>
                                        @endif
                                        <a href="{{ route('admin.blog.categories.create') }}" class="text-amber-600 hover:text-amber-700 text-sm font-medium flex items-center">
                                            <i class="fas fa-plus-circle ml-1"></i>
                                            إضافة تصنيف جديد
                                        </a>
                                    </div>
                                </div>

                                <!-- Tags -->
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">الوسوم</label>
                                    <input type="text" name="tags" 
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-4 focus:ring-yellow-100 transition-all duration-300"
                                           placeholder="أضف وسوم مفصولة بفواصل..." value="{{ old('tags') }}">
                                    @error('tags')
                                        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- SEO Settings -->
                        <div class="bg-gradient-to-r from-fuchsia-50 to-pink-50 rounded-2xl shadow-xl p-8 border border-fuchsia-200">
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <div class="bg-gradient-to-r from-fuchsia-500 to-pink-500 text-white p-2 rounded-lg ml-3">
                                    <i class="fas fa-search text-sm"></i>
                                </div>
                                تحسين محركات البحث
                            </h3>
                            
                            <div class="space-y-4">
                                <!-- Meta Title -->
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">عنوان SEO</label>
                                    <input type="text" name="meta_title" 
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring-4 focus:ring-red-100 transition-all duration-300"
                                           placeholder="عنوان يظهر في نتائج البحث..." value="{{ old('meta_title') }}">
                                    @error('meta_title')
                                        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Meta Description -->
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">وصف SEO</label>
                                    <textarea name="meta_description" rows="3"
                                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring-4 focus:ring-red-100 transition-all duration-300"
                                              placeholder="وصف يظهر في نتائج البحث...">{{ old('meta_description') }}</textarea>
                                    @error('meta_description')
                                        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Featured Image -->
                        <div class="bg-gradient-to-r from-sky-50 to-blue-50 rounded-2xl shadow-xl p-8 border border-sky-200">
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <div class="bg-gradient-to-r from-sky-500 to-blue-500 text-white p-2 rounded-lg ml-3">
                                    <i class="fas fa-image text-sm"></i>
                                </div>
                                الصورة البارزة
                            </h3>
                            
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 transition-colors duration-300">
                                <div class="space-y-4">
                                    <div class="bg-blue-100 text-blue-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto">
                                        <i class="fas fa-cloud-upload-alt text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-gray-700 font-semibold">رفع صورة</p>
                                        <p class="text-sm text-gray-500">اسحب وأفلت الصورة هنا أو انقر للاختيار</p>
                                    </div>
                                    <input type="file" name="featured_image" accept="image/*" class="hidden">
                                    <button type="button" onclick="document.querySelector('input[name=featured_image]').click()" 
                                            class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white px-6 py-3 rounded-xl hover:from-blue-600 hover:to-indigo-600 transition-all duration-300">
                                        <i class="fas fa-folder-open ml-2"></i>
                                        اختر ملف
                                    </button>
                                </div>
                            </div>
                            @error('featured_image')
                                <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-center space-x-4 space-x-reverse mt-8">
                    <button type="button" onclick="saveDraft()" class="group bg-gradient-to-r from-slate-500 to-gray-500 text-white px-8 py-4 rounded-xl hover:from-slate-600 hover:to-gray-600 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
                        <i class="fas fa-save ml-2 group-hover:scale-110 transition-transform"></i>
                        <span class="font-semibold">حفظ مسودة</span>
                    </button>
                    <button type="submit" class="group bg-gradient-to-r from-rose-600 to-pink-600 text-white px-8 py-4 rounded-xl hover:from-rose-700 hover:to-pink-700 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
                        <i class="fas fa-paper-plane ml-2 group-hover:scale-110 transition-transform"></i>
                        <span class="font-semibold">نشر المقال</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function saveDraft() {
    // Set status to draft and submit
    const statusSelect = document.querySelector('select[name="status"]');
    if (statusSelect) {
        statusSelect.value = 'draft';
    }
    document.getElementById('blog-post-form').submit();
}

function previewPost() {
    // Preview functionality
    const title = document.querySelector('input[name="title"]').value;
    const content = document.querySelector('textarea[name="content"]').value;
    
    if (!title && !content) {
        alert('يرجى إضافة عنوان ومحتوى قبل المعاينة');
        return;
    }
    
    // Create preview window
    const previewWindow = window.open('', '_blank', 'width=800,height=600');
    previewWindow.document.write(`
        <html dir="rtl">
        <head>
            <title>معاينة: ${title}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; line-height: 1.6; }
                h1 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }
                .meta { color: #666; font-size: 14px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <h1>${title}</h1>
            <div>${content.replace(/\n/g, '<br>')}</div>
            <div class="meta">
                <p>تاريخ: ${new Date().toLocaleDateString('ar-SA')}</p>
                <p>الحالة: مسودة</p>
            </div>
        </body>
        </html>
    `);
}
</script>
@endsection
