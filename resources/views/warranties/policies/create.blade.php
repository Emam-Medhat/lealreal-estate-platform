@extends('admin.layouts.admin')

@section('title', 'إضافة ضمان جديد')

@section('page-title', 'إضافة ضمان جديد')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 rounded-2xl shadow-xl p-8 text-white">
        <div class="flex justify-between items-center">
            <div>
                <div class="flex items-center mb-3">
                    <div class="bg-white bg-opacity-20 rounded-xl p-3 ml-4">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">إضافة ضمان جديد</h1>
                        <p class="text-blue-100 mt-1">إنشاء سياسة ضمان جديدة للعقار</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4 mt-4">
                    <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                        <span class="text-sm text-blue-100">النظام:</span>
                        <span class="text-sm font-semibold text-white">إدارة الضمانات</span>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                        <span class="text-sm text-blue-100">المستخدم:</span>
                        <span class="text-sm font-semibold text-white">{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('warranties.policies.index') }}" class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 backdrop-blur-sm border border-white border-opacity-30 rounded-xl text-sm font-medium text-white hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للضمانات
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium mb-2">إجمالي الضمانات</p>
                <p class="text-3xl font-bold text-white">{{ App\Models\Warranty::count() }}</p>
                <div class="mt-2 flex items-center text-xs text-blue-100">
                    <i class="fas fa-arrow-up ml-1"></i>
                    <span>نشط هذا الشهر</span>
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
                <p class="text-green-100 text-sm font-medium mb-2">ضمانات نشطة</p>
                <p class="text-3xl font-bold text-white">{{ App\Models\Warranty::where('status', 'active')->count() }}</p>
                <div class="mt-2 flex items-center text-xs text-green-100">
                    <i class="fas fa-check-circle ml-1"></i>
                    <span>تعمل بشكل صحيح</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-check-circle text-white text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-amber-100 text-sm font-medium mb-2">تنتهي قريباً</p>
                <p class="text-3xl font-bold text-white">{{ App\Models\Warranty::where('end_date', '<=', now()->addDays(30))->where('end_date', '>', now())->count() }}</p>
                <div class="mt-2 flex items-center text-xs text-amber-100">
                    <i class="fas fa-exclamation-triangle ml-1"></i>
                    <span>خلال 30 يوم</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm font-medium mb-2">مقدمو الخدمة</p>
                <p class="text-3xl font-bold text-white">{{ App\Models\ServiceProvider::count() }}</p>
                <div class="mt-2 flex items-center text-xs text-purple-100">
                    <i class="fas fa-users ml-1"></i>
                    <span>متاحون للخدمة</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-users text-white text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Form and Quick Actions -->
