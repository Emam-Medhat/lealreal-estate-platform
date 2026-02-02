@extends('admin.layouts.admin')

@section('title', 'تفاصيل المطالبة')

@section('page-title', 'تفاصيل المطالبة')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-red-600 via-red-700 to-pink-800 rounded-2xl shadow-xl p-8 text-white">
        <div class="flex justify-between items-center">
            <div>
                <div class="flex items-center mb-3">
                    <div class="bg-white bg-opacity-20 rounded-xl p-3 ml-4">
                        <i class="fas fa-clipboard-list text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">تفاصيل المطالبة</h1>
                        <p class="text-red-100 mt-1">{{ $claim->claim_number }} - {{ $claim->warranty->title }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4 mt-4">
                    <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                        <span class="text-sm text-red-100">الحالة:</span>
                        <span class="text-sm font-semibold text-white">
                            @if($claim->status == 'pending') معلقة
                            @elseif($claim->status == 'approved') مقبولة
                            @elseif($claim->status == 'rejected') مرفوضة
                            @elseif($claim->status == 'processing') قيد المعالجة
                            @elseif($claim->status == 'completed') مكتملة
                            @else {{ $claim->status }} @endif
                        </span>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                        <span class="text-sm text-red-100">المبلغ:</span>
                        <span class="text-sm font-semibold text-white">{{ number_format($claim->amount, 2) }} ريال</span>
                    </div>
                </div>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('warranties.claims.index') }}" class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 backdrop-blur-sm border border-white border-opacity-30 rounded-xl text-sm font-medium text-white hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للمطالبات
                </a>
                <a href="{{ route('warranties.claims.edit', $claim) }}" class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 backdrop-blur-sm border border-white border-opacity-30 rounded-xl text-sm font-medium text-white hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300">
                    <i class="fas fa-edit ml-2"></i>
                    تعديل
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Claim Status and Actions -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Status Card -->
    <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-amber-100 text-sm font-medium mb-2">حالة المطالبة</p>
                <p class="text-2xl font-bold text-white">
                    @if($claim->status == 'pending') معلقة
                    @elseif($claim->status == 'approved') مقبولة
                    @elseif($claim->status == 'rejected') مرفوضة
                    @elseif($claim->status == 'processing') قيد المعالجة
                    @elseif($claim->status == 'completed') مكتملة
                    @else {{ $claim->status }} @endif
                </p>
                <div class="mt-2 flex items-center text-xs text-amber-100">
                    <i class="fas fa-info-circle ml-1"></i>
                    <span>آخر تحديث: {{ $claim->updated_at->format('Y-m-d H:i') }}</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-flag text-white text-2xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Amount Card -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium mb-2">المبلغ المطلوب</p>
                <p class="text-2xl font-bold text-white">{{ number_format($claim->amount, 2) }}</p>
                <div class="mt-2 flex items-center text-xs text-green-100">
                    <i class="fas fa-money-bill-wave ml-1"></i>
                    <span>ريال سعودي</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-money-bill-wave text-white text-2xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Date Card -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium mb-2">تاريخ المطالبة</p>
                <p class="text-2xl font-bold text-white">{{ $claim->claim_date->format('Y-m-d') }}</p>
                <div class="mt-2 flex items-center text-xs text-blue-100">
                    <i class="fas fa-calendar-alt ml-1"></i>
                    <span>منذ {{ $claim->claim_date->diffForHumans() }}</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-calendar-alt text-white text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Claim Details -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Basic Information -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <div class="bg-red-500 rounded-lg p-2 ml-3">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
                معلومات المطالبة
            </h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">رقم المطالبة</span>
                <span class="text-sm font-semibold text-gray-900">{{ $claim->claim_number }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">تاريخ المطالبة</span>
                <span class="text-sm font-semibold text-gray-900">{{ $claim->claim_date->format('Y-m-d') }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">تاريخ الحادث</span>
                <span class="text-sm font-semibold text-gray-900">{{ $claim->incident_date ? $claim->incident_date->format('Y-m-d') : 'غير محدد' }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">الحالة</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                    @if($claim->status == 'pending') bg-yellow-100 text-yellow-800
                    @elseif($claim->status == 'approved') bg-green-100 text-green-800
                    @elseif($claim->status == 'rejected') bg-red-100 text-red-800
                    @elseif($claim->status == 'processing') bg-blue-100 text-blue-800
                    @elseif($claim->status == 'completed') bg-purple-100 text-purple-800
                    @else bg-gray-100 text-gray-800 @endif">
                    @if($claim->status == 'pending') معلقة
                    @elseif($claim->status == 'approved') مقبولة
                    @elseif($claim->status == 'rejected') مرفوضة
                    @elseif($claim->status == 'processing') قيد المعالجة
                    @elseif($claim->status == 'completed') مكتملة
                    @else {{ $claim->status }} @endif
                </span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">المبلغ</span>
                <span class="text-sm font-semibold text-gray-900">{{ number_format($claim->amount, 2) }} ريال</span>
            </div>
            @if($claim->resolved_at)
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">تاريخ الحل</span>
                <span class="text-sm font-semibold text-gray-900">{{ $claim->resolved_at->format('Y-m-d H:i') }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Warranty Information -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <div class="bg-blue-500 rounded-lg p-2 ml-3">
                    <i class="fas fa-shield-alt text-white"></i>
                </div>
                معلومات الضمان
            </h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">رقم الضمان</span>
                <span class="text-sm font-semibold text-gray-900">{{ $claim->warranty->warranty_number }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">عنوان الضمان</span>
                <span class="text-sm font-semibold text-gray-900">{{ $claim->warranty->title }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">العقار</span>
                <span class="text-sm font-semibold text-gray-900">{{ $claim->warranty->property->title ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">مقدم الخدمة</span>
                <span class="text-sm font-semibold text-gray-900">{{ $claim->warranty->serviceProvider->name ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">مبلغ التغطية</span>
                <span class="text-sm font-semibold text-gray-900">{{ number_format($claim->warranty->coverage_amount, 2) }} ريال</span>
            </div>
        </div>
    </div>
</div>

<!-- Description and Resolution -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Description -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <div class="bg-indigo-500 rounded-lg p-2 ml-3">
                    <i class="fas fa-align-left text-white"></i>
                </div>
                وصف المطالبة
            </h3>
        </div>
        <div class="p-6">
            <p class="text-gray-700 leading-relaxed">{{ $claim->description }}</p>
        </div>
    </div>

    <!-- Resolution -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <div class="bg-teal-500 rounded-lg p-2 ml-3">
                    <i class="fas fa-check-circle text-white"></i>
                </div>
                الحل والتوصية
            </h3>
        </div>
        <div class="p-6">
            @if($claim->resolution)
                <p class="text-gray-700 leading-relaxed">{{ $claim->resolution }}</p>
            @else
                <p class="text-gray-500 italic">لم يتم إضافة حل أو توصية بعد</p>
            @endif
        </div>
    </div>
</div>

<!-- Creator Information -->
<div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-8">
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <div class="bg-purple-500 rounded-lg p-2 ml-3">
                <i class="fas fa-user text-white"></i>
            </div>
            معلومات المنشئ
        </h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">المنشئ</span>
                <span class="text-sm font-semibold text-gray-900">{{ $claim->creator->name ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">البريد الإلكتروني</span>
                <span class="text-sm font-semibold text-gray-900">{{ $claim->creator->email ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">تاريخ الإنشاء</span>
                <span class="text-sm font-semibold text-gray-900">{{ $claim->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
@if($claim->status == 'pending')
<div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <div class="bg-amber-500 rounded-lg p-2 ml-3">
                <i class="fas fa-cogs text-white"></i>
            </div>
            الإجراءات المتاحة
        </h3>
    </div>
    <div class="p-6">
        <div class="flex flex-wrap gap-3">
            <form method="POST" action="{{ route('warranties.claims.approve', $claim) }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-xl text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-check ml-2"></i>
                    قبول المطالبة
                </button>
            </form>
            
            <form method="POST" action="{{ route('warranties.claims.reject', $claim) }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-red-600 border border-transparent rounded-xl text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-times ml-2"></i>
                    رفض المطالبة
                </button>
            </form>
            
            <form method="POST" action="{{ route('warranties.claims.process', $claim) }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-xl text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-cog ml-2"></i>
                    بدء المعالجة
                </button>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
