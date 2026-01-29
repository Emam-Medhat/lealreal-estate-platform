@extends('admin.layouts.admin')

@section('title', 'إنشاء SEO Meta')
@section('page-title', 'إنشاء بيانات SEO جديدة')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">إنشاء بيانات SEO جديدة</h1>
                    <p class="text-gray-600 mt-1">إضافة معلومات SEO لصفحة جديدة</p>
                </div>
                <a href="{{ route('admin.seo.index') }}" 
                    class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form action="{{ route('admin.seo.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Page Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">معلومات الصفحة</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                نوع الصفحة <span class="text-red-500">*</span>
                            </label>
                            <select name="page_type" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">اختر نوع الصفحة</option>
                                <option value="property">عقار</option>
                                <option value="company">شركة</option>
                                <option value="agent">وكيل</option>
                                <option value="blog">مقال</option>
                                <option value="page">صفحة عادية</option>
                                <option value="category">تصنيف</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                رابط الصفحة <span class="text-red-500">*</span>
                            </label>
                            <input type="url" name="url" required
                                placeholder="https://example.com/page"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- SEO Meta Tags -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">وسوم SEO الأساسية</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                عنوان الصفحة (Meta Title) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="meta_title" required maxlength="60"
                                placeholder="عنوان جذاب للصفحة (60 حرف كحد أقصى)"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">
                                <span id="titleCount">0</span>/60 حرف
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                وصف الصفحة (Meta Description) <span class="text-red-500">*</span>
                            </label>
                            <textarea name="meta_description" required maxlength="160" rows="3"
                                placeholder="وصف جذاب للصفحة (160 حرف كحد أقصى)"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                            <p class="text-xs text-gray-500 mt-1">
                                <span id="descCount">0</span>/160 حرف
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                كلمات مفتاحية (Keywords)
                            </label>
                            <input type="text" name="meta_keywords"
                                placeholder="كلمة1, كلمة2, كلمة3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">افصل بين الكلمات بفاصلة</p>
                        </div>
                    </div>
                </div>

                <!-- Open Graph Tags -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">وسوم Open Graph (للسوشيال ميديا)</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                عنوان Open Graph
                            </label>
                            <input type="text" name="og_title" maxlength="100"
                                placeholder="عنوان يظهر عند المشاركة"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                وصف Open Graph
                            </label>
                            <textarea name="og_description" rows="2"
                                placeholder="وصف يظهر عند المشاركة"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                صورة Open Graph
                            </label>
                            <input type="url" name="og_image"
                                placeholder="https://example.com/image.jpg"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">رابط الصورة (1200x630 بكسل مثالي)</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center pt-6">
                    <a href="{{ route('admin.seo.index') }}" 
                        class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-times ml-2"></i>
                        إلغاء
                    </a>
                    <div class="space-x-3">
                        <button type="submit"
                            class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all">
                            <i class="fas fa-save ml-2"></i>
                            حفظ البيانات
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Character counters
document.querySelector('input[name="meta_title"]').addEventListener('input', function() {
    document.getElementById('titleCount').textContent = this.value.length;
});

document.querySelector('textarea[name="meta_description"]').addEventListener('input', function() {
    document.getElementById('descCount').textContent = this.value.length;
});
</script>
@endpush