<div class="space-y-6">
    <!-- Main Form -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <div class="bg-blue-500 rounded-lg p-2 ml-3">
                    <i class="fas fa-edit text-white"></i>
                </div>
                نموذج إضافة الضمان
            </h3>
        </div>
        <form method="POST" action="{{ route('warranties.policies.store') }}" class="p-6">
            @csrf
            
            <!-- Basic Information Section -->
            <div class="mb-8">
                <h4 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-3 ml-3 shadow-lg">
                        <i class="fas fa-info-circle text-white text-lg"></i>
                    </div>
                    <div>
                        <span class="text-gray-900">المعلومات الأساسية</span>
                        <p class="text-sm text-gray-500 font-normal">بيانات الضمان الأساسية</p>
                    </div>
                </h4>
                
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 shadow-sm border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-hashtag text-blue-500 ml-2 text-xs"></i>
                        رقم الضمان *
                    </label>
                    <div class="relative">
                        <input type="text" name="warranty_number" required
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm transition-all duration-200 hover:shadow-md"
                            placeholder="WAR-2024-0001">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-hashtag text-blue-400"></i>
                        </div>
                    </div>
                    @error('warranty_number')
                        <p class="mt-2 text-sm text-red-600 flex items-center animate-pulse">
                            <i class="fas fa-exclamation-circle ml-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-shield-alt text-green-500 ml-2 text-xs"></i>
                        نوع الضمان *
                    </label>
                    <div class="relative">
                        <select name="warranty_type" required
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none bg-white shadow-sm transition-all duration-200 hover:shadow-md">
                            <option value="">اختر النوع</option>
                            <option value="product">ضمان المنتج</option>
                            <option value="labor">ضمان العمالة</option>
                            <option value="combined">ضمان شامل</option>
                            <option value="extended">ضمان ممتد</option>
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-shield-alt text-green-400"></i>
                        </div>
                    </div>
                    @error('warranty_type')
                        <p class="mt-2 text-sm text-red-600 flex items-center animate-pulse">
                            <i class="fas fa-exclamation-circle ml-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                    <i class="fas fa-heading text-purple-500 ml-2 text-xs"></i>
                    عنوان الضمان *
                </label>
                <div class="relative">
                    <input type="text" name="title" required
                        class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm transition-all duration-200 hover:shadow-md"
                        placeholder="أدخل عنوان الضمان">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-heading text-purple-400"></i>
                    </div>
                </div>
                @error('title')
                    <p class="mt-2 text-sm text-red-600 flex items-center animate-pulse">
                        <i class="fas fa-exclamation-circle ml-2"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="mt-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                    <i class="fas fa-align-left text-indigo-500 ml-2 text-xs"></i>
                    وصف الضمان *
                </label>
                <div class="relative">
                    <textarea name="description" required rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none bg-white shadow-sm transition-all duration-200 hover:shadow-md"
                        placeholder="أدخل وصفاً مفصلاً للضمان"></textarea>
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
                    <i class="fas fa-file-contract text-amber-500 ml-2 text-xs"></i>
                    تفاصيل التغطية *
                </label>
                <div class="relative">
                    <textarea name="coverage_details" required rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none bg-white shadow-sm transition-all duration-200 hover:shadow-md"
                        placeholder="أدخل تفاصيل التغطية وما يشمله الضمان"></textarea>
                </div>
                @error('coverage_details')
                    <p class="mt-2 text-sm text-red-600 flex items-center animate-pulse">
                        <i class="fas fa-exclamation-circle ml-2"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        <!-- Property and Service Provider Section -->
        <div class="mb-8">
            <h4 class="text-base font-medium text-gray-900 mb-6 flex items-center">
                <div class="bg-green-100 rounded-lg p-3 ml-3">
                    <i class="fas fa-building text-green-600"></i>
                </div>
                العقار ومقدم الخدمة
            </h4>
            
            <div class="bg-gray-50 rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العقار *</label>
                    <div class="relative">
                        <select name="property_id" required
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                            <option value="">اختر العقار</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}">{{ $property->title }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-home text-gray-400"></i>
                        </div>
                    </div>
                    @error('property_id')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle ml-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">مقدم الخدمة</label>
                    <div class="relative">
                        <select name="service_provider_id"
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                            <option value="">اختر مقدم الخدمة</option>
                            @foreach($serviceProviders as $provider)
                                <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user-tie text-gray-400"></i>
                        </div>
                    </div>
                    @error('service_provider_id')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle ml-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Dates and Duration Section -->
        <div class="mb-8">
            <h4 class="text-base font-medium text-gray-900 mb-6 flex items-center">
                <div class="bg-amber-100 rounded-lg p-3 ml-3">
                    <i class="fas fa-calendar text-amber-600"></i>
                </div>
                التواريخ والمدة
            </h4>
            
            <div class="bg-gray-50 rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ البدء *</label>
                    <div class="relative">
                        <input type="date" name="start_date" required
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar-alt text-gray-400"></i>
                        </div>
                    </div>
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle ml-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الانتهاء *</label>
                    <div class="relative">
                        <input type="date" name="end_date" required
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar-check text-gray-400"></i>
                        </div>
                    </div>
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle ml-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المدة بالأشهر *</label>
                    <div class="relative">
                        <input type="number" name="duration_months" required min="1"
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="12">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-clock text-gray-400"></i>
                        </div>
                    </div>
                    @error('duration_months')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle ml-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Financial Information Section -->
        <div class="mb-8">
            <h4 class="text-base font-medium text-gray-900 mb-6 flex items-center">
                <div class="bg-purple-100 rounded-lg p-3 ml-3">
                    <i class="fas fa-dollar-sign text-purple-600"></i>
                </div>
                المعلومات المالية
            </h4>
            
            <div class="bg-gray-50 rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">مبلغ التغطية *</label>
                    <div class="relative">
                        <input type="number" name="coverage_amount" required min="0" step="0.01"
                            class="w-full px-3 py-2 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="0.00">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm">ريال</span>
                        </div>
                    </div>
                    @error('coverage_amount')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle ml-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">مبلغ الخصمول</label>
                    <div class="relative">
                        <input type="number" name="deductible_amount" min="0" step="0.01"
                            class="w-full px-3 py-2 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="0.00">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm">ريال</span>
                        </div>
                    </div>
                    @error('deductible_amount')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle ml-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Contact Information Section -->
        <div class="mb-8">
            <h4 class="text-base font-medium text-gray-900 mb-6 flex items-center">
                <div class="bg-indigo-100 rounded-lg p-3 ml-3">
                    <i class="fas fa-address-book text-indigo-600"></i>
                </div>
                معلومات التواصل
            </h4>
            
            <div class="bg-gray-50 rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الشخص المسؤول</label>
                    <div class="relative">
                        <input type="text" name="contact_person"
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="الاسم الكامل">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                    </div>
                    @error('contact_person')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle ml-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                    <div class="relative">
                        <input type="tel" name="contact_phone"
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="05xxxxxxxx">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-gray-400"></i>
                        </div>
                    </div>
                    @error('contact_phone')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle ml-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                    <div class="relative">
                        <input type="email" name="contact_email"
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="email@example.com">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                    </div>
                    @error('contact_email')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle ml-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Terms and Notes Section -->
        <div class="mb-8">
            <h4 class="text-base font-medium text-gray-900 mb-6 flex items-center">
                <div class="bg-red-100 rounded-lg p-3 ml-3">
                    <i class="fas fa-file-contract text-red-600"></i>
                </div>
                الشروط والملاحظات
            </h4>
            
            <div class="bg-gray-50 rounded-lg p-6">
                <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الشروط والأحكام</label>
                    <textarea name="terms_conditions" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                        placeholder="أدخل الشروط والأحكام الخاصة بالضمان"></textarea>
                    @error('terms_conditions')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle ml-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات إضافية</label>
                    <textarea name="notes" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                        placeholder="أي ملاحظات إضافية حول الضمان"></textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle ml-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-reverse space-x-3 pt-6 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 -mx-6 px-6 -mb-6 pb-6 rounded-b-2xl">
            <a href="{{ route('warranties.policies.index') }}" class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
                <i class="fas fa-times ml-2"></i>
                إلغاء
            </a>
            <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-xl text-sm font-medium text-white hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                <i class="fas fa-save ml-2"></i>
                حفظ الضمان
        </div>
    </div>

    <!-- Terms and Notes Section -->
    <div class="mb-8">
        <h4 class="text-base font-medium text-gray-900 mb-6 flex items-center">
            <div class="bg-red-100 rounded-lg p-3 ml-3">
                <i class="fas fa-file-contract text-red-600"></i>
            </div>
            الشروط والملاحظات
        </h4>
        
        <div class="bg-gray-50 rounded-lg p-6">
            <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الشروط والأحكام</label>
                <textarea name="terms_conditions" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                    placeholder="أدخل الشروط والأحكام الخاصة بالضمان"></textarea>
                @error('terms_conditions')
                    <p class="mt-1 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle ml-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات إضافية</label>
                <textarea name="notes" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                    placeholder="أي ملاحظات إضافية حول الضمان"></textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle ml-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Submit Buttons -->
    <div class="flex justify-end space-x-reverse space-x-3 pt-6 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 -mx-6 px-6 -mb-6 pb-6 rounded-b-2xl">
        <a href="{{ route('warranties.policies.index') }}" class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
            <i class="fas fa-times ml-2"></i>
            إلغاء
        </a>
        <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-xl text-sm font-medium text-white hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
            <i class="fas fa-save ml-2"></i>
            حفظ الضمان
        </button>
    </div>
