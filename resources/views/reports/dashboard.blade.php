@extends('layouts.dashboard')

@section('title', 'لوحة التقارير')

@section('content')

<div class="max-w-7xl mx-auto">
    <!-- Reports Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">لوحة التقارير</h1>
                <p class="text-gray-600">إدارة وتحليل تقارير العقارات</p>
            </div>
            <div class="flex items-center space-x-2 space-x-reverse">
                <a href="{{ route('reports.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus ml-2"></i>
                    تقرير جديد
                </a>
                <a href="{{ route('reports.index') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-list ml-2"></i>
                    كل التقارير
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">إجمالي التقارير</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $reportStats['total_reports'] ?? 0 }}</h3>
                    <p class="text-xs text-green-600">+5 هذا الشهر</p>
                </div>
                <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
                    <i class="fas fa-file-alt text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">التقارير المنجزة</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $reportStats['completed_reports'] ?? 0 }}</h3>
                    <p class="text-xs text-green-600">+3 هذا الأسبوع</p>
                </div>
                <div class="bg-green-100 text-green-600 p-3 rounded-full">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">قيد المعالجة</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $reportStats['pending_reports'] ?? 0 }}</h3>
                    <p class="text-xs text-orange-600">2 عاجل</p>
                </div>
                <div class="bg-purple-100 text-purple-600 p-3 rounded-full">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">معدل النجاح</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $reportStats['success_rate'] ?? 0 }}%</h3>
                    <p class="text-xs text-green-600">+2% هذا الشهر</p>
                </div>
                <div class="bg-orange-100 text-orange-600 p-3 rounded-full">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">التقارير الحديثة</h2>
        <div class="space-y-4">
            @forelse ($recentReports as $report)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center">
                        <div class="bg-blue-100 text-blue-600 p-2 rounded-full ml-3">
                            <i class="fas fa-file-alt text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $report->title ?? 'تقرير غير مسمى' }}</p>
                            <p class="text-sm text-gray-500">{{ $report->type ?? 'عام' }} • {{ $report->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $report->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $report->status === 'completed' ? 'مكتمل' : 'قيد المعالجة' }}
                        </span>
                        <a href="{{ route('reports.show', $report->id) }}" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-file-alt text-4xl mb-4 text-gray-300"></i>
                    <p>لا توجد تقارير حديثة</p>
                    <a href="{{ route('reports.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                        إنشاء أول تقرير
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="text-center">
                <div class="bg-blue-100 text-blue-600 p-4 rounded-full inline-block mb-4">
                    <i class="fas fa-chart-bar text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">تقارير المبيعات</h3>
                <p class="text-sm text-gray-600 mb-4">تحليلات شاملة للمبيعات</p>
                <a href="{{ route('reports.sales.index') }}" class="text-blue-600 font-medium hover:text-blue-800">
                    عرض التقارير ←
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="text-center">
                <div class="bg-green-100 text-green-600 p-4 rounded-full inline-block mb-4">
                    <i class="fas fa-tachometer-alt text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">تقارير الأداء</h3>
                <p class="text-sm text-gray-600 mb-4">قياسات الأداء والكفاءة</p>
                <a href="{{ route('reports.performance.index') }}" class="text-green-600 font-medium hover:text-green-800">
                    عرض التقارير ←
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="text-center">
                <div class="bg-purple-100 text-purple-600 p-4 rounded-full inline-block mb-4">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">تقارير السوق</h3>
                <p class="text-sm text-gray-600 mb-4">تحليلات السوق والاتجاهات</p>
                <a href="{{ route('reports.market.index') }}" class="text-purple-600 font-medium hover:text-purple-800">
                    عرض التقارير ←
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
