@extends('layouts.app')

@section('title', 'إنشاء تقرير جديد')

@section('content')
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

    <form method="POST" action="{{ route('reports.store') }}">
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
                                        {{ $template->name }} - {{ $template->getTypeLabel() }}
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
                                    <p class="text-muted">سيتم عرض معاينة التقرير هنا بعد تحديد جميع المعلمات</p>
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
</style>
@endpush

@push('scripts')
<script>
// Load template parameters
document.getElementById('template_id').addEventListener('change', function() {
    const templateId = this.value;
    const parametersDiv = document.getElementById('templateParameters');
    
    if (templateId) {
        fetch(`/reports/templates/${templateId}/parameters`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    parametersDiv.innerHTML = data.html;
                } else {
                    parametersDiv.innerHTML = '<div class="text-danger">فشل تحميل المعلمات</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                parametersDiv.innerHTML = '<div class="text-danger">حدث خطأ أثناء تحميل المعلمات</div>';
            });
    } else {
        parametersDiv.innerHTML = '<div class="text-muted"><i class="fas fa-info-circle"></i> اختر قالب لعرض المعلمات المتاحة</div>';
    }
});

// Set date range
function setDateRange(range) {
    const startDate = document.getElementById('date_range_start');
    const endDate = document.getElementById('date_range_end');
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
    const form = document.querySelector('form');
    const formData = new FormData(form);
    
    fetch('/reports/preview', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update preview area
            const previewArea = document.querySelector('.border .text-center');
            previewArea.innerHTML = `
                <h6>معاينة التقرير</h6>
                <div class="text-start">
                    <p><strong>العنوان:</strong> ${formData.get('title')}</p>
                    <p><strong>القالب:</strong> ${document.getElementById('template_id').options[document.getElementById('template_id').selectedIndex].text}</p>
                    <p><strong>التنسيق:</strong> ${formData.get('format').toUpperCase()}</p>
                    <p><strong>السجلات المتوقعة:</strong> ${data.estimated_records || 'غير متاح'}</p>
                    <p><strong>وقت التنفيذ:</strong> ${data.estimated_time || 'غير متاح'}</p>
                </div>
            `;
        } else {
            alert('فشل تحميل المعاينة');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء تحميل المعاينة');
    });
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
    // Open modal for scheduling
    const modal = document.createElement('div');
    modal.innerHTML = `
        <div class="modal fade" id="scheduleModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">جدولة التقرير</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="schedule_frequency" class="form-label">التكرار</label>
                            <select class="form-control" id="schedule_frequency">
                                <option value="daily">يومياً</option>
                                <option value="weekly">أسبوعياً</option>
                                <option value="monthly">شهرياً</option>
                                <option value="quarterly">ربع سنوياً</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="schedule_time" class="form-label">الوقت</label>
                            <input type="time" class="form-control" id="schedule_time" value="09:00">
                        </div>
                        <div class="form-group mb-3">
                            <label for="schedule_recipients" class="form-label">المستلمون</label>
                            <textarea class="form-control" id="schedule_recipients" rows="3" placeholder="أدخل عناوين البريد الإلكتروني مفصولة بفواصل"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="button" class="btn btn-primary" onclick="confirmSchedule()">جدولة</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const modalInstance = new bootstrap.Modal(document.getElementById('scheduleModal'));
    modalInstance.show();
}

function confirmSchedule() {
    // Implement scheduling logic
    alert('تم جدولة التقرير بنجاح');
    bootstrap.Modal.getInstance(document.getElementById('scheduleModal')).hide();
}

// Auto-save draft
setInterval(() => {
    const title = document.getElementById('title').value;
    if (title) {
        // Auto-save logic
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
@endpush
