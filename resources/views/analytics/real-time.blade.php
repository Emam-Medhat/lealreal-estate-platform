@extends('layouts.dashboard')

@section('title', 'البيانات المباشرة')

@section('content')

<div class="max-w-7xl mx-auto">
    <!-- Real-time Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">البيانات المباشرة</h1>
                <p class="text-gray-600">مراقبة الأنشطة الحالية على المنصة</p>
            </div>
            <div class="flex items-center space-x-2 space-x-reverse">
                <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium flex items-center">
                    <span class="w-2 h-2 bg-green-500 rounded-full ml-2 animate-pulse"></span>
                    مباشر
                </div>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-sync-alt ml-2"></i>
                    تحديث
                </button>
            </div>
        </div>
    </div>

    <!-- Live Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">المستخدمون النشطون</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ number_format($activeUsers ?? 0) }}</h3>
                    <p class="text-xs text-green-600">↑ 5% الآن</p>
                </div>
                <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">الجلسات الحالية</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ number_format($currentSessions ?? 0) }}</h3>
                    <p class="text-xs text-green-600">↑ 12% الآن</p>
                </div>
                <div class="bg-green-100 text-green-600 p-3 rounded-full">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">الطلبات/دقيقة</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ number_format($requestsPerMinute ?? 0) }}</h3>
                    <p class="text-xs text-red-600">↓ 2% الآن</p>
                </div>
                <div class="bg-purple-100 text-purple-600 p-3 rounded-full">
                    <i class="fas fa-tachometer-alt text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">معدل الضرب</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ number_format($hitRate ?? 0, 1) }}%</h3>
                    <p class="text-xs text-green-600">↑ 0.5% الآن</p>
                </div>
                <div class="bg-orange-100 text-orange-600 p-3 rounded-full">
                    <i class="fas fa-bullseye text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Activity Feed -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Recent Activity -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">النشاط المباشر</h2>
            <div class="space-y-3 max-h-96 overflow-y-auto" id="activity-feed">
                @if(isset($liveActivities) && $liveActivities->count() > 0)
                    @foreach($liveActivities as $activity)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center">
                            <div class="bg-blue-100 text-blue-600 p-2 rounded-full ml-3">
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
                        <i class="fas fa-stream text-4xl mb-4"></i>
                        <p>لا يوجد نشاط حالي</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Server Stats -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">إحصائيات الخادم</h2>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm text-gray-600">استخدام وحدة المعالجة المركزية</span>
                        <span class="text-sm font-medium">{{ $cpuUsage ?? 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $cpuUsage ?? 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm text-gray-600">استخدام الذاكرة</span>
                        <span class="text-sm font-medium">{{ $memoryUsage ?? 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: {{ $memoryUsage ?? 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm text-gray-600">مساحة التخزين</span>
                        <span class="text-sm font-medium">{{ $diskUsage ?? 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" style="width: {{ $diskUsage ?? 0 }}%"></div>
                    </div>
                </div>

                <div class="pt-4 border-t">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">وقت التشغيل</span>
                        <span class="text-sm font-medium">{{ $uptime ?? '0d 0h' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Map -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">خريطة المستخدمين النشطين</h2>
        <div class="h-64 bg-gray-100 rounded-lg flex items-center justify-center">
            <div class="text-center text-gray-500">
                <i class="fas fa-globe-americas text-4xl mb-4"></i>
                <p>خريطة مباشرة للمستخدمين</p>
                <p class="text-sm">سيتم عرض البيانات قريباً</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-refresh activity feed
setInterval(() => {
    // Refresh activity feed logic here
    console.log('Refreshing activity feed...');
}, 5000);
</script>
@endpush

@endsection
