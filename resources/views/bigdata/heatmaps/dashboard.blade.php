@extends('admin.layouts.admin')

@section('title', 'لوحة تحكم الخرائط الحرارية')
@section('page-title', 'لوحة تحكم الخرائط الحرارية')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-tachometer-alt text-blue-500 ml-3"></i>
                    لوحة تحكم الخرائط الحرارية
                </h1>
                <p class="text-gray-600 mt-2">نظرة عامة على إحصائيات وبيانات الخرائط الحرارية</p>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('bigdata.heatmaps') }}" class="bg-white text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-50 transition-all duration-300 shadow-md hover:shadow-lg border border-gray-200">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للخرائط
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Heatmaps -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">إجمالي الخرائط</p>
                        <p class="text-3xl font-bold mt-2">{{ $heatmapStats['total_heatmaps'] ?? 0 }}</p>
                        <p class="text-blue-100 text-sm mt-2">
                            <i class="fas fa-map ml-1"></i>
                            خريطة نشطة
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-layer-group text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Active Regions -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">المناطق النشطة</p>
                        <p class="text-3xl font-bold mt-2">{{ $heatmapStats['active_regions'] ?? 0 }}</p>
                        <p class="text-green-100 text-sm mt-2">
                            <i class="fas fa-map-marker-alt ml-1"></i>
                            منطقة مغطاة
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-globe-americas text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Data Points -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">نقاط البيانات</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($heatmapStats['data_points'] ?? 0) }}</p>
                        <p class="text-purple-100 text-sm mt-2">
                            <i class="fas fa-database ml-1"></i>
                            نقطة تحليل
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-chart-network text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Last Update -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">آخر تحديث</p>
                        <p class="text-xl font-bold mt-2">{{ $heatmapStats['last_updated'] ?? 'غير متوفر' }}</p>
                        <p class="text-orange-100 text-sm mt-2">
                            <i class="fas fa-clock ml-1"></i>
                            تحديث تلقائي
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-sync text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access Cards -->
        <h2 class="text-2xl font-bold text-gray-800 mb-6 px-2">الوصول السريع</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Property Prices -->
            <a href="{{ route('bigdata.heatmaps.property-prices') }}" class="group bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-100 text-blue-600 p-3 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                        <i class="fas fa-home text-xl"></i>
                    </div>
                    <span class="bg-blue-50 text-blue-600 text-xs px-2 py-1 rounded-full border border-blue-100 group-hover:bg-blue-600 group-hover:text-white group-hover:border-blue-600 transition-colors">
                        {{ $heatmapStats['heatmap_types']['property_prices'] ?? 0 }} خريطة
                    </span>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">أسعار العقارات</h3>
                <p class="text-gray-500 text-sm mb-3">تحليل أسعار العقارات وتوزيعها الجغرافي في مختلف المناطق.</p>
                <span class="text-sm text-blue-500 font-medium group-hover:text-blue-600 transition-colors flex items-center">
                    عرض التفاصيل <i class="fas fa-arrow-left mr-1 transition-transform group-hover:-translate-x-1"></i>
                </span>
            </a>

            <!-- Market Demand -->
            <a href="{{ route('bigdata.heatmaps.market-demand') }}" class="group bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-green-100 text-green-600 p-3 rounded-xl group-hover:bg-green-600 group-hover:text-white transition-colors duration-300">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <span class="bg-green-50 text-green-600 text-xs px-2 py-1 rounded-full border border-green-100 group-hover:bg-green-600 group-hover:text-white group-hover:border-green-600 transition-colors">
                        {{ $heatmapStats['heatmap_types']['market_demand'] ?? 0 }} خريطة
                    </span>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">الطلب السوقي</h3>
                <p class="text-gray-500 text-sm mb-3">مؤشرات الطلب والعرض والاتجاهات الحالية في السوق العقاري.</p>
                <span class="text-sm text-green-500 font-medium group-hover:text-green-600 transition-colors flex items-center">
                    عرض التفاصيل <i class="fas fa-arrow-left mr-1 transition-transform group-hover:-translate-x-1"></i>
                </span>
            </a>

            <!-- Investment Opportunities -->
            <a href="{{ route('bigdata.heatmaps.investment-opportunities') }}" class="group bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-100 text-purple-600 p-3 rounded-xl group-hover:bg-purple-600 group-hover:text-white transition-colors duration-300">
                        <i class="fas fa-hand-holding-usd text-xl"></i>
                    </div>
                    <span class="bg-purple-50 text-purple-600 text-xs px-2 py-1 rounded-full border border-purple-100 group-hover:bg-purple-600 group-hover:text-white group-hover:border-purple-600 transition-colors">
                        {{ $heatmapStats['heatmap_types']['investment_opportunities'] ?? 0 }} خريطة
                    </span>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">فرص الاستثمار</h3>
                <p class="text-gray-500 text-sm mb-3">تحديد المناطق ذات العائد الاستثماري المرتفع والنمو المتوقع.</p>
                <span class="text-sm text-purple-500 font-medium group-hover:text-purple-600 transition-colors flex items-center">
                    عرض التفاصيل <i class="fas fa-arrow-left mr-1 transition-transform group-hover:-translate-x-1"></i>
                </span>
            </a>
        </div>

        <!-- Charts Section (Placeholder) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">توزيع الخرائط حسب النوع</h3>
                <div class="flex items-center justify-center h-64 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                    <div class="text-center text-gray-400">
                        <i class="fas fa-chart-pie text-4xl mb-2"></i>
                        <p>سيتم عرض الرسم البياني هنا</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">نشاط المناطق</h3>
                <div class="flex items-center justify-center h-64 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                    <div class="text-center text-gray-400">
                        <i class="fas fa-chart-bar text-4xl mb-2"></i>
                        <p>سيتم عرض الرسم البياني هنا</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
