@extends('admin.layouts.admin')

@section('title', 'تفاصيل مقدم الخدمة')

@section('page-title', 'تفاصيل مقدم الخدمة')

@push('styles')
<style>
    /* Override global link styles for specific icons */
    .header-icon i,
    .action-icons i,
    .stats-icon i,
    .card-icon i {
        color: inherit !important;
    }
    
    .header-icon i { color: #ffffff !important; }
    .stats-icon .fa-shield-alt { color: #2563eb !important; }
    .stats-icon .fa-flag { color: #16a34a !important; }
    .stats-icon .fa-money-bill-wave { color: #2563eb !important; }
    .stats-icon .fa-chart-line { color: #7c3aed !important; }
    
    .action-icons .fa-edit { color: #2563eb !important; }
    .action-icons .fa-arrow-right { color: #ffffff !important; }
    
    .card-icon .fa-info-circle { color: #0d9488 !important; }
    .card-icon .fa-address-book { color: #2563eb !important; }
    .card-icon .fa-align-left { color: #6366f1 !important; }
    .card-icon .fa-cogs { color: #7c3aed !important; }
    .card-icon .fa-shield-alt { color: #dc2626 !important; }
    
    /* Clean header styling */
    .clean-header {
        background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%);
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .clean-header-btn {
        background-color: rgba(255, 255, 255, 0.9);
        color: #0d9488;
        border: 1px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(10px);
    }
    
    .clean-header-btn:hover {
        background-color: rgba(255, 255, 255, 1);
        color: #0f766e;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    /* Clean stats cards */
    .clean-stats-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .clean-stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border-color: #d1d5db;
    }
    
    .stats-icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .stats-icon-circle.teal { background: linear-gradient(135deg, #14b8a6, #06b6d4); }
    .stats-icon-circle.green { background: linear-gradient(135deg, #10b981, #059669); }
    .stats-icon-circle.blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .stats-icon-circle.purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
    
    /* Clean info cards */
    .clean-info-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .clean-card-header {
        background: linear-gradient(135deg, #f9fafb, #f3f4f6);
        border-bottom: 1px solid #e5e7eb;
    }
    
    .info-row {
        padding: 12px 16px;
        border-bottom: 1px solid #f3f4f6;
        transition: background-color 0.2s ease;
    }
    
    .info-row:hover {
        background-color: #f9fafb;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    /* Ensure text visibility */
    .text-on-gradient {
        color: #ffffff !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }
    
    .text-on-gradient-light {
        color: #f0fdfa !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    /* Fix all text colors */
    .text-white { color: #ffffff !important; }
    .text-teal-100 { color: #f0fdfa !important; }
    .text-gray-900 { color: #111827 !important; }
    .text-gray-800 { color: #1f2937 !important; }
    .text-gray-700 { color: #374151 !important; }
    .text-gray-600 { color: #6b7280 !important; }
    .text-gray-500 { color: #6b7280 !important; }
    .text-blue-600 { color: #2563eb !important; }
    .text-blue-800 { color: #1e40af !important; }
    .text-green-600 { color: #16a34a !important; }
    .text-green-800 { color: #166534 !important; }
    .text-purple-600 { color: #7c3aed !important; }
    .text-indigo-600 { color: #4f46e5 !important; }
    .text-red-600 { color: #dc2626 !important; }
    .text-red-800 { color: #991b1b !important; }
    
    /* Fix status badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .status-badge.active {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .status-badge.inactive {
        background-color: #f3f4f6;
        color: #1f2937;
    }
    
    .status-badge.expired {
        background-color: #fee2e2;
        color: #991b1b;
    }
    
    /* Fix all text in the entire page */
    body, div, span, p, h1, h2, h3, h4, h5, h6, td, th, a, button {
        color: inherit !important;
    }
    
    /* Fix table text */
    td .text-gray-900 { color: #111827 !important; }
    td .text-gray-500 { color: #6b7280 !important; }
    th .text-gray-500 { color: #6b7280 !important; }
    
    /* Fix links */
    a.text-blue-600 { color: #2563eb !important; }
    a.text-blue-600:hover { color: #1d4ed8 !important; }
    a.text-green-600 { color: #16a34a !important; }
    a.text-green-600:hover { color: #15803d !important; }
    a.text-purple-600 { color: #7c3aed !important; }
    a.text-purple-600:hover { color: #6d28d9 !important; }
    a.text-indigo-600 { color: #4f46e5 !important; }
    a.text-indigo-600:hover { color: #4338ca !important; }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="clean-header p-8">
        <div class="flex justify-between items-center">
            <div>
                <div class="flex items-center mb-3">
                    <div class="bg-white bg-opacity-20 rounded-xl p-3 ml-4 header-icon">
                        <i class="fas fa-user-tie text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-on-gradient">تفاصيل مقدم الخدمة</h1>
                        <p class="text-on-gradient-light mt-1">{{ $provider->name }} @if($provider->company_name) - {{ $provider->company_name }} @endif</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4 mt-4">
                    <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                        <span class="text-sm text-on-gradient-light">الحالة:</span>
                        <span class="text-sm font-semibold text-on-gradient">
                            @if($provider->status == 'active') نشط
                            @else غير نشط @endif
                        </span>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                        <span class="text-sm text-on-gradient-light">الضمانات:</span>
                        <span class="text-sm font-semibold text-on-gradient">{{ $provider->warranties->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="flex space-x-reverse space-x-3 action-icons">
                <a href="{{ route('warranties.providers.index') }}" class="clean-header-btn px-6 py-3 rounded-xl transition-all duration-300 flex items-center">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة لمقدمي الخدمة
                </a>
                <a href="{{ route('warranties.providers.edit', $provider) }}" class="clean-header-btn px-6 py-3 rounded-xl transition-all duration-300 flex items-center">
                    <i class="fas fa-edit ml-2"></i>
                    تعديل
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="clean-stats-card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-2">الضمانات المرتبطة</p>
                <p class="text-2xl font-bold text-gray-900">{{ $provider->warranties->count() }}</p>
                <div class="mt-2 flex items-center text-xs text-gray-500">
                    <i class="fas fa-shield-alt ml-1 stats-icon"></i>
                    <span>ضمان نشط</span>
                </div>
            </div>
            <div class="stats-icon-circle teal">
                <i class="fas fa-shield-alt text-white text-xl stats-icon"></i>
            </div>
        </div>
    </div>
    
    <div class="clean-stats-card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-2">الحالة</p>
                <p class="text-2xl font-bold text-gray-900">
                    @if($provider->status == 'active') نشط
                    @else غير نشط @endif
                </p>
                <div class="mt-2 flex items-center text-xs text-gray-500">
                    <i class="fas fa-flag ml-1 stats-icon"></i>
                    <span>حالية</span>
                </div>
            </div>
            <div class="stats-icon-circle green">
                <i class="fas fa-flag text-white text-xl stats-icon"></i>
            </div>
        </div>
    </div>
    
    <div class="clean-stats-card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-2">مجموع التغطية</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($provider->warranties->sum('coverage_amount'), 2) }}</p>
                <div class="mt-2 flex items-center text-xs text-gray-500">
                    <i class="fas fa-money-bill-wave ml-1 stats-icon"></i>
                    <span>ريال سعودي</span>
                </div>
            </div>
            <div class="stats-icon-circle blue">
                <i class="fas fa-money-bill-wave text-white text-xl stats-icon"></i>
            </div>
        </div>
    </div>
    
    <div class="clean-stats-card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-2">النشاط</p>
                <p class="text-2xl font-bold text-gray-900">{{ $provider->warranties->where('status', 'active')->count() }}</p>
                <div class="mt-2 flex items-center text-xs text-gray-500">
                    <i class="fas fa-chart-line ml-1 stats-icon"></i>
                    <span>ضمان نشط</span>
                </div>
            </div>
            <div class="stats-icon-circle purple">
                <i class="fas fa-chart-line text-white text-xl stats-icon"></i>
            </div>
        </div>
    </div>
</div>

<!-- Provider Details -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Basic Information -->
    <div class="clean-info-card">
        <div class="clean-card-header px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <div class="bg-teal-500 rounded-lg p-2 ml-3 card-icon">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
                المعلومات الأساسية
            </h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="info-row">
                <span class="text-sm font-medium text-gray-600">الاسم</span>
                <span class="text-sm font-semibold text-gray-900">{{ $provider->name }}</span>
            </div>
            @if($provider->company_name)
            <div class="info-row">
                <span class="text-sm font-medium text-gray-600">الشركة</span>
                <span class="text-sm font-semibold text-gray-900">{{ $provider->company_name }}</span>
            </div>
            @endif
            @if($provider->contact_person)
            <div class="info-row">
                <span class="text-sm font-medium text-gray-600">الشخص المسؤول</span>
                <span class="text-sm font-semibold text-gray-900">{{ $provider->contact_person }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="text-sm font-medium text-gray-600">الحالة</span>
                <span class="status-badge @if($provider->status == 'active') active @else inactive @endif">
                    @if($provider->status == 'active') نشط
                    @else غير نشط @endif
                </span>
            </div>
            @if($provider->license_number)
            <div class="info-row">
                <span class="text-sm font-medium text-gray-600">رقم الترخيص</span>
                <span class="text-sm font-semibold text-gray-900">{{ $provider->license_number }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Contact Information -->
    <div class="clean-info-card">
        <div class="clean-card-header px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <div class="bg-blue-500 rounded-lg p-2 ml-3 card-icon">
                    <i class="fas fa-address-book text-white"></i>
                </div>
                معلومات التواصل
            </h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="info-row">
                <span class="text-sm font-medium text-gray-600">البريد الإلكتروني</span>
                <a href="mailto:{{ $provider->email }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">{{ $provider->email }}</a>
            </div>
            <div class="info-row">
                <span class="text-sm font-medium text-gray-600">الهاتف</span>
                <a href="tel:{{ $provider->phone }}" class="text-sm font-semibold text-green-600 hover:text-green-800">{{ $provider->phone }}</a>
            </div>
            @if($provider->address)
            <div class="info-row">
                <span class="text-sm font-medium text-gray-600">العنوان</span>
                <span class="text-sm font-semibold text-gray-900">{{ $provider->address }}</span>
            </div>
            @endif
            @if($provider->website)
            <div class="info-row">
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
    <div class="clean-info-card">
        <div class="clean-card-header px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <div class="bg-indigo-500 rounded-lg p-2 ml-3 card-icon">
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
    <div class="clean-info-card">
        <div class="clean-card-header px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <div class="bg-purple-500 rounded-lg p-2 ml-3 card-icon">
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
