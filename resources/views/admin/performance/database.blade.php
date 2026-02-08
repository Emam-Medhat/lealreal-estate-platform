@extends('admin.layouts.admin')

@section('title', 'أداء قاعدة البيانات')
@section('page-title', 'مراقبة قاعدة البيانات')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">أداء قاعدة البيانات</h2>
            <p class="text-gray-600 mt-2">تحليل مفصل لأداء واستعلامات قاعدة البيانات.</p>
        </div>

        <!-- Database Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Connection Info -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-database text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Connection</span>
                </div>
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">معلومات الاتصال</h3>
                    <p class="text-xl font-bold text-gray-800 mt-2" dir="ltr">{{ $metrics['connection'] ?? 'Unknown' }}</p>
                    <p class="text-sm text-gray-500 mt-1" dir="ltr">{{ $metrics['database'] ?? '' }} on {{ $metrics['host'] ?? '' }}</p>
                </div>
            </div>

            <!-- Active Connections -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-emerald-100 p-3 rounded-full">
                        <i class="fas fa-plug text-emerald-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Active</span>
                </div>
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">الاتصالات النشطة</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $metrics['active_connections'] ?? 0 }}</p>
                    <p class="text-sm text-gray-500 mt-1">الحد الأقصى: {{ $metrics['max_connections'] ?? 0 }}</p>
                </div>
            </div>

            <!-- Buffer Pool Hit Rate -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-tachometer-alt text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded-full">Buffer Pool</span>
                </div>
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">معدل الذاكرة المؤقتة</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($metrics['innodb_buffer_pool_hit_rate'] ?? 0, 2) }}%</p>
                    <p class="text-sm text-gray-500 mt-1">الحجم: {{ number_format(($metrics['innodb_buffer_pool_size'] ?? 0) / 1024 / 1024, 0) }} MB</p>
                </div>
            </div>
        </div>

        <!-- Detailed Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Query Statistics -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-search text-blue-500 ml-2"></i>
                    إحصائيات الاستعلامات
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">إجمالي الاستعلامات</td>
                                <td class="py-3 font-medium text-gray-800">{{ number_format($metrics['total_queries'] ?? 0) }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">استعلامات بطيئة</td>
                                <td class="py-3 font-medium {{ ($metrics['slow_queries'] ?? 0) > 0 ? 'text-red-500' : 'text-green-500' }}">
                                    {{ $metrics['slow_queries'] ?? 0 }}
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">معدل إصابة الكاش</td>
                                <td class="py-3 font-medium text-gray-800">{{ number_format($metrics['query_cache_hit_rate'] ?? 0, 2) }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- System Stats -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-server text-emerald-500 ml-2"></i>
                    إحصائيات النظام
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">أقفال الجدول (Table Locks)</td>
                                <td class="py-3 font-medium text-gray-800">{{ $metrics['table_locks_waited'] ?? 0 }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">التشابك (Deadlocks)</td>
                                <td class="py-3 font-medium text-gray-800">{{ $metrics['deadlock_count'] ?? 0 }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">وقت التشغيل</td>
                                <td class="py-3 font-medium text-blue-600" dir="ltr">{{ $metrics['uptime'] ?? 'Unknown' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
