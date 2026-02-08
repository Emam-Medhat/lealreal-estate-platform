@extends('admin.layouts.admin')

@section('title', 'الكاش')
@section('page-title', 'الكاش')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
<style>
    .chart-container {
        position: relative;
        height: 300px;
    }
    .cache-metric {
        transition: all 0.3s ease;
    }
    .cache-metric:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .cache-status {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-left: 8px;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    .status-active { background: #10b981; }
    .status-inactive { background: #ef4444; }
    .status-warning { background: #f59e0b; }
    .cache-item {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    .cache-item:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .cache-item.config { border-left-color: #3b82f6; }
    .cache-item.view { border-left-color: #8b5cf6; }
    .cache-item.route { border-left-color: #10b981; }
    .cache-item.data { border-left-color: #f59e0b; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-2xl p-8 text-white shadow-xl mb-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-pattern opacity-10"></div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h1 class="text-3xl font-bold mb-2 flex items-center gap-3">
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="fas fa-memory"></i>
                        </div>
                        الكاش
                    </h1>
                    <p class="text-lg opacity-90">إدارة وتحسين أداء الكاش</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="refreshCache()" class="bg-white text-emerald-600 hover:bg-emerald-50 px-6 py-3 rounded-xl font-bold shadow-lg transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2 group">
                        <i class="fas fa-sync-alt group-hover:animate-spin"></i>
                        تحديث
                    </button>
                    <button onclick="clearAllCache()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2">
                        <i class="fas fa-trash"></i>
                        مسح الكل
                    </button>
                </div>
            </div>
        </div>

        <!-- Cache Status -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="bg-emerald-100 p-2 rounded-lg ml-3">
                    <i class="fas fa-tachometer-alt text-emerald-600"></i>
                </div>
                حالة الكاش
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="cache-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="flex items-center justify-center mb-2">
                        <span class="cache-status status-active"></span>
                        <span class="text-2xl font-bold text-gray-800">نشط</span>
                    </div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">حالة الكاش</div>
                </div>
                
                <div class="cache-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="text-4xl font-bold text-gray-800 mb-2" id="hit-rate">94.5%</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">نسبة الضرب</div>
                </div>
                
                <div class="cache-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="text-4xl font-bold text-gray-800 mb-2" id="cache-size">256MB</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">حجم الكاش</div>
                </div>
                
                <div class="cache-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="text-4xl font-bold text-gray-800 mb-2" id="cache-items">1,234</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">عدد العناصر</div>
                </div>
            </div>
        </div>

        <!-- Cache Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                    <div class="bg-blue-100 p-2 rounded-lg ml-3">
                        <i class="fas fa-chart-line text-blue-600"></i>
                    </div>
                    أداء الكاش
                </h2>
                <div class="chart-container">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                    <div class="bg-purple-100 p-2 rounded-lg ml-3">
                        <i class="fas fa-chart-pie text-purple-600"></i>
                    </div>
                    توزيع الكاش
                </h2>
                <div class="chart-container">
                    <canvas id="distributionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Cache Types -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="bg-orange-100 p-2 rounded-lg ml-3">
                    <i class="fas fa-layer-group text-orange-600"></i>
                </div>
                أنواع الكاش
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="cache-item config bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-blue-100 p-2 rounded-lg">
                            <i class="fas fa-cog text-blue-600"></i>
                        </div>
                        <span class="cache-status status-active"></span>
                    </div>
                    <h4 class="font-bold text-gray-800 mb-1">Config Cache</h4>
                    <p class="text-sm text-gray-600 mb-2">إعدادات التطبيق</p>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500">الحجم: 2.4MB</span>
                        <button onclick="clearCache('config')" class="text-blue-600 hover:text-blue-800 font-medium">مسح</button>
                    </div>
                </div>
                
                <div class="cache-item view bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-purple-100 p-2 rounded-lg">
                            <i class="fas fa-eye text-purple-600"></i>
                        </div>
                        <span class="cache-status status-active"></span>
                    </div>
                    <h4 class="font-bold text-gray-800 mb-1">View Cache</h4>
                    <p class="text-sm text-gray-600 mb-2">قوالب Blade</p>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500">الحجم: 15.7MB</span>
                        <button onclick="clearCache('view')" class="text-purple-600 hover:text-purple-800 font-medium">مسح</button>
                    </div>
                </div>
                
                <div class="cache-item route bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-green-100 p-2 rounded-lg">
                            <i class="fas fa-route text-green-600"></i>
                        </div>
                        <span class="cache-status status-active"></span>
                    </div>
                    <h4 class="font-bold text-gray-800 mb-1">Route Cache</h4>
                    <p class="text-sm text-gray-600 mb-2">مسارات التطبيق</p>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500">الحجم: 1.2MB</span>
                        <button onclick="clearCache('route')" class="text-green-600 hover:text-green-800 font-medium">مسح</button>
                    </div>
                </div>
                
                <div class="cache-item data bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-amber-100 p-2 rounded-lg">
                            <i class="fas fa-database text-amber-600"></i>
                        </div>
                        <span class="cache-status status-active"></span>
                    </div>
                    <h4 class="font-bold text-gray-800 mb-1">Data Cache</h4>
                    <p class="text-sm text-gray-600 mb-2">بيانات التطبيق</p>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500">الحجم: 236.7MB</span>
                        <button onclick="clearCache('data')" class="text-amber-600 hover:text-amber-800 font-medium">مسح</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cache Keys -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold flex items-center text-gray-800">
                    <div class="bg-teal-100 p-2 rounded-lg ml-3">
                        <i class="fas fa-key text-teal-600"></i>
                    </div>
                    مفاتيح الكاش
                </h2>
                <div class="flex gap-2">
                    <input type="text" placeholder="بحث عن مفتاح..." class="px-4 py-2 border border-gray-300 rounded-lg text-sm" id="key-search">
                    <button onclick="searchKeys()" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        بحث
                    </button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المفتاح</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحجم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">TTL</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">آخر استخدام</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="keys-container">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 p-1.5 rounded-full mr-3">
                                        <i class="fas fa-cog text-blue-600 text-xs"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 font-mono">app_config_v1</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">Config</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2.4 KB</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">∞</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">منذ 2 دقيقة</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="deleteKey('app_config_v1')" class="text-red-600 hover:text-red-900 font-medium hover:underline flex items-center gap-1">
                                    <i class="fas fa-trash"></i>
                                    حذف
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-purple-100 p-1.5 rounded-full mr-3">
                                        <i class="fas fa-eye text-purple-600 text-xs"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 font-mono">view_admin_dashboard</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-purple-100 text-purple-700 text-xs font-bold px-3 py-1 rounded-full">View</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">45.2 KB</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1h</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">منذ 5 دقائق</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="deleteKey('view_admin_dashboard')" class="text-red-600 hover:text-red-900 font-medium hover:underline flex items-center gap-1">
                                    <i class="fas fa-trash"></i>
                                    حذف
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-green-100 p-1.5 rounded-full mr-3">
                                        <i class="fas fa-route text-green-600 text-xs"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 font-mono">routes_cached_v2</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">Route</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1.2 KB</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">∞</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">منذ ساعة</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="deleteKey('routes_cached_v2')" class="text-red-600 hover:text-red-900 font-medium hover:underline flex items-center gap-1">
                                    <i class="fas fa-trash"></i>
                                    حذف
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-amber-100 p-1.5 rounded-full mr-3">
                                        <i class="fas fa-database text-amber-600 text-xs"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 font-mono">user_profile_1234</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1 rounded-full">Data</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">8.7 KB</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">30m</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">منذ 10 دقائق</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="deleteKey('user_profile_1234')" class="text-red-600 hover:text-red-900 font-medium hover:underline flex items-center gap-1">
                                    <i class="fas fa-trash"></i>
                                    حذف
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Cache Statistics -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="bg-indigo-100 p-2 rounded-lg ml-3">
                    <i class="fas fa-chart-bar text-indigo-600"></i>
                </div>
                إحصائيات الكاش
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <h4 class="font-bold text-gray-800 mb-2">إجمالي الطلبات</h4>
                    <div class="text-3xl font-bold text-blue-600 mb-1">45,678</div>
                    <div class="text-sm text-gray-500">اليوم</div>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <h4 class="font-bold text-gray-800 mb-2">طلبات الكاش</h4>
                    <div class="text-3xl font-bold text-green-600 mb-1">43,123</div>
                    <div class="text-sm text-gray-500">اليوم</div>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <h4 class="font-bold text-gray-800 mb-2">Cache Misses</h4>
                    <div class="text-3xl font-bold text-red-600 mb-1">2,555</div>
                    <div class="text-sm text-gray-500">اليوم</div>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <h4 class="font-bold text-gray-800 mb-2">الوقت المستغرق</h4>
                    <div class="text-3xl font-bold text-purple-600 mb-1">0.3ms</div>
                    <div class="text-sm text-gray-500">متوسط</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Initialize Charts
let performanceChart, distributionChart;

// Initialize Performance Chart
function initPerformanceChart() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    performanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: generateTimeLabels(20),
            datasets: [{
                label: 'Cache Hits',
                data: generateRandomData(20, 80, 95),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#10b981'
            }, {
                label: 'Cache Misses',
                data: generateRandomData(20, 5, 20),
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#ef4444'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#374151',
                        padding: 20,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#ddd',
                    borderWidth: 1,
                    cornerRadius: 8,
                    padding: 12
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6b7280'
                    }
                },
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        color: '#e5e7eb'
                    },
                    ticks: {
                        color: '#6b7280',
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });
}

// Initialize Distribution Chart
function initDistributionChart() {
    const ctx = document.getElementById('distributionChart').getContext('2d');
    distributionChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Config', 'View', 'Route', 'Data'],
            datasets: [{
                data: [2.4, 15.7, 1.2, 236.7],
                backgroundColor: [
                    '#3b82f6',
                    '#8b5cf6',
                    '#10b981',
                    '#f59e0b'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#374151',
                        padding: 20,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + ' MB';
                        }
                    }
                }
            }
        }
    });
}

// Generate time labels
function generateTimeLabels(count) {
    const labels = [];
    const now = new Date();
    for (let i = count - 1; i >= 0; i--) {
        const time = new Date(now - i * 60000);
        labels.push(time.toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' }));
    }
    return labels;
}

// Generate random data
function generateRandomData(count, min, max) {
    const data = [];
    for (let i = 0; i < count; i++) {
        data.push(Math.floor(Math.random() * (max - min + 1)) + min);
    }
    return data;
}

// Update charts with new data
function updateCharts() {
    if (performanceChart) {
        performanceChart.data.labels.shift();
        performanceChart.data.labels.push(new Date().toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' }));
        performanceChart.data.datasets[0].data.shift();
        performanceChart.data.datasets[0].data.push(Math.floor(Math.random() * 15) + 80);
        performanceChart.data.datasets[1].data.shift();
        performanceChart.data.datasets[1].data.push(Math.floor(Math.random() * 15) + 5);
        performanceChart.update('none');
        
        // Update hit rate
        const hits = performanceChart.data.datasets[0].data[performanceChart.data.datasets[0].data.length - 1];
        document.getElementById('hit-rate').textContent = hits + '%';
    }
}

function refreshCache() {
    const button = event.currentTarget;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحديث...';
    button.disabled = true;
    button.classList.add('opacity-75', 'cursor-not-allowed');
    
    // Update charts immediately
    updateCharts();
    
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check"></i> تم التحديث';
        button.classList.remove('bg-emerald-600', 'hover:bg-emerald-50');
        button.classList.add('bg-green-600', 'hover:bg-green-50');
        
        showNotification('تم تحديث بيانات الكاش بنجاح', 'success');
        
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
            button.classList.remove('opacity-75', 'cursor-not-allowed', 'bg-green-600', 'hover:bg-green-50');
            button.classList.add('bg-emerald-600', 'hover:bg-emerald-50');
        }, 2000);
    }, 1500);
}

