@extends('admin.layouts.admin')

@section('title', 'إضافة مقدم خدمة جديد')

@section('page-title', 'إضافة مقدم خدمة جديد')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-teal-600 via-teal-700 to-cyan-800 rounded-2xl shadow-xl p-8 text-white">
        <div class="flex justify-between items-center">
            <div>
                <div class="flex items-center mb-3">
                    <div class="bg-white bg-opacity-20 rounded-xl p-3 ml-4">
                        <i class="fas fa-user-plus text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">إضافة مقدم خدمة جديد</h1>
                        <p class="text-teal-100 mt-1">تسجيل مقدم خدمة جديد في النظام</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4 mt-4">
                    <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                        <span class="text-sm text-teal-100">النظام:</span>
                        <span class="text-sm font-semibold text-white">إدارة مقدمي الخدمة</span>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                        <span class="text-sm text-teal-100">المستخدم:</span>
                        <span class="text-sm font-semibold text-white">{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('warranties.providers.index') }}" class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 backdrop-blur-sm border border-white border-opacity-30 rounded-xl text-sm font-medium text-white hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة لمقدمي الخدمة
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-teal-100 text-sm font-medium mb-2">إجمالي مقدمي الخدمة</p>
                <p class="text-3xl font-bold text-white">{{ App\Models\ServiceProvider::count() }}</p>
                <div class="mt-2 flex items-center text-xs text-teal-100">
                    <i class="fas fa-users ml-1"></i>
                    <span>جميع المقدمين</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-users text-white text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium mb-2">مقدمون نشطون</p>
                <p class="text-3xl font-bold text-white">{{ App\Models\ServiceProvider::where('status', 'active')->count() }}</p>
                <div class="mt-2 flex items-center text-xs text-green-100">
                    <i class="fas fa-check-circle ml-1"></i>
                    <span>متاحون للخدمة</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-check-circle text-white text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium mb-2">الضمانات المرتبطة</p>
                <p class="text-3xl font-bold text-white">{{ App\Models\Warranty::whereNotNull('service_provider_id')->count() }}</p>
                <div class="mt-2 flex items-center text-xs text-blue-100">
                    <i class="fas fa-shield-alt ml-1"></i>
                    <span>ضمانات نشطة</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-shield-alt text-white text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm font-medium mb-2">معدل النمو</p>
                <p class="text-3xl font-bold text-white">+12%</p>
                <div class="mt-2 flex items-center text-xs text-purple-100">
                    <i class="fas fa-chart-line ml-1"></i>
                    <span>هذا الشهر</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-chart-line text-white text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Provider Form -->
