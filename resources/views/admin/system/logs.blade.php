@extends('admin.layouts.admin')

@section('title', 'السجلات')
@section('page-title', 'السجلات')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
<style>
    .chart-container {
        position: relative;
        height: 300px;
    }
    .log-metric {
        transition: all 0.3s ease;
    }
    .log-metric:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .log-level {
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
    .level-debug { background: #6b7280; }
    .level-info { background: #3b82f6; }
    .level-warning { background: #f59e0b; }
    .level-error { background: #ef4444; }
    .level-critical { background: #991b1b; }
    .log-entry {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
        font-family: 'Courier New', monospace;
    }
    .log-entry:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .log-entry.debug { border-left-color: #6b7280; }
    .log-entry.info { border-left-color: #3b82f6; }
    .log-entry.warning { border-left-color: #f59e0b; }
    .log-entry.error { border-left-color: #ef4444; }
    .log-entry.critical { border-left-color: #991b1b; }
    .log-content {
        background: #1f2937;
        color: #f3f4f6;
        border-radius: 8px;
        padding: 16px;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        line-height: 1.5;
        overflow-x: auto;
    }
    .highlight {
        background: rgba(59, 130, 246, 0.2);
        padding: 2px 4px;
        border-radius: 3px;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-br from-gray-700 to-gray-900 rounded-2xl p-8 text-white shadow-xl mb-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-pattern opacity-10"></div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h1 class="text-3xl font-bold mb-2 flex items-center gap-3">
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        السجلات
                    </h1>
                    <p class="text-lg opacity-90">مراقبة وتحليل سجلات النظام</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="refreshLogs()" class="bg-white text-gray-700 hover:bg-gray-50 px-6 py-3 rounded-xl font-bold shadow-lg transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2 group">
                        <i class="fas fa-sync-alt group-hover:animate-spin"></i>
                        تحديث
                    </button>
                    <button onclick="clearLogs()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2">
                        <i class="fas fa-trash"></i>
                        مسح الكل
                    </button>
                </div>
            </div>
        </div>

        <!-- Log Statistics -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="bg-gray-100 p-2 rounded-lg ml-3">
                    <i class="fas fa-chart-bar text-gray-600"></i>
                </div>
                إحصائيات السجلات
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <div class="log-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="flex items-center justify-center mb-2">
                        <span class="log-level level-debug"></span>
                        <span class="text-2xl font-bold text-gray-800">234</span>
                    </div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Debug</div>
                </div>
                
                <div class="log-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="flex items-center justify-center mb-2">
                        <span class="log-level level-info"></span>
                        <span class="text-2xl font-bold text-gray-800">1,456</span>
                    </div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Info</div>
                </div>
                
                <div class="log-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="flex items-center justify-center mb-2">
                        <span class="log-level level-warning"></span>
                        <span class="text-2xl font-bold text-gray-800">89</span>
                    </div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Warning</div>
                </div>
                
                <div class="log-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="flex items-center justify-center mb-2">
                        <span class="log-level level-error"></span>
                        <span class="text-2xl font-bold text-gray-800">23</span>
                    </div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Error</div>
                </div>
                
                <div class="log-metric bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
                    <div class="flex items-center justify-center mb-2">
                        <span class="log-level level-critical"></span>
                        <span class="text-2xl font-bold text-gray-800">3</span>
                    </div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Critical</div>
                </div>
            </div>
        </div>

        <!-- Log Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                    <div class="bg-blue-100 p-2 rounded-lg ml-3">
                        <i class="fas fa-chart-line text-blue-600"></i>
                    </div>
                    اتجاه السجلات
                </h2>
                <div class="chart-container">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                    <div class="bg-purple-100 p-2 rounded-lg ml-3">
                        <i class="fas fa-chart-pie text-purple-600"></i>
                    </div>
                    توزيع المستويات
                </h2>
                <div class="chart-container">
                    <canvas id="distributionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Log Filters -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="bg-orange-100 p-2 rounded-lg ml-3">
                    <i class="fas fa-filter text-orange-600"></i>
                </div>
                فلترة السجلات
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المستوى</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg" id="level-filter">
                        <option value="">الكل</option>
                        <option value="debug">Debug</option>
                        <option value="info">Info</option>
                        <option value="warning">Warning</option>
                        <option value="error">Error</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">من التاريخ</label>
                    <input type="datetime-local" class="w-full px-3 py-2 border border-gray-300 rounded-lg" id="date-from">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">إلى التاريخ</label>
                    <input type="datetime-local" class="w-full px-3 py-2 border border-gray-300 rounded-lg" id="date-to">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                    <div class="flex gap-2">
                        <input type="text" placeholder="بحث في السجلات..." class="flex-1 px-3 py-2 border border-gray-300 rounded-lg" id="search-input">
                        <button onclick="applyFilters()" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            تطبيق
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Logs -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold flex items-center text-gray-800">
                    <div class="bg-teal-100 p-2 rounded-lg ml-3">
                        <i class="fas fa-list text-teal-600"></i>
                    </div>
                    السجلات الحديثة
                </h2>
                <button onclick="downloadLogs()" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                    <i class="fas fa-download"></i>
                    تحميل
                </button>
            </div>
            
            <div class="space-y-4" id="logs-container">
                <div class="log-entry critical bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="bg-red-100 p-2 rounded-full">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">Database Connection Failed</h4>
                                <div class="flex items-center gap-4 text-xs text-gray-500 mt-1">
                                    <span>CRITICAL</span>
                                    <span>2024-01-15 14:23:45</span>
                                    <span>App/Models/User.php:123</span>
                                </div>
                            </div>
                        </div>
                        <button onclick="viewLogDetails(1)" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="log-content">
                        <div>[2024-01-15 14:23:45] <span class="highlight">CRITICAL</span>: Database connection failed<br>
                        SQLSTATE[HY000] [2002] Connection refused<br>
                        in <span class="highlight">App/Models/User.php</span> line 123<br>
                        Stack trace:<br>
                        #0 App/Models/User.php(123): Illuminate\\Database\\Connection->connect()<br>
                        #1 App/Http/Controllers/UserController.php(45): App\\Models\\User::find()
                        </div>
                </div>
                
                <div class="log-entry error bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="bg-red-100 p-2 rounded-full">
                                <i class="fas fa-times-circle text-red-600"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">File Not Found</h4>
                                <div class="flex items-center gap-4 text-xs text-gray-500 mt-1">
                                    <span>ERROR</span>
                                    <span>2024-01-15 14:22:30</span>
                                    <span>App/Http/Controllers/FileController.php:67</span>
                                </div>
                            </div>
                        </div>
                        <button onclick="viewLogDetails(2)" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="log-content">
                        <div>[2024-01-15 14:22:30] <span class="highlight">ERROR</span>: File not found at path<br>
                        /var/www/storage/uploads/document.pdf<br>
                        in <span class="highlight">App/Http/Controllers/FileController.php</span> line 67
                        </div>
                </div>
                
                <div class="log-entry warning bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="bg-amber-100 p-2 rounded-full">
                                <i class="fas fa-exclamation-triangle text-amber-600"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">High Memory Usage</h4>
                                <div class="flex items-center gap-4 text-xs text-gray-500 mt-1">
                                    <span>WARNING</span>
                                    <span>2024-01-15 14:20:15</span>
                                    <span>App/Services/ImageProcessor.php:89</span>
                                </div>
                            </div>
                        </div>
                        <button onclick="viewLogDetails(3)" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="log-content">
                        <div>[2024-01-15 14:20:15] <span class="highlight">WARNING</span>: High memory usage detected<br>
                        Current usage: 85% of available memory<br>
                        in <span class="highlight">App/Services/ImageProcessor.php</span> line 89
                        </div>
                </div>
                
                <div class="log-entry info bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-100 p-2 rounded-full">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">User Login Successful</h4>
                                <div class="flex items-center gap-4 text-xs text-gray-500 mt-1">
                                    <span>INFO</span>
                                    <span>2024-01-15 14:18:22</span>
                                    <span>App/Http/Controllers/AuthController.php:156</span>
                                </div>
                            </div>
                        </div>
                        <button onclick="viewLogDetails(4)" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="log-content">
                        <div>[2024-01-15 14:18:22] <span class="highlight">INFO</span>: User login successful<br>
                        User ID: 1234, Email: user@example.com<br>
                        IP Address: 192.168.1.100<br>
                        in <span class="highlight">App/Http/Controllers/AuthController.php</span> line 156
                        </div>
                </div>
                
                <div class="log-entry debug bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="bg-gray-100 p-2 rounded-full">
                                <i class="fas fa-bug text-gray-600"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">API Request Processed</h4>
                                <div class="flex items-center gap-4 text-xs text-gray-500 mt-1">
                                    <span>DEBUG</span>
                                    <span>2024-01-15 14:15:10</span>
                                    <span>App/Http/Controllers/APIController.php:234</span>
                                </div>
                            </div>
                        </div>
                        <button onclick="viewLogDetails(5)" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="log-content">
                        <div>[2024-01-15 14:15:10] <span class="highlight">DEBUG</span>: API request processed<br>
                        Endpoint: /api/v1/properties<br>
                        Method: GET<br>
                        Response Time: 120ms<br>
                        in <span class="highlight">App/Http/Controllers/APIController.php</span> line 234
                        </div>
                </div>
            </div>
        </div>

        <!-- Log Files -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="bg-indigo-100 p-2 rounded-lg ml-3">
                    <i class="fas fa-folder text-indigo-600"></i>
                </div>
                ملفات السجلات
            </h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الملف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحجم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">السطور</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">آخر تعديل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-gray-100 p-2 rounded-full mr-3">
                                        <i class="fas fa-file-alt text-gray-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">laravel.log</p>
                                        <p class="text-xs text-gray-500">السجل الرئيسي</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">45.2 MB</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">234,567</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">منذ 5 دقائق</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="viewLogFile('laravel.log')" class="text-indigo-600 hover:text-indigo-900 font-medium hover:underline flex items-center gap-1">
                                    <i class="fas fa-eye"></i>
                                    عرض
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-red-100 p-2 rounded-full mr-3">
                                        <i class="fas fa-file-alt text-red-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">error.log</p>
                                        <p class="text-xs text-gray-500">سجل الأخطاء</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">12.3 MB</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">45,678</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">منذ ساعة</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="viewLogFile('error.log')" class="text-indigo-600 hover:text-indigo-900 font-medium hover:underline flex items-center gap-1">
                                    <i class="fas fa-eye"></i>
                                    عرض
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 p-2 rounded-full mr-3">
                                        <i class="fas fa-file-alt text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">access.log</p>
                                        <p class="text-xs text-gray-500">سجل الوصول</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">89.7 MB</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">567,890</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">منذ يوم</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="viewLogFile('access.log')" class="text-indigo-600 hover:text-indigo-900 font-medium hover:underline flex items-center gap-1">
                                    <i class="fas fa-eye"></i>
                                    عرض
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Initialize Charts
let trendChart, distributionChart;

// Initialize Trend Chart
function initTrendChart() {
    const ctx = document.getElementById('trendChart').getContext('2d');
    trendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: generateTimeLabels(24),
            datasets: [{
                label: 'Info',
                data: generateRandomData(24, 40, 80),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: false,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6
            }, {
                label: 'Warning',
                data: generateRandomData(24, 2, 10),
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                borderWidth: 2,
                fill: false,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6
            }, {
                label: 'Error',
                data: generateRandomData(24, 0, 5),
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderWidth: 2,
                fill: false,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6
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
            labels: ['Debug', 'Info', 'Warning', 'Error', 'Critical'],
            datasets: [{
                data: [234, 1456, 89, 23, 3],
                backgroundColor: [
                    '#6b7280',
                    '#3b82f6',
                    '#f59e0b',
                    '#ef4444',
                    '#991b1b'
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
                            return context.label + ': ' + context.parsed + ' entries';
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
        const time = new Date(now - i * 3600000); // Hourly intervals
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
    if (trendChart) {
        trendChart.data.labels.shift();
        trendChart.data.labels.push(new Date().toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' }));
        
        trendChart.data.datasets.forEach(dataset => {
            dataset.data.shift();
            if (dataset.label === 'Info') {
                dataset.data.push(Math.floor(Math.random() * 40) + 40);
            } else if (dataset.label === 'Warning') {
                dataset.data.push(Math.floor(Math.random() * 8) + 2);
            } else if (dataset.label === 'Error') {
                dataset.data.push(Math.floor(Math.random() * 5));
            }
        });
        
        trendChart.update('none');
    }
}

function refreshLogs() {
    const button = event.currentTarget;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحديث...';
    button.disabled = true;
    button.classList.add('opacity-75', 'cursor-not-allowed');
    
    // Update charts immediately
    updateCharts();
    
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check"></i> تم التحديث';
        button.classList.remove('bg-gray-700', 'hover:bg-gray-50');
        button.classList.add('bg-green-600', 'hover:bg-green-50');
        
        showNotification('تم تحديث السجلات بنجاح', 'success');
        
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
            button.classList.remove('opacity-75', 'cursor-not-allowed', 'bg-green-600', 'hover:bg-green-50');
            button.classList.add('bg-gray-700', 'hover:bg-gray-50');
        }, 2000);
    }, 1500);
}

function clearLogs() {
    const button = event.currentTarget;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري المسح...';
    button.disabled = true;
    
    setTimeout(() => {
        // Clear logs container
        document.getElementById('logs-container').innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-trash text-4xl text-green-500 mb-3"></i>
                <p>تم مسح جميع السجلات بنجاح</p>
            </div>
        `;
        
        button.innerHTML = '<i class="fas fa-check"></i> تم المسح';
        button.classList.remove('bg-red-600', 'hover:bg-red-700');
        button.classList.add('bg-green-600', 'hover:bg-green-700');
        
        showNotification('تم مسح جميع السجلات بنجاح', 'success');
        
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
            button.classList.remove('bg-green-600', 'hover:bg-green-700');
            button.classList.add('bg-red-600', 'hover:bg-red-700');
        }, 2000);
    }, 2000);
}

function applyFilters() {
    const level = document.getElementById('level-filter').value;
    const dateFrom = document.getElementById('date-from').value;
    const dateTo = document.getElementById('date-to').value;
    const search = document.getElementById('search-input').value;
    
    showNotification('جاري تطبيق الفلاتر...', 'info');
    
    setTimeout(() => {
        showNotification('تم تطبيق الفلاتر بنجاح', 'success');
    }, 1500);
}

function downloadLogs() {
    showNotification('جاري تحميل السجلات...', 'info');
    
    setTimeout(() => {
        showNotification('تم تحميل السجلات بنجاح', 'success');
    }, 2000);
}

function viewLogDetails(logId) {
    showNotification(`عرض تفاصيل السجل #${logId}`, 'info');
}

function viewLogFile(filename) {
    showNotification(`عرض الملف: ${filename}`, 'info');
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
    initTrendChart();
    initDistributionChart();
    
    // Auto-update charts every 10 seconds
    setInterval(updateCharts, 10000);
});
</script>
@endpush
