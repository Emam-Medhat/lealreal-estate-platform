@extends('admin.layouts.admin')

@section('title', 'الاستخبارات المكانية')

@push('styles')
<style>
.intelligence-module {
    transition: all 0.3s ease;
}
.intelligence-module:hover {
    transform: translateY(-2px);
}
.monitoring-active {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}
.dynamic-content {
    transition: all 0.5s ease;
}
.severity-high { border-left: 4px solid #ef4444; }
.severity-medium { border-left: 4px solid #f59e0b; }
.severity-low { border-left: 4px solid #10b981; }
.severity-critical { border-left: 4px solid #dc2626; }
</style>
@endpush

@push('scripts')
<script>
// Intelligence Dashboard State
const intelligenceState = {
    isScanning: false,
    isMonitoring: false,
    monitoringId: null,
    analysisId: null,
    scanInterval: null,
    monitoringInterval: null,
    analysisInterval: null,
    autoRefreshInterval: null,
    lastUpdate: null
};

// API Base URL
const API_BASE = '/blockchain/api/geospatial';

// Auto-refresh data
function startAutoRefresh() {
    intelligenceState.autoRefreshInterval = setInterval(() => {
        refreshDashboardData();
    }, 5000); // Refresh every 5 seconds
}

// Refresh dashboard data
async function refreshDashboardData() {
    try {
        const response = await fetch('/blockchain/geospatial/intelligence/refresh', {
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            updateDashboardUI(data);
        }
    } catch (error) {
        console.error('Error refreshing dashboard:', error);
    }
}

// Update dashboard UI with new data
function updateDashboardUI(data) {
    // Update satellite data
    if (data.satelliteData) {
        Object.keys(data.satelliteData).forEach(key => {
            const el = document.querySelector(`[data-satellite-${key}]`);
            if (el) {
                el.textContent = data.satelliteData[key];
            }
        });
    }
    
    // Update monitoring stats
    if (data.monitoringStats) {
        Object.keys(data.monitoringStats).forEach(key => {
            const el = document.querySelector(`[data-monitoring-${key}]`);
            if (el) {
                el.textContent = data.monitoringStats[key];
            }
        });
    }
    
    // Update patterns
    if (data.patterns) {
        updatePatternsDisplay(data.patterns);
    }
    
    // Update alerts
    if (data.alerts) {
        updateAlertsDisplay(data.alerts);
    }
    
    // Update last update time
    const lastUpdateEl = document.querySelector('[data-last-update]');
    if (lastUpdateEl) {
        lastUpdateEl.textContent = new Date().toLocaleTimeString('ar-SA');
    }
}

// Update alerts display
function updateAlertsDisplay(alerts) {
    const alertsContainer = document.querySelector('.alerts-container');
    if (alertsContainer && alerts.length > 0) {
        alertsContainer.innerHTML = alerts.map(alert => `
            <div class="flex items-start gap-3 p-3 bg-${getSeverityColor(alert.severity)}-50 rounded-xl dynamic-content">
                <div class="w-8 h-8 bg-${getSeverityColor(alert.severity)}-200 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                    <i class="fas fa-${getAlertIcon(alert.type)} text-${getSeverityColor(alert.severity)}-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <div class="text-sm font-medium">${alert.message}</div>
                    <div class="text-xs text-gray-500">${formatTime(alert.created_at)}</div>
                </div>
            </div>
        `).join('');
    }
}

// Get severity color
function getSeverityColor(severity) {
    const colors = {
        'high': 'red',
        'medium': 'yellow',
        'low': 'green',
        'critical': 'red',
        'info': 'blue',
        'success': 'green'
    };
    return colors[severity] || 'gray';
}

// Get alert icon
function getAlertIcon(type) {
    const icons = {
        'system_health': 'heartbeat',
        'data_processing': 'database',
        'satellite_status': 'satellite',
        'security': 'shield-alt'
    };
    return icons[type] || 'info-circle';
}

// Format time
function formatTime(dateTime) {
    return new Date(dateTime).toLocaleTimeString('ar-SA');
}

// Start Satellite Scan
async function startSatelliteScan() {
    if (intelligenceState.isScanning) return;
    
    const button = event.target;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري المسح...';
    
    try {
        // Use existing CSRF token instead of refreshing
        const csrfToken = getCsrfToken();
        
        if (!csrfToken) {
            throw new Error('CSRF token not found');
        }
        
        const response = await fetch(`${API_BASE}/satellite/scan`, {
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
            intelligenceState.isScanning = true;
            intelligenceState.scanId = data.scan_id;
            
            // Start progress polling
            intelligenceState.scanInterval = setInterval(checkScanProgress, 1000);
            
            showNotification('بدأ المسح الفضائي', 'success');
        } else {
            throw new Error(data.message || 'فشل بدء المسح');
        }
    } catch (error) {
        console.error('Error starting scan:', error);
        showNotification('فشل بدء المسح: ' + error.message, 'error');
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-satellite-dish ml-2"></i> بدء المسح';
    }
}

// Get CSRF Token
function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (!token) {
        console.error('CSRF token meta tag not found');
        return '';
    }
    const tokenValue = token.getAttribute('content');
    if (!tokenValue) {
        console.error('CSRF token value is empty');
        return '';
    }
    return tokenValue;
}

// Refresh CSRF token if needed
function refreshCsrfToken() {
    return fetch('/sanctum/csrf-cookie')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to refresh CSRF token');
            }
            return response.text();
        })
        .then(() => {
            // After setting the cookie, get the token from meta tag
            return getCsrfToken();
        })
        .catch(error => {
            console.error('Error refreshing CSRF token:', error);
            // Fallback to existing token
            return getCsrfToken();
        });
}

// Check Scan Progress
async function checkScanProgress() {
    try {
        const response = await fetch(`${API_BASE}/satellite/progress`, {
            headers: {
                'Accept': 'application/json'
            }
        });
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Server returned non-JSON response:', text);
            clearInterval(intelligenceState.scanInterval);
            intelligenceState.isScanning = false;
            return;
        }
        
        const data = await response.json();
        
        if (data.status === 'not_found') {
            clearInterval(intelligenceState.scanInterval);
            intelligenceState.isScanning = false;
            return;
        }
        
        if (data.status === 'completed') {
            clearInterval(intelligenceState.scanInterval);
            intelligenceState.isScanning = false;
            
            const button = document.querySelector('[data-action="start-scan"]');
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-check ml-2"></i> تم المسح';
            button.classList.remove('bg-indigo-600');
            button.classList.add('bg-green-600');
            
            // Update UI with real data
            updateSatelliteData(data.data);
            showNotification('اكتمل المسح بنجاح!', 'success');
            
            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-satellite-dish ml-2"></i> بدء المسح';
                button.classList.remove('bg-green-600');
                button.classList.add('bg-indigo-600');
            }, 3000);
        } else {
            // Update progress
            const button = document.querySelector('[data-action="start-scan"]');
            button.innerHTML = `<i class="fas fa-spinner fa-spin ml-2"></i> جاري المسح... ${data.progress}%`;
        }
    } catch (error) {
        console.error('Error checking progress:', error);
        clearInterval(intelligenceState.scanInterval);
        intelligenceState.isScanning = false;
        
        const button = document.querySelector('[data-action="start-scan"]');
        if (button) {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-satellite-dish ml-2"></i> بدء المسح';
        }
    }
}

