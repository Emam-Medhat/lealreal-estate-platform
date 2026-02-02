@extends('admin.layouts.admin')

@section('title', 'تفاصيل مقدم الخدمة')

@section('page-title', 'تفاصيل مقدم الخدمة')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-teal-600 via-teal-700 to-cyan-800 rounded-2xl shadow-xl p-8 text-white">
        <div class="flex justify-between items-center">
            <div>
                <div class="flex items-center mb-3">
                    <div class="bg-white bg-opacity-20 rounded-xl p-3 ml-4">
                        <i class="fas fa-user-tie text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">تفاصيل مقدم الخدمة</h1>
                        <p class="text-teal-100 mt-1">{{ $provider->name }} @if($provider->company_name) - {{ $provider->company_name }} @endif</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4 mt-4">
                    <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                        <span class="text-sm text-teal-100">الحالة:</span>
                        <span class="text-sm font-semibold text-white">
                            @if($provider->status == 'active') نشط
                            @else غير نشط @endif
                        </span>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                        <span class="text-sm text-teal-100">الضمانات:</span>
                        <span class="text-sm font-semibold text-white">{{ $provider->warranties->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('warranties.providers.index') }}" class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 backdrop-blur-sm border border-white border-opacity-30 rounded-xl text-sm font-medium text-white hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة لمقدمي الخدمة
                </a>
                <a href="{{ route('warranties.providers.edit', $provider) }}" class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 backdrop-blur-sm border border-white border-opacity-30 rounded-xl text-sm font-medium text-white hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300">
                    <i class="fas fa-edit ml-2"></i>
                    تعديل
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
                <p class="text-teal-100 text-sm font-medium mb-2">الضمانات المرتبطة</p>
                <p class="text-3xl font-bold text-white">{{ $provider->warranties->count() }}</p>
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
                <p class="text-green-100 text-sm font-medium mb-2">الحالة</p>
                <p class="text-3xl font-bold text-white">
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
                <p class="text-3xl font-bold text-white">{{ number_format($provider->warranties->sum('coverage_amount'), 2) }}</p>
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
    
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm font-medium mb-2">النشاط</p>
                <p class="text-3xl font-bold text-white">{{ $provider->warranties->where('status', 'active')->count() }}</p>
                <div class="mt-2 flex items-center text-xs text-purple-100">
                    <i class="fas fa-chart-line ml-1"></i>
                    <span>ضمان نشط</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-chart-line text-white text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Provider Details -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Basic Information -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <div class="bg-teal-500 rounded-lg p-2 ml-3">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
                المعلومات الأساسية
            </h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">الاسم</span>
                <span class="text-sm font-semibold text-gray-900">{{ $provider->name }}</span>
            </div>
            @if($provider->company_name)
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">الشركة</span>
                <span class="text-sm font-semibold text-gray-900">{{ $provider->company_name }}</span>
            </div>
            @endif
            @if($provider->contact_person)
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">الشخص المسؤول</span>
                <span class="text-sm font-semibold text-gray-900">{{ $provider->contact_person }}</span>
            </div>
            @endif
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">الحالة</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                    @if($provider->status == 'active') bg-green-100 text-green-800
                    @else bg-gray-100 text-gray-800 @endif">
                    @if($provider->status == 'active') نشط
                    @else غير نشط @endif
                </span>
            </div>
            @if($provider->license_number)
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">رقم الترخيص</span>
                <span class="text-sm font-semibold text-gray-900">{{ $provider->license_number }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Contact Information -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <div class="bg-blue-500 rounded-lg p-2 ml-3">
                    <i class="fas fa-address-book text-white"></i>
                </div>
                معلومات التواصل
            </h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">البريد الإلكتروني</span>
                <a href="mailto:{{ $provider->email }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">{{ $provider->email }}</a>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">الهاتف</span>
                <a href="tel:{{ $provider->phone }}" class="text-sm font-semibold text-green-600 hover:text-green-800">{{ $provider->phone }}</a>
            </div>
            @if($provider->address)
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">العنوان</span>
                <span class="text-sm font-semibold text-gray-900">{{ $provider->address }}</span>
            </div>
            @endif
            @if($provider->website)
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">الموقع الإلكتروني</span>
                <a href="{{ $provider->website }}" target="_blank" class="text-sm font-semibold text-purple-600 hover:text-purple-800">زيارة الموقع</a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Description and Services -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Description -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <div class="bg-indigo-500 rounded-lg p-2 ml-3">
                    <i class="fas fa-align-left text-white"></i>
                </div>
                الوصف
            </h3>
        </div>
        <div class="p-6">
            @if($provider->description)
                <p class="text-gray-700 leading-relaxed">{{ $provider->description }}</p>
            @else
                <p class="text-gray-500 italic">لا يوجد وصف متاح</p>
            @endif
        </div>
    </div>

    <!-- Services -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <div class="bg-purple-500 rounded-lg p-2 ml-3">
                    <i class="fas fa-cogs text-white"></i>
                </div>
                الخدمات المقدمة
            </h3>
        </div>
        <div class="p-6">
            @if($provider->services)
                <p class="text-gray-700 leading-relaxed">{{ $provider->services }}</p>
            @else
                <p class="text-gray-500 italic">لا توجد خدمات محددة</p>
            @endif
        </div>
    </div>
</div>

<!-- Warranties Section -->
@if($provider->warranties->count() > 0)
<div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <div class="bg-red-500 rounded-lg p-2 ml-3">
                <i class="fas fa-shield-alt text-white"></i>
            </div>
            الضمانات المرتبطة
        </h3>
    </div>
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم الضمان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العقار</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($provider->warranties as $warranty)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $warranty->warranty_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $warranty->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $warranty->property->title ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($warranty->status == 'active') bg-green-100 text-green-800
                                @elseif($warranty->status == 'expired') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                @if($warranty->status == 'active') نشط
                                @elseif($warranty->status == 'expired') منتهي
                                @else {{ $warranty->status }} @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('warranties.policies.show', $warranty) }}" class="text-indigo-600 hover:text-indigo-900">عرض</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection
