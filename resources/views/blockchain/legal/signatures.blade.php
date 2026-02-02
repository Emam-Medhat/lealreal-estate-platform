@extends('admin.layouts.admin')

@section('title', 'التوقيعات الإلكترونية')

@push('styles')
<style>
.signatures-module {
    transition: all 0.3s ease;
}
.signatures-module:hover {
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
// Signatures Dashboard State
const signaturesState = {
    autoRefreshInterval: null,
    lastUpdate: null
};

// Auto-refresh data
function startSignaturesAutoRefresh() {
    signaturesState.autoRefreshInterval = setInterval(() => {
        refreshSignaturesData();
    }, 5000); // Refresh every 5 seconds
}

// Refresh signatures data
async function refreshSignaturesData() {
    try {
        const response = await fetch('/blockchain/legal/signatures/refresh', {
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            updateSignaturesUI(data);
        }
    } catch (error) {
        console.error('Error refreshing signatures data:', error);
    }
}

// Update signatures UI with new data
function updateSignaturesUI(data) {
    // Update signatures dashboard
    if (data.signaturesData) {
        Object.keys(data.signaturesData).forEach(key => {
            const el = document.querySelector(`[data-signatures-${key}]`);
            if (el) {
                el.textContent = data.signaturesData[key];
            }
        });
    }
    
    // Update recent signatures
    if (data.recentSignatures) {
        updateRecentSignatures(data.recentSignatures);
    }
    
    // Update last update time
    const lastUpdateEl = document.querySelector('[data-last-update]');
    if (lastUpdateEl) {
        lastUpdateEl.textContent = new Date().toLocaleTimeString('ar-SA');
    }
}

// Update recent signatures display
function updateRecentSignatures(signatures) {
    const container = document.querySelector('.recent-signatures-container');
    if (container && signatures.length > 0) {
        container.innerHTML = signatures.map(signature => `
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl dynamic-content">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-${signature.color}-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-${signature.icon} text-${signature.color}-600"></i>
                    </div>
                    <div>
                        <p class="font-medium">${signature.title}</p>
                        <p class="text-sm text-gray-500">${signature.id} • ${signature.signer} • ${signature.time}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-sm text-${signature.color}-600 font-medium">${signature.status}</span>
                    <p class="text-xs text-gray-500">${signature.verified}</p>
                </div>
            </div>
        `).join('');
    }
}

// Create New Signature
async function createNewSignature() {
    const button = event.target;
    const originalContent = button.innerHTML;
    
    // Check if file is uploaded
    if (!window.uploadedFile) {
        showNotification('يرجى رفع المستند أولاً', 'warning');
        return;
    }
    
    // Get form data
    const signatureType = document.querySelector('select')?.value || 'توقيع بسيط';
    const fullName = document.querySelector('input[placeholder="الاسم الكامل"]')?.value;
    const email = document.querySelector('input[placeholder="البريد الإلكتروني"]')?.value;
    
    // Validate required fields
    if (!fullName || !email) {
        showNotification('يرجى ملء جميع الحقول المطلوبة', 'warning');
        return;
    }
    
    // Disable button and show loading
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري الإنشاء...';
    button.classList.remove('bg-teal-600', 'hover:bg-teal-700');
    button.classList.add('bg-gray-400');
    
    try {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            throw new Error('CSRF token not found');
        }
        
        // Create FormData for file upload
        const formData = new FormData();
        formData.append('file', window.uploadedFile);
        formData.append('type', signatureType);
        formData.append('full_name', fullName);
        formData.append('email', email);
        
        // Start signature creation
        const response = await fetch('/blockchain/legal/signatures/create', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
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
            showNotification('تم إنشاء التوقيع بنجاح!', 'success');
            
            // Reset upload area
            resetUploadArea();
            
            // Reset form fields
            document.querySelector('input[placeholder="الاسم الكامل"]').value = '';
            document.querySelector('input[placeholder="البريد الإلكتروني"]').value = '';
            document.querySelector('select').selectedIndex = 0;
            
            // Update UI with new data
            updateSignaturesUI(data.data);
            
            // Show signature details
            showSignatureDetails(data.signature);
        } else {
            throw new Error(data.message || 'فشل الإنشاء');
        }
    } catch (error) {
        console.error('Error creating signature:', error);
        showNotification('فشل الإنشاء: ' + error.message, 'error');
    } finally {
        // Restore button
        button.disabled = false;
        button.innerHTML = originalContent;
        button.classList.remove('bg-gray-400');
        button.classList.add('bg-teal-600', 'hover:bg-teal-700');
    }
}

// Verify Signature
async function verifySignature() {
    const button = event.target;
    const originalContent = button.innerHTML;
    
    // Check if file is uploaded
    if (!window.verifyUploadedFile) {
        showNotification('يرجى رفع المستند أولاً', 'warning');
        return;
    }
    
    // Get signature ID
    const signatureId = document.querySelector('input[placeholder="أدخل رقم التوقيع"]').value;
    
    if (!signatureId) {
        showNotification('يرجى إدخل رقم التوقيع', 'warning');
        return;
    }
    
    // Disable button and show loading
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري التحقق...';
    button.classList.remove('bg-green-600', 'hover:bg-green-700');
    button.classList.add('bg-gray-400');
    
    try {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            throw new Error('CSRF token not found');
        }
        
        // Create FormData for file upload
        const formData = new FormData();
        formData.append('file', window.verifyUploadedFile);
        formData.append('signature_id', signatureId);
        
        // Start signature verification
        const response = await fetch('/blockchain/legal/signatures/verify', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
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
            showNotification('تم التحقق بنجاح!', 'success');
            
            // Reset verify upload area
            resetVerifyUploadArea();
            
            // Update UI with new data
            updateSignaturesUI(data.data);
            
            // Show verification results
            showVerificationResults(data.verification);
        } else {
            throw new Error(data.message || 'فشل التحقق');
        }
    } catch (error) {
        console.error('Error verifying signature:', error);
        showNotification('فشل التحقق: ' + error.message, 'error');
    } finally {
        // Restore button
        button.disabled = false;
        button.innerHTML = originalContent;
        button.classList.remove('bg-gray-400');
        button.classList.add('bg-green-600', 'hover:bg-green-700');
    }
}

