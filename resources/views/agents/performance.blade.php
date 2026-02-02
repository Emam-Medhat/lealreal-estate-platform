@extends('admin.layouts.admin')

@section('title', 'Agent Performance')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl shadow-xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold mb-2">لوحة الأداء</h1>
                <p class="text-blue-100 text-lg">مرحباً {{ $agent->name ?? 'الوكيل' }} - عرض مفصل لمؤشرات الأداء والتحليلات</p>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold">{{ date('Y-m-d') }}</div>
                <div class="text-blue-100">{{ date('l') }}</div>
            </div>
        </div>
    </div>

    <!-- Performance Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Sales Card -->
        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-3">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <span class="text-sm font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">
                        +{{ rand(5, 25) }}%
                    </span>
                </div>
                <h3 class="text-gray-600 text-sm font-medium mb-1">إجمالي المبيعات</h3>
                <p class="text-3xl font-bold text-gray-900" data-metric="total_sales">{{ $metrics['total_sales'] ?? 0 }}</p>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-arrow-up text-green-500 ml-1"></i>
                        <span>زيادة عن الشهر الماضي</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commission Earned Card -->
        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-3">
                        <i class="fas fa-dollar-sign text-white text-xl"></i>
                    </div>
                    <span class="text-sm font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">
                        +{{ rand(10, 30) }}%
                    </span>
                </div>
                <h3 class="text-gray-600 text-sm font-medium mb-1">العمولة المكتسبة</h3>
                <p class="text-3xl font-bold text-gray-900" data-metric="commission_earned">${{ number_format($metrics['commission_earned'] ?? 0, 2) }}</p>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-arrow-up text-green-500 ml-1"></i>
                        <span>أفضل أداء هذا الربع</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Properties Listed Card -->
        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-3">
                        <i class="fas fa-home text-white text-xl"></i>
                    </div>
                    <span class="text-sm font-semibold text-blue-600 bg-blue-100 px-2 py-1 rounded-full">
                        {{ rand(2, 8) }} جديد
                    </span>
                </div>
                <h3 class="text-gray-600 text-sm font-medium mb-1">العقارات المعروضة</h3>
                <p class="text-3xl font-bold text-gray-900" data-metric="properties_listed">{{ $metrics['properties_listed'] ?? 0 }}</p>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-plus text-blue-500 ml-1"></i>
                        <span>عقارات جديدة هذا الأسبوع</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Client Satisfaction Card -->
        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-lg p-3">
                        <i class="fas fa-star text-white text-xl"></i>
                    </div>
                    <span class="text-sm font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">
                        ممتاز
                    </span>
                </div>
                <h3 class="text-gray-600 text-sm font-medium mb-1">رضا العملاء</h3>
                <p class="text-3xl font-bold text-gray-900" data-metric="satisfaction_rate">{{ $metrics['satisfaction_rate'] ?? 0 }}%</p>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-star text-yellow-500 ml-1"></i>
                        <span>تقييم {{ number_format(rand(45, 50) / 10, 1) }}/5.0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Performance Chart -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">الأداء الشهري</h2>
                <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option>آخر 6 أشهر</option>
                    <option>آخر 12 شهر</option>
                    <option>هذا العام</option>
                </select>
            </div>
            <div class="h-64 bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg flex items-center justify-center">
                <div class="text-center">
                    <canvas id="performanceChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Sales Distribution -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">توزيع المبيعات</h2>
                <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    عرض التفاصيل
                </button>
            </div>
            <div class="h-64 bg-gradient-to-br from-green-50 to-blue-50 rounded-lg flex items-center justify-center">
                <div class="text-center">
                    <canvas id="salesChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities with Enhanced Design -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">الأنشطة الحديثة</h2>
            <div class="flex space-x-2">
                <button onclick="refreshActivities()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                    <i class="fas fa-sync-alt ml-1"></i>
                    تحديث
                </button>
                <select onchange="filterActivities(this.value)" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    <option value="all">جميع الأنشطة</option>
                    <option value="completed">مكتملة</option>
                    <option value="active">نشطة</option>
                    <option value="pending">معلقة</option>
                </select>
            </div>
        </div>
        
        <div class="space-y-4" id="activitiesContainer">
            @forelse ($monthlyData ?? [] as $index => $activity)
                <div class="activity-item flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg hover:from-blue-50 hover:to-purple-50 transition-all duration-300 border border-gray-100" data-status="{{ $activity['status'] ?? 'completed' }}">
                    <div class="flex items-center space-x-4">
                        <div class="bg-gradient-to-br {{ $index % 3 == 0 ? 'from-blue-500 to-blue-600' : ($index % 3 == 1 ? 'from-green-500 to-green-600' : 'from-purple-500 to-purple-600') }} rounded-full p-3">
                            <i class="fas {{ $activity['icon'] ?? 'fa-clipboard-list' }} text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $activity['title'] ?? 'نشاط' }}</p>
                            <p class="text-sm text-gray-600">{{ $activity['date'] ?? 'حديثاً' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-900">{{ $activity['value'] ?? 'غير متاح' }}</p>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $activity['status'] == 'Completed' ? 'bg-green-100 text-green-800' : ($activity['status'] == 'Active' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ $activity['status'] ?? 'مكتمل' }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-gradient-to-br from-gray-50 to-blue-50 rounded-lg">
                    <i class="fas fa-inbox text-6xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">لا توجد أنشطة حديثة</h3>
                    <p class="text-gray-500">سيتم عرض الأنشطة هنا عند توفرها</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Goals Progress Section -->
    @if(isset($goals) && !empty($goals))
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">تقدم الأهداف</h2>
            <span class="text-sm text-gray-600">{{ date('F Y') }}</span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="relative inline-flex items-center justify-center">
                    <svg class="w-32 h-32">
                        <circle class="text-gray-200" stroke-width="10" stroke="currentColor" fill="transparent" r="56" cx="64" cy="64"></circle>
                        <circle class="text-blue-600" stroke-width="10" stroke-dasharray="{{ ($goals['monthly_sales_progress'] ?? 0) * 3.52 }} 352" stroke-linecap="round" stroke="currentColor" fill="transparent" r="56" cx="64" cy="64" transform="rotate(-90 64 64)"></circle>
                    </svg>
                    <span class="absolute text-2xl font-bold text-gray-900">{{ $goals['monthly_sales_progress'] ?? 0 }}%</span>
                </div>
                <h3 class="mt-4 font-semibold text-gray-900">المبيعات الشهرية</h3>
                <p class="text-sm text-gray-600">{{ $goals['monthly_sales_current'] ?? 0 }}/{{ $goals['monthly_sales_target'] ?? 0 }}</p>
            </div>
            
            <div class="text-center">
                <div class="relative inline-flex items-center justify-center">
                    <svg class="w-32 h-32">
                        <circle class="text-gray-200" stroke-width="10" stroke="currentColor" fill="transparent" r="56" cx="64" cy="64"></circle>
                        <circle class="text-green-600" stroke-dasharray="{{ ($goals['commission_progress'] ?? 0) * 3.52 }} 352" stroke-width="10" stroke-linecap="round" stroke="currentColor" fill="transparent" r="56" cx="64" cy="64" transform="rotate(-90 64 64)"></circle>
                    </svg>
                    <span class="absolute text-2xl font-bold text-gray-900">{{ $goals['commission_progress'] ?? 0 }}%</span>
                </div>
                <h3 class="mt-4 font-semibold text-gray-900">العمولات</h3>
                <p class="text-sm text-gray-600">${{ number_format($goals['commission_current'] ?? 0, 0) }}/${{ number_format($goals['commission_target'] ?? 0, 0) }}</p>
            </div>
            
            <div class="text-center">
                <div class="relative inline-flex items-center justify-center">
                    <svg class="w-32 h-32">
                        <circle class="text-gray-200" stroke-width="10" stroke="currentColor" fill="transparent" r="56" cx="64" cy="64"></circle>
                        <circle class="text-yellow-600" stroke-dasharray="{{ ($goals['satisfaction_progress'] ?? 0) * 3.52 }} 352" stroke-width="10" stroke-linecap="round" stroke="currentColor" fill="transparent" r="56" cx="64" cy="64" transform="rotate(-90 64 64)"></circle>
                    </svg>
                    <span class="absolute text-2xl font-bold text-gray-900">{{ $goals['satisfaction_progress'] ?? 0 }}%</span>
                </div>
                <h3 class="mt-4 font-semibold text-gray-900">رضا العملاء</h3>
                <p class="text-sm text-gray-600">{{ $goals['satisfaction_current'] ?? 0 }}%/{{ $goals['satisfaction_target'] ?? 0 }}%</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Agent performance page loaded');
    
    // Initialize performance chart
    initializePerformanceChart();
    
    // Initialize sales chart
    initializeSalesChart();
    
    // Add real-time updates
    startRealTimeUpdates();
});

