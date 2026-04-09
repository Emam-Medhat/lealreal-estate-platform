@extends('layouts.dashboard')

@section('title', 'التحليلات')

@section('content')

<div class="max-w-7xl mx-auto">
    <!-- Analytics Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">لوحة التحليلات</h1>
        <p class="text-gray-600">مراقبة وتحليل أداء المنصة وسلوك المستخدمين</p>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 ml-4">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">إجمالي الأحداث</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $totalEvents ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 ml-4">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">إجمالي الجلسات</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $totalSessions ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 ml-4">
                    <i class="fas fa-exchange-alt text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">التحويلات</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $totalConversions ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600 ml-4">
                    <i class="fas fa-percentage text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">معدل التحويل</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ number_format($conversionRate ?? 0, 2) }}%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Data -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Recent Events -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">الأحداث الأخيرة</h2>
            <div class="space-y-3">
                @if(isset($recentEvents) && $recentEvents->count() > 0)
                    @foreach($recentEvents as $event)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $event->event_name }}</p>
                            <p class="text-sm text-gray-500">{{ $event->page_url }}</p>
                        </div>
                        <div class="text-left">
                            <span class="text-xs text-gray-400">{{ $event->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-chart-bar text-4xl mb-4"></i>
                        <p>لا توجد أحداث حديثة</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Top Pages -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">أكثر الصفحات زيارة</h2>
            <div class="space-y-3">
                @if(isset($topPages) && $topPages->count() > 0)
                    @foreach($topPages as $page)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900 truncate">{{ $page->page_url }}</p>
                            <p class="text-sm text-gray-500">صفحة</p>
                        </div>
                        <div class="text-left">
                            <span class="text-lg font-semibold text-blue-600">{{ $page->views ?? $page->count ?? 0 }}</span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-file-alt text-4xl mb-4"></i>
                        <p>لا توجد بيانات صفحات</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">إجراءات سريعة</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('analytics.overview') }}" class="flex flex-col items-center p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-chart-pie text-blue-600 mb-2 text-2xl"></i>
                <span class="text-sm font-medium">نظرة عامة</span>
            </a>
            <a href="{{ route('analytics.real-time') }}" class="flex flex-col items-center p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-clock text-green-600 mb-2 text-2xl"></i>
                <span class="text-sm font-medium">البيانات المباشرة</span>
            </a>
            <a href="{{ route('analytics.reports') }}" class="flex flex-col items-center p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-file-alt text-purple-600 mb-2 text-2xl"></i>
                <span class="text-sm font-medium">التقارير</span>
            </a>
        </div>
    </div>
</div>

@endsection
