@extends('admin.layouts.admin')

@section('title', 'قاعدة البيانات')
@section('page-title', 'قاعدة البيانات')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
<style>
    .chart-container {
        position: relative;
        height: 300px;
    }
    .db-metric {
        transition: all 0.3s ease;
    }
    .db-metric:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .status-indicator {
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
    .status-online { background: #10b981; }
    .status-warning { background: #f59e0b; }
    .status-offline { background: #ef4444; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl p-8 text-white shadow-xl mb-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-pattern opacity-10"></div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h1 class="text-3xl font-bold mb-2 flex items-center gap-3">
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="fas fa-database"></i>
                        </div>
                        قاعدة البيانات
                    </h1>
                    <p class="text-lg opacity-90">مراقبة وإدارة قاعدة البيانات</p>
                </div>
                <button onclick="refreshDatabase()" class="bg-white text-indigo-600 hover:bg-indigo-50 px-6 py-3 rounded-xl font-bold shadow-lg transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2 group">
                    <i class="fas fa-sync-alt group-hover:animate-spin"></i>
                    تحديث البيانات
                </button>
            </div>
        </div>

        <!-- Database Status -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="bg-green-100 p-2 rounded-lg ml-3">
                    <i class="fas fa-server text-green-600"></i>
                </div>
                حالة قاعدة البيانات
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="db-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="flex items-center justify-center mb-2">
                        <span class="status-indicator status-online"></span>
                        <span class="text-2xl font-bold text-gray-800">متصل</span>
                    </div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">حالة الاتصال</div>
                </div>
                
                <div class="db-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="text-4xl font-bold text-gray-800 mb-2">MySQL 8.0</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">نوع قاعدة البيانات</div>
                </div>
                
                <div class="db-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="text-4xl font-bold text-gray-800 mb-2">3306</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">المنفذ</div>
                </div>
                
                <div class="db-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="text-4xl font-bold text-gray-800 mb-2">localhost</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">الخادم</div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="bg-blue-100 p-2 rounded-lg ml-3">
                    <i class="fas fa-tachometer-alt text-blue-600"></i>
                </div>
                مؤشرات الأداء
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="db-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="text-4xl font-bold text-gray-800 mb-2" id="query-time">0.8ms</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">متوسط وقت الاستعلام</div>
                </div>
                
                <div class="db-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="text-4xl font-bold text-gray-800 mb-2" id="queries-per-sec">125</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">استعلامات في الثانية</div>
                </div>
                
                <div class="db-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="text-4xl font-bold text-gray-800 mb-2" id="active-connections">45</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">الاتصالات النشطة</div>
                </div>
                
                <div class="db-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="text-4xl font-bold text-gray-800 mb-2" id="cache-hit">94.5%</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">نسبة الكاش</div>
                </div>
            </div>
        </div>

        <!-- Database Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                    <div class="bg-purple-100 p-2 rounded-lg ml-3">
                        <i class="fas fa-chart-line text-purple-600"></i>
                    </div>
                    أداء الاستعلامات
                </h2>
                <div class="chart-container">
                    <canvas id="queryChart"></canvas>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                    <div class="bg-orange-100 p-2 rounded-lg ml-3">
                        <i class="fas fa-chart-bar text-orange-600"></i>
                    </div>
                    استخدام المساحة
                </h2>
                <div class="chart-container">
                    <canvas id="storageChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Database Tables -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="bg-teal-100 p-2 rounded-lg ml-3">
                    <i class="fas fa-table text-teal-600"></i>
                </div>
                جداول قاعدة البيانات
            </h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الجدول</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الصفوف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحجم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المحرك</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="tables-container">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <i class="fas fa-table text-indigo-500 ml-3"></i>
                                    <span class="text-sm font-medium text-gray-900">users</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1,234</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">12.5 MB</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">InnoDB</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">نشط</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <i class="fas fa-table text-indigo-500 ml-3"></i>
                                    <span class="text-sm font-medium text-gray-900">properties</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">5,678</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">45.2 MB</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">InnoDB</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">نشط</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <i class="fas fa-table text-indigo-500 ml-3"></i>
                                    <span class="text-sm font-medium text-gray-900">agents</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">892</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">8.7 MB</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">InnoDB</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">نشط</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <i class="fas fa-table text-indigo-500 ml-3"></i>
                                    <span class="text-sm font-medium text-gray-900">companies</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">156</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">3.2 MB</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">InnoDB</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">نشط</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Queries -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold flex items-center text-gray-800">
                    <div class="bg-red-100 p-2 rounded-lg ml-3">
                        <i class="fas fa-code text-red-600"></i>
                    </div>
                    الاستعلامات الحديثة
                </h2>
                <button onclick="clearSlowQueries()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-trash"></i>
                    مسح البطيئة
                </button>
            </div>
            
            <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar" id="queries-container">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <div class="bg-yellow-100 p-1.5 rounded-full">
                                <i class="fas fa-exclamation-triangle text-yellow-600 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-mono text-gray-800 mb-1">SELECT * FROM properties WHERE status = 'active' ORDER BY created_at DESC</p>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span>المدة: 2.3ms</span>
                                    <span>الصفوف: 1,234</span>
                                    <span>منذ 2 دقيقة</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <div class="bg-green-100 p-1.5 rounded-full">
                                <i class="fas fa-check-circle text-green-600 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-mono text-gray-800 mb-1">UPDATE users SET last_login = NOW() WHERE id = 1234</p>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span>المدة: 0.8ms</span>
                                    <span>متأثر: 1 صف</span>
                                    <span>منذ 5 دقائق</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <div class="bg-green-100 p-1.5 rounded-full">
                                <i class="fas fa-check-circle text-green-600 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-mono text-gray-800 mb-1">INSERT INTO logs (user_id, action, created_at) VALUES (?, ?, ?)</p>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span>المدة: 0.5ms</span>
                                    <span>متأثر: 1 صف</span>
                                    <span>منذ 8 دقائق</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <div class="bg-yellow-100 p-1.5 rounded-full">
                                <i class="fas fa-exclamation-triangle text-yellow-600 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-mono text-gray-800 mb-1">SELECT COUNT(*) FROM activities WHERE DATE(created_at) = CURDATE()</p>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span>المدة: 1.8ms</span>
                                    <span>الصفوف: 1</span>
                                    <span>منذ 12 دقيقة</span>
                                </div>
                            </div>
                        </div>
                    </div>
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
let queryChart, storageChart;

// Chart configuration
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false
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
            grid: {
                color: '#e5e7eb'
            },
            ticks: {
                color: '#6b7280'
            }
        }
    }
};

// Initialize Query Performance Chart
function initQueryChart() {
    const ctx = document.getElementById('queryChart').getContext('2d');
    queryChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: generateTimeLabels(20),
            datasets: [{
                label: 'Query Time (ms)',
                data: generateRandomData(20, 0.5, 3.0),
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#8b5cf6'
            }]
        },
        options: {
            ...chartOptions,
            scales: {
                ...chartOptions.scales,
                y: {
                    ...chartOptions.scales.y,
                    ticks: {
                        ...chartOptions.scales.y.ticks,
                        callback: function(value) {
                            return value + 'ms';
                        }
                    }
                }
            }
        }
    });
}

