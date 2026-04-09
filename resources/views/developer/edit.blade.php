@extends('layouts.dashboard')

@section('title', 'تعديل المطور')

@section('page-title', 'تعديل المطور')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-8 text-white shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">تعديل المطور</h1>
                <p class="text-blue-100 text-lg">تعديل بيانات المطور: {{ $developer->company_name }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('developer.show', $developer->id) }}" class="bg-white/20 text-white px-6 py-3 rounded-xl hover:bg-white/30 transition-colors flex items-center font-semibold">
                    <i class="fas fa-eye ml-2"></i>
                    عرض
                </a>
                <a href="{{ route('developer.index') }}" class="bg-white/20 text-white px-6 py-3 rounded-xl hover:bg-white/30 transition-colors flex items-center font-semibold">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة
                </a>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <form method="POST" action="{{ route('developer.update', $developer->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">المعلومات الأساسية</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم الشركة *</label>
                        <input type="text" name="company_name" value="{{ $developer->company_name }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الاسم العربي</label>
                        <input type="text" name="company_name_ar" value="{{ $developer->company_name_ar }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">رقم الترخيص *</label>
                        <input type="text" name="license_number" value="{{ $developer->license_number }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">السجل التجاري *</label>
                        <input type="text" name="commercial_register" value="{{ $developer->commercial_register }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الرقم الضريبي</label>
                        <input type="text" name="tax_number" value="{{ $developer->tax_number }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">نوع المطور *</label>
                        <select name="developer_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="residential" {{ $developer->developer_type == 'residential' ? 'selected' : '' }}>سكني</option>
                            <option value="commercial" {{ $developer->developer_type == 'commercial' ? 'selected' : '' }}>تجاري</option>
                            <option value="mixed" {{ $developer->developer_type == 'mixed' ? 'selected' : '' }}>مختلط</option>
                            <option value="industrial" {{ $developer->developer_type == 'industrial' ? 'selected' : '' }}>صناعي</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الحالة *</label>
                        <select name="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="pending" {{ $developer->status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="active" {{ $developer->status == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="suspended" {{ $developer->status == 'suspended' ? 'selected' : '' }}>معلق</option>
                            <option value="inactive" {{ $developer->status == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">سنة التأسيس</label>
                        <input type="number" name="established_year" value="{{ $developer->established_year }}" min="1900" max="{{ date('Y') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الموقع الإلكتروني</label>
                        <input type="url" name="website" value="{{ $developer->website }}"
                            placeholder="https://example.com"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات الاتصال</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني *</label>
                        <input type="email" name="email" value="{{ $developer->email }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الهاتف *</label>
                        <input type="tel" name="phone" value="{{ $developer->phone }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Project Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات المشاريع</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">إجمالي المشاريع</label>
                        <input type="number" name="total_projects" value="{{ $developer->total_projects }}" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">إجمالي الاستثمار</label>
                        <input type="number" name="total_investment" value="{{ $developer->total_investment }}" min="0" step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">عدد التقييمات</label>
                        <input type="number" name="review_count" value="{{ $developer->review_count }}" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">الوصف</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الوصف بالإنجليزية</label>
                        <textarea name="description" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $developer->description }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الوصف بالعربية</label>
                        <textarea name="description_ar" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $developer->description_ar }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">العنوان</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الشارع</label>
                        <input type="text" name="address[street]" value="{{ $developer->address['street'] ?? '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">المدينة</label>
                        <input type="text" name="address[city]" value="{{ $developer->address['city'] ?? '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الولاية</label>
                        <input type="text" name="address[state]" value="{{ $developer->address['state'] ?? '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الرمز البريدي</label>
                        <input type="text" name="address[postal_code]" value="{{ $developer->address['postal_code'] ?? '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Verification Settings -->
            <div class="pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">إعدادات التحقق</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_verified" {{ $developer->is_verified ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                        <label class="mr-2 text-sm font-medium text-gray-700">مطور موثق</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_featured" {{ $developer->is_featured ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                        <label class="mr-2 text-sm font-medium text-gray-700">مطور مميز</label>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('developer.show', $developer->id) }}" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                    إلغاء
                </a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <i class="fas fa-save ml-2"></i>
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
