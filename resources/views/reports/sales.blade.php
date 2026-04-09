@extends('admin.layouts.admin')

@section('title', 'تقارير المبيعات')
@section('page-title', 'تقارير المبيعات')

@section('content')
    <!-- Page Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-500 rounded-3xl shadow-2xl p-8 text-white relative overflow-hidden">
            <!-- Animated Background Pattern -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full -mr-48 -mt-48 animate-pulse"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-white rounded-full -ml-32 -mb-32 animate-pulse" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 left-1/2 w-48 h-48 bg-yellow-300 rounded-full -ml-24 -mt-24 animate-pulse" style="animation-delay: 2s;"></div>
            </div>
            
            <!-- Grid Pattern Overlay -->
            <div class="absolute inset-0 opacity-10">
                <div class="h-full w-full" style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>
            </div>
            
            <div class="relative z-10">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="bg-white bg-opacity-25 rounded-2xl p-4 ml-4 backdrop-blur-sm border border-white border-opacity-40 shadow-xl transform hover:scale-105 transition-transform duration-300">
                                <i class="fas fa-chart-line text-white text-3xl"></i>
                            </div>
                            <div>
                                <h1 class="text-5xl font-black text-white mb-2 bg-clip-text text-transparent bg-gradient-to-r from-white to-yellow-200">تقارير المبيعات</h1>
                                <p class="text-white text-xl font-medium">تحليل أداء المبيعات والاتجاهات</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-6 mt-6">
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">إجمالي المبيعات:</span>
                                <span class="text-lg font-black text-white ml-2">${{ number_format($stats['total_sales'] ?? 0, 0) }}</span>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">الخصائص المباعة:</span>
                                <span class="text-lg font-black text-white ml-2">{{ number_format($stats['properties_sold'] ?? 0) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('reports.dashboard') }}" class="bg-white bg-opacity-25 backdrop-blur-sm border border-white border-opacity-40 text-white px-6 py-3 rounded-xl font-medium hover:bg-white hover:bg-opacity-30 transition-all duration-300 shadow-lg">
                            <i class="fas fa-arrow-left ml-2"></i>
                            لوحة التحكم
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-700 font-medium">إجمالي المبيعات</p>
                    <p class="text-3xl font-bold text-green-900 mt-2">${{ number_format($stats['total_sales'] ?? 0, 0) }}</p>
                    <p class="text-green-600 text-sm mt-1">
                        <i class="fas fa-arrow-up ml-1"></i>
                        +12.5% من الشهر الماضي
                    </p>
                </div>
                <div class="bg-gradient-to-br from-green-400 to-emerald-500 rounded-2xl p-4 shadow-lg">
                    <i class="fas fa-dollar-sign text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-700 font-medium">الخصائص المباعة</p>
                    <p class="text-3xl font-bold text-blue-900 mt-2">{{ number_format($stats['properties_sold'] ?? 0) }}</p>
                    <p class="text-blue-600 text-sm mt-1">
                        <i class="fas fa-arrow-up ml-1"></i>
                        +8.3% من الشهر الماضي
                    </p>
                </div>
                <div class="bg-gradient-to-br from-blue-400 to-indigo-500 rounded-2xl p-4 shadow-lg">
                    <i class="fas fa-home text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-700 font-medium">متوسط السعر</p>
                    <p class="text-3xl font-bold text-purple-900 mt-2">${{ number_format(($stats['total_sales'] ?? 0) / max($stats['properties_sold'] ?? 1, 1), 0) }}</p>
                    <p class="text-purple-600 text-sm mt-1">
                        <i class="fas fa-arrow-up ml-1"></i>
                        +5.2% من الشهر الماضي
                    </p>
                </div>
                <div class="bg-gradient-to-br from-purple-400 to-pink-500 rounded-2xl p-4 shadow-lg">
                    <i class="fas fa-chart-bar text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-yellow-50 rounded-2xl p-6 border border-orange-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-700 font-medium">معدل التحويل</p>
                    <p class="text-3xl font-bold text-orange-900 mt-2">68.5%</p>
                    <p class="text-orange-600 text-sm mt-1">
                        <i class="fas fa-arrow-up ml-1"></i>
                        +3.1% من الشهر الماضي
                    </p>
                </div>
                <div class="bg-gradient-to-br from-orange-400 to-yellow-500 rounded-2xl p-4 shadow-lg">
                    <i class="fas fa-percentage text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Sales Trend Chart -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">اتجاهات المبيعات</h3>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-200 transition-colors">شهر</button>
                    <button class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">ربع</button>
                    <button class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">سنة</button>
                </div>
            </div>
            <div class="h-80">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        <!-- Sales by Type Chart -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">المبيعات حسب النوع</h3>
                <button class="text-blue-600 hover:text-blue-700 transition-colors">
                    <i class="fas fa-ellipsis-h"></i>
                </button>
            </div>
            <div class="h-80">
                <canvas id="salesByTypeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Sales Table -->
    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800">المبيعات الأخيرة</h3>
            <div class="flex space-x-3">
                <div class="relative">
                    <input type="text" placeholder="بحث..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
                </div>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                    <i class="fas fa-filter ml-2"></i>
                    فلترة
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-right py-3 px-4 font-semibold text-gray-700">الخاصية</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-700">العميل</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-700">السعر</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-700">التاريخ</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-700">الحالة</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-700">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-4">
                            <div class="flex items-center">
                                <img src="https://via.placeholder.com/40x40" alt="Property" class="w-10 h-10 rounded-lg ml-3">
                                <div>
                                    <p class="font-medium text-gray-800">فيلا حديثة</p>
                                    <p class="text-sm text-gray-500">الرياض - حي النخيل</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center">
                                <img src="https://via.placeholder.com/32x32" alt="Client" class="w-8 h-8 rounded-full ml-2">
                                <span class="text-gray-700">أحمد محمد</span>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-semibold text-gray-800">$1,250,000</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-gray-600">15 ديسمبر 2024</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-medium">
                                <i class="fas fa-check-circle ml-1"></i>
                                مكتمل
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex space-x-2">
                                <button class="text-blue-600 hover:text-blue-700 transition-colors">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-gray-600 hover:text-gray-700 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-700 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-4">
                            <div class="flex items-center">
                                <img src="https://via.placeholder.com/40x40" alt="Property" class="w-10 h-10 rounded-lg ml-3">
                                <div>
                                    <p class="font-medium text-gray-800">شقة سكنية</p>
                                    <p class="text-sm text-gray-500">جدة - حي الروضة</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center">
                                <img src="https://via.placeholder.com/32x32" alt="Client" class="w-8 h-8 rounded-full ml-2">
                                <span class="text-gray-700">محمد علي</span>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-semibold text-gray-800">$850,000</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-gray-600">14 ديسمبر 2024</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-sm font-medium">
                                <i class="fas fa-clock ml-1"></i>
                                قيد المعالجة
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex space-x-2">
                                <button class="text-blue-600 hover:text-blue-700 transition-colors">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-gray-600 hover:text-gray-700 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-700 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Custom animations and styles */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-in {
        animation: fadeIn 0.6s ease-out;
    }
    
    .hover-scale {
        transition: transform 0.3s ease;
    }
    
    .hover-scale:hover {
        transform: scale(1.05);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sample data for demonstration
    const salesTrendData = {
        labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
        datasets: [{
            label: 'إجمالي المبيعات',
            data: [1200000, 1500000, 1800000, 1600000, 2100000, 2400000],
            borderColor: 'rgb(16, 185, 129)',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
        }]
    };
    
    const salesByTypeData = {
        labels: ['فيلا', 'شقة', 'أرض', 'مبنى تجاري', 'منزل'],
        datasets: [{
            data: [35, 28, 15, 12, 10],
            backgroundColor: [
                'rgb(16, 185, 129)',
                'rgb(59, 130, 246)',
                'rgb(168, 85, 247)',
                'rgb(251, 146, 60)',
                'rgb(236, 72, 153)'
            ]
        }]
    };
    
    // Sales Trend Chart
    const salesTrendCtx = document.getElementById('salesTrendChart').getContext('2d');
    new Chart(salesTrendCtx, {
        type: 'line',
        data: salesTrendData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            family: 'system-ui'
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    
    // Sales by Type Chart
    const salesByTypeCtx = document.getElementById('salesByTypeChart').getContext('2d');
    new Chart(salesByTypeCtx, {
        type: 'doughnut',
        data: salesByTypeData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: {
                            family: 'system-ui'
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
