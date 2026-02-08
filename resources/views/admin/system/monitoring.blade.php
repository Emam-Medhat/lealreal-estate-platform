@extends('admin.layouts.admin')

@section('title', 'مراقبة النظام')
@section('page-title', 'مراقبة النظام')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
<style>
    .chart-container {
        position: relative;
        height: 300px;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-br from-teal-600 to-cyan-700 rounded-2xl p-8 text-white shadow-xl mb-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-pattern opacity-10"></div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h1 class="text-3xl font-bold mb-2 flex items-center gap-3">
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        مراقبة النظام
                    </h1>
                    <p class="text-lg opacity-90">مراقبة أداء النظام في الوقت الفعلي</p>
                </div>
                <button onclick="refreshMonitoring()" class="bg-white text-teal-600 hover:bg-teal-50 px-6 py-3 rounded-xl font-bold shadow-lg transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2 group">
                    <i class="fas fa-sync-alt group-hover:animate-spin"></i>
                    تحديث البيانات
                </button>
            </div>
        </div>

        <!-- System Performance -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="bg-teal-100 p-2 rounded-lg ml-3">
                    <i class="fas fa-tachometer-alt text-teal-600"></i>
                </div>
                أداء النظام
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gray-50 rounded-xl p-6 text-center border border-gray-200 hover:shadow-md transition-all duration-300 group">
                    <div class="text-4xl font-bold text-gray-800 mb-2 group-hover:text-teal-600 transition-colors" id="cpu-usage">85%</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">استخدام المعالج</div>
                    <div class="mt-3 bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-teal-500 h-full rounded-full transition-all duration-500" id="cpu-progress" style="width: 85%"></div>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-6 text-center border border-gray-200 hover:shadow-md transition-all duration-300 group">
                    <div class="text-4xl font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors" id="memory-usage">4.2GB</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">استخدام الذاكرة</div>
                    <div class="mt-3 bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-blue-500 h-full rounded-full transition-all duration-500" id="memory-progress" style="width: 65%"></div>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-6 text-center border border-gray-200 hover:shadow-md transition-all duration-300 group">
                    <div class="text-4xl font-bold text-gray-800 mb-2 group-hover:text-green-600 transition-colors" id="storage-usage">120GB</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">مساحة التخزين</div>
                    <div class="mt-3 bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-green-500 h-full rounded-full transition-all duration-500" id="storage-progress" style="width: 45%"></div>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-6 text-center border border-gray-200 hover:shadow-md transition-all duration-300 group">
                    <div class="text-4xl font-bold text-gray-800 mb-2 group-hover:text-purple-600 transition-colors" id="uptime">99.9%</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">وقت التشغيل</div>
                    <div class="mt-3 bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-purple-500 h-full rounded-full transition-all duration-500" id="uptime-progress" style="width: 99.9%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-time Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                    <div class="bg-blue-100 p-2 rounded-lg ml-3">
                        <i class="fas fa-chart-area text-blue-600"></i>
                    </div>
                    استخدام المعالج
                </h2>
                <div class="chart-container">
                    <canvas id="cpuChart"></canvas>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                    <div class="bg-green-100 p-2 rounded-lg ml-3">
                        <i class="fas fa-chart-bar text-green-600"></i>
                    </div>
                    استخدام الذاكرة
                </h2>
                <div class="chart-container">
                    <canvas id="memoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Active Processes -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="bg-purple-100 p-2 rounded-lg ml-3">
                    <i class="fas fa-cogs text-purple-600"></i>
                </div>
                العمليات النشطة
            </h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العملية</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المعالج</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الذاكرة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-2.5 w-2.5 rounded-full bg-green-500 ml-3"></div>
                                    <span class="text-sm font-medium text-gray-900">Apache</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">12%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">256MB</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">نشط</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-2.5 w-2.5 rounded-full bg-blue-500 ml-3"></div>
                                    <span class="text-sm font-medium text-gray-900">MySQL</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">25%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">512MB</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">نشط</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-2.5 w-2.5 rounded-full bg-purple-500 ml-3"></div>
                                    <span class="text-sm font-medium text-gray-900">PHP-FPM</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">18%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">384MB</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">نشط</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-2.5 w-2.5 rounded-full bg-yellow-500 ml-3"></div>
                                    <span class="text-sm font-medium text-gray-900">Redis</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">5%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">128MB</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full">خامل</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- System Logs -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold flex items-center text-gray-800">
                    <div class="bg-orange-100 p-2 rounded-lg ml-3">
                        <i class="fas fa-file-alt text-orange-600"></i>
                    </div>
                    سجلات النظام
                </h2>
                <button onclick="refreshLogs()" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-sync-alt"></i>
                    تحديث السجلات
                </button>
            </div>
            
            <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar" id="logs-container">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <div class="bg-green-100 p-1.5 rounded-full">
                                <i class="fas fa-info-circle text-green-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">تم بدء خدمة المراقبة بنجاح</p>
                                <p class="text-xs text-gray-500 mt-1">System monitoring service started successfully</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-400">منذ 2 دقيقة</span>
                    </div>
                </div>
                
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <div class="bg-blue-100 p-1.5 rounded-full">
                                <i class="fas fa-database text-blue-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">اتصال قاعدة البيانات ناجح</p>
                                <p class="text-xs text-gray-500 mt-1">Database connection established successfully</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-400">منذ 5 دقائق</span>
                    </div>
                </div>
                
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <div class="bg-yellow-100 p-1.5 rounded-full">
                                <i class="fas fa-exclamation-triangle text-yellow-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">استخدام الذاكرة مرتفع</p>
                                <p class="text-xs text-gray-500 mt-1">Memory usage is above 80% threshold</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-400">منذ 10 دقائق</span>
                    </div>
                </div>
                
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <div class="bg-green-100 p-1.5 rounded-full">
                                <i class="fas fa-check-circle text-green-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">تم إكمال النسخ الاحتياطي</p>
                                <p class="text-xs text-gray-500 mt-1">Backup process completed successfully</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-400">منذ 15 دقيقة</span>
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
let cpuChart, memoryChart;

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
};

