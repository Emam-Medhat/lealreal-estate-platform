@extends('admin.layouts.admin')

@section('title', 'لوحة تحكم DeFi')
@section('page-title', 'نظرة عامة DeFi')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-coins text-violet-500 ml-3"></i>
                    لوحة تحكم DeFi
                </h1>
                <p class="text-gray-600 mt-2">نظرة شاملة على منصة التمويل اللامركزي اللامركزي</p>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('defi.crowdfunding.index') }}" class="bg-gradient-to-r from-violet-500 to-violet-600 text-white px-6 py-3 rounded-lg hover:from-violet-600 hover:to-violet-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-users ml-2"></i>
                    التمويل الجماعي
                </a>
                <a href="{{ route('defi.loans.index') }}" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-hand-holding-usd ml-2"></i>
                    قروض DeFi
                </a>
                <a href="{{ route('defi.risk-assessment.index') }}" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-exclamation-triangle ml-2"></i>
                    تقييم المخاطر
                </a>
            </div>
        </div>

        <!-- Overview Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-violet-500 to-violet-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-violet-100 text-sm font-medium">إجمالي الاستثمارات</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($overview['total_investments']) }}</p>
                        <p class="text-violet-100 text-sm mt-2">
                            <i class="fas fa-chart-line ml-1"></i>
                            +{{ number_format($overview['monthly_revenue']) }} هذا الشهر
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-coins text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">القروض النشطة</p>
                        <p class="text-3xl font-bold mt-2">{{ $overview['active_loans'] }}</p>
                        <p class="text-blue-100 text-sm mt-2">
                            <i class="fas fa-hand-holding-usd ml-1"></i>
                            متوسط القرض: {{ number_format($overview['total_loan_amount'] / $overview['active_loans']) }} ريال
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-hand-holding-usd text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">معدد العقارات</p>
                        <p class="text-3xl font-bold mt-2">{{ $overview['total_properties'] }}</p>
                        <p class="text-green-100 text-sm mt-2">
                            <i class="fas fa-building ml-1"></i>
                            {{ $overview['active_campaigns'] }} حملات نشطة
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-building text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">معدد المستخدمين</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($overview['total_users']) }}</p>
                        <p class="text-orange-100 text-sm mt-2">
                            <i class="fas fa-users ml-1"></i>
                            نمو {{ $overview['platform_growth'] }}% هذا الشهر
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-chart-line text-blue-500 ml-3"></i>
                        مؤشرات الأداء
                    </h3>
                    <div class="bg-blue-100 rounded-full p-2">
                        <i class="fas fa-chart-pie text-blue-600"></i>
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="text-center">
                        <h4 class="text-2xl font-bold text-blue-600">{{ $performance['roi_average'] }}%</h4>
                        <p class="text-sm text-gray-600 mt-2">متوسط العائد</p>
                    </div>
                    <div class="text-center">
                        <h4 class="text-2xl font-bold text-green-600">{{ $performance['default_rate'] }}%</h4>
                        <p class="text-sm text-gray-600 mt-2">معدم التخلف</p>
                    </div>
                    <div class="text-center">
                        <h4 class="text-2xl font-bold text-purple-600">{{ $performance['liquidity_ratio'] }}%</h4>
                        <p class="text-sm text-gray-600 mt-2">نسبة السيولة</p>
                    </div>
                    <div class="text-center">
                        <h4 class="text-2xl font-bold text-amber-600">{{ $performance['profit_margin'] }}%</h4>
                        <p class="text-sm text-gray-600 mt-2">هامش الربح</p>
                    </div>
                    <div class="text-center">
                        <h4 class="text-2xl font-bold text-cyan-600">{{ $performance['user_satisfaction'] }}%</h4>
                        <p class="text-sm text-gray-600 mt-2">رضا العملاء</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-trophy text-yellow-500 ml-3"></i>
                        أفضل الأداء
                    </h3>
                    <div class="bg-yellow-100 rounded-full p-2">
                        <i class="fas fa-star text-yellow-600"></i>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-green-50 to-green-100 rounded-lg border border-green-200">
                        <div>
                            <h6 class="font-bold text-green-800">أعلى عائد</h6>
                            <p class="text-xs text-green-600">مشروع سكني الرياض</p>
                        </div>
                        <span class="bg-green-500 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">25.8%</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg border border-blue-200">
                        <div>
                            <h6 class="font-bold text-blue-800">أقل مخاطرة</h6>
                            <p class="text-xs text-blue-600">مجمع تجاري جدة</p>
                        </div>
                        <span class="bg-blue-500 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">1.2%</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg border border-purple-200">
                        <div>
                            <h6 class="font-bold text-purple-800">أكثر شعبية</h6>
                            <p class="text-xs text-purple-600">مشاركين نشطون</p>
                        </div>
                        <span class="bg-purple-500 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">342</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-clock text-orange-500 ml-3"></i>
                    النشاط الأخير
                </h3>
                <div class="bg-orange-100 rounded-full p-2">
                    <i class="fas fa-history text-orange-600"></i>
                </div>
            </div>
            <div class="space-y-4">
                @foreach($recentActivity as $activity)
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div>
                        <h6 class="font-medium text-gray-800">{{ $activity['description'] }}</h6>
                        <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                    </div>
                    <span class="bg-{{ $activity['color'] }}-100 text-{{ $activity['color'] }}-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                        <i class="fas fa-{{ $activity['icon'] }} ml-1"></i>
                        {{ $activity['type'] == 'investment' ? 'استثمار' : ($activity['type'] == 'loan' ? 'قرض' : 'تقييم') }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Trends Section -->
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-chart-area text-blue-500 ml-3"></i>
                    اتجاهات المنصة
                </h3>
                <div class="bg-blue-100 rounded-full p-2">
                    <i class="fas fa-chart-line text-blue-600"></i>
                </div>
            </div>
            
            <!-- Investment Trends -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-bold text-violet-600">
                        <i class="fas fa-coins ml-2"></i>
                        اتجاهات الاستثمار
                    </h4>
                    <span class="bg-violet-100 text-violet-800 text-xs font-medium px-3 py-1 rounded-full">
                        آخر 30 يوم
                    </span>
                </div>
                <div class="bg-gradient-to-r from-violet-50 to-violet-100 rounded-xl p-6 border border-violet-200">
                    <div class="flex items-end justify-between h-32 space-x-reverse space-x-2">
                        @foreach($trends['investment_trend'] as $data)
                        <div class="flex-1 flex flex-col items-center">
                            <div class="text-xs text-gray-600 mb-2">{{ \Carbon\Carbon::parse($data['date'])->format('d M') }}</div>
                            <div class="w-full flex items-end justify-center h-20">
                                <div class="bg-gradient-to-t from-violet-500 to-violet-400 rounded-t-lg hover:from-violet-600 hover:to-violet-500 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1" 
                                     style="height: {{ max(20, min(100, ($data['total'] / 1000000) * 100)) }}%; width: 20px;"
                                     title="{{ number_format($data['total']) }} ريال">
                                </div>
                            </div>
                            <div class="text-xs font-bold text-violet-700 mt-2">{{ number_format($data['total'] / 1000) }}K</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Loan Trends -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-bold text-blue-600">
                        <i class="fas fa-hand-holding-usd ml-2"></i>
                        اتجاهات القروض
                    </h4>
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">
                        آخر 30 يوم
                    </span>
                </div>
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                    <div class="flex items-end justify-between h-32 space-x-reverse space-x-2">
                        @foreach($trends['loan_trend'] as $data)
                        <div class="flex-1 flex flex-col items-center">
                            <div class="text-xs text-gray-600 mb-2">{{ \Carbon\Carbon::parse($data['date'])->format('d M') }}</div>
                            <div class="w-full flex items-end justify-center h-20">
                                <div class="bg-gradient-to-t from-blue-500 to-blue-400 rounded-t-lg hover:from-blue-600 hover:to-blue-500 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1" 
                                     style="height: {{ max(20, min(100, ($data['total'] / 300000) * 100)) }}%; width: 20px;"
                                     title="{{ number_format($data['total']) }} ريال">
                                </div>
                            </div>
                            <div class="text-xs font-bold text-blue-700 mt-2">{{ number_format($data['total'] / 1000) }}K</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Risk Trends -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-bold text-orange-600">
                        <i class="fas fa-exclamation-triangle ml-2"></i>
                        اتجاهات المخاطر
                    </h4>
                    <span class="bg-orange-100 text-orange-800 text-xs font-medium px-3 py-1 rounded-full">
                        آخر 30 يوم
                    </span>
                </div>
                <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-xl p-6 border border-orange-200">
                    <div class="flex items-end justify-between h-32 space-x-reverse space-x-2">
                        @foreach($trends['risk_trend'] as $data)
                        <div class="flex-1 flex flex-col items-center">
                            <div class="text-xs text-gray-600 mb-2">{{ \Carbon\Carbon::parse($data['date'])->format('d M') }}</div>
                            <div class="w-full flex items-end justify-center h-20">
                                <div class="bg-gradient-to-t from-orange-500 to-orange-400 rounded-t-lg hover:from-orange-600 hover:to-orange-500 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1" 
                                     style="height: {{ max(20, $data['avg_score']) }}%; width: 20px;"
                                     title="درجة المخاطر: {{ $data['avg_score'] }}%">
                                </div>
                            </div>
                            <div class="text-xs font-bold text-orange-700 mt-2">{{ round($data['avg_score']) }}%</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