function initializePerformanceChart() {
    const ctx = document.getElementById('performanceChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
            datasets: [{
                label: 'المبيعات',
                data: [12, 19, 15, 25, 22, 30],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }, {
                label: 'العمولات',
                data: [8000, 12000, 10000, 18000, 15000, 22000],
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
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
}

function initializeSalesChart() {
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['شقق', 'فلل', 'أراضي', 'تجاري'],
            datasets: [{
                data: [35, 25, 20, 20],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(251, 146, 60, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
}

function startRealTimeUpdates() {
    // Simulate real-time updates
    setInterval(() => {
        // Update random metrics
        const metrics = document.querySelectorAll('.text-3xl');
        metrics.forEach(metric => {
            if (metric.textContent.includes('$')) {
                const currentValue = parseFloat(metric.textContent.replace(/[$,]/g, ''));
                const change = (Math.random() - 0.5) * 1000;
                metric.textContent = '$' + (currentValue + change).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
        });
    }, 30000); // Update every 30 seconds
}

// Refresh activities function
function refreshActivities() {
    const container = document.getElementById('activitiesContainer');
    const refreshBtn = event.target;
    
    // Add loading state
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-1"></i> جاري التحديث...';
    refreshBtn.disabled = true;
    
    // Fetch fresh data from server
    fetch('/agents/performance/refresh-activities', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update container with fresh data
            container.innerHTML = data.activities.map((activity, index) => `
                <div class="activity-item flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg hover:from-blue-50 hover:to-purple-50 transition-all duration-300 border border-gray-100" data-status="${activity.status}">
                    <div class="flex items-center space-x-4">
                        <div class="bg-gradient-to-br ${index % 3 == 0 ? 'from-blue-500 to-blue-600' : (index % 3 == 1 ? 'from-green-500 to-green-600' : 'from-purple-500 to-purple-600')} rounded-full p-3">
                            <i class="fas ${activity.icon} text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">${activity.title}</p>
                            <p class="text-sm text-gray-600">${activity.date}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-900">${activity.value}</p>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${activity.status == 'completed' ? 'bg-green-100 text-green-800' : (activity.status == 'active' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')}">
                            ${activity.status == 'completed' ? 'مكتمل' : (activity.status == 'active' ? 'نشط' : 'معلق')}
                        </span>
                    </div>
                </div>
            `).join('');
            
            // Update metrics if provided
            if (data.metrics) {
                updateMetricsDisplay(data.metrics);
            }
            
            showNotification('تم تحديث الأنشطة بنجاح', 'success');
        } else {
            throw new Error(data.message || 'فشل تحديث البيانات');
        }
    })
    .catch(error => {
        console.error('Error refreshing activities:', error);
        showNotification('فشل تحديث الأنشطة', 'error');
    })
    .finally(() => {
        // Reset button state
        refreshBtn.innerHTML = '<i class="fas fa-sync-alt ml-1"></i> تحديث';
        refreshBtn.disabled = false;
    });
}

// Update metrics display
function updateMetricsDisplay(metrics) {
    if (metrics.total_sales !== undefined) {
        const salesElement = document.querySelector('[data-metric="total_sales"]');
        if (salesElement) salesElement.textContent = metrics.total_sales;
    }
    
    if (metrics.commission_earned !== undefined) {
        const commissionElement = document.querySelector('[data-metric="commission_earned"]');
        if (commissionElement) commissionElement.textContent = '$' + Number(metrics.commission_earned).toLocaleString();
    }
    
    if (metrics.properties_listed !== undefined) {
        const propertiesElement = document.querySelector('[data-metric="properties_listed"]');
        if (propertiesElement) propertiesElement.textContent = metrics.properties_listed;
    }
    
    if (metrics.satisfaction_rate !== undefined) {
        const satisfactionElement = document.querySelector('[data-metric="satisfaction_rate"]');
        if (satisfactionElement) satisfactionElement.textContent = metrics.satisfaction_rate + '%';
    }
}

// Filter activities function
function filterActivities(status) {
    const activities = document.querySelectorAll('.activity-item');
    
    activities.forEach(activity => {
        if (status === 'all') {
            activity.style.display = 'flex';
        } else {
            const activityStatus = activity.getAttribute('data-status');
            if (activityStatus === status) {
                activity.style.display = 'flex';
            } else {
                activity.style.display = 'none';
            }
        }
    });
    
    // Show notification
    const statusText = status === 'all' ? 'جميع الأنشطة' : 
                      status === 'completed' ? 'الأنشطة المكتملة' :
                      status === 'active' ? 'الأنشطة النشطة' : 'الأنشطة المعلقة';
    showNotification(`تم تصفية: ${statusText}`, 'info');
}

// Show notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 left-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 
                           type === 'error' ? 'fa-exclamation-circle' : 
                           type === 'warning' ? 'fa-exclamation-triangle' : 
                           'fa-info-circle'} ml-2"></i>
            ${message}
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Add interactive hover effects
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.hover\\:shadow-xl');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endpush
