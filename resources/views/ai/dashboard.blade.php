@extends('admin.layouts.admin')

@section('title', 'لوحة الذكاء الاصطناعي')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">لوحة الذكاء الاصطناعي</h1>
            <p class="text-gray-600 mt-2">مراقبة وإدارة أنظمة الذكاء الاصطناعي</p>
        </div>
        <div class="flex space-x-3 space-x-reverse">
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-sync-alt ml-2"></i>
                تحديث البيانات
            </button>
            <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-download ml-2"></i>
                تصدير تقرير
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">إجمالي التوقعات</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_predictions'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-crystal-ball text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600"><i class="fas fa-arrow-up ml-1"></i>12%</span>
                <span class="text-gray-500 mr-2">من الشهر الماضي</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">حالات الاحتيال النشطة</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active_fraud_cases'] ?? 0 }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-lg">
                    <i class="fas fa-user-secret text-red-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-red-600"><i class="fas fa-arrow-up ml-1"></i>8%</span>
                <span class="text-gray-500 mr-2">تحتاج مراجعة</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">جولات الواقع الافتراضي</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['vr_tours_count'] ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-vr-cardboard text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600"><i class="fas fa-arrow-up ml-1"></i>25%</span>
                <span class="text-gray-500 mr-2">زيادة هذا الشهر</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">متوسط دقة التوقعات</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['avg_prediction_accuracy'] ?? 0, 1) }}%</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-chart-line text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600"><i class="fas fa-arrow-up ml-1"></i>3.2%</span>
                <span class="text-gray-500 mr-2">تحسن الأداء</span>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Predictions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">آخر التوقعات</h3>
            </div>
            <div class="p-6">
                @if($recentPredictions->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentPredictions->take(5) as $prediction)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 p-2 rounded-lg ml-3">
                                        <i class="fas fa-home text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">عقار #{{ $prediction->property_id ?? 'N/A' }}</p>
                                        <p class="text-sm text-gray-600">{{ $prediction->created_at->format('Y-m-d H:i') }}</p>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <p class="font-semibold text-gray-900">{{ number_format($prediction->predicted_price ?? 0, 0) }} ريال</p>
                                    <p class="text-sm text-gray-600">دقة: {{ number_format($prediction->confidence_score ?? 0, 1) }}%</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-crystal-ball text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">لا توجد توقعات حالياً</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Fraud Cases -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">حالات الاحتيال</h3>
            </div>
            <div class="p-6">
                @if($fraudCases->count() > 0)
                    <div class="space-y-4">
                        @foreach($fraudCases->take(5) as $case)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="bg-red-100 p-2 rounded-lg ml-3">
                                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">مستخدم #{{ $case->user_id ?? 'N/A' }}</p>
                                        <p class="text-sm text-gray-600">{{ $case->created_at->format('Y-m-d H:i') }}</p>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($case->risk_level == 'high') bg-red-100 text-red-800
                                        @elseif($case->risk_level == 'medium') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ $case->risk_level ?? 'unknown' }}
                                    </span>
                                    <p class="text-sm text-gray-600 mt-1">{{ $case->status ?? 'pending' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-shield-alt text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">لا توجد حالات احتيال حالياً</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- AI Models Status -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">حالة نماذج الذكاء الاصطناعي</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="relative inline-flex items-center justify-center">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-brain text-green-600 text-2xl"></i>
                        </div>
                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div>
                    </div>
                    <h4 class="font-semibold text-gray-900 mt-3">نموذج التسعير</h4>
                    <p class="text-sm text-gray-600">يعمل بنسبة 98%</p>
                    <div class="mt-2">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 98%"></div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <div class="relative inline-flex items-center justify-center">
                        <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-shield text-yellow-600 text-2xl"></i>
                        </div>
                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-500 border-2 border-white rounded-full"></div>
                    </div>
                    <h4 class="font-semibold text-gray-900 mt-3">كشف الاحتيال</h4>
                    <p class="text-sm text-gray-600">يعمل بنسبة 85%</p>
                    <div class="mt-2">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full" style="width: 85%"></div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <div class="relative inline-flex items-center justify-center">
                        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-vr-cardboard text-blue-600 text-2xl"></i>
                        </div>
                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-blue-500 border-2 border-white rounded-full"></div>
                    </div>
                    <h4 class="font-semibold text-gray-900 mt-3">الواقع الافتراضي</h4>
                    <p class="text-sm text-gray-600">يعمل بنسبة 92%</p>
                    <div class="mt-2">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 92%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