// Reset upload areas
function resetUploadArea() {
    const uploadContent = document.getElementById('upload-content');
    const uploadProgress = document.getElementById('upload-progress');
    const uploadResult = document.getElementById('upload-result');
    
    uploadContent.classList.remove('hidden');
    uploadProgress.classList.add('hidden');
    uploadResult.classList.add('hidden');
    
    // Reset file input
    const fileInput = document.getElementById('file-input');
    fileInput.value = '';
    
    // Clear uploaded file reference
    window.uploadedFile = null;
    
    // Reset upload content
    uploadContent.innerHTML = `
        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
        <p class="text-sm text-gray-600">اسحب وأفلت الملفات هنا أو انقر للاختيار</p>
        <p class="text-xs text-gray-500 mt-1">PDF, DOC, DOCX (حتى 10MB)</p>
    `;
}

function resetVerifyUploadArea() {
    const verifyUploadContent = document.getElementById('verify-upload-content');
    const verifyUploadProgress = document.getElementById('verify-upload-progress');
    const verifyUploadResult = document.getElementById('verify-upload-result');
    
    verifyUploadContent.classList.remove('hidden');
    verifyUploadProgress.classList.add('hidden');
    verifyUploadResult.classList.add('hidden');
    
    // Reset file input
    const verifyFileInput = document.getElementById('verify-file-input');
    verifyFileInput.value = '';
    
    // Clear uploaded file reference
    window.verifyUploadedFile = null;
    
    // Reset upload content
    verifyUploadContent.innerHTML = `
        <i class="fas fa-file-upload text-3xl text-gray-400 mb-2"></i>
        <p class="text-sm text-gray-600">اسحب وأفلت الملف الموقع</p>
        <p class="text-xs text-gray-500 mt-1">PDF, DOC, DOCX</p>
    `;
}

