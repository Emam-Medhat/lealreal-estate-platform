@extends('layouts.app')

@section('title', 'موارد النظام (System Resources)')

@section('content')
<div class="container mx-auto px-4 py-8">
<!-- Page Header -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-slate-700 to-slate-900 rounded-2xl shadow-2xl p-8 text-white relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -mr-32 -mt-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full -ml-24 -mb-24"></div>
        </div>
        
        <div class="relative z-10">
            <div class="flex justify-between items-center">
                <div>
                    <div class="flex items-center mb-3">
                        <div class="bg-white bg-opacity-20 rounded-xl p-3 ml-4 backdrop-blur-sm border border-white border-opacity-30">
                            <i class="fas fa-server text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold text-white mb-1">موارد النظام</h1>
                            <p class="text-white text-lg">مراقبة موارد النظام والخادم</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 mt-4">
                        <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2 backdrop-blur-sm border border-white border-opacity-30">
                            <span class="text-sm text-white">CPU:</span>
                            <span class="text-sm font-bold text-white">{{ $metrics['cpu_usage'] ?? 0 }}%</span>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2 backdrop-blur-sm border border-white border-opacity-30">
                            <span class="text-sm text-white">الذاكرة:</span>
                            <span class="text-sm font-bold text-white">{{ $metrics['memory_usage'] ?? 0 }}MB</span>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2 backdrop-blur-sm border border-white border-opacity-30">
                            <span class="text-sm text-white">القرص:</span>
                            <span class="text-sm font-bold text-white">{{ $metrics['disk_usage']['percentage'] ?? 0 }}%</span>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-reverse space-x-3">
                    <a href="{{ route('admin.performance.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-white text-black rounded-xl text-sm font-medium hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-arrow-left ml-2"></i>
                        العودة للوحة الأداء
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- Resources Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- CPU Usage -->
    <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-300 p-6 transform hover:scale-105 transition-all duration-300 relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-blue-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-black text-sm font-bold mb-2">استهلاك المعالج</p>
                    <p class="text-4xl font-black text-black">{{ $metrics['cpu_usage'] ?? 0 }}%</p>
                    <div class="mt-2 flex items-center text-sm text-black">
                        <i class="fas fa-microchip ml-1 text-blue-600"></i>
                        <span>CPU Usage</span>
                    </div>
                </div>
                <div class="bg-blue-100 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 border-2 border-blue-200">
                    <i class="fas fa-microchip text-blue-700 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Memory Usage -->
    <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-300 p-6 transform hover:scale-105 transition-all duration-300 relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-emerald-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-black text-sm font-bold mb-2">الذاكرة</p>
                    <p class="text-4xl font-black text-black">{{ $metrics['memory_usage'] ?? 0 }}</p>
                    <div class="mt-2 flex items-center text-sm text-black">
                        <i class="fas fa-memory ml-1 text-emerald-600"></i>
                        <span>MB Used</span>
                    </div>
                </div>
                <div class="bg-emerald-100 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 border-2 border-emerald-200">
                    <i class="fas fa-memory text-emerald-700 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Disk Usage -->
    <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-300 p-6 transform hover:scale-105 transition-all duration-300 relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-amber-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-black text-sm font-bold mb-2">القرص الصلب</p>
                    <p class="text-4xl font-black text-black">{{ $metrics['disk_usage']['percentage'] ?? 0 }}%</p>
                    <div class="mt-2 flex items-center text-sm text-black">
                        <i class="fas fa-hdd ml-1 text-amber-600"></i>
                        <span>Disk Used</span>
                    </div>
                </div>
                <div class="bg-amber-100 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 border-2 border-amber-200">
                    <i class="fas fa-hdd text-amber-700 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Uptime -->
    <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-300 p-6 transform hover:scale-105 transition-all duration-300 relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-purple-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-black text-sm font-bold mb-2">وقت التشغيل</p>
                    <p class="text-2xl font-black text-black" dir="ltr">{{ $metrics['uptime'] ?? 'Unknown' }}</p>
                    <div class="mt-2 flex items-center text-sm text-black">
                        <i class="fas fa-clock ml-1 text-purple-600"></i>
                        <span>System Uptime</span>
                    </div>
                </div>
                <div class="bg-purple-100 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 border-2 border-purple-200">
                    <i class="fas fa-clock text-purple-700 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- Detailed System Info -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Load Average -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-black mb-6 flex items-center">
            <div class="bg-slate-600 rounded-lg p-2 ml-3 shadow-md">
                <i class="fas fa-tachometer-alt text-white"></i>
            </div>
            متوسط الحمل (Load Average)
        </h3>
        <div class="grid grid-cols-3 gap-4 text-center">
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                <h4 class="text-black font-medium mb-2">1 دقيقة</h4>
                <div class="text-2xl font-black text-black">{{ $metrics['load_average']['1min'] ?? 0 }}</div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                <h4 class="text-black font-medium mb-2">5 دقائق</h4>
                <div class="text-2xl font-black text-black">{{ $metrics['load_average']['5min'] ?? 0 }}</div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                <h4 class="text-black font-medium mb-2">15 دقيقة</h4>
                <div class="text-2xl font-black text-black">{{ $metrics['load_average']['15min'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Process Info -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-black mb-6 flex items-center">
            <div class="bg-slate-600 rounded-lg p-2 ml-3 shadow-md">
                <i class="fas fa-tasks text-white"></i>
            </div>
            معلومات العمليات
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 text-black">عدد العمليات (Processes)</td>
                        <td class="py-3 font-medium text-black">{{ $metrics['processes'] ?? 0 }}</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 text-black">واصفات الملفات (File Descriptors)</td>
                        <td class="py-3 font-medium text-black">{{ $metrics['file_descriptors'] ?? 0 }}</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 text-black">القرص المستخدم (مساحة)</td>
                        <td class="py-3 font-medium text-black">{{ $metrics['disk_usage']['used'] ?? 0 }} / {{ $metrics['disk_usage']['total'] ?? 0 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Actions -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 mb-8">
    <h3 class="text-lg font-semibold text-black mb-6 flex items-center">
        <div class="bg-slate-600 rounded-lg p-2 ml-3 shadow-md">
            <i class="fas fa-cogs text-white"></i>
        </div>
        إجراءات النظام
    </h3>
    <div class="flex flex-wrap gap-4">
        <a href="{{ route('admin.performance.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-slate-600 text-white rounded-xl text-sm font-medium hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
            <i class="fas fa-arrow-left ml-2"></i>
            العودة للوحة الأداء
        </a>
        
        <form action="{{ route('admin.performance.clear_cache') }}" method="POST" class="inline-block">
            @csrf
            <button type="submit" class="inline-flex items-center px-6 py-3 bg-amber-600 text-white rounded-xl text-sm font-medium hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                <i class="fas fa-broom ml-2"></i>
                تنظيف الكاش
            </button>
        </form>
    </div>
</div>

</div>
@endsection