// Initialize Storage Chart
function initStorageChart() {
    const ctx = document.getElementById('storageChart').getContext('2d');
    storageChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['البيانات', 'الفهرسة', 'الحر', 'النظام'],
            datasets: [{
                data: [65, 15, 12, 8],
                backgroundColor: [
                    '#3b82f6',
                    '#8b5cf6',
                    '#f59e0b',
                    '#10b981'
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
                            return context.label + ': ' + context.parsed + ' GB';
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
        data.push((Math.random() * (max - min) + min).toFixed(2));
    }
    return data;
}

// Update charts with new data
function updateCharts() {
    // Update Query Chart
    if (queryChart) {
        queryChart.data.labels.shift();
        queryChart.data.labels.push(new Date().toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' }));
        queryChart.data.datasets[0].data.shift();
        queryChart.data.datasets[0].data.push((Math.random() * 2.5 + 0.5).toFixed(2));
        queryChart.update('none');
        
        // Update query time display
        const newQueryTime = queryChart.data.datasets[0].data[queryChart.data.datasets[0].data.length - 1];
        document.getElementById('query-time').textContent = newQueryTime + 'ms';
    }
}

function refreshDatabase() {
    const button = event.currentTarget;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحديث...';
    button.disabled = true;
    button.classList.add('opacity-75', 'cursor-not-allowed');
    
    // Update charts immediately
    updateCharts();
    
    // Update metrics
    document.getElementById('queries-per-sec').textContent = Math.floor(Math.random() * 50 + 100);
    document.getElementById('active-connections').textContent = Math.floor(Math.random() * 20 + 35);
    document.getElementById('cache-hit').textContent = (Math.random() * 5 + 92).toFixed(1) + '%';
    
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check"></i> تم التحديث';
        button.classList.remove('bg-indigo-600', 'hover:bg-indigo-50');
        button.classList.add('bg-green-600', 'hover:bg-green-50');
        
        showNotification('تم تحديث بيانات قاعدة البيانات بنجاح', 'success');
        
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
            button.classList.remove('opacity-75', 'cursor-not-allowed', 'bg-green-600', 'hover:bg-green-50');
            button.classList.add('bg-indigo-600', 'hover:bg-indigo-50');
        }, 2000);
    }, 1500);
}

function clearSlowQueries() {
    const button = event.currentTarget;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري المسح...';
    button.disabled = true;
    
    setTimeout(() => {
        // Clear queries container
        document.getElementById('queries-container').innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-check-circle text-4xl text-green-500 mb-3"></i>
                <p>تم مسح جميع الاستعلامات البطيئة</p>
            </div>
        `;
        
        button.innerHTML = '<i class="fas fa-check"></i> تم المسح';
        button.classList.remove('bg-red-600', 'hover:bg-red-700');
        button.classList.add('bg-green-600', 'hover:bg-green-700');
        
        showNotification('تم مسح الاستعلامات البطيئة بنجاح', 'success');
        
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
            button.classList.remove('bg-green-600', 'hover:bg-green-700');
            button.classList.add('bg-red-600', 'hover:bg-red-700');
        }, 2000);
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
    initQueryChart();
    initStorageChart();
    
    // Auto-update charts every 5 seconds
    setInterval(updateCharts, 5000);
});
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endpush
