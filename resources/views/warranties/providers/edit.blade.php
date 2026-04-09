@extends('admin.layouts.admin')

@section('title', 'تعديل مقدم الخدمة')

@section('page-title', 'تعديل مقدم الخدمة')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-amber-600 via-amber-700 to-orange-800 rounded-2xl shadow-xl p-8 text-white">
        <div class="flex justify-between items-center">
            <div>
                <div class="flex items-center mb-3">
                    <div class="bg-white bg-opacity-20 rounded-xl p-3 ml-4">
                        <i class="fas fa-user-edit text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">تعديل مقدم الخدمة</h1>
                        <p class="text-amber-100 mt-1">{{ $provider->name }} @if($provider->company_name) - {{ $provider->company_name }} @endif</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4 mt-4">
                    <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                        <span class="text-sm text-amber-100">الحالة الحالية:</span>
                        <span class="text-sm font-semibold text-white">
                            @if($provider->status == 'active') نشط
                            @else غير نشط @endif
                        </span>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                        <span class="text-sm text-amber-100">الضمانات:</span>
                        <span class="text-sm font-semibold text-white">{{ $provider->warranties->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('warranties.providers.show', $provider) }}" class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 backdrop-blur-sm border border-white border-opacity-30 rounded-xl text-sm font-medium text-white hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للتفاصيل
                </a>
                <a href="{{ route('warranties.providers.index') }}" class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 backdrop-blur-sm border border-white border-opacity-30 rounded-xl text-sm font-medium text-white hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300">
                    <i class="fas fa-list ml-2"></i>
                    قائمة مقدمي الخدمة
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Provider Statistics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-teal-100 text-sm font-medium mb-2">الضمانات المرتبطة</p>
                <p class="text-2xl font-bold text-white">{{ $provider->warranties->count() }}</p>
                <div class="mt-2 flex items-center text-xs text-teal-100">
                    <i class="fas fa-shield-alt ml-1"></i>
                    <span>ضمان نشط</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-shield-alt text-white text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium mb-2">الحالة الحالية</p>
                <p class="text-2xl font-bold text-white">
                    @if($provider->status == 'active') نشط
                    @else غير نشط @endif
                </p>
                <div class="mt-2 flex items-center text-xs text-green-100">
                    <i class="fas fa-flag ml-1"></i>
                    <span>حالية</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-flag text-white text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium mb-2">مجموع التغطية</p>
                <p class="text-2xl font-bold text-white">{{ number_format($provider->warranties->sum('coverage_amount'), 2) }}</p>
                <div class="mt-2 flex items-center text-xs text-blue-100">
                    <i class="fas fa-money-bill-wave ml-1"></i>
                    <span>ريال سعودي</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-money-bill-wave text-white text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Edit Form -->
<div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <div class="bg-amber-500 rounded-lg p-2 ml-3">
                <i class="fas fa-user-edit text-white"></i>
            </div>
            تعديل بيانات مقدم الخدمة
        </h3>
    </div>
    
    <form method="POST" action="{{ route('warranties.providers.update', $provider) }}" class="p-6">
        @csrf
        @method('PUT')
        
        <!-- Basic Information Section -->
        <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-3 ml-3 shadow-lg">
                    <i class="fas fa-info-circle text-white text-lg"></i>
                </div>
                <div>
                    <span class="text-gray-900">المعلومات الأساسية</span>
                    <p class="text-sm text-gray-500 font-normal">تحديث بيانات مقدم الخدمة الأساسية</p>
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
                                value="{{ $provider->name }}"
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white shadow-sm transition-all duration-200 hover:shadow-md"
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
                                value="{{ $provider->company_name ?? '' }}"
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white shadow-sm transition-all duration-200 hover:shadow-md"
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
                                value="{{ $provider->contact_person ?? '' }}"
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white shadow-sm transition-all duration-200 hover:shadow-md"
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
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 appearance-none bg-white shadow-sm transition-all duration-200 hover:shadow-md">
                                <option value="active" {{ $provider->status == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ $provider->status == 'inactive' ? 'selected' : '' }}>غير نشط</option>
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
                    <p class="text-sm text-gray-500 font-normal">تحديث بيانات الاتصال بمقدم الخدمة</p>
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
                                value="{{ $provider->email }}"
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white shadow-sm transition-all duration-200 hover:shadow-md"
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
                                value="{{ $provider->phone }}"
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white shadow-sm transition-all duration-200 hover:shadow-md"
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
                            value="{{ $provider->address ?? '' }}"
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white shadow-sm transition-all duration-200 hover:shadow-md"
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
                    <p class="text-sm text-gray-500 font-normal">تحديث التفاصيل الإضافية لمقدم الخدمة</p>
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
                                value="{{ $provider->website ?? '' }}"
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white shadow-sm transition-all duration-200 hover:shadow-md"
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
                                value="{{ $provider->license_number ?? '' }}"
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white shadow-sm transition-all duration-200 hover:shadow-md"
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
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 resize-none bg-white shadow-sm transition-all duration-200 hover:shadow-md"
                            placeholder="أدخل وصفاً لمقدم الخدمة والخدمات المقدمة">{{ $provider->description ?? '' }}</textarea>
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
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 resize-none bg-white shadow-sm transition-all duration-200 hover:shadow-md"
                            placeholder="أدخل قائمة بالخدمات المقدمة">{{ $provider->services ?? '' }}</textarea>
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
            <a href="{{ route('warranties.providers.show', $provider) }}" class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
                <i class="fas fa-times ml-2"></i>
                إلغاء
            </a>
            <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-amber-600 to-amber-700 border border-transparent rounded-xl text-sm font-medium text-white hover:from-amber-700 hover:to-amber-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                <i class="fas fa-save ml-2"></i>
                حفظ التغييرات
            </button>
        </div>
    </form>
</div>

@endsection
