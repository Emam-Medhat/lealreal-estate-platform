@extends('layouts.dashboard')

@section('title', 'نظرة عامة')

@section('content')

<div class="max-w-7xl mx-auto">
    <!-- Overview Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">نظرة عامة على التحليلات</h1>
        <p class="text-gray-600">ملخص شامل لأداء المنصة والبيانات الرئيسية</p>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">إجمالي المستخدمين</p>
                    <h3 class="text-3xl font-bold">{{ number_format($totalUsers ?? 0) }}</h3>
                    <p class="text-blue-100 text-xs mt-1">+12% من الشهر الماضي</p>
                </div>
                <div class="bg-blue-700 bg-opacity-50 p-3 rounded-full">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">إجمالي العقارات</p>
                    <h3 class="text-3xl font-bold">{{ number_format($totalProperties ?? 0) }}</h3>
                    <p class="text-green-100 text-xs mt-1">+8% من الشهر الماضي</p>
                </div>
                <div class="bg-green-700 bg-opacity-50 p-3 rounded-full">
                    <i class="fas fa-building text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">التفاعلات</p>
                    <h3 class="text-3xl font-bold">{{ number_format($totalInteractions ?? 0) }}</h3>
                    <p class="text-purple-100 text-xs mt-1">+25% من الشهر الماضي</p>
                </div>
                <div class="bg-purple-700 bg-opacity-50 p-3 rounded-full">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">معدل التحويل</p>
                    <h3 class="text-3xl font-bold">{{ number_format($conversionRate ?? 0, 1) }}%</h3>
                    <p class="text-orange-100 text-xs mt-1">+3% من الشهر الماضي</p>
                </div>
                <div class="bg-orange-700 bg-opacity-50 p-3 rounded-full">
                    <i class="fas fa-percentage text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- User Growth Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">نمو المستخدمين</h2>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <div class="text-center text-gray-500">
                    <i class="fas fa-chart-area text-4xl mb-4"></i>
                    <p>مخطط نمو المستخدمين</p>
                    <p class="text-sm">سيتم عرض البيانات قريباً</p>
                </div>
            </div>
        </div>

        <!-- Activity Heatmap -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">خريطة النشاط</h2>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <div class="text-center text-gray-500">
                    <i class="fas fa-fire text-4xl mb-4"></i>
                    <p>خريطة حرارية للنشاط</p>
                    <p class="text-sm">سيتم عرض البيانات قريباً</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">الأنشطة الحديثة</h2>
        <div class="space-y-4">
            @if(isset($recentActivities) && $recentActivities->count() > 0)
                @foreach($recentActivities as $activity)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center">
                        <div class="bg-blue-100 text-blue-600 p-2 rounded-full ml-4">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $activity->description }}</p>
                            <p class="text-sm text-gray-500">{{ $activity->user->name ?? 'مستخدم مجهول' }}</p>
                        </div>
                    </div>
                    <div class="text-left">
                        <span class="text-xs text-gray-400">{{ $activity->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-history text-4xl mb-4"></i>
                    <p>لا توجد أنشطة حديثة</p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
