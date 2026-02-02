@extends('admin.layouts.admin')

@section('title', 'اتجاهات المشاعر')
@section('page-title', 'اتجاهات المشاعر')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-chart-line text-green-500 ml-3"></i>
                    اتجاهات المشاعر
                </h1>
                <p class="text-gray-600 mt-2">تحليل اتجاهات المشاعر وتغيراتها مع الوقت</p>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('bigdata.sentiment-analysis') }}" class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-3 rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للتحليل
                </a>
                <button onclick="refreshTrends()" class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-sync-alt ml-2"></i>
                    تحديث
                </button>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">تحسن المشاعر</p>
                        <p class="text-3xl font-bold mt-2">+15.3%</p>
                        <p class="text-green-100 text-sm mt-2">
                            <i class="fas fa-arrow-up ml-1"></i>
                            هذا الشهر
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-trending-up text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">أعلى إيجابية</p>
                        <p class="text-3xl font-bold mt-2">89.2%</p>
                        <p class="text-blue-100 text-sm mt-2">
                            <i class="fas fa-peak ml-1"></i>
                            هذا الأسبوع
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-peak text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-100 text-sm font-medium">متوسط التغيير</p>
                        <p class="text-3xl font-bold mt-2">+2.8%</p>
                        <p class="text-amber-100 text-sm mt-2">
                            <i class="fas fa-chart-line ml-1"></i>
                            يومياً
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">توقعات الشهر</p>
                        <p class="text-3xl font-bold mt-2">إيجابي</p>
                        <p class="text-purple-100 text-sm mt-2">
                            <i class="fas fa-crystal-ball ml-1"></i>
                            78% احتمالية
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-crystal-ball text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Trend Chart -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-chart-area text-blue-500 ml-3"></i>
                    اتجاه المشاعر - 6 أشهر
                </h3>
                <div class="flex space-x-reverse space-x-3">
                    <select class="bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option>6 أشهر</option>
                        <option>3 أشهر</option>
                        <option>شهر</option>
                        <option>سنة</option>
                    </select>
                    <select class="bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option>جميع المصادر</option>
                        <option>مراجعات</option>
                        <option>وسائل تواصل</option>
                        <option>أخبار</option>
                    </select>
                </div>
            </div>
            
            <div class="h-96">
                <canvas id="mainTrendChart"></canvas>
            </div>
        </div>

        <!-- Detailed Trends -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-calendar-alt text-purple-500 ml-3"></i>
                        اتجاه شهري
                    </h3>
                    <div class="bg-purple-100 rounded-full p-2">
                        <i class="fas fa-calendar-alt text-purple-600"></i>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-clock text-orange-500 ml-3"></i>
                        اتجاه أسبوعي
                    </h3>
                    <div class="bg-orange-100 rounded-full p-2">
                        <i class="fas fa-clock text-orange-600"></i>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="weeklyTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Trend Analysis -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-arrow-up text-green-500 ml-3"></i>
                        عوامل التحسن
                    </h3>
                    <div class="bg-green-100 rounded-full p-2">
                        <i class="fas fa-thumbs-up text-green-600"></i>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <span class="text-sm font-medium text-green-800">تحسين الخدمة</span>
                        <span class="text-sm font-bold text-green-600">+8.5%</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <span class="text-sm font-medium text-green-800">أسعار تنافسية</span>
                        <span class="text-sm font-bold text-green-600">+6.2%</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <span class="text-sm font-medium text-green-800">جودة العقارات</span>
                        <span class="text-sm font-bold text-green-600">+4.8%</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <span class="text-sm font-medium text-green-800">سرعة الاستجابة</span>
                        <span class="text-sm font-bold text-green-600">+3.9%</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-arrow-down text-red-500 ml-3"></i>
                        عوامل التدهور
                    </h3>
                    <div class="bg-red-100 rounded-full p-2">
                        <i class="fas fa-thumbs-down text-red-600"></i>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                        <span class="text-sm font-medium text-red-800">تأخير التسليم</span>
                        <span class="text-sm font-bold text-red-600">-2.3%</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                        <span class="text-sm font-medium text-red-800">مشاكل الصيانة</span>
                        <span class="text-sm font-bold text-red-600">-1.8%</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                        <span class="text-sm font-medium text-red-800">ارتفاع الأسعار</span>
                        <span class="text-sm font-bold text-red-600">-1.2%</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                        <span class="text-sm font-medium text-red-800">ضعف التواصل</span>
                        <span class="text-sm font-bold text-red-600">-0.8%</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-chart-pie text-blue-500 ml-3"></i>
                        توزيع التغيير
                    </h3>
                    <div class="bg-blue-100 rounded-full p-2">
                        <i class="fas fa-chart-pie text-blue-600"></i>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="distributionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Predictions -->
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-crystal-ball text-purple-500 ml-3"></i>
                    التنبؤات المستقبلية
                </h3>
                <div class="bg-purple-100 rounded-full p-2">
                    <i class="fas fa-brain text-purple-600"></i>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-green-800">الأسبوع القادم</h4>
                        <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full">إيجابي</span>
                    </div>
                    <p class="text-2xl font-bold text-green-600">+3.2%</p>
                    <p class="text-sm text-green-600 mt-1">تحسن متوقع</p>
                    <div class="mt-2 text-xs text-green-500">
                        <i class="fas fa-chart-line ml-1"></i>
                        85% دقة
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-blue-800">الشهر القادم</h4>
                        <span class="bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-full">إيجابي</span>
                    </div>
                    <p class="text-2xl font-bold text-blue-600">+8.7%</p>
                    <p class="text-sm text-blue-600 mt-1">تحسن قوي</p>
                    <div class="mt-2 text-xs text-blue-500">
                        <i class="fas fa-chart-line ml-1"></i>
                        78% دقة
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-purple-800">3 أشهر</h4>
                        <span class="bg-purple-500 text-white text-xs font-bold px-2 py-1 rounded-full">إيجابي</span>
                    </div>
                    <p class="text-2xl font-bold text-purple-600">+12.4%</p>
                    <p class="text-sm text-purple-600 mt-1">نمو مستمر</p>
                    <div class="mt-2 text-xs text-purple-500">
                        <i class="fas fa-chart-line ml-1"></i>
                        72% دقة
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg p-4 border border-yellow-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-yellow-800">6 أشهر</h4>
                        <span class="bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded-full">محايد</span>
                    </div>
                    <p class="text-2xl font-bold text-yellow-600">+5.8%</p>
                    <p class="text-sm text-yellow-600 mt-1">استقرار</p>
                    <div class="mt-2 text-xs text-yellow-500">
                        <i class="fas fa-chart-line ml-1"></i>
                        68% دقة
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function refreshTrends() {
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
    // Main Trend Chart
    const mainCtx = document.getElementById('mainTrendChart').getContext('2d');
    new Chart(mainCtx, {
        type: 'line',
        data: {
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
            datasets: [{
                label: 'إيجابي',
                data: [65, 68, 72, 75, 82, 89],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4
            }, {
                label: 'سلبي',
                data: [25, 22, 18, 15, 12, 8],
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4
            }, {
                label: 'محايد',
                data: [10, 10, 10, 10, 6, 3],
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });

    // Monthly Trend Chart
    const monthlyCtx = document.getElementById('monthlyTrendChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
            datasets: [{
                label: 'تغير المشاعر (%)',
                data: [2.1, 3.5, 4.2, 5.8, 7.3, 8.9],
                backgroundColor: '#10b981'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Weekly Trend Chart
    const weeklyCtx = document.getElementById('weeklyTrendChart').getContext('2d');
    new Chart(weeklyCtx, {
        type: 'line',
        data: {
            labels: ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'],
            datasets: [{
                label: 'مشاعر اليوم',
                data: [82, 84, 86, 85, 88, 89, 87],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Distribution Chart
    const distributionCtx = document.getElementById('distributionChart').getContext('2d');
    new Chart(distributionCtx, {
        type: 'doughnut',
        data: {
            labels: ['تحسن', 'استقرار', 'تدهور'],
            datasets: [{
                data: [65, 25, 10],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
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
