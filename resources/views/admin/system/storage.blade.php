@extends('admin.layouts.admin')

@section('title', 'التخزين')
@section('page-title', 'التخزين')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
<style>
    .chart-container {
        position: relative;
        height: 300px;
    }
    .storage-metric {
        transition: all 0.3s ease;
    }
    .storage-metric:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .progress-ring {
        transform: rotate(-90deg);
    }
    .progress-ring-circle {
        transition: stroke-dashoffset 0.35s;
        stroke-dasharray: 283;
        stroke-dashoffset: 0;
    }
    .file-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 18px;
    }
    .file-pdf { background: #fee2e2; color: #dc2626; }
    .file-image { background: #dbeafe; color: #2563eb; }
    .file-video { background: #fef3c7; color: #d97706; }
    .file-doc { background: #e0e7ff; color: #6366f1; }
    .file-zip { background: #f3e8ff; color: #8b5cf6; }
    .file-audio { background: #d1fae5; color: #10b981; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-br from-amber-600 to-orange-700 rounded-2xl p-8 text-white shadow-xl mb-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-pattern opacity-10"></div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h1 class="text-3xl font-bold mb-2 flex items-center gap-3">
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="fas fa-hdd"></i>
                        </div>
                        التخزين
                    </h1>
                    <p class="text-lg opacity-90">إدارة ومراقبة مساحة التخزين</p>
                </div>
                <button onclick="refreshStorage()" class="bg-white text-amber-600 hover:bg-amber-50 px-6 py-3 rounded-xl font-bold shadow-lg transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2 group">
                    <i class="fas fa-sync-alt group-hover:animate-spin"></i>
                    تحديث البيانات
                </button>
            </div>
        </div>

        <!-- Storage Overview -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="bg-amber-100 p-2 rounded-lg ml-3">
                    <i class="fas fa-chart-pie text-amber-600"></i>
                </div>
                نظرة عامة على التخزين
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="storage-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <svg class="progress-ring w-20 h-20 mx-auto mb-4">
                        <circle cx="40" cy="40" r="36" stroke="#e5e7eb" stroke-width="8" fill="none"></circle>
                        <circle class="progress-ring-circle" cx="40" cy="40" r="36" stroke="#f59e0b" stroke-width="8" fill="none" style="stroke-dashoffset: 85"></circle>
                    </svg>
                    <div class="text-4xl font-bold text-gray-800 mb-2">250GB</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">المساحة الإجمالية</div>
                </div>
                
                <div class="storage-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <svg class="progress-ring w-20 h-20 mx-auto mb-4">
                        <circle cx="40" cy="40" r="36" stroke="#e5e7eb" stroke-width="8" fill="none"></circle>
                        <circle class="progress-ring-circle" cx="40" cy="40" r="36" stroke="#3b82f6" stroke-width="8" fill="none" style="stroke-dashoffset: 113"></circle>
                    </svg>
                    <div class="text-4xl font-bold text-gray-800 mb-2">125GB</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">المستخدم</div>
                </div>
                
                <div class="storage-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <svg class="progress-ring w-20 h-20 mx-auto mb-4">
                        <circle cx="40" cy="40" r="36" stroke="#e5e7eb" stroke-width="8" fill="none"></circle>
                        <circle class="progress-ring-circle" cx="40" cy="40" r="36" stroke="#10b981" stroke-width="8" fill="none" style="stroke-dashoffset: 170"></circle>
                    </svg>
                    <div class="text-4xl font-bold text-gray-800 mb-2">125GB</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">المتاح</div>
                </div>
                
                <div class="storage-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="text-4xl font-bold text-gray-800 mb-2">50%</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">نسبة الاستخدام</div>
                    <div class="mt-3 bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-amber-500 h-full rounded-full transition-all duration-500" style="width: 50%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Storage Distribution Chart -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                    <div class="bg-blue-100 p-2 rounded-lg ml-3">
                        <i class="fas fa-chart-pie text-blue-600"></i>
                    </div>
                    توزيع المساحة
                </h2>
                <div class="chart-container">
                    <canvas id="storageChart"></canvas>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                    <div class="bg-green-100 p-2 rounded-lg ml-3">
                        <i class="fas fa-chart-line text-green-600"></i>
                    </div>
                    استخدام التخزين
                </h2>
                <div class="chart-container">
                    <canvas id="usageChart"></canvas>
                </div>
            </div>
        </div>

        <!-- File Categories -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="bg-purple-100 p-2 rounded-lg ml-3">
                    <i class="fas fa-folder text-purple-600"></i>
                </div>
                تصنيف الملفات
            </h2>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-200 hover:border-purple-500 hover:shadow-md transition-all duration-300">
                    <div class="file-icon file-pdf mx-auto mb-3">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-800 mb-1">2.5GB</div>
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">ملفات PDF</div>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-200 hover:border-blue-500 hover:shadow-md transition-all duration-300">
                    <div class="file-icon file-image mx-auto mb-3">
                        <i class="fas fa-file-image"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-800 mb-1">45.2GB</div>
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">الصور</div>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-200 hover:border-amber-500 hover:shadow-md transition-all duration-300">
                    <div class="file-icon file-video mx-auto mb-3">
                        <i class="fas fa-file-video"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-800 mb-1">28.7GB</div>
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">الفيديوهات</div>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-200 hover:border-indigo-500 hover:shadow-md transition-all duration-300">
                    <div class="file-icon file-doc mx-auto mb-3">
                        <i class="fas fa-file-word"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-800 mb-1">8.3GB</div>
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">المستندات</div>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-200 hover:border-purple-500 hover:shadow-md transition-all duration-300">
                    <div class="file-icon file-zip mx-auto mb-3">
                        <i class="fas fa-file-archive"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-800 mb-1">15.6GB</div>
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">المضغوطات</div>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-200 hover:border-green-500 hover:shadow-md transition-all duration-300">
                    <div class="file-icon file-audio mx-auto mb-3">
                        <i class="fas fa-file-audio"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-800 mb-1">4.8GB</div>
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">الصوتيات</div>
                </div>
            </div>
        </div>

        <!-- Recent Files -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold flex items-center text-gray-800">
                    <div class="bg-red-100 p-2 rounded-lg ml-3">
                        <i class="fas fa-clock text-red-600"></i>
                    </div>
                    الملفات الحديثة
                </h2>
                <button onclick="cleanupFiles()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-broom"></i>
                    تنظيف
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الملف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحجم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ التعديل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="files-container">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="file-icon file-image mr-3">
                                        <i class="fas fa-file-image"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">property_image_001.jpg</p>
                                        <p class="text-xs text-gray-500">images/properties/</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2.4 MB</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">صورة</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">منذ 5 دقائق</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="downloadFile('property_image_001.jpg')" class="text-amber-600 hover:text-amber-900 font-medium hover:underline flex items-center gap-1">
                                    <i class="fas fa-download"></i>
                                    تحميل
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="file-icon file-pdf mr-3">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">contract_2024.pdf</p>
                                        <p class="text-xs text-gray-500">documents/contracts/</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1.8 MB</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full">PDF</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">منذ ساعة</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="downloadFile('contract_2024.pdf')" class="text-amber-600 hover:text-amber-900 font-medium hover:underline flex items-center gap-1">
                                    <i class="fas fa-download"></i>
                                    تحميل
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="file-icon file-video mr-3">
                                        <i class="fas fa-file-video"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">property_tour.mp4</p>
                                        <p class="text-xs text-gray-500">videos/properties/</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">45.6 MB</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1 rounded-full">فيديو</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">منذ 3 ساعات</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="downloadFile('property_tour.mp4')" class="text-amber-600 hover:text-amber-900 font-medium hover:underline flex items-center gap-1">
                                    <i class="fas fa-download"></i>
                                    تحميل
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="file-icon file-zip mr-3">
                                        <i class="fas fa-file-archive"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">backup_2024_01.zip</p>
                                        <p class="text-xs text-gray-500">backups/</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">128.5 MB</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-purple-100 text-purple-700 text-xs font-bold px-3 py-1 rounded-full">مضغوط</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">منذ يوم</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="downloadFile('backup_2024_01.zip')" class="text-amber-600 hover:text-amber-900 font-medium hover:underline flex items-center gap-1">
                                    <i class="fas fa-download"></i>
                                    تحميل
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Storage Alerts -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="bg-orange-100 p-2 rounded-lg ml-3">
                    <i class="fas fa-exclamation-triangle text-orange-600"></i>
                </div>
                تنبيهات التخزين
            </h2>
            
            <div class="space-y-4">
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-start gap-4">
                    <div class="bg-yellow-100 p-2 rounded-full">
                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-yellow-800 mb-1">مساحة التخزين تقترب من الامتلاء</h4>
                        <p class="text-yellow-700 text-sm">تم استخدام 85% من المساحة المتاحة. يرجى التفكير في ترقية الخطة.</p>
                    </div>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-4">
                    <div class="bg-blue-100 p-2 rounded-full">
                        <i class="fas fa-info-circle text-blue-600"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-blue-800 mb-1">النسخ الاحتياطي التلقائي نشط</h4>
                        <p class="text-blue-700 text-sm">يتم إنشاء نسخ احتياطية تلقائي كل 24 ساعة.</p>
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
let storageChart, usageChart;

// Initialize Storage Distribution Chart
function initStorageChart() {
    const ctx = document.getElementById('storageChart').getContext('2d');
    storageChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['الصور', 'الفيديوهات', 'المستندات', 'الملفات الأخرى'],
            datasets: [{
                data: [45.2, 28.7, 8.3, 42.8],
                backgroundColor: [
                    '#3b82f6',
                    '#f59e0b',
                    '#6366f1',
                    '#8b5cf6'
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

// Initialize Usage Chart
function initUsageChart() {
    const ctx = document.getElementById('usageChart').getContext('2d');
    usageChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: generateTimeLabels(30),
            datasets: [{
                label: 'Storage Usage (GB)',
                data: generateRandomData(30, 100, 150),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#10b981'
            }]
        },
        options: {
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
                        color: '#6b7280',
                        callback: function(value) {
                            return value + ' GB';
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
        const time = new Date(now - i * 86400000); // Daily intervals
        labels.push(time.toLocaleDateString('ar-SA', { month: 'short', day: 'numeric' }));
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
    if (usageChart) {
        usageChart.data.labels.shift();
        usageChart.data.labels.push(new Date().toLocaleDateString('ar-SA', { month: 'short', day: 'numeric' }));
        usageChart.data.datasets[0].data.shift();
        usageChart.data.datasets[0].data.push(Math.floor(Math.random() * 50) + 100);
        usageChart.update('none');
    }
}

function refreshStorage() {
    const button = event.currentTarget;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحديث...';
    button.disabled = true;
    button.classList.add('opacity-75', 'cursor-not-allowed');
    
    // Update charts immediately
    updateCharts();
    
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check"></i> تم التحديث';
        button.classList.remove('bg-amber-600', 'hover:bg-amber-50');
        button.classList.add('bg-green-600', 'hover:bg-green-50');
        
        showNotification('تم تحديث بيانات التخزين بنجاح', 'success');
        
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
            button.classList.remove('opacity-75', 'cursor-not-allowed', 'bg-green-600', 'hover:bg-green-50');
            button.classList.add('bg-amber-600', 'hover:bg-amber-50');
        }, 2000);
    }, 1500);
}

function cleanupFiles() {
    const button = event.currentTarget;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التنظيف...';
    button.disabled = true;
    
    setTimeout(() => {
        // Clear files container
        document.getElementById('files-container').innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-8 text-gray-500">
                    <i class="fas fa-broom text-4xl text-green-500 mb-3"></i>
                    <p>تم تنظيف الملفات المؤقتة بنجاح</p>
                </td>
            </tr>
        `;
        
        button.innerHTML = '<i class="fas fa-check"></i> تم التنظيف';
        button.classList.remove('bg-red-600', 'hover:bg-red-700');
        button.classList.add('bg-green-600', 'hover:bg-green-700');
        
        showNotification('تم تنظيف الملفات المؤقتة بنجاح', 'success');
        
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
            button.classList.remove('bg-green-600', 'hover:bg-green-700');
            button.classList.add('bg-red-600', 'hover:bg-red-700');
        }, 2000);
    }, 2000);
}

function downloadFile(filename) {
    showNotification(`جاري تحميل الملف: ${filename}`, 'info');
    
    // Simulate download
    setTimeout(() => {
        showNotification(`تم تحميل الملف: ${filename}`, 'success');
    }, 2000);
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
    initStorageChart();
    initUsageChart();
    
    // Auto-update charts every 10 seconds
    setInterval(updateCharts, 10000);
});
</script>
@endpush
