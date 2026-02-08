@extends('admin.layouts.admin')

@section('title', 'لوحة الأداء')
@section('page-title', 'مراقبة الأداء')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">لوحة تحكم الأداء</h2>
            <p class="text-gray-600 mt-2">مراقبة حية لأداء النظام والمقاييس الحيوية.</p>
        </div>

        <!-- System Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- CPU Usage -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-microchip text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">CPU</span>
                </div>
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">استهلاك المعالج</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $metrics['system']['cpu_usage'] ?? 0 }}%</p>
                </div>
            </div>

            <!-- Memory Usage -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-emerald-100 p-3 rounded-full">
                        <i class="fas fa-memory text-emerald-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">RAM</span>
                </div>
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">الذاكرة المستخدمة</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $metrics['memory']['current_usage'] ?? 0 }} <span class="text-sm text-gray-500">MB</span></p>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-3">
                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $metrics['memory']['usage_percentage'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Cache Hit Rate -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-bolt text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded-full">Cache</span>
                </div>
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">معدل إصابة الكاش</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $metrics['cache']['hit_rate'] ?? 0 }}%</p>
                </div>
            </div>

            <!-- Active Connections -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-amber-100 p-3 rounded-full">
                        <i class="fas fa-network-wired text-amber-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-full">DB</span>
                </div>
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">الاتصالات النشطة</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $metrics['database']['active_connections'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Charts & Details Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Core Web Vitals -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-800">مؤشرات الويب الأساسية (Core Web Vitals)</h3>
                    <div class="bg-gray-100 p-2 rounded-lg">
                        <i class="fas fa-chart-bar text-gray-500"></i>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- LCP -->
                    <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-100">
                        <h4 class="text-gray-500 font-medium mb-2">LCP</h4>
                        <div class="text-2xl font-bold {{ ($metrics['performance']['largest_contentful_paint'] ?? 0) < 2500 ? 'text-green-600' : 'text-amber-600' }} mb-1">
                            {{ $metrics['performance']['largest_contentful_paint'] ?? 0 }}ms
                        </div>
                        <p class="text-xs text-gray-400">Largest Contentful Paint</p>
                    </div>

                    <!-- FID -->
                    <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-100">
                        <h4 class="text-gray-500 font-medium mb-2">FID</h4>
                        <div class="text-2xl font-bold {{ ($metrics['performance']['first_input_delay'] ?? 0) < 100 ? 'text-green-600' : 'text-amber-600' }} mb-1">
                            {{ $metrics['performance']['first_input_delay'] ?? 0 }}ms
                        </div>
                        <p class="text-xs text-gray-400">First Input Delay</p>
                    </div>

                    <!-- CLS -->
                    <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-100">
                        <h4 class="text-gray-500 font-medium mb-2">CLS</h4>
                        <div class="text-2xl font-bold {{ ($metrics['performance']['cumulative_layout_shift'] ?? 0) < 0.1 ? 'text-green-600' : 'text-amber-600' }} mb-1">
                            {{ $metrics['performance']['cumulative_layout_shift'] ?? 0 }}
                        </div>
                        <p class="text-xs text-gray-400">Cumulative Layout Shift</p>
                    </div>
                </div>
            </div>

            <!-- System Info -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-800">معلومات النظام</h3>
                    <div class="bg-gray-100 p-2 rounded-lg">
                        <i class="fas fa-server text-gray-500"></i>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">وقت التشغيل</span>
                        <span class="font-mono text-blue-600 font-bold">{{ $metrics['system']['uptime'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">متوسط الحمل (1د)</span>
                        <span class="font-mono text-gray-800 font-bold">{{ $metrics['system']['load_average']['1min'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">إصدار PHP</span>
                        <span class="font-mono text-gray-800 font-bold">{{ phpversion() }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">إصدار Laravel</span>
                        <span class="font-mono text-red-500 font-bold">{{ app()->version() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database & Requests Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Database Metrics -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-database text-blue-500 ml-2"></i>
                    مقاييس قاعدة البيانات
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">الاتصال</td>
                                <td class="py-3 font-medium text-gray-800" dir="ltr">{{ $metrics['database']['connection'] ?? 'N/A' }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">حجم قاعدة البيانات</td>
                                <td class="py-3 font-medium text-gray-800" dir="ltr">{{ $metrics['database']['size'] ?? 'N/A' }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">إجمالي الاستعلامات</td>
                                <td class="py-3 font-medium text-gray-800">{{ number_format($metrics['queries']['total_queries'] ?? 0) }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">استعلامات بطيئة</td>
                                <td class="py-3 font-medium text-red-500">{{ $metrics['queries']['slow_queries'] ?? 0 }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Request Metrics -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-globe text-emerald-500 ml-2"></i>
                    مقاييس الطلبات (Requests)
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">إجمالي الطلبات</td>
                                <td class="py-3 font-medium text-gray-800">{{ number_format($metrics['requests']['total_requests'] ?? 0) }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">متوسط زمن الاستجابة</td>
                                <td class="py-3 font-medium text-gray-800">{{ $metrics['requests']['average_response_time'] ?? 0 }}ms</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">طلبات/ثانية</td>
                                <td class="py-3 font-medium text-gray-800">{{ $metrics['requests']['requests_per_second'] ?? 0 }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">معدل الخطأ</td>
                                <td class="py-3 font-medium {{ ($metrics['requests']['error_rate'] ?? 0) > 1 ? 'text-red-500' : 'text-green-500' }}">
                                    {{ $metrics['requests']['error_rate'] ?? 0 }}%
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Actions Section -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-6">إجراءات سريعة</h3>
            <div class="flex flex-wrap gap-4">
                <form action="{{ route('admin.performance.clear_cache') }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2.5 rounded-xl transition-all duration-300 flex items-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <i class="fas fa-broom ml-2"></i>
                        تنظيف الكاش
                    </button>
                </form>
                
                <a href="{{ route('admin.system.cache') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2.5 rounded-xl transition-all duration-300 flex items-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-cog ml-2"></i>
                    إعدادات الكاش
                </a>
                
                <a href="{{ route('admin.system.logs') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2.5 rounded-xl transition-all duration-300 flex items-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-list ml-2"></i>
                    سجلات النظام
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
