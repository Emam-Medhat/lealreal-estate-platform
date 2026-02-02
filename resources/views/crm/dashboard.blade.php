@extends('admin.layouts.admin')

@section('title', 'لوحة CRM')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">لوحة إدارة علاقات العملاء</h1>
            <p class="text-gray-600 mt-2">إدارة العملاء والعروض والعمولات</p>
        </div>
        <div class="flex space-x-3 space-x-reverse">
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-user-plus ml-2"></i>
                عميل جديد
            </button>
            <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-chart-line ml-2"></i>
                التقارير
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">إجمالي العملاء</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_leads'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600"><i class="fas fa-arrow-up ml-1"></i>12%</span>
                <span class="text-gray-500 mr-2">من الشهر الماضي</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">العروض النشطة</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active_offers'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-handshake text-green-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600"><i class="fas fa-arrow-up ml-1"></i>8%</span>
                <span class="text-gray-500 mr-2">زيادة هذا الشهر</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">العمولات المستحقة</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending_commissions'] ?? 0, 0) }} ريال</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-coins text-yellow-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-yellow-600"><i class="fas fa-clock ml-1"></i>قيد الانتظار</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">معدل التحويل</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['conversion_rate'] ?? 0, 1) }}%</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-chart-pie text-purple-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600"><i class="fas fa-arrow-up ml-1"></i>3.2%</span>
                <span class="text-gray-500 mr-2">تحسن الأداء</span>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Leads -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">آخر العملاء</h3>
            </div>
            <div class="p-6">
                @if($recentLeads->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentLeads->take(5) as $lead)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 p-2 rounded-lg ml-3">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $lead->name ?? 'عميل بدون اسم' }}</p>
                                        <p class="text-sm text-gray-600">{{ $lead->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($lead->status == 'new') bg-green-100 text-green-800
                                        @elseif($lead->status == 'contacted') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $lead->status ?? 'new' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-users text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">لا توجد عملاء حالياً</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Offers -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">آخر العروض</h3>
            </div>
            <div class="p-6">
                @if($recentOffers->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentOffers->take(5) as $offer)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="bg-green-100 p-2 rounded-lg ml-3">
                                        <i class="fas fa-handshake text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">عرض #{{ $offer->id ?? 'N/A' }}</p>
                                        <p class="text-sm text-gray-600">{{ number_format($offer->amount ?? 0, 0) }} ريال</p>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($offer->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($offer->status == 'accepted') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $offer->status ?? 'pending' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-handshake text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">لا توجد عروض حالياً</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sales Pipeline -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">مسار المبيعات</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Lead Stage -->
                <div class="text-center">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="text-3xl font-bold text-blue-600 mb-2">45</div>
                        <h4 class="font-semibold text-gray-900">عملاء محتملون</h4>
                        <p class="text-sm text-gray-600 mt-1">مرحلة أولية</p>
                    </div>
                    <div class="mt-3 space-y-2">
                        @for($i = 1; $i <= 3; $i++)
                        <div class="bg-white border border-gray-200 rounded-lg p-2 text-right">
                            <p class="text-sm font-medium text-gray-900">عميل {{ $i }}</p>
                            <p class="text-xs text-gray-600">500K - 1M ريال</p>
                        </div>
                        @endfor
                    </div>
                </div>

                <!-- Qualified Stage -->
                <div class="text-center">
                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="text-3xl font-bold text-green-600 mb-2">32</div>
                        <h4 class="font-semibold text-gray-900">مؤهلون</h4>
                        <p class="text-sm text-gray-600 mt-1">تحت الدراسة</p>
                    </div>
                    <div class="mt-3 space-y-2">
                        @for($i = 1; $i <= 3; $i++)
                        <div class="bg-white border border-gray-200 rounded-lg p-2 text-right">
                            <p class="text-sm font-medium text-gray-900">عميل {{ $i + 3 }}</p>
                            <p class="text-xs text-gray-600">1M - 2M ريال</p>
                        </div>
                        @endfor
                    </div>
                </div>

                <!-- Proposal Stage -->
                <div class="text-center">
                    <div class="bg-yellow-50 rounded-lg p-4">
                        <div class="text-3xl font-bold text-yellow-600 mb-2">18</div>
                        <h4 class="font-semibold text-gray-900">عروض مقدمة</h4>
                        <p class="text-sm text-gray-600 mt-1">في انتظار الرد</p>
                    </div>
                    <div class="mt-3 space-y-2">
                        @for($i = 1; $i <= 3; $i++)
                        <div class="bg-white border border-gray-200 rounded-lg p-2 text-right">
                            <p class="text-sm font-medium text-gray-900">عميل {{ $i + 6 }}</p>
                            <p class="text-xs text-gray-600">2M - 3M ريال</p>
                        </div>
                        @endfor
                    </div>
                </div>

                <!-- Negotiation Stage -->
                <div class="text-center">
                    <div class="bg-orange-50 rounded-lg p-4">
                        <div class="text-3xl font-bold text-orange-600 mb-2">12</div>
                        <h4 class="font-semibold text-gray-900">تفاوض</h4>
                        <p class="text-sm text-gray-600 mt-1">جاري المفاوضات</p>
                    </div>
                    <div class="mt-3 space-y-2">
                        @for($i = 1; $i <= 3; $i++)
                        <div class="bg-white border border-gray-200 rounded-lg p-2 text-right">
                            <p class="text-sm font-medium text-gray-900">عميل {{ $i + 9 }}</p>
                            <p class="text-xs text-gray-600">3M - 5M ريال</p>
                        </div>
                        @endfor
                    </div>
                </div>

                <!-- Closed Stage -->
                <div class="text-center">
                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="text-3xl font-bold text-purple-600 mb-2">8</div>
                        <h4 class="font-semibold text-gray-900">مغلق</h4>
                        <p class="text-sm text-gray-600 mt-1">تم الصفقة</p>
                    </div>
                    <div class="mt-3 space-y-2">
                        @for($i = 1; $i <= 3; $i++)
                        <div class="bg-white border border-gray-200 rounded-lg p-2 text-right">
                            <p class="text-sm font-medium text-gray-900">عميل {{ $i + 12 }}</p>
                            <p class="text-xs text-gray-600">5M+ ريال</p>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Overview -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">نظرة عامة على العمولات</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="bg-green-100 p-4 rounded-lg mb-3">
                        <i class="fas fa-check-circle text-green-600 text-3xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900">العمولات المدفوعة</h4>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format(125000, 0) }} ريال</p>
                    <p class="text-sm text-green-600 mt-1">
                        <i class="fas fa-arrow-up ml-1"></i>15% من الشهر الماضي
                    </p>
                </div>

                <div class="text-center">
                    <div class="bg-yellow-100 p-4 rounded-lg mb-3">
                        <i class="fas fa-clock text-yellow-600 text-3xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900">العمولات المعلقة</h4>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format(85000, 0) }} ريال</p>
                    <p class="text-sm text-yellow-600 mt-1">
                        <i class="fas fa-hourglass-half ml-1"></i>قيد المراجعة
                    </p>
                </div>

                <div class="text-center">
                    <div class="bg-blue-100 p-4 rounded-lg mb-3">
                        <i class="fas fa-chart-line text-blue-600 text-3xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900">العمولات المتوقعة</h4>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format(150000, 0) }} ريال</p>
                    <p class="text-sm text-blue-600 mt-1">
                        <i class="fas fa-calendar ml-1"></i>هذا الشهر
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
