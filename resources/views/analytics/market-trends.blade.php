@extends('admin.layouts.admin')

@section('title', 'تحليلات السوق')
@section('page-title', 'تحليلات السوق')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">تحليلات السوق</h1>
                    <p class="text-gray-600">نظرة شاملة على اتجاهات السوق والتحليلات التنافسية</p>
                </div>
                <a href="{{ route('analytics.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left ml-2"></i>
                    العودة
                </a>
            </div>
        </div>

        <!-- Market Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Market Size Card -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium mb-1">حجم السوق</p>
                        <h3 class="text-2xl font-bold">{{ number_format($marketSize, 2) }}</h3>
                        <p class="text-blue-100 text-xs mt-2">ريال سعودي</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fas fa-chart-pie text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Market Growth Card -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium mb-1">نمو السوق</p>
                        <h3 class="text-2xl font-bold">{{ number_format($marketGrowth, 2) }}%</h3>
                        <p class="text-green-100 text-xs mt-2">مقارنة بالفترة السابقة</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Competitors Count Card -->
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium mb-1">عدد المنافسين</p>
                        <h3 class="text-2xl font-bold">{{ number_format($competitorCount) }}</h3>
                        <p class="text-purple-100 text-xs mt-2">شركة نشطة</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Market Share Card -->
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium mb-1">حصتنا في السوق</p>
                        <h3 class="text-2xl font-bold">{{ number_format($ourShare, 2) }}%</h3>
                        <p class="text-orange-100 text-xs mt-2">من إجمالي السوق</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fas fa-percentage text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">الفلترة والبحث</h2>
            <form method="GET" action="{{ route('analytics.market.trends') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="category" name="category">
                            <option value="">جميع الفئات</option>
                            <option value="residential" {{ request('category') == 'residential' ? 'selected' : '' }}>سكني</option>
                            <option value="commercial" {{ request('category') == 'commercial' ? 'selected' : '' }}>تجاري</option>
                            <option value="industrial" {{ request('category') == 'industrial' ? 'selected' : '' }}>صناعي</option>
                            <option value="land" {{ request('category') == 'land' ? 'selected' : '' }}>أراضي</option>
                        </select>
                    </div>
                    <div>
                        <label for="region" class="block text-sm font-medium text-gray-700 mb-2">المنطقة</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="region" name="region">
                            <option value="">جميع المناطق</option>
                            <option value="riyadh" {{ request('region') == 'riyadh' ? 'selected' : '' }}>الرياض</option>
                            <option value="jeddah" {{ request('region') == 'jeddah' ? 'selected' : '' }}>جدة</option>
                            <option value="dammam" {{ request('dammam') == 'dammam' ? 'selected' : '' }}>الدمام</option>
                            <option value="mecca" {{ request('mecca') == 'mecca' ? 'selected' : '' }}>مكة</option>
                        </select>
                    </div>
                    <div>
                        <label for="period" class="block text-sm font-medium text-gray-700 mb-2">الفترة</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="period" name="period">
                            <option value="7d" {{ request('period') == '7d' ? 'selected' : '' }}>7 أيام</option>
                            <option value="30d" {{ request('period') == '30d' ? 'selected' : '' }}>30 يوم</option>
                            <option value="90d" {{ request('period') == '90d' ? 'selected' : '' }}>90 يوم</option>
                            <option value="1y" {{ request('period') == '1y' ? 'selected' : '' }}>سنة</option>
                        </select>
                    </div>
                    <div class="flex items-end space-x-2 space-x-reverse">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search ml-2"></i>
                            بحث
                        </button>
                        <a href="{{ route('analytics.market.trends') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Market Trends Chart -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">اتجاهات السوق</h2>
                <div class="bg-gray-50 rounded-xl p-4" style="height: 400px;">
                    <div id="marketTrendsChart" class="flex items-center justify-center h-full">
                        <div class="text-center text-gray-500">
                            <i class="fas fa-chart-line text-4xl mb-2"></i>
                            <p>سيتم عرض اتجاهات السوق هنا</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Competitor Analysis -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">تحليل المنافسين</h2>
                <div class="bg-gray-50 rounded-xl p-4" style="height: 400px;">
                    <div id="competitorChart" class="flex items-center justify-center h-full">
                        <div class="text-center text-gray-500">
                            <i class="fas fa-users text-4xl mb-2"></i>
                            <p>سيتم عرض تحليل المنافسين هنا</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Market Segments and Opportunities -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Market Segments -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">تقسيمات السوق</h2>
                <div class="bg-gray-50 rounded-xl p-4" style="height: 350px;">
                    <div id="marketSegmentsChart" class="flex items-center justify-center h-full">
                        <div class="text-center text-gray-500">
                            <i class="fas fa-chart-pie text-4xl mb-2"></i>
                            <p>سيتم عرض تقسيمات السوق هنا</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Opportunities -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">الفرص السوقية</h2>
                <div class="bg-gray-50 rounded-xl p-4" style="height: 350px;">
                    <div id="opportunitiesList" class="flex items-center justify-center h-full">
                        <div class="text-center text-gray-500">
                            <i class="fas fa-lightbulb text-4xl mb-2"></i>
                            <p>سيتم عرض الفرص هنا</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Trends Table -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">الاتجاهات الأخيرة</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاتجاه</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الفئة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاتجاه</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التغيير</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الثقة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentTrends as $trend)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $trend->trend_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $trend->getTrendLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $trend->getCategoryLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $trend->isIncreasing() ? 'bg-green-100 text-green-800' : ($trend->isDecreasing() ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $trend->getDirectionLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="{{ $trend->change_percentage > 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                    {{ number_format($trend->change_percentage, 2) }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-{{ $trend->getConfidenceLevel() === 'high' ? 'green' : ($trend->getConfidenceLevel() === 'medium' ? 'yellow' : 'red') }}-500 h-2 rounded-full" style="width: {{ $trend->confidence_score }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-600">{{ number_format($trend->confidence_score, 1) }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $trend->created_at->format('Y-m-d') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-chart-line text-2xl mb-2"></i>
                                <p>لا توجد اتجاهات حالياً</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Chart initialization will be added here when needed
document.addEventListener('DOMContentLoaded', function() {
    console.log('Market trends page loaded');
});
</script>
@endpush