// Initialize CPU Chart
function initCpuChart() {
    const ctx = document.getElementById('cpuChart').getContext('2d');
    cpuChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: generateTimeLabels(20),
            datasets: [{
                label: 'CPU Usage',
                data: generateRandomData(20, 60, 90),
                borderColor: '#14b8a6',
                backgroundColor: 'rgba(20, 184, 166, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#14b8a6'
            }]
        },
        options: chartOptions
    });
}

// Initialize Memory Chart
function initMemoryChart() {
    const ctx = document.getElementById('memoryChart').getContext('2d');
    memoryChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: generateTimeLabels(10),
            datasets: [{
                label: 'Memory Usage',
                data: generateRandomData(10, 50, 80),
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: '#3b82f6',
                borderWidth: 1,
                borderRadius: 6,
                hoverBackgroundColor: '#2563eb'
            }]
        },
        options: {
            ...chartOptions,
            plugins: {
                ...chartOptions.plugins,
                legend: {
                    display: false
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
    // Update CPU Chart
    if (cpuChart) {
        cpuChart.data.labels.shift();
        cpuChart.data.labels.push(new Date().toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' }));
        cpuChart.data.datasets[0].data.shift();
        cpuChart.data.datasets[0].data.push(Math.floor(Math.random() * 30) + 60);
        cpuChart.update('none');
        
        // Update CPU display
        const newCpuValue = cpuChart.data.datasets[0].data[cpuChart.data.datasets[0].data.length - 1];
        document.getElementById('cpu-usage').textContent = newCpuValue + '%';
        document.getElementById('cpu-progress').style.width = newCpuValue + '%';
    }
    
    // Update Memory Chart
    if (memoryChart) {
        memoryChart.data.labels.shift();
        memoryChart.data.labels.push(new Date().toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' }));
        memoryChart.data.datasets[0].data.shift();
        memoryChart.data.datasets[0].data.push(Math.floor(Math.random() * 30) + 50);
        memoryChart.update('none');
        
        // Update Memory display
        const newMemoryValue = memoryChart.data.datasets[0].data[memoryChart.data.datasets[0].data.length - 1];
        const memoryGB = (newMemoryValue * 8 / 100).toFixed(1);
        document.getElementById('memory-usage').textContent = memoryGB + 'GB';
        document.getElementById('memory-progress').style.width = newMemoryValue + '%';
    }
}

function refreshMonitoring() {
    const button = event.currentTarget;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحديث...';
    button.disabled = true;
    button.classList.add('opacity-75', 'cursor-not-allowed');
    
    // Update charts immediately
    updateCharts();
    
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check"></i> تم التحديث';
        button.classList.remove('bg-teal-600', 'hover:bg-teal-50');
        button.classList.add('bg-green-600', 'hover:bg-green-50');
        
        showNotification('تم تحديث بيانات المراقبة بنجاح', 'success');
        
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
            button.classList.remove('opacity-75', 'cursor-not-allowed', 'bg-green-600', 'hover:bg-green-50');
            button.classList.add('bg-teal-600', 'hover:bg-teal-50');
        }, 2000);
    }, 1000);
}

function refreshLogs() {
    const button = event.currentTarget;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحديث...';
    button.disabled = true;
    
    const logsContainer = document.getElementById('logs-container');
    logsContainer.style.opacity = '0.5';
    
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check"></i> تم التحديث';
        logsContainer.style.opacity = '1';
        
        showNotification('تم تحديث السجلات بنجاح', 'success');
        
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
        }, 2000);
    }, 1500);
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
    initCpuChart();
    initMemoryChart();
    
    // Auto-update charts every 3 seconds
    setInterval(updateCharts, 3000);
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
