@extends('layouts.app')

@section('title', 'إنشاء تقرير مالي - Real Estate Pro')

@section('content')
<div style="background: #f8f9fa; min-height: 100vh; padding: 20px 0;">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 style="color: #2c3e50; font-size: 2.5rem; font-weight: 300; margin-bottom: 10px;">إنشاء تقرير مالي جديد</h1>
            <p style="color: #7f8c8d; font-size: 1.1rem;">إنشاء تقرير مالي شامل لتحليل الأداء المالي والعائدات</p>
            <div class="mt-3">
                <a href="{{ route('reports.financial.index') }}" class="btn btn-outline-secondary" style="border-radius: 10px;">
                    <i class="fas fa-arrow-right"></i> العودة للتقارير
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <!-- Main Form Card -->
                <div class="card mb-4" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <h5 style="color: #2c3e50; font-size: 1.3rem; font-weight: 500; margin-bottom: 20px;">
                            <i class="fas fa-file-invoice-dollar me-2" style="color: #3498db;"></i>
                            معلومات التقرير الأساسية
                        </h5>
                        
                        <form method="POST" action="{{ route('reports.financial.store') }}">
                            @csrf
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="title" style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">
                                        <i class="fas fa-heading me-1" style="color: #3498db;"></i>
                                        عنوان التقرير
                                    </label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="{{ old('title') }}" placeholder="أدخل عنوان التقرير" required
                                           style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;">
                                    @error('title')
                                        <div style="color: #e74c3c; font-size: 0.875rem; margin-top: 5px;">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="report_type" style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">
                                        <i class="fas fa-chart-line me-1" style="color: #3498db;"></i>
                                        نوع التقرير
                                    </label>
                                    <select class="form-select" id="report_type" name="report_type" required
                                            style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;">
                                        <option value="">اختر نوع التقرير</option>
                                        <option value="income_statement" {{ old('report_type') == 'income_statement' ? 'selected' : '' }}>
                                            بيان الدخل
                                        </option>
                                        <option value="balance_sheet" {{ old('report_type') == 'balance_sheet' ? 'selected' : '' }}>
                                            الميزانية العمومية
                                        </option>
                                        <option value="cash_flow" {{ old('report_type') == 'cash_flow' ? 'selected' : '' }}>
                                            التدفق النقدي
                                        </option>
                                        <option value="profit_loss" {{ old('report_type') == 'profit_loss' ? 'selected' : '' }}>
                                            بيان الربح والخسارة
                                        </option>
                                        <option value="revenue_analysis" {{ old('report_type') == 'revenue_analysis' ? 'selected' : '' }}>
                                            تحليل الإيرادات
                                        </option>
                                    </select>
                                    @error('report_type')
                                        <div style="color: #e74c3c; font-size: 0.875rem; margin-top: 5px;">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="date_range_start" style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">
                                        <i class="fas fa-calendar-alt me-1" style="color: #3498db;"></i>
                                        تاريخ البداية
                                    </label>
                                    <input type="date" class="form-control" id="date_range_start" name="date_range[start]" 
                                           value="{{ old('date_range.start') }}" required
                                           style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;">
                                    @error('date_range.start')
                                        <div style="color: #e74c3c; font-size: 0.875rem; margin-top: 5px;">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="date_range_end" style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">
                                        <i class="fas fa-calendar-check me-1" style="color: #3498db;"></i>
                                        تاريخ النهاية
                                    </label>
                                    <input type="date" class="form-control" id="date_range_end" name="date_range[end]" 
                                           value="{{ old('date_range.end') }}" required
                                           style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;">
                                    @error('date_range.end')
                                        <div style="color: #e74c3c; font-size: 0.875rem; margin-top: 5px;">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4">
                                <label for="description" style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">
                                    <i class="fas fa-align-left me-1" style="color: #3498db;"></i>
                                    وصف التقرير
                                </label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="4" placeholder="أدخل وصفاً مفصلاً للتقرير" style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;">{{ old('description') }}</textarea>
                                @error('description')
                                    <div style="color: #e74c3c; font-size: 0.875rem; margin-top: 5px;">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Additional Options -->
                            <div class="row g-3 mt-3">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="include_charts" name="include_charts" value="1" {{ old('include_charts') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="include_charts" style="color: #2c3e50; font-weight: 500;">
                                            <i class="fas fa-chart-pie me-1" style="color: #3498db;"></i>
                                            تضمين الرسوم البيانية
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="include_details" name="include_details" value="1" {{ old('include_details') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="include_details" style="color: #2c3e50; font-weight: 500;">
                                            <i class="fas fa-list me-1" style="color: #3498db;"></i>
                                            تضمين التفاصيل
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="format" style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">
                                        <i class="fas fa-file-export me-1" style="color: #3498db;"></i>
                                        تنسيق التقرير
                                    </label>
                                    <select class="form-select" id="format" name="format" required
                                            style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;">
                                        <option value="pdf" {{ old('format') == 'pdf' ? 'selected' : '' }}>PDF</option>
                                        <option value="excel" {{ old('format') == 'excel' ? 'selected' : '' }}>Excel</option>
                                        <option value="csv" {{ old('format') == 'csv' ? 'selected' : '' }}>CSV</option>
                                    </select>
                                    @error('format')
                                        <div style="color: #e74c3c; font-size: 0.875rem; margin-top: 5px;">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="resetForm()" style="border-radius: 10px;">
                                    <i class="fas fa-redo me-2"></i>
                                    إعادة تعيين
                                </button>
                                <button type="submit" class="btn" style="background: #3498db; color: white; border: none; border-radius: 10px; padding: 12px 30px; font-weight: 500;">
                                    <i class="fas fa-save me-2"></i>
                                    إنشاء التقرير
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Info Card -->
                <div class="card mb-4" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <h5 style="color: #2c3e50; font-size: 1.2rem; font-weight: 500; margin-bottom: 20px;">
                            <i class="fas fa-info-circle me-2" style="color: #3498db;"></i>
                            معلومات سريعة
                        </h5>
                        
                        <div class="alert alert-info" style="border: none; border-radius: 10px; background: #e3f2fd; color: #1976d2;">
                            <h6 style="font-weight: 500; margin-bottom: 15px;">
                                <i class="fas fa-lightbulb me-2"></i>
                                نصائح هامة
                            </h6>
                            <ul style="font-size: 0.9rem; margin-bottom: 0; padding-right: 20px;">
                                <li style="margin-bottom: 8px;">اختر نوع التقرير المناسب</li>
                                <li style="margin-bottom: 8px;">حدد نطاق التاريخ الصحيح</li>
                                <li style="margin-bottom: 8px;">اختر التنسيق المناسب للتصدير</li>
                                <li>يمكنك تضمين الرسوم البيانية</li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <label for="status" style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">
                                <i class="fas fa-flag me-1" style="color: #3498db;"></i>
                                حالة التقرير
                            </label>
                            <select class="form-select" name="status" style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;">
                                <option value="draft">مسودة</option>
                                <option value="completed">مكتمل</option>
                                <option value="archived">مؤرشف</option>
                            </select>
                        </div>

                        <div class="text-center">
                            <button type="button" class="btn btn-outline-secondary w-100" onclick="resetForm()" style="border-radius: 10px;">
                                <i class="fas fa-redo me-2"></i>
                                إعادة تعيين النموذج
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Report Types Card -->
                <div class="card" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <h5 style="color: #2c3e50; font-size: 1.2rem; font-weight: 500; margin-bottom: 20px;">
                            <i class="fas fa-chart-pie me-2" style="color: #3498db;"></i>
                            أنواع التقارير
                        </h5>
                        
                        <div class="list-group" style="border-radius: 10px; overflow: hidden;">
                            <div class="list-group-item d-flex justify-content-between align-items-center" style="border: none; border-bottom: 1px solid #f0f0f0; padding: 12px 15px;">
                                <div>
                                    <i class="fas fa-file-invoice-dollar me-2" style="color: #27ae60;"></i>
                                    <span style="color: #2c3e50;">بيان الدخل</span>
                                </div>
                                <small style="color: #7f8c8d;">موصى به</small>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center" style="border: none; border-bottom: 1px solid #f0f0f0; padding: 12px 15px;">
                                <div>
                                    <i class="fas fa-balance-scale me-2" style="color: #3498db;"></i>
                                    <span style="color: #2c3e50;">الميزانية العمومية</span>
                                </div>
                                <small style="color: #7f8c8d;">شامل</small>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center" style="border: none; border-bottom: 1px solid #f0f0f0; padding: 12px 15px;">
                                <div>
                                    <i class="fas fa-water me-2" style="color: #3498db;"></i>
                                    <span style="color: #2c3e50;">التدفق النقدي</span>
                                </div>
                                <small style="color: #7f8c8d;">تفصيلي</small>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center" style="border: none; border-bottom: 1px solid #f0f0f0; padding: 12px 15px;">
                                <div>
                                    <i class="fas fa-chart-line me-2" style="color: #f39c12;"></i>
                                    <span style="color: #2c3e50;">الربح والخسارة</span>
                                </div>
                                <small style="color: #7f8c8d;">تحليلي</small>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center" style="border: none; padding: 12px 15px;">
                                <div>
                                    <i class="fas fa-coins me-2" style="color: #27ae60;"></i>
                                    <span style="color: #2c3e50;">تحليل الإيرادات</span>
                                </div>
                                <small style="color: #7f8c8d;">مخصص</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.querySelector('form').reset();
    // Set default dates again
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    document.getElementById('date_range_start').value = firstDay.toISOString().split('T')[0];
    document.getElementById('date_range_end').value = lastDay.toISOString().split('T')[0];
}
</script>
@endsection

@push('scripts')
<script>
// Set default dates
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth(), 1, 0);
    
    document.getElementById('date_range_start').value = firstDay.toISOString().split('T')[0];
    document.getElementById('date_range_end').value = lastDay.toISOString().split('T')[0];
    
    // Add validation for date range
    document.getElementById('date_range_start').addEventListener('change', validateDateRange);
    document.getElementById('date_range_end').addEventListener('change', validateDateRange);
    
    // Add input animations
    const inputs = document.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.borderColor = '#3498db';
            this.style.boxShadow = '0 0 0 0.2rem rgba(52, 152, 219, 0.25)';
        });
        input.addEventListener('blur', function() {
            this.style.borderColor = '#e0e0e0';
            this.style.boxShadow = 'none';
        });
    });
});

function validateDateRange() {
    const startDate = new Date(document.getElementById('date_range_start').value);
    const endDate = new Date(document.getElementById('date_range_end').value);
    
    if (startDate > endDate) {
        document.getElementById('date_range_end').value = document.getElementById('date_range_start').value;
    }
}

// Add form submission animation
document.querySelector('form').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري إنشاء التقرير...';
    submitBtn.disabled = true;
    submitBtn.style.background = '#95a5a6';
});

// Add hover effects for cards
document.querySelectorAll('.card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-2px)';
        this.style.transition = 'all 0.3s ease';
    });
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});

// Add button hover effects
document.querySelectorAll('.btn').forEach(btn => {
    btn.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-1px)';
        this.style.transition = 'all 0.2s ease';
    });
    btn.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});
</script>
@endpush
