@extends('admin.layouts.admin')

@section('title', 'تدقيق الامتثال')

@push('styles')
<style>
    /* Clean website-style compliance page */
    .page-header {
        background: white;
        border-bottom: 1px solid #e5e7eb;
        padding: 2rem 0;
        margin-bottom: 2rem;
    }
    
    .page-header h1 {
        font-size: 2.5rem;
        font-weight: 300;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .page-header p {
        color: #7f8c8d;
        font-size: 1.1rem;
        margin: 0;
    }
    
    .header-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 1rem;
    }
    
    .btn-refresh {
        display: inline-flex;
        align-items: center;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid transparent;
        background: #f59e0b;
        color: white;
        border-color: #f59e0b;
    }
    
    .btn-refresh:hover {
        background: #d97706;
        border-color: #d97706;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15);
    }
    
    .btn-refresh i {
        margin-right: 0.5rem;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }
    
    .stat-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        color: white;
        font-size: 1rem;
    }
    
    .stat-icon.green { background: #10b981; }
    .stat-icon.blue { background: #3b82f6; }
    .stat-icon.purple { background: #8b5cf6; }
    .stat-icon.orange { background: #f59e0b; }
    
    .stat-info h3 {
        font-size: 2rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }
    
    .stat-info p {
        color: #7f8c8d;
        font-size: 0.875rem;
        margin: 0;
    }
    
    .stat-trend {
        font-size: 0.75rem;
        margin-top: 0.5rem;
        font-weight: 500;
    }
    
    .trend-positive { color: #10b981; }
    .trend-negative { color: #ef4444; }
    .trend-neutral { color: #6b7280; }
    
    .compliance-section {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        margin-bottom: 2rem;
    }
    
    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .section-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        color: white;
        font-size: 1.25rem;
    }
    
    .section-icon.blue { background: #3b82f6; }
    .section-icon.green { background: #10b981; }
    
    .section-title h2 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }
    
    .section-title p {
        color: #7f8c8d;
        font-size: 0.875rem;
        margin: 0.25rem 0 0 0;
    }
    
    .compliance-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .compliance-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .compliance-item:hover {
        background: #f8fafc;
    }
    
    .compliance-item.status-green { 
        background: #f0fdf4; 
        border-left-color: #10b981;
    }
    
    .compliance-item.status-yellow { 
        background: #fffbeb; 
        border-left-color: #f59e0b;
    }
    
    .compliance-item.status-red { 
        background: #fef2f2; 
        border-left-color: #ef4444;
    }
    
    .compliance-item.status-blue { 
        background: #eff6ff; 
        border-left-color: #3b82f6;
    }
    
    .item-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .item-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.875rem;
    }
    
    .item-icon.green { background: #10b981; }
    .item-icon.blue { background: #3b82f6; }
    .item-icon.yellow { background: #f59e0b; }
    .item-icon.red { background: #ef4444; }
    .item-icon.purple { background: #8b5cf6; }
    
    .item-name {
        font-weight: 500;
        color: #374151;
        font-size: 0.875rem;
    }
    
    .item-right {
        text-align: left;
    }
    
    .item-percentage {
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }
    
    .item-percentage.green { color: #10b981; }
    .item-percentage.blue { color: #3b82f6; }
    .item-percentage.yellow { color: #f59e0b; }
    .item-percentage.red { color: #ef4444; }
    .item-percentage.purple { color: #8b5cf6; }
    
    .item-status {
        font-size: 0.75rem;
        color: #6b7280;
    }
    
    .activities-section {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
    }
    
    .activities-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .activities-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }
    
    .view-all-btn {
        color: #3b82f6;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        transition: color 0.3s ease;
    }
    
    .view-all-btn:hover {
        color: #2563eb;
        text-decoration: underline;
    }
    
    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .activity-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        border-radius: 8px;
        background: #f8fafc;
        transition: all 0.3s ease;
    }
    
    .activity-item:hover {
        background: #f1f5f9;
    }
    
    .activity-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
    }
    
    .activity-icon.green { background: #10b981; }
    .activity-icon.blue { background: #3b82f6; }
    .activity-icon.yellow { background: #f59e0b; }
    .activity-icon.red { background: #ef4444; }
    
    .activity-info h4 {
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin: 0 0 0.25rem 0;
    }
    
    .activity-info p {
        font-size: 0.75rem;
        color: #6b7280;
        margin: 0;
    }
    
    .activity-status {
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .activity-status.green { color: #10b981; }
    .activity-status.blue { color: #3b82f6; }
    .activity-status.yellow { color: #f59e0b; }
    .activity-status.red { color: #ef4444; }
    
    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            padding: 1.5rem 1rem;
        }
        
        .page-header h1 {
            font-size: 2rem;
        }
        
        .header-actions {
            flex-direction: column;
        }
        
        .btn-refresh {
            width: 100%;
            justify-content: center;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .compliance-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .item-right {
            text-align: right;
        }
        
        .activity-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
    }
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
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1>تدقيق الامتثال</h1>
                    <p>مراقبة الامتثال القانوني والتنظيمي</p>
                </div>
                <div class="header-actions">
                    <button data-action="perform-check" class="btn-refresh">
                        <i class="fas fa-sync-alt"></i>
                        تحديث الفحص
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Statistics Dashboard -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3 data-compliance-compliance_rate>{{ $complianceData['compliance_rate'] }}%</h3>
                        <p>معدل الامتثال</p>
                        <div class="stat-trend trend-positive" data-compliance-compliance_trend>{{ $complianceData['compliance_trend'] }} من الشهر الماضي</div>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon blue">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div class="stat-info">
                        <h3 data-compliance-completed_documents>{{ $complianceData['completed_documents'] }}</h3>
                        <p>وثائق مكتملة</p>
                        <div class="stat-trend trend-neutral" data-compliance-pending_reviews>{{ $complianceData['pending_reviews'] }} معلقة للمراجعة</div>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon purple">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-info">
                        <h3 data-compliance-identified_risks>{{ $complianceData['identified_risks'] }}</h3>
                        <p>مخاطر محددة</p>
                        <div class="stat-trend trend-negative" data-compliance-high_priority_risks>{{ $complianceData['high_priority_risks'] }} عالية الأولوية</div>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon orange">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3 data-compliance-average_review_time>{{ $complianceData['average_review_time'] }}</h3>
                        <p>متوسط وقت المراجعة</p>
                        <div class="stat-trend trend-positive" data-compliance-review_time_improvement>{{ $complianceData['review_time_improvement'] }} من الأسبوع الماضي</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compliance Areas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Regulatory Compliance -->
            <div class="compliance-section">
                <div class="section-header">
                    <div class="section-icon blue">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <div class="section-title">
                        <h2>الامتثال التنظيمي</h2>
                        <p>الالتزام بالقوانين واللوائح</p>
                    </div>
                </div>
                
                <div class="compliance-list">
                    @foreach($regulatoryCompliance as $item)
                    <div class="compliance-item status-{{ $item['color'] }}">
                        <div class="item-left">
                            <div class="item-icon {{ $item['color'] }}">
                                <i class="fas fa-{{ $item['icon'] }}"></i>
                            </div>
                            <span class="item-name">{{ $item['name'] }}</span>
                        </div>
                        <div class="item-right">
                            <div class="item-percentage {{ $item['color'] }}">{{ $item['percentage'] }}%</div>
                            <div class="item-status">{{ $item['status'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Internal Compliance -->
            <div class="compliance-section">
                <div class="section-header">
                    <div class="section-icon green">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="section-title">
                        <h2>الامتثال الداخلي</h2>
                        <p>السياسات والإجراءات الداخلية</p>
                    </div>
                </div>
                
                <div class="compliance-list">
                    @foreach($internalCompliance as $item)
                    <div class="compliance-item status-{{ $item['color'] }}">
                        <div class="item-left">
                            <div class="item-icon {{ $item['color'] }}">
                                <i class="fas fa-{{ $item['icon'] }}"></i>
                            </div>
                            <span class="item-name">{{ $item['name'] }}</span>
                        </div>
                        <span class="item-status {{ $item['color'] }}">{{ $item['status'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="activities-section">
            <div class="activities-header">
                <h2 class="activities-title">الأنشطة الحديثة</h2>
                <a href="#" class="view-all-btn">عرض الكل</a>
            </div>
            
            <div class="activity-list">
                @foreach($recentActivities as $activity)
                <div class="activity-item">
                    <div class="activity-left">
                        <div class="activity-icon {{ $activity['color'] }}">
                            <i class="fas fa-{{ $activity['icon'] }}"></i>
                        </div>
                        <div class="activity-info">
                            <h4>{{ $activity['title'] }}</h4>
                            <p>{{ $activity['time'] }}</p>
                        </div>
                    </div>
                    <span class="activity-status {{ $activity['color'] }}">{{ $activity['status'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