</form>
</div>

<!-- Quick Actions Section -->
<div class="space-y-6">
    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-bolt ml-2"></i>
                إجراءات سريعة
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('warranties.policies.index') }}" class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl hover:from-blue-100 hover:to-indigo-100 transition-all duration-300 transform hover:scale-105 border border-blue-100">
                    <div class="flex items-center">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-2 ml-3">
                            <i class="fas fa-list text-white"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900">عرض جميع الضمانات</h4>
                            <p class="text-xs text-gray-600">قائمة الضمانات الحالية</p>
                        </div>
                    </div>
                    <i class="fas fa-arrow-left text-blue-500"></i>
                </a>
                
                <a href="{{ route('warranties.providers.index') }}" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-lg p-2 ml-3">
                            <i class="fas fa-user-tie text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">مقدمو الخدمة</h4>
                            <p class="text-xs text-gray-500">إدارة مقدمي الخدمة</p>
                        </div>
                    </div>
                    <i class="fas fa-arrow-left text-gray-400"></i>
                </a>
                
                <a href="{{ route('warranties.reports') }}" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center">
                        <div class="bg-amber-100 rounded-lg p-2 ml-3">
                            <i class="fas fa-chart-bar text-amber-600"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">التقارير</h4>
                            <p class="text-xs text-gray-500">تقارير الضمانات</p>
                        </div>
                    </div>
                    <i class="fas fa-arrow-left text-gray-400"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Warranties -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">آخر الضمانات</h3>
        </div>
        <div class="p-6">
            @php
                $recentWarranties = App\Models\Warranty::with(['property', 'serviceProvider'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            @endphp
            @if($recentWarranties->count() > 0)
                <div class="space-y-4">
                    @foreach($recentWarranties as $warranty)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900">{{ $warranty->title }}</h4>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $warranty->property->title ?? 'N/A' }} - 
                                    {{ $warranty->warranty_number }}
                                </p>
                            </div>
                            <div class="mr-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($warranty->status == 'active') bg-green-100 text-green-800
                                    @elseif($warranty->status == 'expired') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ __('warranties.status_' . $warranty->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-shield-alt text-gray-400 text-3xl mb-3"></i>
                    <p class="text-gray-500">لا توجد ضمانات حالياً</p>
                </div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-calculate duration when dates change
    document.addEventListener('DOMContentLoaded', function() {
        const startDate = document.querySelector('input[name="start_date"]');
        const endDate = document.querySelector('input[name="end_date"]');
        const durationMonths = document.querySelector('input[name="duration_months"]');

        function calculateDuration() {
            if (startDate.value && endDate.value) {
                const start = new Date(startDate.value);
                const end = new Date(endDate.value);
                
                if (end > start) {
                    const months = (end.getFullYear() - start.getFullYear()) * 12 + 
                                   (end.getMonth() - start.getMonth());
                    durationMonths.value = months;
                }
            }
        }

        startDate.addEventListener('change', calculateDuration);
        endDate.addEventListener('change', calculateDuration);
    });
</script>
@endpush