<div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <div class="bg-teal-500 rounded-lg p-2 ml-3">
                <i class="fas fa-user-plus text-white"></i>
            </div>
            نموذج إضافة مقدم الخدمة
        </h3>
    </div>
    
    <form method="POST" action="{{ route('warranties.providers.store') }}" class="p-6">
        @csrf
        
        <!-- Basic Information Section -->
        <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-3 ml-3 shadow-lg">
                    <i class="fas fa-info-circle text-white text-lg"></i>
                </div>
                <div>
                    <span class="text-gray-900">المعلومات الأساسية</span>
                    <p class="text-sm text-gray-500 font-normal">بيانات مقدم الخدمة الأساسية</p>
                </div>
            </h4>
            
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-user text-blue-500 ml-2 text-xs"></i>
                            اسم مقدم الخدمة *
                        </label>
                        <div class="relative">
                            <input type="text" name="name" required
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white shadow-sm transition-all duration-200 hover:shadow-md"
                                placeholder="أدخل اسم مقدم الخدمة">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-blue-400"></i>
                            </div>
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 flex items-center animate-pulse">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-building text-green-500 ml-2 text-xs"></i>
                            اسم الشركة
                        </label>
                        <div class="relative">
                            <input type="text" name="company_name"
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white shadow-sm transition-all duration-200 hover:shadow-md"
                                placeholder="أدخل اسم الشركة">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-building text-green-400"></i>
                            </div>
                        </div>
                        @error('company_name')
                            <p class="mt-2 text-sm text-red-600 flex items-center animate-pulse">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-user-tie text-purple-500 ml-2 text-xs"></i>
                            الشخص المسؤول
                        </label>
                        <div class="relative">
                            <input type="text" name="contact_person"
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white shadow-sm transition-all duration-200 hover:shadow-md"
                                placeholder="اسم الشخص المسؤول">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user-tie text-purple-400"></i>
                            </div>
                        </div>
                        @error('contact_person')
                            <p class="mt-2 text-sm text-red-600 flex items-center animate-pulse">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-flag text-red-500 ml-2 text-xs"></i>
                            الحالة *
                        </label>
                        <div class="relative">
                            <select name="status" required
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none bg-white shadow-sm transition-all duration-200 hover:shadow-md">
                                <option value="active">نشط</option>
                                <option value="inactive">غير نشط</option>
                            </select>
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-flag text-red-400"></i>
                            </div>
                        </div>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600 flex items-center animate-pulse">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information Section -->
        <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-3 ml-3 shadow-lg">
                    <i class="fas fa-address-book text-white text-lg"></i>
                </div>
                <div>
                    <span class="text-gray-900">معلومات التواصل</span>
                    <p class="text-sm text-gray-500 font-normal">بيانات الاتصال بمقدم الخدمة</p>
                </div>
            </h4>
            
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-envelope text-blue-500 ml-2 text-xs"></i>
                            البريد الإلكتروني *
                        </label>
                        <div class="relative">
                            <input type="email" name="email" required
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white shadow-sm transition-all duration-200 hover:shadow-md"
                                placeholder="example@email.com">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-blue-400"></i>
                            </div>
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600 flex items-center animate-pulse">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-phone text-green-500 ml-2 text-xs"></i>
                            رقم الهاتف *
                        </label>
                        <div class="relative">
                            <input type="tel" name="phone" required
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white shadow-sm transition-all duration-200 hover:shadow-md"
                                placeholder="05xxxxxxxx">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-phone text-green-400"></i>
                            </div>
                        </div>
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600 flex items-center animate-pulse">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-map-marker-alt text-red-500 ml-2 text-xs"></i>
                        العنوان
                    </label>
                    <div class="relative">
                        <input type="text" name="address"
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white shadow-sm transition-all duration-200 hover:shadow-md"
                            placeholder="أدخل العنوان الكامل">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-map-marker-alt text-red-400"></i>
                        </div>
                    </div>
                    @error('address')
                        <p class="mt-2 text-sm text-red-600 flex items-center animate-pulse">
                            <i class="fas fa-exclamation-circle ml-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Additional Information Section -->
        <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-3 ml-3 shadow-lg">
                    <i class="fas fa-info text-white text-lg"></i>
                </div>
                <div>
                    <span class="text-gray-900">معلومات إضافية</span>
                    <p class="text-sm text-gray-500 font-normal">تفاصيل إضافية عن مقدم الخدمة</p>
                </div>
            </h4>
            
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-globe text-blue-500 ml-2 text-xs"></i>
                            الموقع الإلكتروني
                        </label>
                        <div class="relative">
                            <input type="url" name="website"
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white shadow-sm transition-all duration-200 hover:shadow-md"
                                placeholder="https://example.com">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-globe text-blue-400"></i>
                            </div>
                        </div>
                        @error('website')
                            <p class="mt-2 text-sm text-red-600 flex items-center animate-pulse">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-id-card text-green-500 ml-2 text-xs"></i>
                            رقم الترخيص
                        </label>
                        <div class="relative">
                            <input type="text" name="license_number"
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white shadow-sm transition-all duration-200 hover:shadow-md"
                                placeholder="رقم الترخيص التجاري">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-id-card text-green-400"></i>
                            </div>
                        </div>
                        @error('license_number')
                            <p class="mt-2 text-sm text-red-600 flex items-center animate-pulse">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-align-left text-indigo-500 ml-2 text-xs"></i>
                        الوصف
                    </label>
                    <div class="relative">
                        <textarea name="description" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 resize-none bg-white shadow-sm transition-all duration-200 hover:shadow-md"
                            placeholder="أدخل وصفاً لمقدم الخدمة والخدمات المقدمة"></textarea>
                    </div>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600 flex items-center animate-pulse">
                            <i class="fas fa-exclamation-circle ml-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-cogs text-purple-500 ml-2 text-xs"></i>
                        الخدمات المقدمة
                    </label>
                    <div class="relative">
                        <textarea name="services" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 resize-none bg-white shadow-sm transition-all duration-200 hover:shadow-md"
                            placeholder="أدخل قائمة بالخدمات المقدمة"></textarea>
                    </div>
                    @error('services')
                        <p class="mt-2 text-sm text-red-600 flex items-center animate-pulse">
                            <i class="fas fa-exclamation-circle ml-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-reverse space-x-3 pt-6 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 -mx-6 px-6 -mb-6 pb-6 rounded-b-2xl">
            <a href="{{ route('warranties.providers.index') }}" class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
                <i class="fas fa-times ml-2"></i>
                إلغاء
            </a>
            <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-teal-600 to-teal-700 border border-transparent rounded-xl text-sm font-medium text-white hover:from-teal-700 hover:to-teal-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                <i class="fas fa-save ml-2"></i>
                حفظ مقدم الخدمة
            </button>
        </div>
    </form>
</div>

@endsection