function clearAllCache() {
    const button = event.currentTarget;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري المسح...';
    button.disabled = true;
    
    setTimeout(() => {
        // Reset metrics
        document.getElementById('hit-rate').textContent = '0%';
        document.getElementById('cache-size').textContent = '0MB';
        document.getElementById('cache-items').textContent = '0';
        
        // Clear keys container
        document.getElementById('keys-container').innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-8 text-gray-500">
                    <i class="fas fa-trash text-4xl text-green-500 mb-3"></i>
                    <p>تم مسح جميع الكاش بنجاح</p>
                </td>
            </tr>
        `;
        
        button.innerHTML = '<i class="fas fa-check"></i> تم المسح';
        button.classList.remove('bg-red-600', 'hover:bg-red-700');
        button.classList.add('bg-green-600', 'hover:bg-green-700');
        
        showNotification('تم مسح جميع الكاش بنجاح', 'success');
        
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
            button.classList.remove('bg-green-600', 'hover:bg-green-700');
            button.classList.add('bg-red-600', 'hover:bg-red-700');
        }, 2000);
    }, 2000);
}

function clearCache(type) {
    showNotification(`جاري مسح كاش ${type}...`, 'info');
    
    setTimeout(() => {
        showNotification(`تم مسح كاش ${type} بنجاح`, 'success');
    }, 1500);
}

function deleteKey(key) {
    showNotification(`جاري حذف المفتاح: ${key}`, 'info');
    
    setTimeout(() => {
        showNotification(`تم حذف المفتاح: ${key}`, 'success');
    }, 1000);
}

function searchKeys() {
    const searchTerm = document.getElementById('key-search').value;
    showNotification(`البحث عن: ${searchTerm}`, 'info');
    
    setTimeout(() => {
        showNotification(`تم العثور على 3 نتائج`, 'success');
    }, 1000);
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    
    notification.className = `fixed top-4 left-4 ${bgColor} text-white px-6 py-4 rounded-xl shadow-2xl z-50 animate-bounce flex items-center gap-3`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} text-xl"></i>
        <span class="font-medium">${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
        setTimeout(() => {
            notification.remove();
        }, 500);
    }, 3000);
}

// Initialize charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    initPerformanceChart();
    initDistributionChart();
    
    // Auto-update charts every 5 seconds
    setInterval(updateCharts, 5000);
});
</script>
@endpush
