@extends('admin.layouts.admin')

@section('title', 'الكاتب العدل المركزي')

@push('styles')
<style>
.notary-module {
    transition: all 0.3s ease;
}
.notary-module:hover {
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
// Notary Dashboard State
const notaryState = {
    autoRefreshInterval: null,
    lastUpdate: null
};

// Auto-refresh data
function startNotaryAutoRefresh() {
    notaryState.autoRefreshInterval = setInterval(() => {
        refreshNotaryData();
    }, 5000); // Refresh every 5 seconds
}

// Refresh notary data
async function refreshNotaryData() {
    try {
        const response = await fetch('/blockchain/legal/notary/refresh', {
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            updateNotaryUI(data);
        }
    } catch (error) {
        console.error('Error refreshing notary data:', error);
    }
}

// Update notary UI with new data
function updateNotaryUI(data) {
    // Update services dashboard
    if (data.servicesData) {
        Object.keys(data.servicesData).forEach(key => {
            const el = document.querySelector(`[data-notary-${key}]`);
            if (el) {
                el.textContent = data.servicesData[key];
            }
        });
    }
    
    // Update recent requests
    if (data.recentRequests) {
        updateRecentRequests(data.recentRequests);
    }
    
    // Update last update time
    const lastUpdateEl = document.querySelector('[data-last-update]');
    if (lastUpdateEl) {
        lastUpdateEl.textContent = new Date().toLocaleTimeString('ar-SA');
    }
}

// Update recent requests display
function updateRecentRequests(requests) {
    const container = document.querySelector('.recent-requests-container');
    if (container && requests.length > 0) {
        container.innerHTML = requests.map(request => `
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl dynamic-content">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-${request.color}-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-${request.icon} text-${request.color}-600"></i>
                    </div>
                    <div>
                        <p class="font-medium">${request.title}</p>
                        <p class="text-sm text-gray-500">${request.id} • ${request.time}</p>
                    </div>
                </div>
                <span class="text-sm text-${request.color}-600 font-medium">${request.status}</span>
            </div>
        `).join('');
    }
}

// Request New Service
async function requestNewService() {
    const button = event.target;
    const originalContent = button.innerHTML;
    
    // Disable button and show loading
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري الطلب...';
    button.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
    button.classList.add('bg-gray-400');
    
    try {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            throw new Error('CSRF token not found');
        }
        
        // Start service request
        const response = await fetch('/blockchain/legal/notary/request', {
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
            showNotification('تم إنشاء الطلب بنجاح!', 'success');
            
            // Update UI with new data
            updateNotaryUI(data.data);
            
            // Show request details
            showRequestDetails(data.request);
        } else {
            throw new Error(data.message || 'فشل الطلب');
        }
    } catch (error) {
        console.error('Error requesting service:', error);
        showNotification('فشل الطلب: ' + error.message, 'error');
    } finally {
        // Restore button
        button.disabled = false;
        button.innerHTML = originalContent;
        button.classList.remove('bg-gray-400');
        button.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
    }
}

// Show request details modal
function showRequestDetails(request) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl p-6 max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold">تفاصيل الطلب</h3>
                <button onclick="closeModal(this)" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-check-circle text-blue-600 text-xl"></i>
                        <h4 class="text-lg font-semibold text-blue-800">تم إنشاء الطلب بنجاح</h4>
                    </div>
                    <p class="text-sm text-blue-700">رقم الطلب: ${request.id}</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-medium mb-2">معلومات الطلب</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>نوع الخدمة:</span>
                                <span class="font-medium">${request.service_type}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>الحالة:</span>
                                <span class="font-medium text-yellow-600">${request.status}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>الوقت المتوقع:</span>
                                <span class="font-medium">${request.estimated_time}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-medium mb-2">التفاصيل</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>التاريخ:</span>
                                <span class="font-medium">${request.created_at}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>الأولوية:</span>
                                <span class="font-medium">${request.priority}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>المسؤول:</span>
                                <span class="font-medium">${request.assignee}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h5 class="font-medium mb-2 text-green-800">الخطوات التالية</h5>
                    <ol class="space-y-1 text-sm text-green-700 list-decimal list-inside">
                        <li>مراجعة الطلب من قبل الفريق المختص</li>
                        <li>التأكد من استكمال جميع المستندات المطلوبة</li>
                        <li>معالجة الطلب خلال الوقت المحدد</li>
                        <li>إشعارك بالنتيجة النهائية</li>
                    </ol>
                </div>
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

// Request Document Notarization
async function requestDocumentNotarization() {
    const button = event.target;
    const originalContent = button.innerHTML;
    
    // Disable button and show loading
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري الطلب...';
    button.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
    button.classList.add('bg-gray-400');
    
    try {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            throw new Error('CSRF token not found');
        }
        
        // Start notarization request
        const response = await fetch('/blockchain/legal/notary/notarize', {
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
            showNotification('تم طلب التوثيق بنجاح!', 'success');
            
            // Update UI with new data
            updateNotaryUI(data.data);
            
            // Show notarization details
            showNotarizationDetails(data.request);
        } else {
            throw new Error(data.message || 'فشل الطلب');
        }
    } catch (error) {
        console.error('Error requesting notarization:', error);
        showNotification('فشل الطلب: ' + error.message, 'error');
    } finally {
        // Restore button
        button.disabled = false;
        button.innerHTML = originalContent;
        button.classList.remove('bg-gray-400');
        button.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
    }
}

// Get Digital Certificate
async function getDigitalCertificate() {
    const button = event.target;
    const originalContent = button.innerHTML;
    
    // Disable button and show loading
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري الإنشاء...';
    button.classList.remove('bg-green-600', 'hover:bg-green-700');
    button.classList.add('bg-gray-400');
    
    try {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            throw new Error('CSRF token not found');
        }
        
        // Start certificate request
        const response = await fetch('/blockchain/legal/notary/certificate', {
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
            showNotification('تم إنشاء الشهادة بنجاح!', 'success');
            
            // Update UI with new data
            updateNotaryUI(data.data);
            
            // Show certificate details
            showCertificateDetails(data.certificate);
        } else {
            throw new Error(data.message || 'فشل الطلب');
        }
    } catch (error) {
        console.error('Error getting certificate:', error);
        showNotification('فشل الطلب: ' + error.message, 'error');
    } finally {
        // Restore button
        button.disabled = false;
        button.innerHTML = originalContent;
        button.classList.remove('bg-gray-400');
        button.classList.add('bg-green-600', 'hover:bg-green-700');
    }
}

// Book Consultation
async function bookConsultation() {
    const button = event.target;
    const originalContent = button.innerHTML;
    
    // Disable button and show loading
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري الحجز...';
    button.classList.remove('bg-purple-600', 'hover:bg-purple-700');
    button.classList.add('bg-gray-400');
    
    try {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            throw new Error('CSRF token not found');
        }
        
        // Start consultation booking
        const response = await fetch('/blockchain/legal/notary/consultation', {
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
            showNotification('تم حجز الاستشارة بنجاح!', 'success');
            
            // Update UI with new data
            updateNotaryUI(data.data);
            
            // Show consultation details
            showConsultationDetails(data.consultation);
        } else {
            throw new Error(data.message || 'فشل الطلب');
        }
    } catch (error) {
        console.error('Error booking consultation:', error);
        showNotification('فشل الطلب: ' + error.message, 'error');
    } finally {
        // Restore button
        button.disabled = false;
        button.innerHTML = originalContent;
        button.classList.remove('bg-gray-400');
        button.classList.add('bg-purple-600', 'hover:bg-purple-700');
    }
}

// View All Requests
async function viewAllRequests() {
    const button = event.target;
    const originalContent = button.innerHTML;
    
    // Disable button and show loading
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري التحميل...';
    
    try {
        // Get all requests
        const response = await fetch('/blockchain/legal/notary/requests', {
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
            // Show all requests modal
            showAllRequestsModal(data.requests);
        } else {
            throw new Error(data.message || 'فشل التحميل');
        }
    } catch (error) {
        console.error('Error loading requests:', error);
        showNotification('فشل التحميل: ' + error.message, 'error');
    } finally {
        // Restore button
        button.disabled = false;
        button.innerHTML = originalContent;
    }
}

// Show notarization details modal
function showNotarizationDetails(request) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl p-6 max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold">تفاصيل طلب التوثيق</h3>
                <button onclick="closeModal(this)" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-file-contract text-blue-600 text-xl"></i>
                        <h4 class="text-lg font-semibold text-blue-800">تم إنشاء طلب التوثيق</h4>
                    </div>
                    <p class="text-sm text-blue-700">رقم الطلب: ${request.id}</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-medium mb-2">معلومات التوثيق</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>نوع الوثيقة:</span>
                                <span class="font-medium">${request.document_type}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>الحالة:</span>
                                <span class="font-medium text-yellow-600">${request.status}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>الوقت المتوقع:</span>
                                <span class="font-medium">${request.estimated_time}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-medium mb-2">التفاصيل</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>التاريخ:</span>
                                <span class="font-medium">${request.created_at}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>الأولوية:</span>
                                <span class="font-medium">${request.priority}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>الموثيق:</span>
                                <span class="font-medium">${request.notary_name}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h5 class="font-medium mb-2 text-green-800">الخطوات التالية</h5>
                    <ol class="space-y-1 text-sm text-green-700 list-decimal list-inside">
                        <li>مراجعة الوثيقة من قبل الكاتب العدل</li>
                        <li>التأكد من صحة جميع البيانات والمستندات</li>
                        <li>إتمام عملية التوثيق الرسمي</li>
                        <li>إصدار الوثيقة الموثقة والتوقيع الرقمي</li>
                        <li>إشعارك باستكمال العملية</li>
                    </ol>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Show certificate details modal
function showCertificateDetails(certificate) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl p-6 max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold">تفاصيل الشهادة الرقمية</h3>
                <button onclick="closeModal(this)" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-certificate text-green-600 text-xl"></i>
                        <h4 class="text-lg font-semibold text-green-800">تم إنشاء الشهادة الرقمية</h4>
                    </div>
                    <p class="text-sm text-green-700">رقم الشهادة: ${certificate.id}</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-medium mb-2">معلومات الشهادة</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>نوع الشهادة:</span>
                                <span class="font-medium">${certificate.type}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>الحالة:</span>
                                <span class="font-medium text-green-600">${certificate.status}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>صلاحية:</span>
                                <span class="font-medium">${certificate.validity}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-medium mb-2">التفاصيل</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>تاريخ الإصدار:</span>
                                <span class="font-medium">${certificate.issued_at}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>انتهاء الصلاحية:</span>
                                <span class="font-medium">${certificate.expires_at}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>المصادر:</span>
                                <span class="font-medium">${certificate.issuer}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h5 class="font-medium mb-2 text-blue-800">معلومات التحقق</h5>
                    <div class="space-y-2 text-sm text-blue-700">
                        <p>• يمكن التحقق من صحة الشهادة باستخدام الرقم التعريفي</p>
                        <p>• الشهادة موقعة رقمياً ومشفرة بتقنية متقدمة</p>
                        <p>• معتمة من قبل الهيئات الرسمية المختصة</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Show consultation details modal
function showConsultationDetails(consultation) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl p-6 max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold">تفاصيل الاستشارة</h3>
                <button onclick="closeModal(this)" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-calendar-check text-purple-600 text-xl"></i>
                        <h4 class="text-lg font-semibold text-purple-800">تم حجز الاستشارة</h4>
                    </div>
                    <p class="text-sm text-purple-700">رقم الاستشارة: ${consultation.id}</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-medium mb-2">معلومات الاستشارة</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>نوع الاستشارة:</span>
                                <span class="font-medium">${consultation.type}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>الحالة:</span>
                                <span class="font-medium text-yellow-600">${consultation.status}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>المدة:</span>
                                <span class="font-medium">${consultation.duration}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-medium mb-2">التفاصيل</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>التاريخ:</span>
                                <span class="font-medium">${consultation.date}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>الوقت:</span>
                                <span class="font-medium">${consultation.time}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>المستشار:</span>
                                <span class="font-medium">${consultation.consultant}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h5 class="font-medium mb-2 text-yellow-800">معلومات هامة</h5>
                    <div class="space-y-1 text-sm text-yellow-700">
                        <p>• يرجى الحضور قبل الموعد بـ 15 دقيقة</p>
                        <p>• إحضار جميع المستندات المطلوبة مسبقاً</p>
                        <p>• يمكن إعادة جدولة الاستشارة قبل 24 ساعة</p>
                        <p>• سيتم إشعارك بتأكيد الحجز</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Show all requests modal
function showAllRequestsModal(requests) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl p-6 max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold">جميع الطلبات</h3>
                <button onclick="closeModal(this)" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                ${requests.map(request => `
                    <div class="border rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-${request.color}-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-${request.icon} text-${request.color}-600 text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium">${request.title}</h4>
                                    <p class="text-sm text-gray-500">${request.id} • ${request.time}</p>
                                </div>
                            </div>
                            <span class="text-sm text-${request.color}-600 font-medium">${request.status}</span>
                        </div>
                        <div class="text-sm text-gray-600">
                            <p>النوع: ${request.type}</p>
                            <p>المسؤول: ${request.assignee}</p>
                            <p>الوقت المتوقع: ${request.estimated_time}</p>
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Start auto-refresh
    startNotaryAutoRefresh();
    
    // Add click handlers for all buttons
    const requestButton = document.querySelector('[data-action="request-service"]');
    if (requestButton) {
        requestButton.addEventListener('click', requestNewService);
    }
    
    const notarizeButton = document.querySelector('[data-action="notarize"]');
    if (notarizeButton) {
        notarizeButton.addEventListener('click', requestDocumentNotarization);
    }
    
    const certificateButton = document.querySelector('[data-action="certificate"]');
    if (certificateButton) {
        certificateButton.addEventListener('click', getDigitalCertificate);
    }
    
    const consultationButton = document.querySelector('[data-action="consultation"]');
    if (consultationButton) {
        consultationButton.addEventListener('click', bookConsultation);
    }
    
    const viewAllButton = document.querySelector('[data-action="view-all"]');
    if (viewAllButton) {
        viewAllButton.addEventListener('click', viewAllRequests);
    }
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (notaryState.autoRefreshInterval) {
            clearInterval(notaryState.autoRefreshInterval);
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
                        <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-stamp text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                                الكاتب العدل المركزي
                            </h1>
                            <p class="text-gray-600 text-lg">خدمات التوثيق والتوقيع الرقمي</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <button data-action="request-service" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl hover:bg-indigo-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-plus ml-2"></i>
                        طلب خدمة جديدة
                    </button>
                </div>
            </div>
        </div>

        <!-- Services Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6 notary-module">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-signature text-white text-xl"></i>
                    </div>
                    <div class="text-xs text-gray-500">مباشر</div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900" data-notary-signed_documents>{{ $servicesData['signed_documents'] }}</h3>
                <p class="text-sm text-gray-600">وثائق موقعة</p>
                <div class="mt-2 text-xs text-blue-600" data-notary-monthly_growth>{{ $servicesData['monthly_growth'] }} هذا الشهر</div>
            </div>
            
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6 notary-module">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-double text-white text-xl"></i>
                    </div>
                    <div class="text-xs text-gray-500">مباشر</div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900" data-notary-success_rate>{{ $servicesData['success_rate'] }}%</h3>
                <p class="text-sm text-gray-600">معدل النجاح</p>
                <div class="mt-2 text-xs text-green-600" data-notary-success_improvement>{{ $servicesData['success_improvement'] }} تحسن</div>
            </div>
            
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6 notary-module">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                    <div class="text-xs text-gray-500">مباشر</div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900" data-notary-average_processing_time>{{ $servicesData['average_processing_time'] }}</h3>
                <p class="text-sm text-gray-600">متوسط وقت المعالجة</p>
                <div class="mt-2 text-xs text-purple-600" data-notary-processing_improvement>{{ $servicesData['processing_improvement'] }} من الأسبوع الماضي</div>
            </div>
            
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6 notary-module">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <div class="text-xs text-gray-500">مباشر</div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900" data-notary-active_clients>{{ $servicesData['active_clients'] }}</h3>
                <p class="text-sm text-gray-600">عملاء نشطين</p>
                <div class="mt-2 text-xs text-orange-600" data-notary-new_clients>{{ $servicesData['new_clients'] }} هذا الشهر</div>
            </div>
        </div>

        <!-- Available Services -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Document Notarization -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-file-contract text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">توثيق الوثائق</h2>
                        <p class="text-sm text-gray-600">توقيع وتوثيق الوثائق الرسمية</p>
                    </div>
                </div>
                
                <div class="space-y-4 mb-6">
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <i class="fas fa-check text-green-500"></i>
                        <span>العقود والاتفاقيات</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <i class="fas fa-check text-green-500"></i>
                        <span>الوكالات الرسمية</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <i class="fas fa-check text-green-500"></i>
                        <span>الإقرارات والتصاريح</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <i class="fas fa-check text-green-500"></i>
                        <span>الشهادات والمستندات</span>
                    </div>
                </div>
                
                <button data-action="notarize" class="w-full bg-indigo-600 text-white px-6 py-3 rounded-2xl hover:bg-indigo-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-plus ml-2"></i>
                    طلب توثيق
                </button>
            </div>
            
            <!-- Digital Signatures -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-pen-fancy text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">التوقيعات الرقمية</h2>
                        <p class="text-sm text-gray-600">توقيعات إلكترونية معتمدة</p>
                    </div>
                </div>
                
                <div class="space-y-4 mb-6">
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <i class="fas fa-check text-green-500"></i>
                        <span>توقيعات رقمية متقدمة</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <i class="fas fa-check text-green-500"></i>
                        <span>شهادات التوقيع الرقمي</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <i class="fas fa-check text-green-500"></i>
                        <span>التحقق من التوقيعات</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <i class="fas fa-check text-green-500"></i>
                        <span>الأرشفة الرقمية</span>
                    </div>
                </div>
                
                <button data-action="certificate" class="w-full bg-green-600 text-white px-6 py-3 rounded-2xl hover:bg-green-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-shield-alt ml-2"></i>
                    الحصول على شهادة
                </button>
            </div>
            
            <!-- Legal Consultation -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-gavel text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">الاستشارات القانونية</h2>
                        <p class="text-sm text-gray-600">خدمات استشارية قانونية</p>
                    </div>
                </div>
                
                <div class="space-y-4 mb-6">
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <i class="fas fa-check text-green-500"></i>
                        <span>مراجعة العقود</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <i class="fas fa-check text-green-500"></i>
                        <span>النصائح القانونية</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <i class="fas fa-check text-green-500"></i>
                        <span>التحكيم والوساطة</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <i class="fas fa-check text-green-500"></i>
                        <span>التمثيل القانوني</span>
                    </div>
                </div>
                
                <button data-action="consultation" class="w-full bg-purple-600 text-white px-6 py-3 rounded-2xl hover:bg-purple-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-calendar ml-2"></i>
                    حجز استشارة
                </button>
            </div>
        </div>

        <!-- Recent Requests -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">الطلبات الحديثة</h2>
                <button data-action="view-all" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    عرض الكل
                </button>
            </div>
            
            <div class="space-y-4">
                @foreach($recentRequests as $request)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl dynamic-content">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-{{ $request['color'] }}-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-{{ $request['icon'] }} text-{{ $request['color'] }}-600"></i>
                        </div>
                        <div>
                            <p class="font-medium">{{ $request['title'] }}</p>
                            <p class="text-sm text-gray-500">{{ $request['id'] }} • {{ $request['time'] }}</p>
                        </div>
                    </div>
                    <span class="text-sm text-{{ $request['color'] }}-600 font-medium">{{ $request['status'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