// View All Signatures
async function viewAllSignatures() {
    const button = event.target;
    const originalContent = button.innerHTML;
    
    // Disable button and show loading
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري التحميل...';
    
    try {
        // Get all signatures
        const response = await fetch('/blockchain/legal/signatures/all', {
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
            // Show all signatures modal
            showAllSignaturesModal(data.signatures);
        } else {
            throw new Error(data.message || 'فشل التحميل');
        }
    } catch (error) {
        console.error('Error loading signatures:', error);
        showNotification('فشل التحميل: ' + error.message, 'error');
    } finally {
        // Restore button
        button.disabled = false;
        button.innerHTML = originalContent;
    }
}

// Show signature details modal
function showSignatureDetails(signature) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl p-6 max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold">تفاصيل التوقيع</h3>
                <button onclick="closeModal(this)" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="bg-teal-50 border border-teal-200 rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-pen-nib text-teal-600 text-xl"></i>
                        <h4 class="text-lg font-semibold text-teal-800">تم إنشاء التوقيع الرقمي</h4>
                    </div>
                    <p class="text-sm text-teal-700">رقم التوقيع: ${signature.id}</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-medium mb-2">معلومات التوقيع</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>نوع التوقيع:</span>
                                <span class="font-medium">${signature.type}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>الحالة:</span>
                                <span class="font-medium text-green-600">${signature.status}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>مستوى التشفير:</span>
                                <span class="font-medium">${signature.encryption_level}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-medium mb-2">التفاصيل</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>التاريخ:</span>
                                <span class="font-medium">${signature.created_at}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>الصلاحية:</span>
                                <span class="font-medium">${signature.validity}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>الموقع:</span>
                                <span class="font-medium">${signature.signer}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h5 class="font-medium mb-2 text-blue-800">معلومات التحقق</h5>
                    <div class="space-y-2 text-sm text-blue-700">
                        <p>• التوقيع موثق رقمياً ومشفّر</p>
                        <p>• يمكن التحقق من الصلاحية في أي وقت</p>
                        <p>• معتمد من قبل الجهات الرسمية</p>
                        <p>• متوافق مع المعايير الدولية</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Show verification results modal
function showVerificationResults(verification) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl p-6 max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold">نتائج التحقق</h3>
                <button onclick="closeModal(this)" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        <h4 class="text-lg font-semibold text-green-800">تم التحقق بنجاح</h4>
                    </div>
                    <p class="text-sm text-green-700">التوقيع صالح وموثق</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-medium mb-2">معلومات التحقق</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>رقم التوقيع:</span>
                                <span class="font-medium">${verification.id}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>الحالة:</span>
                                <span class="font-medium text-green-600">${verification.status}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>النتيجة:</span>
                                <span class="font-medium text-green-600">${verification.result}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-medium mb-2">التفاصيل</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>وقت التحقق:</span>
                                <span class="font-medium">${verification.verified_at}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>الموقع:</span>
                                <span class="font-medium">${verification.signer}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>الوثيقة:</span>
                                <span class="font-medium">${verification.document}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h5 class="font-medium mb-2 text-yellow-800">معلومات هامة</h5>
                    <div class="space-y-1 text-sm text-yellow-700">
                        <p>• التوقيع صالح للاستخدام الرسمي</p>
                        <p>• يمكن الاعتماد عليه في المعاملات الرسمية</p>
                        <p>• التحقق صالح لمدة 24 ساعة</p>
                        <p>• يمكن حفظ نتائج التحقق للمراجعة</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Show all signatures modal
function showAllSignaturesModal(signatures) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl p-6 max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold">جميع التوقيعات</h3>
                <button onclick="closeModal(this)" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                ${signatures.map(signature => `
                    <div class="border rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-${signature.color}-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-${signature.icon} text-${signature.color}-600 text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium">${signature.title}</h4>
                                    <p class="text-sm text-gray-500">${signature.id} • ${signature.signer}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-sm text-${signature.color}-600 font-medium">${signature.status}</span>
                                <p class="text-xs text-gray-500">${signature.verified}</p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">
                            <p>النوع: ${signature.type}</p>
                            <p>التاريخ: ${signature.time}</p>
                            <p>التشفير: ${signature.encryption}</p>
                        </div>
                    </div>
                `).join('')}
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

// File Upload Handlers
function initializeFileUploads() {
    // Signature Creation File Upload
    const fileUploadArea = document.getElementById('file-upload-area');
    const fileInput = document.getElementById('file-input');
    const uploadContent = document.getElementById('upload-content');
    const uploadProgress = document.getElementById('upload-progress');
    const progressBar = document.getElementById('progress-bar');
    const uploadResult = document.getElementById('upload-result');
    
    if (fileUploadArea && fileInput) {
        fileUploadArea.addEventListener('click', () => fileInput.click());
        
        fileInput.addEventListener('change', handleFileUpload);
        
        // Drag and drop
        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('border-teal-500', 'bg-teal-50');
        });
        
        fileUploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('border-teal-500', 'bg-teal-50');
        });
        
        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('border-teal-500', 'bg-teal-50');
            handleFileUpload({ target: { files: e.dataTransfer.files } });
        });
    }
    
    // Verification File Upload
    const verifyFileUploadArea = document.getElementById('verify-file-upload-area');
    const verifyFileInput = document.getElementById('verify-file-input');
    const verifyUploadContent = document.getElementById('verify-upload-content');
    const verifyUploadProgress = document.getElementById('verify-upload-progress');
    const verifyProgressBar = document.getElementById('verify-progress-bar');
    const verifyUploadResult = document.getElementById('verify-upload-result');
    
    if (verifyFileUploadArea && verifyFileInput) {
        verifyFileUploadArea.addEventListener('click', () => verifyFileInput.click());
        
        verifyFileInput.addEventListener('change', handleVerifyFileUpload);
        
        // Drag and drop
        verifyFileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            verifyFileUploadArea.classList.add('border-green-500', 'bg-green-50');
        });
        
        verifyFileUploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            verifyFileUploadArea.classList.remove('border-green-500', 'bg-green-50');
        });
        
        verifyFileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            verifyFileUploadArea.classList.remove('border-green-500', 'bg-green-50');
            handleVerifyFileUpload({ target: { files: e.dataTransfer.files } });
        });
    }
}

