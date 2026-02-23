@extends('admin.layouts.admin')

@section('title', 'لوحة تحكم تقييم العقارات')
@section('page-title', 'لوحة تحكم تقييم العقارات')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-home text-blue-500 ml-3"></i>
                    لوحة تحكم تقييم العقارات
                </h1>
                <p class="text-gray-600 mt-2">نظرة عامة على عمليات التقييم العقاري وتحليلات الذكاء الاصطناعي</p>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('ai.valuation.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-plus ml-2"></i>
                    تقييم جديد
                </a>
                <a href="{{ route('ai.valuation.index') }}" class="bg-white text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-50 transition-all duration-300 shadow-md hover:shadow-lg border border-gray-200">
                    <i class="fas fa-list ml-2"></i>
                    سجل التقييمات
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Valuations -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">إجمالي التقييمات</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_valuations'] ?? 0) }}</p>
                        <p class="text-blue-100 text-sm mt-2">
                            <i class="fas fa-file-contract ml-1"></i>
                            عملية تقييم
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-calculator text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Completed Valuations -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">التقييمات المكتملة</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($stats['completed_valuations'] ?? 0) }}</p>
                        <p class="text-green-100 text-sm mt-2">
                            <i class="fas fa-check-circle ml-1"></i>
                            ناجحة
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-check-double text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Average Valuation -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">متوسط قيمة العقارات</p>
                        <p class="text-xl font-bold mt-2">{{ number_format($stats['average_valuation'] ?? 0) }} SAR</p>
                        <p class="text-purple-100 text-sm mt-2">
                            <i class="fas fa-dollar-sign ml-1"></i>
                            متوسط القيمة
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Accuracy Score -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">دقة النموذج</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format(($stats['accuracy_score'] ?? 0) * 100, 1) }}%</p>
                        <p class="text-orange-100 text-sm mt-2">
                            <i class="fas fa-bullseye ml-1"></i>
                            معدل الدقة
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-crosshairs text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Recent Valuations List -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">أحدث التقييمات</h3>
                    <a href="{{ route('ai.valuation.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">عرض الكل</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العقار</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">القيمة المقدرة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentValuations as $valuation)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-building text-gray-500"></i>
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $valuation->property->title ?? 'عقار غير معروف' }}</div>
                                            <div class="text-sm text-gray-500">{{ $valuation->user->name ?? 'مستخدم غير معروف' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-bold">{{ number_format($valuation->estimated_value, 2) }} SAR</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $valuation->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $valuation->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($valuation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ match($valuation->status) {
                                            'completed' => 'مكتمل',
                                            'pending' => 'قيد الانتظار',
                                            'failed' => 'فشل',
                                            default => $valuation->status
                                        } }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">لا توجد تقييمات حديثة</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Accuracy Metrics -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-800 mb-6">مقاييس الدقة</h3>
                <div class="space-y-6">
                    @foreach($accuracyMetrics as $metric => $value)
                    @if(is_array($value))
                        @foreach($value as $model => $score)
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">{{ $model }}</span>
                                <span class="text-sm font-medium text-gray-700">{{ number_format($score * 100, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $score * 100 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">{{ $metric }}</span>
                                <span class="text-sm font-medium text-gray-700">
                                    @if(in_array($metric, ['total_valuations', 'accurate_valuations']))
                                        {{ number_format($value) }}
                                    @else
                                        {{ number_format($value * 100, 1) }}%
                                    @endif
                                </span>
                            </div>
                            @if(!in_array($metric, ['total_valuations', 'accurate_valuations']))
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $value * 100 }}%"></div>
                            </div>
                            @endif
                        </div>
                    @endif
                    @endforeach
                    
                    @if(empty($accuracyMetrics))
                    <div class="text-center text-gray-500 py-4">
                        لا توجد بيانات دقة متاحة حالياً
                    </div>
                    @endif
                </div>

                <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <h4 class="font-bold text-blue-800 mb-2">ملاحظة</h4>
                    <p class="text-sm text-blue-600">
                        تعتمد دقة التقييمات على جودة البيانات المدخلة وتاريخ المعاملات المماثلة في المنطقة.
                    </p>
                </div>

                <!-- Valuation Trends (Placeholder) -->
                <div class="mt-8 border-t pt-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">اتجاهات التقييم</h3>
                    <div class="bg-gray-50 rounded-lg p-4 text-center border border-dashed border-gray-300">
                        <i class="fas fa-chart-line text-gray-400 text-3xl mb-2"></i>
                        <p class="text-sm text-gray-500">
                            {{ count($valuationTrends ?? []) }} نقطة بيانات متاحة للتحليل
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
