@extends('admin.layouts.admin')

@section('title', 'سلوك المستخدمين')
@section('page-title', 'سلوك المستخدمين')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">سلوك المستخدمين</h1>
                    <p class="text-gray-600">تحليل شامل لسلوك المستخدمين وتفاعلهم مع المنصة</p>
                </div>
                <a href="{{ route('analytics.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left ml-2"></i>
                    العودة
                </a>
            </div>
        </div>

        <!-- Engagement Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Avg Session Duration -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium mb-1">متوسط مدة الجلسة</p>
                        <h3 class="text-2xl font-bold">{{ number_format($avgSessionDuration ?? 0, 1) }}دقيقة</h3>
                        <p class="text-blue-100 text-xs mt-2">متوسط وقت التفاعل</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Pages Per Session -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium mb-1">صفحات لكل جلسة</p>
                        <h3 class="text-2xl font-bold">{{ number_format($pagesPerSession ?? 0, 1) }}</h3>
                        <p class="text-green-100 text-xs mt-2">عمق التصفح</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fas fa-file-alt text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Bounce Rate -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium mb-1">معدل الارتداد</p>
                        <h3 class="text-2xl font-bold">{{ number_format($bounceRate ?? 0, 1) }}%</h3>
                        <p class="text-red-100 text-xs mt-2">مغادرة سريعة</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fas fa-sign-out-alt text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Return Visitors -->
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium mb-1">الزوار العائدون</p>
                        <h3 class="text-2xl font-bold">{{ number_format($returnVisitorRate ?? 0, 1) }}%</h3>
                        <p class="text-purple-100 text-xs mt-2">ولاء المستخدمين</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fas fa-user-check text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">الفلترة والبحث</h2>
            <form method="GET" action="{{ route('analytics.behavior.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="period" class="block text-sm font-medium text-gray-700 mb-2">الفترة</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="period" name="period">
                            <option value="1d" {{ request('period') == '1d' ? 'selected' : '' }}>24 ساعة</option>
                            <option value="7d" {{ request('period') == '7d' ? 'selected' : '' }}>7 أيام</option>
                            <option value="30d" {{ request('period') == '30d' ? 'selected' : '' }}>30 يوم</option>
                            <option value="90d" {{ request('period') == '90d' ? 'selected' : '' }}>90 يوم</option>
                        </select>
                    </div>
                    <div>
                        <label for="segment" class="block text-sm font-medium text-gray-700 mb-2">الشريحة</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="segment" name="segment">
                            <option value="">جميع الشرائح</option>
                            <option value="new" {{ request('segment') == 'new' ? 'selected' : '' }}>مستخدمون جدد</option>
                            <option value="returning" {{ request('segment') == 'returning' ? 'selected' : '' }}>مستخدمون عائدون</option>
                            <option value="high_value" {{ request('segment') == 'high_value' ? 'selected' : '' }}>ذوو القيمة العالية</option>
                        </select>
                    </div>
                    <div>
                        <label for="device" class="block text-sm font-medium text-gray-700 mb-2">الجهاز</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="device" name="device">
                            <option value="">جميع الأجهزة</option>
                            <option value="desktop" {{ request('device') == 'desktop' ? 'selected' : '' }}>كمبيوتر</option>
                            <option value="mobile" {{ request('device') == 'mobile' ? 'selected' : '' }}>جوال</option>
                            <option value="tablet" {{ request('device') == 'tablet' ? 'selected' : '' }}>تابلت</option>
                        </select>
                    </div>
                    <div class="flex items-end space-x-2 space-x-reverse">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search ml-2"></i>
                            بحث
                        </button>
                        <a href="{{ route('analytics.behavior.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Session Patterns Chart -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">أنماط الجلسات</h2>
                <div class="bg-gray-50 rounded-xl p-4" style="height: 400px;">
                    <div id="sessionPatternsChart" class="flex items-center justify-center h-full">
                        <div class="text-center text-gray-500">
                            <i class="fas fa-chart-line text-4xl mb-2"></i>
                            <p>سيتم عرض أنماط الجلسات هنا</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Patterns Chart -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">أنماط التصفح</h2>
                <div class="bg-gray-50 rounded-xl p-4" style="height: 400px;">
                    <div id="navigationPatternsChart" class="flex items-center justify-center h-full">
                        <div class="text-center text-gray-500">
                            <i class="fas fa-sitemap text-4xl mb-2"></i>
                            <p>سيتم عرض مسارات التصفح هنا</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Segments and Journey -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- User Segments -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">شرائح المستخدمين</h2>
                <div class="bg-gray-50 rounded-xl p-4" style="height: 350px;">
                    <div id="userSegmentsChart" class="flex items-center justify-center h-full">
                        <div class="text-center text-gray-500">
                            <i class="fas fa-users text-4xl mb-2"></i>
                            <p>سيتم عرض شرائح المستخدمين هنا</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Real-time Activity -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">النشاط المباشر</h2>
                <div class="bg-gray-50 rounded-xl p-4" style="height: 350px;">
                    <div id="realtimeActivity" class="flex items-center justify-center h-full">
                        <div class="text-center text-gray-500">
                            <i class="fas fa-pulse text-4xl mb-2"></i>
                            <p>سيتم عرض النشاط المباشر هنا</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Table -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">النشاط الأخير للمستخدمين</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المستخدم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النشاط</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الصفحة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الجهاز</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المدة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوقت</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-history text-2xl mb-2"></i>
                                <p>لا توجد بيانات نشاط حالياً</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// User behavior analytics will be initialized here
document.addEventListener('DOMContentLoaded', function() {
    console.log('User behavior page loaded');
});
</script>
@endpush