// Handle file upload for signature creation
async function handleFileUpload(event) {
    const files = event.target.files;
    const uploadContent = document.getElementById('upload-content');
    const uploadProgress = document.getElementById('upload-progress');
    const progressBar = document.getElementById('progress-bar');
    const uploadResult = document.getElementById('upload-result');
    
    if (files.length === 0) return;
    
    const file = files[0];
    const maxSize = 10 * 1024 * 1024; // 10MB
    
    // Validate file type
    const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    if (!allowedTypes.includes(file.type)) {
        showNotification('نوع الملف غير مدعول. يرجى اختيار PDF, DOC, أو DOCX', 'error');
        return;
    }
    
    // Validate file size
    if (file.size > maxSize) {
        showNotification('حجم الملف كبير جداً. الحد الأقصى هو 10MB', 'error');
        return;
    }
    
    // Show upload progress
    uploadContent.classList.add('hidden');
    uploadProgress.classList.remove('hidden');
    uploadResult.classList.add('hidden');
    
    // Simulate upload progress
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += Math.random() * 30;
        if (progress >= 100) {
            progress = 100;
            clearInterval(progressInterval);
            
            // Show success result
            uploadProgress.classList.add('hidden');
            uploadResult.classList.remove('hidden');
            uploadContent.classList.remove('hidden');
            
            // Update upload content to show file info
            uploadContent.innerHTML = `
                <i class="fas fa-file-alt text-3xl text-teal-600 mb-2"></i>
                <p class="text-sm text-gray-700 font-medium">${file.name}</p>
                <p class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
            `;
            
            // Store file reference for later use
            window.uploadedFile = file;
        }
        
        progressBar.style.width = progress + '%';
    }, 200);
}

