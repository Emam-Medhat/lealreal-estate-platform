@extends('admin.layouts.admin')

@section('title', 'قائمة الانتظار')
@section('page-title', 'قائمة الانتظار')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
<style>
    .chart-container {
        position: relative;
        height: 300px;
    }
    .queue-metric {
        transition: all 0.3s ease;
    }
    .queue-metric:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .job-status {
        width: 8px;
        height: 8px;
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
    .status-pending { background: #f59e0b; }
    .status-processing { background: #3b82f6; }
    .status-completed { background: #10b981; }
    .status-failed { background: #ef4444; }
    .job-card {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    .job-card:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .job-card.pending { border-left-color: #f59e0b; }
    .job-card.processing { border-left-color: #3b82f6; }
    .job-card.completed { border-left-color: #10b981; }
    .job-card.failed { border-left-color: #ef4444; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-br from-cyan-600 to-blue-700 rounded-2xl p-8 text-white shadow-xl mb-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-pattern opacity-10"></div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h1 class="text-3xl font-bold mb-2 flex items-center gap-3">
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="fas fa-tasks"></i>
                        </div>
                        قائمة الانتظار
                    </h1>
                    <p class="text-lg opacity-90">مراقبة وإدارة المهام المجدولة</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="refreshQueue()" class="bg-white text-cyan-600 hover:bg-cyan-50 px-6 py-3 rounded-xl font-bold shadow-lg transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2 group">
                        <i class="fas fa-sync-alt group-hover:animate-spin"></i>
                        تحديث
                    </button>
                    <button onclick="clearFailedJobs()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2">
                        <i class="fas fa-trash"></i>
                        مسح الفاشلة
                    </button>
                </div>
            </div>
        </div>

        <!-- Queue Status -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="bg-cyan-100 p-2 rounded-lg ml-3">
                    <i class="fas fa-chart-bar text-cyan-600"></i>
                </div>
                حالة قائمة الانتظار
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="queue-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="text-4xl font-bold text-gray-800 mb-2" id="pending-count">15</div>
                    <div class="flex items-center justify-center gap-2">
                        <span class="job-status status-pending"></span>
                        <span class="text-sm font-semibold text-gray-500 uppercase tracking-wider">في الانتظار</span>
                    </div>
                </div>
                
                <div class="queue-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="text-4xl font-bold text-gray-800 mb-2" id="processing-count">8</div>
                    <div class="flex items-center justify-center gap-2">
                        <span class="job-status status-processing"></span>
                        <span class="text-sm font-semibold text-gray-500 uppercase tracking-wider">قيد المعالجة</span>
                    </div>
                </div>
                
                <div class="queue-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="text-4xl font-bold text-gray-800 mb-2" id="completed-count">234</div>
                    <div class="flex items-center justify-center gap-2">
                        <span class="job-status status-completed"></span>
                        <span class="text-sm font-semibold text-gray-500 uppercase tracking-wider">مكتملة</span>
                    </div>
                </div>
                
                <div class="queue-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="text-4xl font-bold text-gray-800 mb-2" id="failed-count">3</div>
                    <div class="flex items-center justify-center gap-2">
                        <span class="job-status status-failed"></span>
                        <span class="text-sm font-semibold text-gray-500 uppercase tracking-wider">فاشلة</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Queue Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                    <div class="bg-purple-100 p-2 rounded-lg ml-3">
                        <i class="fas fa-chart-line text-purple-600"></i>
                    </div>
                    أداء المهام
                </h2>
                <div class="chart-container">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                    <div class="bg-orange-100 p-2 rounded-lg ml-3">
                        <i class="fas fa-chart-pie text-orange-600"></i>
                    </div>
                    توزيع المهام
                </h2>
                <div class="chart-container">
                    <canvas id="distributionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Active Jobs -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="bg-blue-100 p-2 rounded-lg ml-3">
                    <i class="fas fa-spinner text-blue-600"></i>
                </div>
                المهام النشطة
            </h2>
            
            <div class="space-y-4" id="active-jobs-container">
                <div class="job-card processing bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <div class="bg-blue-100 p-2 rounded-full">
                                <i class="fas fa-envelope text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800 mb-1">إرسال إشعارات البريد الإلكتروني</h4>
                                <p class="text-sm text-gray-600 mb-2">إرسال رسائل البريد للمستخدمين الجدد</p>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span>المعرف: #1234</span>
                                    <span>المدة: 2.3s</span>
                                    <span>التقدم: 45%</span>
                                </div>
                                <div class="mt-2 bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-blue-500 h-full rounded-full transition-all duration-500" style="width: 45%"></div>
                                </div>
                            </div>
                        </div>
                        <button onclick="retryJob(1234)" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                </div>
                
                <div class="job-card processing bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <div class="bg-purple-100 p-2 rounded-full">
                                <i class="fas fa-image text-purple-600"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800 mb-1">معالجة الصور</h4>
                                <p class="text-sm text-gray-600 mb-2">تغيير حجم وتحسين صور العقارات</p>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span>المعرف: #1235</span>
                                    <span>المدة: 5.7s</span>
                                    <span>التقدم: 78%</span>
                                </div>
                                <div class="mt-2 bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-purple-500 h-full rounded-full transition-all duration-500" style="width: 78%"></div>
                                </div>
                            </div>
                        </div>
                        <button onclick="retryJob(1235)" class="text-purple-600 hover:text-purple-800 font-medium text-sm">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                </div>
                
                <div class="job-card pending bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <div class="bg-amber-100 p-2 rounded-full">
                                <i class="fas fa-file-export text-amber-600"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800 mb-1">تصدير التقارير</h4>
                                <p class="text-sm text-gray-600 mb-2">إنشاء تقارير شهرية للعملاء</p>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span>المعرف: #1236</span>
                                    <span>المدة: 0s</span>
                                    <span>التقدم: 0%</span>
                                </div>
                                <div class="mt-2 bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-amber-500 h-full rounded-full transition-all duration-500" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                        <button onclick="retryJob(1236)" class="text-amber-600 hover:text-amber-800 font-medium text-sm">
                            <i class="fas fa-play"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Jobs -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="bg-green-100 p-2 rounded-lg ml-3">
                    <i class="fas fa-history text-green-600"></i>
                </div>
                المهام الحديثة
            </h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المهمة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المدة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوقت</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="recent-jobs-container">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-green-100 p-2 rounded-full mr-3">
                                        <i class="fas fa-check text-green-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">نسخ احتياطي قاعدة البيانات</p>
                                        <p class="text-xs text-gray-500">#1231</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">نظام</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">مكتملة</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">12.3s</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">منذ 5 دقائق</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="viewJobDetails(1231)" class="text-blue-600 hover:text-blue-900 font-medium hover:underline flex items-center gap-1">
                                    <i class="fas fa-eye"></i>
                                    عرض
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-red-100 p-2 rounded-full mr-3">
                                        <i class="fas fa-times text-red-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">إرسال إشعارات</p>
                                        <p class="text-xs text-gray-500">#1232</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-purple-100 text-purple-700 text-xs font-bold px-3 py-1 rounded-full">إشعارات</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full">فشلت</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">3.1s</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">منذ 15 دقيقة</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="retryJob(1232)" class="text-amber-600 hover:text-amber-900 font-medium hover:underline flex items-center gap-1">
                                    <i class="fas fa-redo"></i>
                                    إعادة
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-green-100 p-2 rounded-full mr-3">
                                        <i class="fas fa-check text-green-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">معالجة الصور</p>
                                        <p class="text-xs text-gray-500">#1233</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-orange-100 text-orange-700 text-xs font-bold px-3 py-1 rounded-full">معالجة</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">مكتملة</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">8.7s</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">منذ ساعة</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="viewJobDetails(1233)" class="text-blue-600 hover:text-blue-900 font-medium hover:underline flex items-center gap-1">
                                    <i class="fas fa-eye"></i>
                                    عرض
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Queue Configuration -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="bg-indigo-100 p-2 rounded-lg ml-3">
                    <i class="fas fa-cog text-indigo-600"></i>
                </div>
                إعدادات قائمة الانتظار
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <h4 class="font-bold text-gray-800 mb-2">عدد العمال</h4>
                    <div class="flex items-center gap-3">
                        <input type="number" value="5" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-center" id="worker-count">
                        <button onclick="updateWorkerCount()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            تحديث
                        </button>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <h4 class="font-bold text-gray-800 mb-2">محاولات إعادة التشغيل</h4>
                    <div class="flex items-center gap-3">
                        <input type="number" value="3" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-center" id="retry-count">
                        <button onclick="updateRetryCount()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            تحديث
                        </button>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <h4 class="font-bold text-gray-800 mb-2">مهلة المهمة</h4>
                    <div class="flex items-center gap-3">
                        <input type="number" value="60" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-center" id="timeout">
                        <button onclick="updateTimeout()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            تحديث
                        </button>
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
let performanceChart, distributionChart;

// Initialize Performance Chart
function initPerformanceChart() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    performanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: generateTimeLabels(20),
            datasets: [{
                label: 'Completed Jobs',
                data: generateRandomData(20, 8, 25),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#10b981'
            }, {
                label: 'Failed Jobs',
                data: generateRandomData(20, 0, 5),
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
                    grid: {
                        color: '#e5e7eb'
                    },
                    ticks: {
                        color: '#6b7280'
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
            labels: ['في الانتظار', 'قيد المعالجة', 'مكتملة', 'فاشلة'],
            datasets: [{
                data: [15, 8, 234, 3],
                backgroundColor: [
                    '#f59e0b',
                    '#3b82f6',
                    '#10b981',
                    '#ef4444'
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
                            return context.label + ': ' + context.parsed + ' مهمة';
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
        performanceChart.data.datasets[0].data.push(Math.floor(Math.random() * 17) + 8);
        performanceChart.data.datasets[1].data.shift();
        performanceChart.data.datasets[1].data.push(Math.floor(Math.random() * 5));
        performanceChart.update('none');
    }
    
    if (distributionChart) {
        const newPending = Math.floor(Math.random() * 10) + 10;
        const newProcessing = Math.floor(Math.random() * 5) + 5;
        const newCompleted = Math.floor(Math.random() * 50) + 200;
        const newFailed = Math.floor(Math.random() * 3);
        
        distributionChart.data.datasets[0].data = [newPending, newProcessing, newCompleted, newFailed];
        distributionChart.update('none');
        
        // Update counters
        document.getElementById('pending-count').textContent = newPending;
        document.getElementById('processing-count').textContent = newProcessing;
        document.getElementById('completed-count').textContent = newCompleted;
        document.getElementById('failed-count').textContent = newFailed;
    }
}

function refreshQueue() {
    const button = event.currentTarget;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحديث...';
    button.disabled = true;
    button.classList.add('opacity-75', 'cursor-not-allowed');
    
    // Update charts immediately
    updateCharts();
    
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check"></i> تم التحديث';
        button.classList.remove('bg-cyan-600', 'hover:bg-cyan-50');
        button.classList.add('bg-green-600', 'hover:bg-green-50');
        
        showNotification('تم تحديث قائمة الانتظار بنجاح', 'success');
        
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
            button.classList.remove('opacity-75', 'cursor-not-allowed', 'bg-green-600', 'hover:bg-green-50');
            button.classList.add('bg-cyan-600', 'hover:bg-cyan-50');
        }, 2000);
    }, 1500);
}

function clearFailedJobs() {
    const button = event.currentTarget;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري المسح...';
    button.disabled = true;
    
    setTimeout(() => {
        // Update failed count
        document.getElementById('failed-count').textContent = '0';
        
        // Update distribution chart
        if (distributionChart) {
            distributionChart.data.datasets[0].data[3] = 0;
            distributionChart.update('none');
        }
        
        button.innerHTML = '<i class="fas fa-check"></i> تم المسح';
        button.classList.remove('bg-red-600', 'hover:bg-red-700');
        button.classList.add('bg-green-600', 'hover:bg-green-700');
        
        showNotification('تم مسح المهام الفاشلة بنجاح', 'success');
        
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
            button.classList.remove('bg-green-600', 'hover:bg-green-700');
            button.classList.add('bg-red-600', 'hover:bg-red-700');
        }, 2000);
    }, 2000);
}

function retryJob(jobId) {
    showNotification(`جاري إعادة تشغيل المهمة #${jobId}`, 'info');
    
    setTimeout(() => {
        showNotification(`تم إعادة تشغيل المهمة #${jobId} بنجاح`, 'success');
    }, 2000);
}

function viewJobDetails(jobId) {
    showNotification(`عرض تفاصيل المهمة #${jobId}`, 'info');
}

function updateWorkerCount() {
    const count = document.getElementById('worker-count').value;
    showNotification(`تم تحديث عدد العمال إلى ${count}`, 'success');
}

function updateRetryCount() {
    const count = document.getElementById('retry-count').value;
    showNotification(`تم تحديث عدد المحاولات إلى ${count}`, 'success');
}

function updateTimeout() {
    const timeout = document.getElementById('timeout').value;
    showNotification(`تم تحديث مهلة المهمة إلى ${timeout} ثانية`, 'success');
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
