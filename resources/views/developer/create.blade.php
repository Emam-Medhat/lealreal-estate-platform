@extends('layouts.dashboard')

@section('title', 'إضافة مطور جديد')

@section('page-title', 'إضافة مطور جديد')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-8 text-white shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">إضافة مطور جديد</h1>
                <p class="text-blue-100 text-lg">تسجيل مطور عقاري جديد في المنصة</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('developer.index') }}" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-lg p-8">
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <h4 class="text-red-800 font-semibold mb-2">خطأ في البيانات:</h4>
                <ul class="text-red-700 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <h4 class="text-red-800 font-semibold">خطأ:</h4>
                <p class="text-red-700 text-sm">{{ session('error') }}</p>
            </div>
        @endif

        <form action="{{ route('developer.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Basic Information -->
            <div class="border-b pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">المعلومات الأساسية</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            اسم الشركة <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="company_name" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="أدخل اسم الشركة">
                        @error('company_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            نوع المطور <span class="text-red-500">*</span>
                        </label>
                        <select name="developer_type" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">اختر النوع</option>
                            <option value="residential">سكني</option>
                            <option value="commercial">تجاري</option>
                            <option value="mixed">استخدام مختلط</option>
                            <option value="industrial">صناعي</option>
                        </select>
                        @error('developer_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            رقم الرخصة
                        </label>
                        <input type="text" name="license_number"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="أدخل رقم الرخصة">
                        @error('license_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            السجل التجاري
                        </label>
                        <input type="text" name="commercial_register"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="أدخل السجل التجاري">
                        @error('commercial_register')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="border-b pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات الاتصال</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            البريد الإلكتروني <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="contact@company.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            رقم الهاتف <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="phone" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="+966 50 123 4567">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            الموقع الإلكتروني
                        </label>
                        <input type="url" name="website"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="https://www.company.com">
                        @error('website')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="border-b pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات العنوان</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        العنوان <span class="text-red-500">*</span>
                    </label>
                    <textarea name="address" rows="3" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="أدخل العنوان الكامل"></textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Additional Information -->
            <div class="border-b pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات إضافية</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            سنة التأسيس
                        </label>
                        <input type="number" name="established_year" min="1900" max="{{ date('Y') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="2020">
                        @error('established_year')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            الوصف
                        </label>
                        <textarea name="description" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="وصف المطور والخدمات المقدمة"></textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">الإعدادات</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_verified" value="1"
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label class="mr-2 text-sm font-medium text-gray-700">
                            موثق
                        </label>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            الحالة
                        </label>
                        <select name="status"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="active">نشط</option>
                            <option value="inactive">غير نشط</option>
                            <option value="suspended">معلق</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-between pt-6">
                <a href="{{ route('developer.index') }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    إلغاء
                </a>
                
                <div class="space-x-4">
                    <button type="button" onclick="window.location.reload()"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        إعادة تعيين
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save ml-2"></i>
                        حفظ المطور
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