// Handle file upload for verification
async function handleVerifyFileUpload(event) {
    const files = event.target.files;
    const verifyUploadContent = document.getElementById('verify-upload-content');
    const verifyUploadProgress = document.getElementById('verify-upload-progress');
    const verifyProgressBar = document.getElementById('verify-progress-bar');
    const verifyUploadResult = document.getElementById('verify-upload-result');
    
    if (files.length === 0) return;
    
    const file = files[0];
    const maxSize = 10 * 1024 * 1024; // 10MB
    
    // Validate file type
    const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    if (!allowedTypes.includes(file.type)) {
        showNotification('نوع الملف غير مدعول. يرجى اختيار PDF, DOC, أو DOCX', 'error');
        return;
    }
    
    // Validate file size
    if (file.size > maxSize) {
        showNotification('حجم الملف كبير جداً. الحد الأقصى هو 10MB', 'error');
        return;
    }
    
    // Show upload progress
    verifyUploadContent.classList.add('hidden');
    verifyUploadProgress.classList.remove('hidden');
    verifyUploadResult.classList.add('hidden');
    
    // Simulate upload progress
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += Math.random() * 30;
        if (progress >= 100) {
            progress = 100;
            clearInterval(progressInterval);
            
            // Show success result
            verifyUploadProgress.classList.add('hidden');
            verifyUploadResult.classList.remove('hidden');
            verifyUploadContent.classList.remove('hidden');
            
            // Update upload content to show file info
            verifyUploadContent.innerHTML = `
                <i class="fas fa-file-alt text-3xl text-green-600 mb-2"></i>
                <p class="text-sm text-gray-700 font-medium">${file.name}</p>
                <p class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
            `;
            
            // Store file reference for later use
            window.verifyUploadedFile = file;
        }
        
        verifyProgressBar.style.width = progress + '%';
    }, 200);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Start auto-refresh
    startSignaturesAutoRefresh();
    
    // Initialize file uploads
    initializeFileUploads();
    
    // Add click handlers for all buttons
    const createButton = document.querySelector('[data-action="create-signature"]');
    if (createButton) {
        createButton.addEventListener('click', createNewSignature);
    }
    
    const verifyButton = document.querySelector('[data-action="verify-signature"]');
    if (verifyButton) {
        verifyButton.addEventListener('click', verifySignature);
    }
    
    const viewAllButton = document.querySelector('[data-action="view-all"]');
    if (viewAllButton) {
        viewAllButton.addEventListener('click', viewAllSignatures);
    }
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (signaturesState.autoRefreshInterval) {
            clearInterval(signaturesState.autoRefreshInterval);
        }
    });
});
</script>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-teal-50 to-cyan-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-teal-500 to-cyan-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-pen-nib text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                                التوقيعات الإلكترونية
                            </h1>
                            <p class="text-gray-600 text-lg">إدارة التوقيعات الرقمية المعتمدة</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <button data-action="create-signature" class="bg-teal-600 text-white px-6 py-3 rounded-2xl hover:bg-teal-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-plus ml-2"></i>
                        توقيع جديد
                    </button>
                </div>
            </div>
        </div>

        <!-- Signature Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6 signatures-module">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-teal-500 to-cyan-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-signature text-white text-xl"></i>
                    </div>
                    <div class="text-xs text-gray-500">مباشر</div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900" data-signatures-active_signatures>{{ $signaturesData['active_signatures'] }}</h3>
                <p class="text-sm text-gray-600">توقيعات نشطة</p>
                <div class="mt-2 text-xs text-teal-600" data-signatures-monthly_growth>{{ $signaturesData['monthly_growth'] }} هذا الشهر</div>
            </div>
            
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6 signatures-module">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-shield-check text-white text-xl"></i>
                    </div>
                    <div class="text-xs text-gray-500">مباشر</div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900" data-signatures-verification_rate>{{ $signaturesData['verification_rate'] }}%</h3>
                <p class="text-sm text-gray-600">معدل التحقق</p>
                <div class="mt-2 text-xs text-green-600" data-signatures-verification_status>{{ $signaturesData['verification_status'] }}</div>
            </div>
            
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6 signatures-module">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-certificate text-white text-xl"></i>
                    </div>
                    <div class="text-xs text-gray-500">مباشر</div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900" data-signatures-issued_certificates>{{ $signaturesData['issued_certificates'] }}</h3>
                <p class="text-sm text-gray-600">شهادات صادرة</p>
                <div class="mt-2 text-xs text-purple-600" data-signatures-certificates_growth>{{ $signaturesData['certificates_growth'] }} هذا الشهر</div>
            </div>
            
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6 signatures-module">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-lock text-white text-xl"></i>
                    </div>
                    <div class="text-xs text-gray-500">مباشر</div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900" data-signatures-encryption_level>{{ $signaturesData['encryption_level'] }}</h3>
                <p class="text-sm text-gray-600">تشفير AES</p>
                <div class="mt-2 text-xs text-orange-600" data-signatures-security_status>{{ $signaturesData['security_status'] }}</div>
            </div>
        </div>

        <!-- Signature Services -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Digital Signature Creation -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-gradient-to-r from-teal-500 to-cyan-500 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-pen-alt text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">إنشاء التوقيع الرقمي</h2>
                        <p class="text-sm text-gray-600">توقيع إلكتروني معتمد وموثوق</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <label class="block text-sm font-medium text-gray-700 mb-2">اختر نوع التوقيع</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                            <option>توقيع بسيط</option>
                            <option>توقيع متقدم</option>
                            <option>توقيع مؤسسي</option>
                            <option>توقيع حكومي</option>
                        </select>
                    </div>
                    
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <label class="block text-sm font-medium text-gray-700 mb-2">رفع المستند</label>
                        <div id="file-upload-area" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-teal-500 transition-colors cursor-pointer relative">
                            <input type="file" id="file-input" class="hidden" accept=".pdf,.doc,.docx" multiple>
                            <div id="upload-content">
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600">اسحب وأفلت الملفات هنا أو انقر للاختيار</p>
                                <p class="text-xs text-gray-500 mt-1">PDF, DOC, DOCX (حتى 10MB)</p>
                            </div>
                            <div id="upload-progress" class="hidden">
                                <div class="mb-2">
                                    <div class="bg-teal-500 text-white text-xs px-2 py-1 rounded">جاري الرفع...</div>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div id="progress-bar" class="bg-teal-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                            </div>
                            <div id="upload-result" class="hidden">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-check-circle text-green-600"></i>
                                        <span class="text-sm text-green-800">تم رفع الملف بنجاح</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <label class="block text-sm font-medium text-gray-700 mb-2">معلومات التوقيع</label>
                        <input type="text" placeholder="الاسم الكامل" class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2 focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        <input type="email" placeholder="البريد الإلكتروني" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    </div>
                    
                    <button data-action="create-signature" class="w-full bg-teal-600 text-white px-6 py-3 rounded-2xl hover:bg-teal-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-pen-nib ml-2"></i>
                        إنشاء التوقيع
                    </button>
                </div>
            </div>
            
            <!-- Signature Verification -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-search text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">التحقق من التوقيع</h2>
                        <p class="text-sm text-gray-600">التحقق من صحة التوقيعات الرقمية</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <label class="block text-sm font-medium text-gray-700 mb-2">رقم التوقيع أو المعرف</label>
                        <input type="text" placeholder="أدخل رقم التوقيع" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <label class="block text-sm font-medium text-gray-700 mb-2">رفع المستند الموقع</label>
                        <div id="verify-file-upload-area" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-500 transition-colors cursor-pointer relative">
                            <input type="file" id="verify-file-input" class="hidden" accept=".pdf,.doc,.docx">
                            <div id="verify-upload-content">
                                <i class="fas fa-file-upload text-3xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600">اسحب وأفلت الملف الموقع</p>
                                <p class="text-xs text-gray-500 mt-1">PDF, DOC, DOCX</p>
                            </div>
                            <div id="verify-upload-progress" class="hidden">
                                <div class="mb-2">
                                    <div class="bg-green-500 text-white text-xs px-2 py-1 rounded">جاري التحقق...</div>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div id="verify-progress-bar" class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                            </div>
                            <div id="verify-upload-result" class="hidden">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-check-circle text-blue-600"></i>
                                        <span class="text-sm text-blue-800">تم تحميل الملف للتحقق</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                            <div>
                                <p class="text-sm text-blue-800 font-medium">معلومات التحقق</p>
                                <p class="text-xs text-blue-600 mt-1">سيتم التحقق من صحة التوقيع والشهادة والتاريخ</p>
                            </div>
                        </div>
                    </div>
                    
                    <button data-action="verify-signature" class="w-full bg-green-600 text-white px-6 py-3 rounded-2xl hover:bg-green-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-check-circle ml-2"></i>
                        التحقق من التوقيع
                    </button>
                </div>
            </div>
        </div>

        <!-- Recent Signatures -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">التوقيعات الحديثة</h2>
                <button data-action="view-all" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    عرض الكل
                </button>
            </div>
            
            <div class="space-y-4">
                @foreach($recentSignatures as $signature)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl dynamic-content">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-{{ $signature['color'] }}-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-{{ $signature['icon'] }} text-{{ $signature['color'] }}-600"></i>
                        </div>
                        <div>
                            <p class="font-medium">{{ $signature['title'] }}</p>
                            <p class="text-sm text-gray-500">{{ $signature['id'] }} • {{ $signature['signer'] }} • {{ $signature['time'] }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-sm text-{{ $signature['color'] }}-600 font-medium">{{ $signature['status'] }}</span>
                        <p class="text-xs text-gray-500">{{ $signature['verified'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
