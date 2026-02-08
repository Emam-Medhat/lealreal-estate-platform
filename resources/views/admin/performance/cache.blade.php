@php
function convertToBytes($memoryString) {
    $memoryString = trim($memoryString);
    $lastChar = strtolower($memoryString[strlen($memoryString) - 1]);
    $value = (int) $memoryString;
    
    switch ($lastChar) {
        case 'g':
            $value *= 1024;
            // no break
        case 'm':
            $value *= 1024;
            // no break
        case 'k':
            $value *= 1024;
    }
    
    return $value;
}
@endphp

@extends('admin.layouts.admin')

@section('title', 'أداء التخزين المؤقت (Cache)')
@section('page-title', 'مراقبة الكاش')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">أداء التخزين المؤقت</h2>
            <p class="text-gray-600 mt-2">تحليل ومراقبة أداء نظام الكاش.</p>
        </div>

        <!-- Cache Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Cache Driver -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-server text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Driver</span>
                </div>
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">نظام الكاش</h3>
                    <p class="text-xl font-bold text-gray-800 mt-2" dir="ltr">{{ $metrics['driver'] ?? 'file' }}</p>
                    <p class="text-sm text-gray-500 mt-1">الحالة: <span class="text-green-500">نشط</span></p>
                </div>
            </div>

            <!-- Hit Rate -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-emerald-100 p-3 rounded-full">
                        <i class="fas fa-crosshairs text-emerald-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Hit Rate</span>
                </div>
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">معدل الإصابة</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $metrics['hit_rate'] ?? 0 }}%</p>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-3">
                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $metrics['hit_rate'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Memory Usage -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-memory text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded-full">Memory</span>
                </div>
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">الذاكرة المستخدمة</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format(convertToBytes($metrics['memory_usage'] ?? 0) / 1024 / 1024, 2) }} <span class="text-sm text-gray-500">MB</span></p>
                </div>
            </div>
        </div>

        <!-- Detailed Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Key Statistics -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-key text-blue-500 ml-2"></i>
                    إحصائيات المفاتيح (Keys)
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">إجمالي المفاتيح</td>
                                <td class="py-3 font-medium text-gray-800">{{ number_format($metrics['total_keys'] ?? 0) }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">Hits (إصابات)</td>
                                <td class="py-3 font-medium text-green-600">{{ number_format($metrics['hits'] ?? 0) }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">Misses (إخفاقات)</td>
                                <td class="py-3 font-medium text-red-500">{{ number_format($metrics['misses'] ?? 0) }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">Writes (كتابة)</td>
                                <td class="py-3 font-medium text-gray-800">{{ number_format($metrics['writes'] ?? 0) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Operations Stats -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-cogs text-emerald-500 ml-2"></i>
                    إحصائيات العمليات
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">معدل القراءة/ثانية</td>
                                <td class="py-3 font-medium text-gray-800">{{ number_format($metrics['read_rate'] ?? 0, 2) }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">معدل الكتابة/ثانية</td>
                                <td class="py-3 font-medium text-gray-800">{{ number_format($metrics['write_rate'] ?? 0, 2) }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">Evictions (حذف)</td>
                                <td class="py-3 font-medium text-amber-600">{{ number_format($metrics['evictions'] ?? 0) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-6">إجراءات الصيانة</h3>
            <div class="flex flex-wrap gap-4">
                <form action="{{ route('admin.performance.clear_cache') }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2.5 rounded-xl transition-all duration-300 flex items-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <i class="fas fa-trash-alt ml-2"></i>
                        مسح الكاش بالكامل
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
