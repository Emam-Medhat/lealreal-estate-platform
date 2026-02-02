@extends('admin.layouts.admin')

@section('title', 'تدقيق الامتثال')

@push('styles')
<style>
.compliance-module {
    transition: all 0.3s ease;
}
.compliance-module:hover {
    transform: translateY(-2px);
}
.dynamic-content {
    transition: all 0.5s ease;
}
.status-green { border-left: 4px solid #10b981; }
.status-yellow { border-left: 4px solid #f59e0b; }
.status-red { border-left: 4px solid #ef4444; }
.status-blue { border-left: 4px solid #3b82f6; }
</style>
@endpush

@push('scripts')
<script>
// Compliance Dashboard State
const complianceState = {
    autoRefreshInterval: null,
    lastUpdate: null
};

// Auto-refresh data
function startComplianceAutoRefresh() {
    complianceState.autoRefreshInterval = setInterval(() => {
        refreshComplianceData();
    }, 5000); // Refresh every 5 seconds
}

// Refresh compliance data
async function refreshComplianceData() {
    try {
        const response = await fetch('/blockchain/legal/compliance/refresh', {
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            updateComplianceUI(data);
        }
    } catch (error) {
        console.error('Error refreshing compliance data:', error);
    }
}

// Update compliance UI with new data
function updateComplianceUI(data) {
    // Update compliance dashboard
    if (data.complianceData) {
        Object.keys(data.complianceData).forEach(key => {
            const el = document.querySelector(`[data-compliance-${key}]`);
            if (el) {
                el.textContent = data.complianceData[key];
            }
        });
    }
    
    // Update regulatory compliance
    if (data.regulatoryCompliance) {
        updateRegulatoryCompliance(data.regulatoryCompliance);
    }
    
    // Update internal compliance
    if (data.internalCompliance) {
        updateInternalCompliance(data.internalCompliance);
    }
    
    // Update recent activities
    if (data.recentActivities) {
        updateRecentActivities(data.recentActivities);
    }
    
    // Update last update time
    const lastUpdateEl = document.querySelector('[data-last-update]');
    if (lastUpdateEl) {
        lastUpdateEl.textContent = new Date().toLocaleTimeString('ar-SA');
    }
}

// Update regulatory compliance display
function updateRegulatoryCompliance(compliance) {
    const container = document.querySelector('.regulatory-compliance-container');
    if (container && compliance.length > 0) {
        container.innerHTML = compliance.map(item => `
            <div class="flex items-center justify-between p-3 bg-${item.color}-50 rounded-xl dynamic-content status-${item.color}">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-${item.color}-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-${item.icon} text-${item.color}-600 text-sm"></i>
                    </div>
                    <span class="text-sm font-medium">${item.name}</span>
                </div>
                <div class="text-right">
                    <div class="text-sm text-${item.color}-600 font-medium">${item.percentage}%</div>
                    <div class="text-xs text-gray-500">${item.status}</div>
                </div>
            </div>
        `).join('');
    }
}

// Update internal compliance display
function updateInternalCompliance(compliance) {
    const container = document.querySelector('.internal-compliance-container');
    if (container && compliance.length > 0) {
        container.innerHTML = compliance.map(item => `
            <div class="flex items-center justify-between p-3 bg-${item.color}-50 rounded-xl dynamic-content status-${item.color}">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-${item.color}-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-${item.icon} text-${item.color}-600 text-sm"></i>
                    </div>
                    <span class="text-sm font-medium">${item.name}</span>
                </div>
                <span class="text-sm text-${item.color}-600 font-medium">${item.status}</span>
            </div>
        `).join('');
    }
}

// Update recent activities display
function updateRecentActivities(activities) {
    const container = document.querySelector('.recent-activities-container');
    if (container && activities.length > 0) {
        container.innerHTML = activities.map(activity => `
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl dynamic-content">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-${activity.color}-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-${activity.icon} text-${activity.color}-600"></i>
                    </div>
                    <div>
                        <p class="font-medium">${activity.title}</p>
                        <p class="text-sm text-gray-500">${activity.time}</p>
                    </div>
                </div>
                <span class="text-sm text-${activity.color}-600 font-medium">${activity.status}</span>
            </div>
        `).join('');
    }
}

// Perform Compliance Check
async function performComplianceCheck() {
    const button = event.target;
    const originalContent = button.innerHTML;
    
    // Disable button and show loading
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري الفحص...';
    button.classList.remove('bg-amber-600', 'hover:bg-amber-700');
    button.classList.add('bg-gray-400');
    
    try {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            throw new Error('CSRF token not found');
        }
        
        // Start compliance check
        const response = await fetch('/blockchain/legal/compliance/check', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Server returned non-JSON response:', text);
            throw new Error('الخادم أرجع استجابة غير صالحة');
        }
        
        const data = await response.json();
        
        if (data.success) {
            // Show success message
            showNotification('اكتمل الفحص بنجاح!', 'success');
            
            // Update UI with new data
            updateComplianceUI(data.data);
            
            // Show results
            showComplianceResults(data.results);
        } else {
            throw new Error(data.message || 'فشل الفحص');
        }
    } catch (error) {
        console.error('Error performing compliance check:', error);
        showNotification('فشل الفحص: ' + error.message, 'error');
    } finally {
        // Restore button
        button.disabled = false;
        button.innerHTML = originalContent;
        button.classList.remove('bg-gray-400');
        button.classList.add('bg-amber-600', 'hover:bg-amber-700');
    }
}

// Show compliance results modal
function showComplianceResults(results) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl p-6 max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold">نتائج الفحص</h3>
                <button onclick="closeModal(this)" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        <h4 class="text-lg font-semibold text-green-800">الفحص اكتمل بنجاح</h4>
                    </div>
                    <p class="text-sm text-green-700">تم فحص جميع جوانب الامتثال بنجاح</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-medium mb-2">ملخص النتائج</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>معدل الامتثال العام:</span>
                                <span class="font-medium text-green-600">${results.overall_compliance}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span>المخاطر المكتشفة:</span>
                                <span class="font-medium text-red-600">${results.risks_found}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>التوصيات:</span>
                                <span class="font-medium text-blue-600">${results.recommendations}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-medium mb-2">التفاصيل</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>الوثائق المدروسة:</span>
                                <span class="font-medium">${results.documents_reviewed}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>الوقت المستغرق:</span>
                                <span class="font-medium">${results.duration}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>تاريخ الفحص:</span>
                                <span class="font-medium">${results.check_date}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                ${results.issues && results.issues.length > 0 ? `
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h5 class="font-medium mb-2 text-yellow-800">المسائل التي تحتاج اهتمام</h5>
                    <ul class="space-y-1 text-sm text-yellow-700">
                        ${results.issues.map(issue => `<li>• ${issue}</li>`).join('')}
                    </ul>
                </div>
                ` : ''}
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Close modal function
function closeModal(button) {
    const modal = button.closest('.fixed');
    if (modal && modal.parentNode) {
        modal.parentNode.removeChild(modal);
    }
}

// Show notification function
function showNotification(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
        warning: 'bg-yellow-500'
    };
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
    notification.innerHTML = `
        <div class="flex items-center gap-3">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Start auto-refresh
    startComplianceAutoRefresh();
    
    // Add click handler for compliance check button
    const checkButton = document.querySelector('[data-action="perform-check"]');
    if (checkButton) {
        checkButton.addEventListener('click', performComplianceCheck);
    }
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (complianceState.autoRefreshInterval) {
            clearInterval(complianceState.autoRefreshInterval);
        }
    });
});
</script>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-amber-50 to-orange-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check-shield text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                                تدقيق الامتثال
                            </h1>
                            <p class="text-gray-600 text-lg">مراقبة الامتثال القانوني والتنظيمي</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <button data-action="perform-check" class="bg-amber-600 text-white px-6 py-3 rounded-2xl hover:bg-amber-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-sync-alt ml-2"></i>
                        تحديث الفحص
                    </button>
                </div>
            </div>
        </div>

        <!-- Compliance Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6 compliance-module">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                    <div class="text-xs text-gray-500">مباشر</div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900" data-compliance-compliance_rate>{{ $complianceData['compliance_rate'] }}%</h3>
                <p class="text-sm text-gray-600">معدل الامتثال</p>
                <div class="mt-2 text-xs text-green-600" data-compliance-compliance_trend>{{ $complianceData['compliance_trend'] }} من الشهر الماضي</div>
            </div>
            
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6 compliance-module">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-contract text-white text-xl"></i>
                    </div>
                    <div class="text-xs text-gray-500">مباشر</div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900" data-compliance-completed_documents>{{ $complianceData['completed_documents'] }}</h3>
                <p class="text-sm text-gray-600">وثائق مكتملة</p>
                <div class="mt-2 text-xs text-blue-600" data-compliance-pending_reviews>{{ $complianceData['pending_reviews'] }} معلقة للمراجعة</div>
            </div>
            
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6 compliance-module">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                    </div>
                    <div class="text-xs text-gray-500">مباشر</div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900" data-compliance-identified_risks>{{ $complianceData['identified_risks'] }}</h3>
                <p class="text-sm text-gray-600">مخاطر محددة</p>
                <div class="mt-2 text-xs text-purple-600" data-compliance-high_priority_risks>{{ $complianceData['high_priority_risks'] }} عالية الأولوية</div>
            </div>
            
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6 compliance-module">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                    <div class="text-xs text-gray-500">مباشر</div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900" data-compliance-average_review_time>{{ $complianceData['average_review_time'] }}</h3>
                <p class="text-sm text-gray-600">متوسط وقت المراجعة</p>
                <div class="mt-2 text-xs text-orange-600" data-compliance-review_time_improvement>{{ $complianceData['review_time_improvement'] }} من الأسبوع الماضي</div>
            </div>
        </div>

        <!-- Compliance Areas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Regulatory Compliance -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-balance-scale text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">الامتثال التنظيمي</h2>
                        <p class="text-sm text-gray-600">الالتزام بالقوانين واللوائح</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                @foreach($regulatoryCompliance as $item)
                <div class="flex items-center justify-between p-3 bg-{{ $item['color'] }}-50 rounded-xl dynamic-content status-{{ $item['color'] }}">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-{{ $item['color'] }}-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-{{ $item['icon'] }} text-{{ $item['color'] }}-600 text-sm"></i>
                        </div>
                        <span class="text-sm font-medium">{{ $item['name'] }}</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-{{ $item['color'] }}-600 font-medium">{{ $item['percentage'] }}%</div>
                        <div class="text-xs text-gray-500">{{ $item['status'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            </div>
            
            <!-- Internal Compliance -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">الامتثال الداخلي</h2>
                        <p class="text-sm text-gray-600">السياسات والإجراءات الداخلية</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                @foreach($internalCompliance as $item)
                <div class="flex items-center justify-between p-3 bg-{{ $item['color'] }}-50 rounded-xl dynamic-content status-{{ $item['color'] }}">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-{{ $item['color'] }}-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-{{ $item['icon'] }} text-{{ $item['color'] }}-600 text-sm"></i>
                        </div>
                        <span class="text-sm font-medium">{{ $item['name'] }}</span>
                    </div>
                    <span class="text-sm text-{{ $item['color'] }}-600 font-medium">{{ $item['status'] }}</span>
                </div>
                @endforeach
            </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">الأنشطة الحديثة</h2>
                <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    عرض الكل
                </button>
            </div>
            
            <div class="space-y-4">
                @foreach($recentActivities as $activity)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl dynamic-content">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-{{ $activity['color'] }}-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-{{ $activity['icon'] }} text-{{ $activity['color'] }}-600"></i>
                        </div>
                        <div>
                            <p class="font-medium">{{ $activity['title'] }}</p>
                            <p class="text-sm text-gray-500">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                    <span class="text-sm text-{{ $activity['color'] }}-600 font-medium">{{ $activity['status'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