// View Satellite Images
async function viewSatelliteImages() {
    const button = event.target;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري التحميل...';
    
    try {
        const response = await fetch(`${API_BASE}/satellite/images`, {
            headers: {
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
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-images ml-2"></i> عرض الصور';
            
            showSatelliteImagesModal(data.images);
        } else {
            throw new Error(data.message || 'فشل تحميل الصور');
        }
    } catch (error) {
        console.error('Error loading images:', error);
        showNotification('فشل تحميل الصور: ' + error.message, 'error');
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-images ml-2"></i> عرض الصور';
    }
}

// Advanced Pattern Analysis
async function startAdvancedAnalysis() {
    const button = event.target;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-brain fa-pulse ml-2"></i> جاري التحليل...';
    
    try {
        // Use existing CSRF token instead of refreshing
        const csrfToken = getCsrfToken();
        
        if (!csrfToken) {
            throw new Error('CSRF token not found');
        }
        
        const response = await fetch(`${API_BASE}/analysis/start`, {
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
            intelligenceState.analysisId = data.analysis_id;
            
            // Start progress polling
            intelligenceState.analysisInterval = setInterval(() => {
                checkAnalysisProgress(data.analysis_id);
            }, 1500);
            
            showNotification('بدأ التحليل المتقدم', 'success');
        } else {
            throw new Error(data.message || 'فشل بدء التحليل');
        }
    } catch (error) {
        console.error('Error starting analysis:', error);
        showNotification('فشل بدء التحليل: ' + error.message, 'error');
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-brain ml-2"></i> تحليل متقدم';
    }
}

// Check Analysis Progress
async function checkAnalysisProgress(analysisId) {
    try {
        const response = await fetch(`${API_BASE}/analysis/progress/${analysisId}`, {
            headers: {
                'Accept': 'application/json'
            }
        });
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Server returned non-JSON response:', text);
            clearInterval(intelligenceState.analysisInterval);
            return;
        }
        
        const data = await response.json();
        
        if (data.status === 'completed') {
            clearInterval(intelligenceState.analysisInterval);
            
            const button = document.querySelector('[data-action="advanced-analysis"]');
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-chart-line ml-2"></i> عرض النتائج';
            
            // Update patterns display
            updatePatternsDisplay(data.patterns);
            showNotification('اكتمل التحليل المتقدم!', 'success');
        } else {
            // Update progress
            const button = document.querySelector('[data-action="advanced-analysis"]');
            button.innerHTML = `<i class="fas fa-brain fa-pulse ml-2"></i> جاري التحليل... ${data.progress}%`;
        }
    } catch (error) {
        console.error('Error checking analysis progress:', error);
        clearInterval(intelligenceState.analysisInterval);
        
        const button = document.querySelector('[data-action="advanced-analysis"]');
        if (button) {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-brain ml-2"></i> تحليل متقدم';
        }
    }
}

// View Predictions
async function viewPredictions() {
    const button = event.target;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري التحميل...';
    
    try {
        const response = await fetch(`${API_BASE}/predictions`, {
            headers: {
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
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-eye ml-2"></i> عرض التنبؤات';
            
            showPredictionsModal(data.predictions);
        } else {
            throw new Error(data.message || 'فشل تحميل التنبؤات');
        }
    } catch (error) {
        console.error('Error loading predictions:', error);
        showNotification('فشل تحميل التنبؤات: ' + error.message, 'error');
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-chart-line ml-2"></i> عرض التنبؤات';
    }
}

// Start/Stop Monitoring
async function toggleMonitoring() {
    const button = event.target;
    const monitoringArea = document.querySelector('.monitoring-area');
    
    if (intelligenceState.isMonitoring) {
        // Stop monitoring
        try {
            // Use existing CSRF token instead of refreshing
            const csrfToken = getCsrfToken();
            
            if (!csrfToken) {
                throw new Error('CSRF token not found');
            }
            
            const response = await fetch(`${API_BASE}/monitoring/stop/${intelligenceState.monitoringId}`, {
                method: 'POST',
                headers: {
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
                intelligenceState.isMonitoring = false;
                clearInterval(intelligenceState.monitoringInterval);
                
                button.innerHTML = '<i class="fas fa-play ml-2"></i> بدء المراقبة';
                button.classList.remove('bg-red-100', 'text-red-700');
                button.classList.add('bg-green-100', 'text-green-700');
                monitoringArea.classList.remove('monitoring-active');
                
                showNotification('تم إيقاف المراقبة', 'info');
            } else {
                throw new Error(data.message || 'فشل إيقاف المراقبة');
            }
        } catch (error) {
            console.error('Error stopping monitoring:', error);
            showNotification('فشل إيقاف المراقبة: ' + error.message, 'error');
        }
    } else {
        // Start monitoring
        try {
            // Use existing CSRF token instead of refreshing
            const csrfToken = getCsrfToken();
            
            if (!csrfToken) {
                throw new Error('CSRF token not found');
            }
            
            const response = await fetch(`${API_BASE}/monitoring/start`, {
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
                intelligenceState.isMonitoring = true;
                intelligenceState.monitoringId = data.monitoring_id;
                
                button.innerHTML = '<i class="fas fa-stop ml-2"></i> إيقاف المراقبة';
                button.classList.remove('bg-green-100', 'text-green-700');
                button.classList.add('bg-red-100', 'text-red-700');
                monitoringArea.classList.add('monitoring-active');
                
                showNotification('بدأت المراقبة في الوقت الفعلي', 'success');
                
                // Start real-time updates
                intelligenceState.monitoringInterval = setInterval(() => {
                    updateMonitoringStats();
                }, 3000);
            } else {
                throw new Error(data.message || 'فشل بدء المراقبة');
            }
        } catch (error) {
            console.error('Error starting monitoring:', error);
            showNotification('فشل بدء المراقبة: ' + error.message, 'error');
        }
    }
}

// Update Monitoring Stats
async function updateMonitoringStats() {
    if (!intelligenceState.isMonitoring) return;
    
    try {
        const response = await fetch(`${API_BASE}/monitoring/stats/${intelligenceState.monitoringId}`, {
            headers: {
                'Accept': 'application/json'
            }
        });
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Server returned non-JSON response:', text);
            return;
        }
        
        const stats = await response.json();
        
        // Update UI with new stats
        Object.keys(stats).forEach(key => {
            const el = document.querySelector(`[data-monitoring-${key}]`);
            if (el) {
                el.textContent = stats[key];
            }
        });
    } catch (error) {
        console.error('Error updating monitoring stats:', error);
    }
}

// Update Satellite Data Display
function updateSatelliteData(data) {
    if (data) {
        // Update UI elements with real data
        Object.keys(data).forEach(key => {
            const el = document.querySelector(`[data-satellite-${key}]`);
            if (el) {
                el.textContent = data[key];
            }
        });
    }
}

// Update Patterns Display
function updatePatternsDisplay(patterns) {
    const patternsContainer = document.querySelector('.patterns-container');
    if (patternsContainer && patterns.length > 0) {
        patternsContainer.innerHTML = patterns.map(pattern => `
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-xl dynamic-content severity-${pattern.severity}">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <span class="text-sm font-medium">${pattern.type}</span>
                        <div class="text-xs text-gray-500">${formatTime(pattern.last_detected)}</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-blue-600 font-medium">${pattern.confidence}%</div>
                    <div class="text-xs text-gray-500">${pattern.trend === 'increasing' ? '↗' : pattern.trend === 'decreasing' ? '↘' : '→'} ${pattern.change_rate}</div>
                    <div class="text-xs text-gray-500">${pattern.affected_areas} منطقة</div>
                </div>
            </div>
        `).join('');
    }
}

// Show Notification
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

// Show Satellite Images Modal
function showSatelliteImagesModal(images) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl p-6 max-w-6xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold">الصور الفضائية الحية</h3>
                <button onclick="closeModal(this)" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                ${images.map(img => `
                    <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow dynamic-content">
                        <img src="https://picsum.photos/seed/satellite${img.id}/300/200.jpg" alt="${img.name}" class="w-full h-32 object-cover rounded mb-2">
                        <h4 class="font-medium">${img.name}</h4>
                        <p class="text-sm text-gray-600">${img.date}</p>
                        <div class="text-xs text-gray-500 space-y-1">
                            <div>📏 ${img.resolution}</div>
                            <div>📍 ${img.coordinates}</div>
                            <div>💾 ${img.size}</div>
                            <div>☁️ ${img.cloud_cover}</div>
                            <div>⭐ ${img.quality_score}%</div>
                            <div>🕐 ${formatTime(img.captured_at)}</div>
                        </div>
                        <div class="mt-2">
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">${img.processing_status}</span>
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Show Predictions Modal
function showPredictionsModal(predictions) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl p-6 max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold">التنبؤات المكانية الحية</h3>
                <button onclick="closeModal(this)" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                ${predictions.map(pred => `
                    <div class="border rounded-lg p-4 dynamic-content severity-${pred.impact_level}">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-medium text-lg">${pred.area}</h4>
                                <p class="text-xl font-semibold text-blue-600">${pred.prediction}</p>
                                <div class="text-sm text-gray-500 mt-1">ID: ${pred.id}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold text-green-600">${pred.confidence}%</div>
                                <div class="text-sm text-gray-500">${pred.timeframe}</div>
                                <div class="text-sm text-gray-500">احتمالية: ${(pred.probability * 100).toFixed(1)}%</div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <p class="text-sm text-gray-600 mb-2">العوامل المؤثرة:</p>
                            <div class="flex flex-wrap gap-2">
                                ${pred.factors.map(factor => `
                                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">${factor}</span>
                                `).join('')}
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            تم الإنشاء: ${formatTime(pred.created_at)}
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Close Modal
function closeModal(button) {
    const modal = button.closest('.fixed');
    if (modal && modal.parentNode) {
        modal.parentNode.removeChild(modal);
    }
}

// Open Settings
function openSettings() {
    showNotification('فتح إعدادات الاستخبارات المكانية', 'info');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Debug: Check if CSRF token is available
    const csrfToken = getCsrfToken();
    console.log('CSRF Token available:', csrfToken ? 'Yes' : 'No');
    if (!csrfToken) {
        console.error('CSRF token not found in page');
        showNotification('خطأ: CSRF token غير موجود', 'error');
    }
    
    // Start auto-refresh
    startAutoRefresh();
    
    // Add click handlers to buttons
    document.querySelectorAll('[data-action]').forEach(button => {
        button.addEventListener('click', function() {
            const action = this.dataset.action;
            switch(action) {
                case 'start-scan':
                    startSatelliteScan();
                    break;
                case 'view-images':
                    viewSatelliteImages();
                    break;
                case 'advanced-analysis':
                    startAdvancedAnalysis();
                    break;
                case 'view-predictions':
                    viewPredictions();
                    break;
                case 'toggle-monitoring':
                    toggleMonitoring();
                    break;
                case 'open-settings':
                    openSettings();
                    break;
            }
        });
    });
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (intelligenceState.monitoringInterval) {
            clearInterval(intelligenceState.monitoringInterval);
        }
        if (intelligenceState.scanInterval) {
            clearInterval(intelligenceState.scanInterval);
        }
        if (intelligenceState.analysisInterval) {
            clearInterval(intelligenceState.analysisInterval);
        }
        if (intelligenceState.autoRefreshInterval) {
            clearInterval(intelligenceState.autoRefreshInterval);
        }
    });
});
</script>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-indigo-50 to-purple-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-satellite text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                                الاستخبارات المكانية
                            </h1>
                            <p class="text-gray-600 text-lg">تحليل متقدم للبيانات المكانية والاستخبارات الجغرافية</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <button data-action="start-scan" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl hover:bg-indigo-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-satellite-dish ml-2"></i>
                        بدء المسح
                    </button>
                </div>
            </div>
        </div>

        <!-- Intelligence Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-satellite text-white text-xl"></i>
                    </div>
                    <div class="text-xs text-gray-500">مباشر</div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900" data-satellite-satellites_count>{{ $satelliteData['satellites_count'] }}</h3>
                <p class="text-sm text-gray-600">قمر صناعي</p>
                <div class="mt-2 text-xs text-green-600">+{{ $satelliteData['active_satellites'] }} نشط</div>
            </div>
            
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-globe text-white text-xl"></i>
                    </div>
                    <div class="text-xs text-gray-500">مباشر</div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900" data-satellite-coverage_percentage>{{ $satelliteData['coverage_percentage'] }}%</h3>
                <p class="text-sm text-gray-600">معدل التغطية</p>
                <div class="mt-2 text-xs text-blue-600">{{ $satelliteData['areas_covered'] }} كم²</div>
            </div>
            
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-database text-white text-xl"></i>
                    </div>
                    <div class="text-xs text-gray-500">مباشر</div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900" data-satellite-data_stored>{{ $satelliteData['data_stored'] }}</h3>
                <p class="text-sm text-gray-600">بيانات مخزنة</p>
                <div class="mt-2 text-xs text-purple-600">{{ $satelliteData['data_rate'] }}</div>
            </div>
            
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-crosshairs text-white text-xl"></i>
                    </div>
                    <div class="text-xs text-gray-500">مباشر</div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900" data-satellite-accuracy>{{ $satelliteData['accuracy'] }}m</h3>
                <p class="text-sm text-gray-600">دقة الموقع</p>
                <div class="mt-2 text-xs text-orange-600">{{ $satelliteData['resolution'] }}</div>
            </div>
        </div>

        <!-- Intelligence Modules -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Satellite Imagery -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8 intelligence-module">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-satellite-dish text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">الصور الفضائية</h2>
                        <p class="text-sm text-gray-600">تحليل الصور في الوقت الفعلي</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-sm text-gray-600">آخر تحديث</span>
                        <span class="text-sm font-medium" data-last-update>{{ now()->format('H:i:s') }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-sm text-gray-600">جودة الصور</span>
                        <span class="text-sm font-medium">{{ $satelliteData['resolution'] }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-sm text-gray-600">حالة المسح</span>
                        <span class="text-sm font-medium" data-satellite-scan_status>{{ $satelliteData['scan_status'] }}</span>
                    </div>
                </div>
                
                <div class="mt-6 space-y-3">
                    <button data-action="start-scan" class="w-full bg-indigo-600 text-white px-6 py-3 rounded-2xl hover:bg-indigo-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-satellite-dish ml-2"></i>
                        بدء المسح
                    </button>
                    
                    <button data-action="view-images" class="w-full bg-gray-100 text-gray-700 px-6 py-3 rounded-2xl hover:bg-gray-200 transition-all duration-300 font-semibold">
                        <i class="fas fa-images ml-2"></i>
                        عرض الصور
                    </button>
                </div>
            </div>
            
            <!-- Pattern Analysis -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8 intelligence-module">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">تحليل الأنماط</h2>
                        <p class="text-sm text-gray-600">اكتشاف الأنماط المكانية</p>
                    </div>
                </div>
                
                <div class="patterns-container space-y-3">
                    @foreach($patterns as $pattern)
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-xl dynamic-content severity-{{ $pattern['severity'] }}">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-line text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <span class="text-sm font-medium">{{ $pattern['type'] }}</span>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($pattern['last_detected'])->format('H:i:s') }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-blue-600 font-medium">{{ $pattern['confidence'] }}%</div>
                            <div class="text-xs text-gray-500">
                                {{ $pattern['trend'] === 'increasing' ? '↗' : ($pattern['trend'] === 'decreasing' ? '↘' : '→') }} {{ $pattern['change_rate'] }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $pattern['affected_areas'] }} منطقة</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-6">
                    <button data-action="advanced-analysis" class="w-full bg-green-600 text-white px-6 py-3 rounded-2xl hover:bg-green-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-brain ml-2"></i>
                        تحليل متقدم
                    </button>
                </div>
            </div>
            
            <!-- Predictions -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8 intelligence-module">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-chart-pie text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">التنبؤات</h2>
                        <p class="text-sm text-gray-600">توقعات مستقبلية دقيقة</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    @foreach($predictions as $prediction)
                    <div class="border rounded-lg p-4 dynamic-content severity-{{ $prediction['impact_level'] }}">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-medium">{{ $prediction['area'] }}</h4>
                                <p class="text-sm text-blue-600">{{ $prediction['prediction'] }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-green-600">{{ $prediction['confidence'] }}%</div>
                                <div class="text-xs text-gray-500">{{ $prediction['timeframe'] }}</div>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500">
                            احتمالية: {{ ($prediction['probability'] * 100) }}%
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-6">
                    <button data-action="view-predictions" class="w-full bg-orange-600 text-white px-6 py-3 rounded-2xl hover:bg-orange-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-chart-line ml-2"></i>
                        عرض التنبؤات
                    </button>
                </div>
            </div>
        </div>

        <!-- Real-time Monitoring -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8 monitoring-area">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-r from-red-500 to-pink-500 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-satellite text-white text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">المراقبة في الوقت الفعلي</h2>
                            <p class="text-gray-600">مراقبة مستمرة للبيانات المكانية</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <button data-action="toggle-monitoring" class="bg-green-100 text-green-700 px-6 py-3 rounded-2xl hover:bg-green-200 transition-all duration-300 font-semibold">
                        <i class="fas fa-play ml-2"></i>
                        بدء المراقبة
                    </button>
                    <button data-action="open-settings" class="bg-gray-100 text-gray-700 px-6 py-3 rounded-2xl hover:bg-gray-200 transition-all duration-300 font-semibold">
                        <i class="fas fa-cog ml-2"></i>
                        الإعدادات
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gray-50 rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-link text-blue-600"></i>
                        </div>
                        <div class="text-xs text-gray-500">مباشر</div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900" data-monitoring-active_connections>{{ $monitoringStats['active_connections'] }}</h3>
                    <p class="text-sm text-gray-600">اتصال نشط</p>
                </div>
                
                <div class="bg-gray-50 rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-database text-green-600"></i>
                        </div>
                        <div class="text-xs text-gray-500">مباشر</div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900" data-monitoring-data_processed>{{ $monitoringStats['data_processed'] }}</h3>
                    <p class="text-sm text-gray-600">بيانات معالجة</p>
                </div>
                
                <div class="bg-gray-50 rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                        </div>
                        <div class="text-xs text-gray-500">مباشر</div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900" data-monitoring-alerts_count>{{ $monitoringStats['alerts_count'] }}</h3>
                    <p class="text-sm text-gray-600">تنبيهات</p>
                </div>
                
                <div class="bg-gray-50 rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-tachometer-alt text-purple-600"></i>
                        </div>
                        <div class="text-xs text-gray-500">مباشر</div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900" data-monitoring-response_time>{{ $monitoringStats['response_time'] }}</h3>
                    <p class="text-sm text-gray-600">وقت الاستجابة</p>
                </div>
            </div>
            
            <!-- System Alerts -->
            <div class="mt-8">
                <h3 class="text-lg font-bold text-gray-900 mb-4">التنبيهات النظامية</h3>
                <div class="alerts-container space-y-3">
                    @foreach($alerts as $alert)
                    <div class="flex items-start gap-3 p-3 bg-{{ app('App\Http\Controllers\MetaverseController')->getSeverityColor($alert['severity']) }}-50 rounded-xl dynamic-content">
                        <div class="w-8 h-8 bg-{{ app('App\Http\Controllers\MetaverseController')->getSeverityColor($alert['severity']) }}-200 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                            <i class="fas fa-{{ app('App\Http\Controllers\MetaverseController')->getAlertIcon($alert['type']) }} text-{{ app('App\Http\Controllers\MetaverseController')->getSeverityColor($alert['severity']) }}-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium">{{ $alert['message'] }}</div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($alert['created_at'])->format('H:i:s') }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
