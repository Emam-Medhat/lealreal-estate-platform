@extends('layouts.app')

@section('title', 'إنشاء تقرير جديد')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-3">إنشاء تقرير جديد</h2>
                    <p class="text-muted">اختر نوع التقرير وحدد المعلمات المطلوبة</p>
                </div>
                <div>
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> العودة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('reports.store') }}" id="reportForm">
        @csrf
        <div class="row">
            <!-- Basic Information -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">المعلومات الأساسية</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="title" class="form-label">عنوان التقرير <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="template_id" class="form-label">قالب التقرير <span class="text-danger">*</span></label>
                            <select class="form-control @error('template_id') is-invalid @enderror" 
                                    id="template_id" name="template_id" required>
                                <option value="">اختر قالب...</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}" 
                                            {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                        {{ $template->name }} - {{ ucfirst($template->type) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('template_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="format" class="form-label">التنسيق <span class="text-danger">*</span></label>
                            <select class="form-control @error('format') is-invalid @enderror" 
                                    id="format" name="format" required>
                                <option value="pdf" {{ old('format') == 'pdf' ? 'selected' : '' }}>PDF</option>
                                <option value="excel" {{ old('format') == 'excel' ? 'selected' : '' }}>Excel</option>
                                <option value="csv" {{ old('format') == 'csv' ? 'selected' : '' }}>CSV</option>
                                <option value="html" {{ old('format') == 'html' ? 'selected' : '' }}>HTML</option>
                            </select>
                            @error('format')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Date Range -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">نطاق التاريخ</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="date_range_start" class="form-label">من تاريخ</label>
                            <input type="date" class="form-control @error('date_range.start') is-invalid @enderror" 
                                   id="date_range_start" name="date_range[start]" value="{{ old('date_range.start') }}">
                            @error('date_range.start')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="date_range_end" class="form-label">إلى تاريخ</label>
                            <input type="date" class="form-control @error('date_range.end') is-invalid @enderror" 
                                   id="date_range_end" name="date_range[end]" value="{{ old('date_range.end') }}">
                            @error('date_range.end')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="btn-group w-100" role="group">
                                <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('today')">اليوم</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('week')">هذا الأسبوع</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('month')">هذا الشهر</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('quarter')">هذا الربع</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('year')">هذا العام</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Advanced Options -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">خيارات متقدمة</h5>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="mb-4">
                            <h6>الفلاتر</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="property_type" class="form-label">نوع العقار</label>
                                        <select class="form-control" id="property_type" name="filters[property_type]">
                                            <option value="">الكل</option>
                                            <option value="apartment">شقة</option>
                                            <option value="house">منزل</option>
                                            <option value="villa">فيلا</option>
                                            <option value="land">أرض</option>
                                            <option value="commercial">تجاري</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="status" class="form-label">الحالة</label>
                                        <select class="form-control" id="status" name="filters[status]">
                                            <option value="">الكل</option>
                                            <option value="active">نشط</option>
                                            <option value="sold">مباع</option>
                                            <option value="pending">في الانتظار</option>
                                            <option value="inactive">غير نشط</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="min_price" class="form-label">الحد الأدنى للسعر</label>
                                        <input type="number" class="form-control" id="min_price" name="filters[min_price]" 
                                               placeholder="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="max_price" class="form-label">الحد الأقصى للسعر</label>
                                        <input type="number" class="form-control" id="max_price" name="filters[max_price]" 
                                               placeholder="0" step="0.01">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Parameters (Template-specific) -->
                        <div class="mb-4">
                            <h6>المعلمات</h6>
                            <div id="templateParameters">
                                <!-- Dynamic parameters will be loaded here based on selected template -->
                                <div class="text-muted">
                                    <i class="fas fa-info-circle"></i> اختر قالب لعرض المعلمات المتاحة
                                </div>
                            </div>
                        </div>

                        <!-- Additional Options -->
                        <div class="mb-4">
                            <h6>خيارات إضافية</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="include_charts" 
                                               name="parameters[include_charts]" value="1" checked>
                                        <label class="form-check-label" for="include_charts">
                                            تضمين الرسوم البيانية
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="include_details" 
                                               name="parameters[include_details]" value="1" checked>
                                        <label class="form-check-label" for="include_details">
                                            تضمين التفاصيل الكاملة
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="include_summary" 
                                               name="parameters[include_summary]" value="1" checked>
                                        <label class="form-check-label" for="include_summary">
                                            تضمين الملخص التنفيذي
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="include_recommendations" 
                                               name="parameters[include_recommendations]" value="1">
                                        <label class="form-check-label" for="include_recommendations">
                                            تضمين التوصيات
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="include_forecasts" 
                                               name="parameters[include_forecasts]" value="1">
                                        <label class="form-check-label" for="include_forecasts">
                                            تضمين التنبؤات
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="include_comparisons" 
                                               name="parameters[include_comparisons]" value="1">
                                        <label class="form-check-label" for="include_comparisons">
                                            تضمين المقارنات
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div class="mb-4">
                            <h6>معاينة التقرير</h6>
                            <div class="border rounded p-3 bg-light">
                                <div class="text-center">
                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">سيتم عرض معاينة التقرير في نافذة منبثقة</p>
                                    <button type="button" class="btn btn-outline-primary" onclick="previewReport()">
                                        <i class="fas fa-eye"></i> معاينة التقرير
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> إنشاء التقرير
                                </button>
                                <button type="button" class="btn btn-outline-secondary ms-2" onclick="saveAsDraft()">
                                    <i class="fas fa-save"></i> حفظ كمسودة
                                </button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-info" onclick="scheduleReport()">
                                    <i class="fas fa-calendar-alt"></i> جدولة التقرير
                                </button>
                                <a href="{{ route('reports.index') }}" class="btn btn-secondary ms-2">
                                    <i class="fas fa-times"></i> إلغاء
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .btn-group .btn {
        margin: 0 1px;
    }
    
    .form-check {
        margin-bottom: 0.5rem;
    }
    
    .border {
        border: 1px solid #dee2e6 !important;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
    
    /* Custom Modal Styles */
    .custom-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.85);
        z-index: 999999; /* Increased z-index */
        overflow-x: hidden;
        overflow-y: auto;
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }
    
    .custom-modal.show {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideIn {
        from { 
            transform: scale(0.8) translateY(-50px);
            opacity: 0;
        }
        to { 
            transform: scale(1) translateY(0);
            opacity: 1;
        }
    }
    
    .custom-modal-dialog {
        position: relative;
        width: 90%;
        max-width: 800px;
        margin: 0;
        animation: slideIn 0.3s ease-out;
        z-index: 100000;
    }
    
    .custom-modal-content {
        background-color: #fff;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        overflow: hidden;
        border: 2px solid rgba(255, 255, 255, 0.3);
        position: relative;
        z-index: 100001;
    }
    
    .custom-modal-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #dee2e6;
        background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
        color: white;
        border-radius: 16px 16px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        z-index: 100002;
    }
    
    .custom-modal-header h5 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }
    
    .custom-modal-body {
        padding: 2.5rem;
        max-height: 60vh;
        overflow-y: auto;
        background-color: #fff;
        position: relative;
        z-index: 100002;
    }
    
    .custom-modal-footer {
        padding: 1.5rem 2rem;
        border-top: 1px solid #dee2e6;
        background-color: #f8f9fa;
        border-radius: 0 0 16px 16px;
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        position: relative;
        z-index: 100002;
    }
    
    .spinner-border {
        display: inline-block;
        width: 4rem;
        height: 4rem;
        border: 0.4em solid rgba(13, 110, 253, 0.25);
        border-right-color: #0d6efd;
        border-radius: 50%;
        animation: spinner-border 1s linear infinite;
    }
    
    @keyframes spinner-border {
        to { transform: rotate(360deg); }
    }
    
    .text-center { text-align: center; }
    .mt-3 { margin-top: 1rem; }
    .mb-2 { margin-bottom: 0.5rem; }
    .me-2 { margin-right: 0.5rem; }
    .py-5 { padding-top: 3rem; padding-bottom: 3rem; }
    .btn-close { 
        background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath d='m.235.867 8.832 8.832L8.832 8.068.235.235 8.068 8.068 8.832 8.832 8.832 8.068 8.832-.235-.235-8.832-8.832z'/%3e%3c/svg%3e") center/1.25em auto no-repeat; 
        border: 0; 
        border-radius: 0.375rem; 
        opacity: 0.8; 
        width: 2em; 
        height: 2em;
        cursor: pointer;
        transition: all 0.2s;
        padding: 0.5rem;
    }
    
    .btn-close:hover {
        opacity: 1;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 0.5rem;
    }
    
    /* Ensure modal is completely on top */
    .custom-modal.show {
        backdrop-filter: blur(8px);
        isolation: isolate;
    }
    
    /* Prevent scrolling on body when modal is open */
    body.modal-open {
        overflow: hidden;
    }
    
    /* Responsive design */
    @media (max-width: 768px) {
        .custom-modal-dialog {
            width: 95%;
            margin: 1rem;
        }
        
        .custom-modal-body {
            padding: 2rem;
        }
        
        .custom-modal-header {
            padding: 1.25rem 1.5rem;
        }
        
        .custom-modal-footer {
            padding: 1.25rem 1.5rem;
            flex-direction: column;
        }
        
        .custom-modal-footer button {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }
</style>
@endpush

<script>
// Global error handler
window.addEventListener('error', function(event) {
    console.error('Global error:', event.error);
    showErrorNotification('حدث خطأ غير متوقع: ' + event.error.message);
});

// Global unhandled promise rejection handler
window.addEventListener('unhandledrejection', function(event) {
    console.error('Unhandled promise rejection:', event.reason);
    showErrorNotification('حدث خطأ في طلب البيانات: ' + event.reason.message);
});

// Function to show error notifications
function showErrorNotification(message) {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.error-notification');
    existingNotifications.forEach(notif => notif.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'error-notification';
    notification.innerHTML = `
        <div class="alert alert-danger alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 99999; min-width: 300px; max-width: 500px;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>خطأ:</strong> ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Function to show success notifications
function showSuccessNotification(message) {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.success-notification');
    existingNotifications.forEach(notif => notif.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'success-notification';
    notification.innerHTML = `
        <div class="alert alert-success alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 99999; min-width: 300px; max-width: 500px;">
            <i class="fas fa-check-circle me-2"></i>
            <strong>نجاح:</strong> ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 3000);
}

// Function to show info notifications
function showInfoNotification(message) {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.info-notification');
    existingNotifications.forEach(notif => notif.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'info-notification';
    notification.innerHTML = `
        <div class="alert alert-info alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 99999; min-width: 300px; max-width: 500px;">
            <i class="fas fa-info-circle me-2"></i>
            <strong>معلومات:</strong> ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 4 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 4000);
}

// Enhanced fetch wrapper with error handling
function safeFetch(url, options = {}) {
    return fetch(url, options)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error('Invalid JSON response from server');
                }
            });
        })
        .catch(error => {
            showErrorNotification('فشل الاتصال بالخادم: ' + error.message);
            throw error;
        });
}

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Load template parameters
    const templateSelect = document.getElementById('template_id');
    if (templateSelect) {
        templateSelect.addEventListener('change', function() {
            const templateId = this.value;
            const parametersDiv = document.getElementById('templateParameters');
            
            if (templateId) {
                showInfoNotification('جاري تحميل معلمات القالب...');
                
                safeFetch(`/reports/sales/templates/${templateId}/parameters`)
                    .then(data => {
                        if (data.success) {
                            parametersDiv.innerHTML = data.html;
                            showSuccessNotification('تم تحميل معلمات القالب بنجاح');
                        } else {
                            parametersDiv.innerHTML = '<div class="text-danger">فشل تحميل المعلمات</div>';
                            showErrorNotification('فشل تحميل المعلمات: ' + (data.message || 'خطأ غير معروف'));
                        }
                    })
                    .catch(error => {
                        parametersDiv.innerHTML = '<div class="text-warning">تعذر تحميل المعلمات. يرجى المحاولة مرة أخرى.</div>';
                    });
            } else {
                parametersDiv.innerHTML = '<div class="text-muted"><i class="fas fa-info-circle"></i> اختر قالب لعرض المعلمات المتاحة</div>';
            }
        });
    }
});

// Set date range
function setDateRange(range) {
    const startDate = document.getElementById('date_range_start');
    const endDate = document.getElementById('date_range_end');
    
    if (!startDate || !endDate) {
        console.error('Date range elements not found');
        return;
    }
    
    const today = new Date();
    
    switch(range) {
        case 'today':
            startDate.value = today.toISOString().split('T')[0];
            endDate.value = today.toISOString().split('T')[0];
            break;
        case 'week':
            const weekStart = new Date(today);
            weekStart.setDate(today.getDate() - today.getDay());
            const weekEnd = new Date(weekStart);
            weekEnd.setDate(weekStart.getDate() + 6);
            startDate.value = weekStart.toISOString().split('T')[0];
            endDate.value = weekEnd.toISOString().split('T')[0];
            break;
        case 'month':
            const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
            const monthEnd = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            startDate.value = monthStart.toISOString().split('T')[0];
            endDate.value = monthEnd.toISOString().split('T')[0];
            break;
        case 'quarter':
            const quarterStart = new Date(today.getFullYear(), Math.floor(today.getMonth() / 3) * 3, 1);
            const quarterEnd = new Date(today.getFullYear(), Math.floor(today.getMonth() / 3) * 3 + 3, 0);
            startDate.value = quarterStart.toISOString().split('T')[0];
            endDate.value = quarterEnd.toISOString().split('T')[0];
            break;
        case 'year':
            const yearStart = new Date(today.getFullYear(), 0, 1);
            const yearEnd = new Date(today.getFullYear(), 11, 31);
            startDate.value = yearStart.toISOString().split('T')[0];
            endDate.value = yearEnd.toISOString().split('T')[0];
            break;
    }
}

// Preview report
function previewReport() {
    const form = document.getElementById('reportForm');
    
    if (!form) {
        console.error('Form element not found');
        showErrorNotification('نموذج التقرير غير موجود');
        return;
    }

    // Check if template is selected
    const templateSelect = document.getElementById('template_id');
    if (!templateSelect || !templateSelect.value) {
        showErrorNotification('يرجى اختيار قالب التقرير أولاً من قسم "المعلومات الأساسية"');
        if (templateSelect) {
            templateSelect.focus();
            templateSelect.classList.add('is-invalid');
            setTimeout(() => templateSelect.classList.remove('is-invalid'), 3000);
        }
        return;
    }
    
    const templateId = templateSelect.value;
    console.log('Starting report preview for template:', templateId);
    
    // Create FormData from form
            const formData = new FormData(form);
            
            // Explicitly ensure template_id is in formData
            if (!formData.has('template_id')) {
                formData.append('template_id', templateId);
            }
    
    // Show loading modal
    showLoadingModal();
    
    safeFetch('{{ route('reports.sales.preview_data') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(data => {
        console.log('Preview data received:', data);
        hideLoadingModal();
        
        if (data.success) {
            showPreviewModal(data.data);
            showSuccessNotification('تم تحميل المعاينة بنجاح');
        } else {
            console.error('Server reported error:', data.message);
            if (data.debug) {
                console.log('Server debug info:', data.debug);
            }
            showErrorModal(data.message || 'فشل تحميل المعاينة');
        }
    })
    .catch(error => {
        console.error('Preview error:', error);
        hideLoadingModal();
        showErrorNotification('حدث خطأ أثناء تحميل المعاينة: ' + error.message);
    });
}

// Show loading modal
function showLoadingModal() {
    // Prevent body scrolling
    document.body.style.overflow = 'hidden';
    document.body.classList.add('modal-open');
    
    const modalHtml = `
        <div class="custom-modal show" id="previewModal" onclick="handleModalClick(event)">
            <div class="custom-modal-dialog" onclick="event.stopPropagation()">
                <div class="custom-modal-content">
                    <div class="custom-modal-body text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                        <h5 class="mt-3">جاري تحميل المعاينة...</h5>
                        <p class="text-muted">يرجى الانتظار لحظات</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('previewModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

// Hide loading modal
function hideLoadingModal() {
    const existingModal = document.getElementById('previewModal');
    if (existingModal) {
        existingModal.remove();
    }
}

// Show preview modal with data
function showPreviewModal(data) {
    console.log('showPreviewModal called with data:', data);
    
    // Prevent body scrolling
    document.body.style.overflow = 'hidden';
    document.body.classList.add('modal-open');
    
    // Handle empty data
    if (!data) {
        console.error('showPreviewModal: No data provided');
        showErrorModal('لا توجد بيانات للمعاينة');
        return;
    }
    
    try {
        const modalHtml = `
            <div class="custom-modal show" id="previewModal" onclick="handleModalClick(event)">
                <div class="custom-modal-dialog" onclick="event.stopPropagation()">
                    <div class="custom-modal-content">
                        <div class="custom-modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-eye me-2"></i>
                                معاينة التقرير
                            </h5>
                            <button type="button" class="btn-close" onclick="hideModal()"></button>
                        </div>
                        <div class="custom-modal-body">
                            <div class="row">
                                <!-- Report Information -->
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-info-circle text-primary me-2"></i>
                                                معلومات التقرير
                                            </h6>
                                            <div class="mb-2">
                                                <strong>العنوان:</strong> ${data.title || 'تقرير غير مسمى'}
                                            </div>
                                            <div class="mb-2">
                                                <strong>القالب:</strong> ${data.template_name || 'غير محدد'}
                                            </div>
                                            <div class="mb-2">
                                                <strong>النوع:</strong> 
                                                <span class="badge bg-info">${data.template_type || 'غير محدد'}</span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>التنسيق:</strong> 
                                                <span class="badge bg-primary">${(data.format || 'PDF').toUpperCase()}</span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>الحالة:</strong> 
                                                <span class="badge bg-success">جاهز للمعاينة</span>
                                            </div>
                                            ${data.date_range && data.date_range.start ? `
                                            <div class="mb-2">
                                                <strong>الفترة:</strong> ${formatDate(data.date_range.start)} - ${formatDate(data.date_range.end)}
                                            </div>
                                            ` : ''}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Statistics -->
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-chart-bar text-success me-2"></i>
                                                الإحصائيات المتوقعة
                                            </h6>
                                            <div class="mb-2">
                                                <strong>السجلات المتوقعة:</strong> 
                                                <span class="badge bg-info">${data.estimated_records || 'غير متاح'}</span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>وقت التنفيذ:</strong> 
                                                <span class="badge bg-warning">${data.estimated_time || 'غير متاح'}</span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>الحجم المتوقع:</strong> 
                                                <span class="badge bg-secondary">${data.estimated_size || 'غير متاح'}</span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>تاريخ الإنشاء:</strong> 
                                                <span class="text-muted">${data.generated_at ? new Date(data.generated_at).toLocaleString('ar-SA') : 'غير محدد'}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Filters Section -->
                            ${data.filters && Object.keys(data.filters).length > 0 ? `
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-filter text-warning me-2"></i>
                                                الفلاتر المطبقة
                                            </h6>
                                            <div class="row">
                                                ${data.filters.property_type ? `
                                                <div class="col-md-3 col-sm-6 mb-2">
                                                    <strong>نوع العقار:</strong> ${getFilterLabel('property_type', data.filters.property_type)}
                                                </div>
                                                ` : ''}
                                                ${data.filters.status ? `
                                                <div class="col-md-3 col-sm-6 mb-2">
                                                    <strong>الحالة:</strong> ${getFilterLabel('status', data.filters.status)}
                                                </div>
                                                ` : ''}
                                                ${data.filters.min_price ? `
                                                <div class="col-md-3 col-sm-6 mb-2">
                                                    <strong>الحد الأدنى للسعر:</strong> ${data.filters.min_price}
                                                </div>
                                                ` : ''}
                                                ${data.filters.max_price ? `
                                                <div class="col-md-3 col-sm-6 mb-2">
                                                    <strong>الحد الأقصى للسعر:</strong> ${data.filters.max_price}
                                                </div>
                                                ` : ''}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ` : ''}
                            
                            <!-- Notice -->
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-lightbulb me-2"></i>
                                <strong>ملاحظة:</strong> هذه معاينة تقديرية. قد تختلف البيانات الفعلية عند إنشاء التقرير النهائي.
                            </div>
                        </div>
                        <div class="custom-modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="hideModal()">
                                <i class="fas fa-times me-2"></i>
                                إغلاق
                            </button>
                            <button type="button" class="btn btn-primary" onclick="previewReport()">
                                <i class="fas fa-sync me-2"></i>
                                تحديث المعاينة
                            </button>
                            <button type="button" class="btn btn-success" onclick="proceedWithReport()">
                                <i class="fas fa-check me-2"></i>
                                متابعة إنشاء التقرير
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        console.log('Generated modal HTML, length:', modalHtml.length);
        
        // Remove existing modal if any
        const existingModal = document.getElementById('previewModal');
        if (existingModal) {
            console.log('Removing existing modal');
            existingModal.remove();
        }
        
        console.log('Inserting modal into body');
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        console.log('Modal inserted successfully');
    } catch (e) {
        console.error('Error in showPreviewModal:', e);
        showErrorModal('حدث خطأ أثناء عرض المعاينة: ' + e.message);
    }
}

// Show error modal
function showErrorModal(message) {
    // Prevent body scrolling
    document.body.style.overflow = 'hidden';
    document.body.classList.add('modal-open');
    
    const modalHtml = `
        <div class="custom-modal show" id="errorModal" onclick="handleModalClick(event)">
            <div class="custom-modal-dialog" onclick="event.stopPropagation()">
                <div class="custom-modal-content">
                    <div class="custom-modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            خطأ
                        </h5>
                        <button type="button" class="btn-close" onclick="hideErrorModal()"></button>
                    </div>
                    <div class="custom-modal-body text-center py-4">
                        <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                        <h5>حدث خطأ</h5>
                        <p class="text-muted">${message}</p>
                    </div>
                    <div class="custom-modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="hideErrorModal()">
                            إغلاق
                        </button>
                        <button type="button" class="btn btn-primary" onclick="previewReport()">
                            <i class="fas fa-sync me-2"></i>
                            إعادة المحاولة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing error modal if any
    const existingModal = document.getElementById('errorModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

// Hide error modal
function hideErrorModal() {
    // Restore body scrolling
    document.body.style.overflow = '';
    document.body.classList.remove('modal-open');
    
    const existingModal = document.getElementById('errorModal');
    if (existingModal) {
        existingModal.remove();
    }
}

// Helper function to format date
function formatDate(dateString) {
    try {
        return new Date(dateString).toLocaleDateString('ar-SA');
    } catch (e) {
        return dateString;
    }
}

// Helper function to get filter labels
function getFilterLabel(filterType, value) {
    const labels = {
        property_type: {
            'apartment': 'شقة',
            'house': 'منزل',
            'villa': 'فيلا',
            'land': 'أرض',
            'commercial': 'تجاري'
        },
        status: {
            'active': 'نشط',
            'sold': 'مباع',
            'pending': 'في الانتظار',
            'inactive': 'غير نشط'
        }
    };
    
    return labels[filterType]?.[value] || value;
}

// Hide modal
function hideModal() {
    // Restore body scrolling
    document.body.style.overflow = '';
    document.body.classList.remove('modal-open');
    
    const existingModal = document.getElementById('previewModal');
    if (existingModal) {
        existingModal.remove();
    }
}

// Handle modal click (close when clicking outside)
function handleModalClick(event) {
    if (event.target.id === 'previewModal') {
        hideModal();
    } else if (event.target.id === 'errorModal') {
        hideErrorModal();
    }
}

// Proceed with report creation
function proceedWithReport() {
    hideModal();
    
    const form = document.querySelector('form');
    if (!form) {
        console.error('Form element not found');
        return;
    }
    
    // Scroll to form and highlight submit button
    form.scrollIntoView({ behavior: 'smooth' });
    const submitBtn = document.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.style.backgroundColor = '#198754';
        submitBtn.innerHTML = '<i class="fas fa-check me-2"></i> جاهز للإنشاء';
        setTimeout(() => {
            submitBtn.style.backgroundColor = '';
            submitBtn.innerHTML = '<i class="fas fa-save"></i> إنشاء التقرير';
        }, 3000);
    }
}

// Save as draft
function saveAsDraft() {
    const form = document.querySelector('form');
    const formData = new FormData(form);
    formData.append('save_as_draft', '1');
    
    fetch('/reports/save-draft', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('تم حفظ المسودة بنجاح');
            window.location.href = '/reports/drafts';
        } else {
            alert('فشل حفظ المسودة');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء حفظ المسودة');
    });
}

// Schedule report
function scheduleReport() {
    alert('وظيفة الجدولة قيد التطوير');
}

// Auto-save draft
setInterval(() => {
    const title = document.getElementById('title').value;
    if (title) {
        localStorage.setItem('report_draft', JSON.stringify({
            title: title,
            description: document.getElementById('description').value,
            template_id: document.getElementById('template_id').value,
            timestamp: new Date().toISOString()
        }));
    }
}, 30000);

// Load draft on page load
window.addEventListener('load', () => {
    const draft = localStorage.getItem('report_draft');
    if (draft) {
        const draftData = JSON.parse(draft);
        if (confirm(`هل تريد استعادة المسودة المحفوظة (${new Date(draftData.timestamp).toLocaleString()})؟`)) {
            document.getElementById('title').value = draftData.title;
            document.getElementById('description').value = draftData.description;
            document.getElementById('template_id').value = draftData.template_id;
        }
    }
});
</script>
