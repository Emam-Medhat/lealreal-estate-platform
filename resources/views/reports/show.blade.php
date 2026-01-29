@extends('layouts.app')

@section('title', 'عرض التقرير')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ $report->title }}</h1>
            <p class="text-gray-600 mt-2">{{ $report->description }}</p>
        </div>
        <div class="flex space-x-4 space-x-reverse">
            <a href="{{ route('reports.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة
            </a>
            @if ($report->status === 'completed' && $report->file_path)
                <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-download ml-2"></i>
                    تحميل
                </a>
            @endif
        </div>
    </div>

    <!-- Report Info -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-500">النوع</p>
                    <p class="text-lg font-semibold">{{ $report->type === 'sales' ? 'المبيعات' : ($report->type === 'performance' ? 'الأداء' : 'السوق') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-500">الحالة</p>
                    <p class="text-lg font-semibold">{{ $report->status === 'completed' ? 'مكتمل' : ($report->status === 'generating' ? 'قيد الإنشاء' : 'فشل') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-file-pdf text-purple-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-500">التنسيق</p>
                    <p class="text-lg font-semibold">{{ strtoupper($report->format) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    @if(isset($stats))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">إجمالي المبيعات</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats->total_sales) }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-shopping-cart text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">القيمة الإجمالية</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats->total_value) }} ريال</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-dollar-sign text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">متوسط السعر</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats->average_price) }} ريال</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-chart-line text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">العقارات المباعة</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats->properties_sold) }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-home text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Charts -->
    @if(isset($charts))
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">اتجاه المبيعات</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded">
                <div class="text-center">
                    <i class="fas fa-chart-line text-4xl text-gray-400 mb-2"></i>
                    <p class="text-gray-500">رسم بياني لاتجاه المبيعات</p>
                    <p class="text-sm text-gray-400 mt-2">{{ implode(', ', $charts['sales_trend']['labels']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">أنواع العقارات</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded">
                <div class="text-center">
                    <i class="fas fa-chart-pie text-4xl text-gray-400 mb-2"></i>
                    <p class="text-gray-500">توزيع أنواع العقارات</p>
                    <p class="text-sm text-gray-400 mt-2">{{ implode(', ', $charts['property_types']['labels']) }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Report Details -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">تفاصيل التقرير</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">تاريخ الإنشاء</p>
                <p class="font-medium">{{ $report->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">آخر تحديث</p>
                <p class="font-medium">{{ $report->updated_at->format('Y-m-d H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">عدد المشاهدات</p>
                <p class="font-medium">{{ $report->view_count }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">عدد التحميلات</p>
                <p class="font-medium">{{ $report->download_count ?? 0 }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">حجم الملف</p>
                <p class="font-medium">{{ number_format($report->file_size / 1024 / 1024, 2) }} MB</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">المعرف</p>
                <p class="font-medium">#{{ $report->id }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
