@extends('admin.layouts.admin')

@section('title', 'الخرائط الحرارية')
@section('page-title', 'الخرائط الحرارية')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-fire text-orange-500 ml-3"></i>
                    الخرائط الحرارية
                </h1>
                <p class="text-gray-600 mt-2">تحليل وتصور البيانات الجغرافية للعقارات والأسواق</p>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('bigdata.heatmaps.dashboard') }}" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-chart-line ml-2"></i>
                    لوحة التحكم
                </a>
                <button type="button" onclick="refreshMaps()" class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-3 rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-sync-alt ml-2"></i>
                    تحديث
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">إجمالي الخرائط</p>
                        <p class="text-3xl font-bold mt-2">{{ $stats['total_heatmaps'] }}</p>
                        <p class="text-orange-100 text-sm mt-2">
                            <i class="fas fa-chart-line ml-1"></i>
                            نشطة حالياً
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-map text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">المناطق النشطة</p>
                        <p class="text-3xl font-bold mt-2">{{ $stats['active_regions'] }}</p>
                        <p class="text-green-100 text-sm mt-2">
                            <i class="fas fa-map-marked-alt ml-1"></i>
                            تحت المراقبة
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-map-marked-alt text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">نقاط البيانات</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($stats['data_points']) }}</p>
                        <p class="text-blue-100 text-sm mt-2">
                            <i class="fas fa-database ml-1"></i>
                            محسوبة
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-database text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">آخر تحديث</p>
                        <p class="text-3xl font-bold mt-2">{{ explode(' ', $stats['last_updated'])[0] }}</p>
                        <p class="text-purple-100 text-sm mt-2">
                            <i class="fas fa-clock ml-1"></i>
                            {{ explode(' ', $stats['last_updated'])[1] ?? 'دقيقة' }}
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Heatmap Types -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">خرائط أسعار العقارات</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $stats['heatmap_types']['property_prices'] }} خريطة نشطة</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <i class="fas fa-home text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">تحليل أسعار العقارات عبر المناطق المختلفة</p>
                    <div class="mb-4">
                        <img src="https://via.placeholder.com/300x200/4e73df/ffffff?text=Property+Prices" class="w-full h-48 object-cover rounded-lg" alt="Property Prices Heatmap">
                    </div>
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">50,000 ريال</span>
                            <span class="text-gray-600">5,000,000 ريال</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-green-400 via-yellow-400 to-red-400 h-2 rounded-full"></div>
                        </div>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('bigdata.heatmaps.property-prices') }}" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300">
                            <i class="fas fa-expand ml-2"></i>
                            عرض الخريطة
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">خرائط الطلب في السوق</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $stats['heatmap_types']['market_demand'] }} خريطة نشطة</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-chart-area text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">تحليل الطلب على العقارات حسب النوع والموقع</p>
                    <div class="mb-4">
                        <img src="https://via.placeholder.com/300x200/36b9cc/ffffff?text=Market+Demand" class="w-full h-48 object-cover rounded-lg" alt="Market Demand Heatmap">
                    </div>
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">طلب منخفض</span>
                            <span class="text-gray-600">طلب مرتفع جداً</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-blue-400 via-cyan-400 to-red-400 h-2 rounded-full"></div>
                        </div>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('bigdata.heatmaps.market-demand') }}" class="bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-2 rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-300">
                            <i class="fas fa-expand ml-2"></i>
                            عرض الخريطة
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">خرائط فرص الاستثمار</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $stats['heatmap_types']['investment_opportunities'] }} خريطة نشطة</p>
                        </div>
                        <div class="bg-purple-100 rounded-full p-3">
                            <i class="fas fa-coins text-purple-600 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">تحديد أفضل المناطق والأنواع للاستثمار العقاري</p>
                    <div class="mb-4">
                        <img src="https://via.placeholder.com/300x200/1cc88a/ffffff?text=Investment+Opportunities" class="w-full h-48 object-cover rounded-lg" alt="Investment Opportunities Heatmap">
                    </div>
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">عائد منخفض</span>
                            <span class="text-gray-600">عائد مرتفع جداً</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-gray-400 via-blue-400 to-green-400 h-2 rounded-full"></div>
                        </div>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('bigdata.heatmaps.investment-opportunities') }}" class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-4 py-2 rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-300">
                            <i class="fas fa-expand ml-2"></i>
                            عرض الخريطة
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-chart-bar text-blue-500 ml-3"></i>
                    إحصائيات سريعة
                </h3>
                <div class="bg-blue-100 rounded-full p-2">
                    <i class="fas fa-chart-pie text-blue-600"></i>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <h4 class="text-3xl font-bold text-blue-600">{{ $stats['heatmap_types']['property_prices'] }}</h4>
                    <p class="text-sm text-gray-600 mt-2">خرائط أسعار العقارات</p>
                </div>
                <div class="text-center">
                    <h4 class="text-3xl font-bold text-green-600">{{ $stats['heatmap_types']['market_demand'] }}</h4>
                    <p class="text-sm text-gray-600 mt-2">خرائط الطلب في السوق</p>
                </div>
                <div class="text-center">
                    <h4 class="text-3xl font-bold text-cyan-600">{{ $stats['heatmap_types']['investment_opportunities'] }}</h4>
                    <p class="text-sm text-gray-600 mt-2">خرائط فرص الاستثمار</p>
                </div>
                <div class="text-center">
                    <h4 class="text-3xl font-bold text-purple-600">{{ $stats['active_regions'] }}</h4>
                    <p class="text-sm text-gray-600 mt-2">المناطق النشطة</p>
                </div>
            </div>
        </div>

        <!-- Recent Activity & Active Regions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-clock text-orange-500 ml-3"></i>
                        النشاط الأخير
                    </h3>
                    <div class="bg-orange-100 rounded-full p-2">
                        <i class="fas fa-history text-orange-600"></i>
                    </div>
                </div>
                <div class="space-y-4">
                    @foreach($recentActivity as $activity)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                        <div>
                            <h6 class="font-medium text-gray-800">{{ $activity['description'] }}</h6>
                            <p class="text-xs text-gray-500">{{ $activity['created_at'] }}</p>
                        </div>
                        <span class="bg-{{ $activity['status'] == 'مكتمل' ? 'green' : ($activity['status'] == 'جاري' ? 'blue' : 'gray') }}-100 text-{{ $activity['status'] == 'مكتمل' ? 'green' : ($activity['status'] == 'جاري' ? 'blue' : 'gray') }}-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $activity['status'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-map-pin text-red-500 ml-3"></i>
                        المناطق الأكثر نشاطاً
                    </h3>
                    <div class="bg-red-100 rounded-full p-2">
                        <i class="fas fa-fire text-red-600"></i>
                    </div>
                </div>
                <div class="space-y-4">
                    @foreach($activeRegions as $region)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                        <div>
                            <h6 class="font-medium text-gray-800">{{ $region['name'] }}</h6>
                            <p class="text-xs text-gray-500">{{ number_format($region['data_points']) }} نقطة بيانات</p>
                        </div>
                        <span class="bg-{{ $region['activity_level'] == 'ساخن' ? 'red' : ($region['activity_level'] == 'دافئ' ? 'amber' : 'blue') }}-100 text-{{ $region['activity_level'] == 'ساخن' ? 'red' : ($region['activity_level'] == 'دافئ' ? 'amber' : 'blue') }}-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $region['activity_level'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function refreshMaps() {
    // Show loading state
    const button = event.target;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري التحديث...';
    button.disabled = true;
    
    // Simulate refresh with AJAX call
    setTimeout(() => {
        location.reload();
    }, 1000);
}

// Auto-refresh every 5 minutes
setInterval(function() {
    console.log('Refreshing heatmap data...');
    // Here you would typically make an AJAX call to refresh the data
    fetch('/bigdata/heatmaps/refresh-data')
        .then(response => response.json())
        .then(data => {
            console.log('Data refreshed:', data);
            // Update UI with new data
        })
        .catch(error => {
            console.error('Error refreshing data:', error);
        });
}, 300000);

// Add interactive hover effects
document.addEventListener('DOMContentLoaded', function() {
    // Add ripple effect to cards
    const cards = document.querySelectorAll('.bg-white.rounded-2xl');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endpush
