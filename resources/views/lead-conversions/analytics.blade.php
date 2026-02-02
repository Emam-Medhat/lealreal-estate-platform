@extends('admin.layouts.admin')

@section('title', 'تحليلات التحويل')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تحليلات التحويل</h1>
            <p class="text-gray-600 mt-1">إحصائيات وتحليلات تحويل العملاء</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('lead-conversions.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2 space-x-reverse">
                <i class="fas fa-list"></i>
                <span>كل التحويلات</span>
            </a>
            <a href="{{ route('lead-conversions.funnel') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2 space-x-reverse">
                <i class="fas fa-filter"></i>
                <span>مسار التحويل</span>
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">إجمالي التحويلات</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['total_conversions'] }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-exchange-alt text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">معدل التحويل</p>
                <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['conversion_rate'], 2) }}%</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-percentage text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">متوسط وقت التحويل</p>
                <p class="text-2xl font-bold text-orange-600">{{ $stats['avg_conversion_time'] }} يوم</p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <i class="fas fa-clock text-orange-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">قيمة التحويلات</p>
                <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_conversion_value'], 2) }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-dollar-sign text-purple-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Monthly Conversions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">التحويلات الشهرية</h3>
        <div class="space-y-3">
            @forelse($monthlyConversions as $conversion)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-100 rounded-full mr-3"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">{{ $conversion->month }}</p>
                            <p class="text-xs text-gray-500">{{ $conversion->year }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">{{ $conversion->count }}</p>
                        <p class="text-xs text-gray-500">تحويلات</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-sm">لا توجد بيانات</p>
            @endforelse
        </div>
    </div>

    <!-- Conversion by Source -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">التحويلات حسب المصدر</h3>
        <div class="space-y-3">
            @forelse($conversionBySource as $source)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-100 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-700">{{ $source->source_name }}</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">{{ $source->count }}</p>
                        <p class="text-xs text-gray-500">تحويلات</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-sm">لا توجد بيانات</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Conversion by Type -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">التحويلات حسب النوع</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($conversionByType as $type)
                <div class="text-center p-6 bg-gray-50 rounded-lg">
                    <div class="mb-4">
                        <i class="fas 
                            @if($type->converted_to_type == 'client') fa-user text-blue-600
                            @elseif($type->converted_to_type == 'opportunity') fa-lightbulb text-green-600
                            @else fa-home text-orange-600
                            @endif text-4xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">
                        @if($type->converted_to_type == 'client') عملاء
                        @elseif($type->converted_to_type == 'opportunity') فرص
                        @else عقارات
                        @endif
                    </h4>
                    <p class="text-2xl font-bold text-gray-600">{{ $type->count }}</p>
                    <p class="text-sm text-gray-500">تحويلات</p>
                </div>
            @empty
                <div class="text-center py-8">
                    <i class="fas fa-chart-pie text-gray-300 text-4xl mb-4"></i>
                    <p class="text-gray-500">لا توجد بيانات</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Performance Metrics -->
<div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">مؤشرات الأداء</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="text-center">
            <div class="bg-blue-50 rounded-lg p-4">
                <i class="fas fa-tachometer-alt text-blue-600 text-2xl mb-2"></i>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['best_month_conversions'] }}</p>
                <p class="text-sm text-gray-600">أفضل شهر</p>
            </div>
        </div>
        
        <div class="text-center">
            <div class="bg-green-50 rounded-lg p-4">
                <i class="fas fa-award text-green-600 text-2xl mb-2"></i>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['best_source'] }}</p>
                <p class="text-sm text-gray-600">أفضل مصدر</p>
            </div>
        </div>
        
        <div class="text-center">
            <div class="bg-orange-50 rounded-lg p-4">
                <i class="fas fa-chart-line text-orange-600 text-2xl mb-2"></i>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['conversion_trend'] }}%</p>
                <p class="text-sm text-gray-600">اتجاه التحويل</p>
            </div>
        </div>
        
        <div class="text-center">
            <div class="bg-purple-50 rounded-lg p-4">
                <i class="fas fa-trophy text-purple-600 text-2xl mb-2"></i>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['top_conversion_type'] }}</p>
                <p class="text-sm text-gray-600">أكثر نوع تحويل</p>
            </div>
        </div>
    </div>
</div>
@endsection
