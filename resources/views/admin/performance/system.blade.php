@extends('admin.layouts.admin')

@section('title', 'موارد النظام (System Resources)')
@section('page-title', 'مراقبة موارد النظام')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">موارد النظام</h2>
            <p class="text-gray-600 mt-2">تحليل شامل لاستخدام موارد الخادم والنظام.</p>
        </div>

        <!-- Resources Overview -->
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
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $metrics['cpu_usage'] ?? 0 }}%</p>
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
                    <h3 class="text-gray-500 text-sm font-medium">الذاكرة</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $metrics['memory_usage'] ?? 0 }} <span class="text-sm text-gray-500">MB</span></p>
                </div>
            </div>

            <!-- Disk Usage -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-amber-100 p-3 rounded-full">
                        <i class="fas fa-hdd text-amber-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-full">Disk</span>
                </div>
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">القرص الصلب</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $metrics['disk_usage']['percentage'] ?? 0 }}%</p>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-3">
                        <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $metrics['disk_usage']['percentage'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Uptime -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-clock text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded-full">Uptime</span>
                </div>
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">وقت التشغيل</h3>
                    <p class="text-xl font-bold text-gray-800 mt-2" dir="ltr">{{ $metrics['uptime'] ?? 'Unknown' }}</p>
                </div>
            </div>
        </div>

        <!-- Detailed System Info -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Load Average -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-tachometer-alt text-blue-500 ml-2"></i>
                    متوسط الحمل (Load Average)
                </h3>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h4 class="text-gray-500 font-medium mb-2">1 دقيقة</h4>
                        <div class="text-2xl font-bold text-gray-800">{{ $metrics['load_average']['1min'] ?? 0 }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h4 class="text-gray-500 font-medium mb-2">5 دقائق</h4>
                        <div class="text-2xl font-bold text-gray-800">{{ $metrics['load_average']['5min'] ?? 0 }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h4 class="text-gray-500 font-medium mb-2">15 دقيقة</h4>
                        <div class="text-2xl font-bold text-gray-800">{{ $metrics['load_average']['15min'] ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <!-- Process Info -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-tasks text-emerald-500 ml-2"></i>
                    معلومات العمليات
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">عدد العمليات (Processes)</td>
                                <td class="py-3 font-medium text-gray-800">{{ $metrics['processes'] ?? 0 }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">واصفات الملفات (File Descriptors)</td>
                                <td class="py-3 font-medium text-gray-800">{{ $metrics['file_descriptors'] ?? 0 }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">القرص المستخدم (مساحة)</td>
                                <td class="py-3 font-medium text-gray-800">{{ $metrics['disk_usage']['used'] ?? 0 }} / {{ $metrics['disk_usage']['total'] ?? 0 }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-6">إجراءات النظام</h3>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('admin.performance.dashboard') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2.5 rounded-xl transition-all duration-300 flex items-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للوحة الأداء
                </a>
                
                <form action="{{ route('admin.performance.clear_cache') }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2.5 rounded-xl transition-all duration-300 flex items-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <i class="fas fa-broom ml-2"></i>
                        تنظيف الكاش
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
