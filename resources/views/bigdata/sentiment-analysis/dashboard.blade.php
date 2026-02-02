@extends('admin.layouts.admin')

@section('title', 'لوحة تحكم تحليل المشاعر')
@section('page-title', 'لوحة تحكم تحليل المشاعر')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-chart-line text-blue-500 ml-3"></i>
                    لوحة تحكم تحليل المشاعر
                </h1>
                <p class="text-gray-600 mt-2">نظرة شاملة على اتجاهات المشاعر وتحليلات السوق</p>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('bigdata.sentiment-analysis') }}" class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-3 rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للتحليل
                </a>
                <button onclick="refreshDashboard()" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-sync-alt ml-2"></i>
                    تحديث
                </button>
            </div>
        </div>

        <!-- Main Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">إجمالي التحليلات</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($sentimentStats['total_analyzed']) }}</p>
                        <p class="text-green-100 text-sm mt-2">
                            <i class="fas fa-chart-line ml-1"></i>
                            +15% هذا الشهر
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-analytics text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">معدل الإيجابية</p>
                        <p class="text-3xl font-bold mt-2">{{ $sentimentStats['positive_percentage'] }}%</p>
                        <p class="text-blue-100 text-sm mt-2">
                            <i class="fas fa-smile ml-1"></i>
                            مشاعر إيجابية
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-smile text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">مستوى الثقة</p>
                        <p class="text-3xl font-bold mt-2">{{ $sentimentStats['confidence_score'] }}%</p>
                        <p class="text-purple-100 text-sm mt-2">
                            <i class="fas fa-shield-alt ml-1"></i>
                            دقة عالية
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-shield-alt text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-100 text-sm font-medium">اتجاه المشاعر</p>
                        <p class="text-3xl font-bold mt-2">{{ $sentimentStats['trend_direction'] == 'positive' ? 'إيجابي' : 'سلبي' }}</p>
                        <p class="text-amber-100 text-sm mt-2">
                            <i class="fas fa-arrow-up ml-1"></i>
                            تحسن مستمر
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-trending-up text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-chart-pie text-blue-500 ml-3"></i>
                        توزيع المشاعر
                    </h3>
                    <div class="bg-blue-100 rounded-full p-2">
                        <i class="fas fa-chart-pie text-blue-600"></i>
                    </div>
                </div>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="sentimentPieChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-chart-line text-green-500 ml-3"></i>
                        اتجاه المشاعر عبر الوقت
                    </h3>
                    <div class="bg-green-100 rounded-full p-2">
                        <i class="fas fa-chart-line text-green-600"></i>
                    </div>
                </div>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="sentimentLineChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Detailed Analysis -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-star text-yellow-500 ml-3"></i>
                        تحليل المراجعات
                    </h3>
                    <div class="bg-yellow-100 rounded-full p-2">
                        <i class="fas fa-star text-yellow-600"></i>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">إيجابي</span>
                        <span class="text-sm font-bold text-green-600">72%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: 72%"></div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">محايد</span>
                        <span class="text-sm font-bold text-yellow-600">18%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-600 h-2 rounded-full" style="width: 18%"></div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">سلبي</span>
                        <span class="text-sm font-bold text-red-600">10%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-600 h-2 rounded-full" style="width: 10%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-share-alt text-blue-500 ml-3"></i>
                        تحليل وسائل التواصل
                    </h3>
                    <div class="bg-blue-100 rounded-full p-2">
                        <i class="fas fa-share-alt text-blue-600"></i>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">إيجابي</span>
                        <span class="text-sm font-bold text-green-600">68%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: 68%"></div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">محايد</span>
                        <span class="text-sm font-bold text-yellow-600">22%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-600 h-2 rounded-full" style="width: 22%"></div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">سلبي</span>
                        <span class="text-sm font-bold text-red-600">10%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-600 h-2 rounded-full" style="width: 10%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-newspaper text-purple-500 ml-3"></i>
                        تحليل الأخبار
                    </h3>
                    <div class="bg-purple-100 rounded-full p-2">
                        <i class="fas fa-newspaper text-purple-600"></i>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">إيجابي</span>
                        <span class="text-sm font-bold text-green-600">65%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: 65%"></div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">محايد</span>
                        <span class="text-sm font-bold text-yellow-600">25%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-600 h-2 rounded-full" style="width: 25%"></div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">سلبي</span>
                        <span class="text-sm font-bold text-red-600">10%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-600 h-2 rounded-full" style="width: 10%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Analysis -->
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-clock text-orange-500 ml-3"></i>
                    التحليلات الأخيرة
                </h3>
                <div class="bg-orange-100 rounded-full p-2">
                    <i class="fas fa-history text-orange-600"></i>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div>
                        <h6 class="font-medium text-gray-800">مراجعة عقار الرياض</h6>
                        <p class="text-xs text-gray-500">منذ 5 دقائق</p>
                    </div>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">إيجابي</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div>
                        <h6 class="font-medium text-gray-800">منشور تويتر</h6>
                        <p class="text-xs text-gray-500">منذ 15 دقيقة</p>
                    </div>
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">محايد</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div>
                        <h6 class="font-medium text-gray-800">خبر عقاري</h6>
                        <p class="text-xs text-gray-500">منذ 30 دقيقة</p>
                    </div>
                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">سلبي</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function refreshDashboard() {
    const button = event.target;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري التحديث...';
    button.disabled = true;
    
    setTimeout(() => {
        location.reload();
    }, 1000);
}

// Initialize charts
document.addEventListener('DOMContentLoaded', function() {
    // Pie Chart
    const pieCtx = document.getElementById('sentimentPieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: ['إيجابي', 'محايد', 'سلبي'],
            datasets: [{
                data: [{{ $sentimentStats['positive_percentage'] }}, {{ $sentimentStats['neutral_percentage'] }}, {{ $sentimentStats['negative_percentage'] }}],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Line Chart
    const lineCtx = document.getElementById('sentimentLineChart').getContext('2d');
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
            datasets: [{
                label: 'إيجابي',
                data: [65, 68, 72, 70, 75, {{ $sentimentStats['positive_percentage'] }}],
                borderColor: '#10b981',
                tension: 0.4
            }, {
                label: 'سلبي',
                data: [15, 12, 10, 12, 8, {{ $sentimentStats['negative_percentage'] }}],
                borderColor: '#ef4444',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>
@endpush
