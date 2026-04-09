@extends('admin.layouts.admin')

@section('title', 'تحليل الاستعلامات (Queries)')
@section('page-title', 'أداء الاستعلامات')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">تحليل الاستعلامات</h2>
            <p class="text-gray-600 mt-2">مراقبة أداء استعلامات قاعدة البيانات وتحديد البطء.</p>
        </div>

        <!-- Queries Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Queries -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-list-ol text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Total</span>
                </div>
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">إجمالي الاستعلامات</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($metrics['total_queries'] ?? 0) }}</p>
                    <p class="text-sm text-gray-500 mt-1">المعدل: {{ number_format($metrics['queries_per_second'] ?? 0, 2) }} / ثانية</p>
                </div>
            </div>

            <!-- Slow Queries -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 rounded-full">Slow</span>
                </div>
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">استعلامات بطيئة</h3>
                    <p class="text-3xl font-bold {{ ($metrics['slow_queries'] ?? 0) > 0 ? 'text-red-500' : 'text-green-500' }} mt-2">
                        {{ number_format($metrics['slow_queries'] ?? 0) }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1">تتجاوز الحد المسموح</p>
                </div>
            </div>

            <!-- Query Cache -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-emerald-100 p-3 rounded-full">
                        <i class="fas fa-bolt text-emerald-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Cache</span>
                </div>
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">معدل كاش الاستعلامات</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($metrics['query_cache_hit_rate'] ?? 0, 2) }}%</p>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-3">
                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $metrics['query_cache_hit_rate'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Analysis -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Slowest Queries List -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-clock text-amber-500 ml-2"></i>
                    أبطأ الاستعلامات
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 rounded-tr-lg">الاستعلام</th>
                                <th class="px-4 py-3 rounded-tl-lg">الزمن (ms)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($metrics['slowest_queries'] ?? [] as $query)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-600 font-mono" dir="ltr">{{ Str::limit($query['sql'] ?? 'Unknown', 50) }}</td>
                                    <td class="px-4 py-3 font-medium text-red-500">{{ number_format($query['time'] ?? 0, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-8 text-center text-gray-500">
                                        <i class="fas fa-check-circle text-green-500 text-3xl mb-2 block"></i>
                                        لا توجد استعلامات بطيئة مسجلة
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Frequent Queries List -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-sync text-blue-500 ml-2"></i>
                    الاستعلامات الأكثر تكراراً
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 rounded-tr-lg">الاستعلام</th>
                                <th class="px-4 py-3 rounded-tl-lg">التكرار</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($metrics['most_frequent_queries'] ?? [] as $query)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-600 font-mono" dir="ltr">{{ Str::limit($query['sql'] ?? 'Unknown', 50) }}</td>
                                    <td class="px-4 py-3 font-medium text-blue-600">{{ number_format($query['count'] ?? 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-8 text-center text-gray-500">
                                        لا توجد بيانات متاحة
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-6">إجراءات التحسين</h3>
            <div class="flex flex-wrap gap-4">
                <form action="{{ route('admin.performance.clear_cache') }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2.5 rounded-xl transition-all duration-300 flex items-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <i class="fas fa-broom ml-2"></i>
                        تنظيف كاش الاستعلامات
                    </button>
                </form>
                
                <a href="{{ route('admin.performance.database') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2.5 rounded-xl transition-all duration-300 flex items-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-database ml-2"></i>
                    عرض مقاييس قاعدة البيانات
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
