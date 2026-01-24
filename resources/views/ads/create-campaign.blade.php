@extends('layouts.app')

@section('title', 'إنشاء حملة إعلانية جديدة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">إنشاء حملة إعلانية جديدة</h1>
                <a href="{{ route('ads.campaigns.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right"></i> العودة للحملات
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('ads.campaigns.store') }}">
                        @csrf

                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="card-title">المعلومات الأساسية</h5>
                                <hr>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">اسم الحملة</label>
                                    <input type="text" class="form-control" id="name" name="name" required
                                           value="{{ old('name') }}" maxlength="255">
                                    <small class="text-muted">اسم وصفي للحملة الإعلانية</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="objective" class="form-label">هدف الحملة</label>
                                    <select class="form-select" id="objective" name="objective" required>
                                        <option value="">اختر هدف الحملة</option>
                                        <option value="awareness">زيادة الوعي</option>
                                        <option value="traffic">زيادة الزيارات</option>
                                        <option value="conversions">زيادة التحويلات</option>
                                        <option value="engagement">زيادة التفاعل</option>
                                    </select>
                                    <small class="text-muted">الهدف الرئيسي للحملة</small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">وصف الحملة</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"
                                              maxlength="500" placeholder="صف الحملة الإعلانية...">{{ old('description') }}</textarea>
                                    <small class="text-muted">وصف مفصل للحملة وأهدافها</small>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="card-title">جدولة الحملة</h5>
                                <hr>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">تاريخ البدء</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required
                                           value="{{ old('start_date', now()->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">تاريخ الانتهاء</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required
                                           value="{{ old('end_date', now()->addDays(30)->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="target_audience_size" class="form-label">حجم الجمهور المستهدف</label>
                                    <input type="number" class="form-control" id="target_audience_size" name="target_audience_size" 
                                           min="1" value="{{ old('target_audience_size') }}">
                                    <small class="text-muted">عدد المستخدمين المستهدفين</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="estimated_reach" class="form-label">الوصول المقدر</label>
                                    <input type="number" class="form-control" id="estimated_reach" name="estimated_reach" 
                                           min="1" value="{{ old('estimated_reach') }}">
                                    <small class="text-muted">عدد المستخدمين المتوقع الوصول إليهم</small>
                                </div>
                            </div>
                        </div>

                        <!-- Budget -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="card-title">الميزانية</h5>
                                <hr>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="total_budget" class="form-label">الميزانية الإجمالية (ريال)</label>
                                    <input type="number" class="form-control" id="total_budget" name="total_budget" 
                                           step="0.01" min="10" required value="{{ old('total_budget', 1000) }}">
                                    <small class="text-muted">الميزانية الإجمالية للحملة</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="daily_budget" class="form-label">الميزانية اليومية (ريال)</label>
                                    <input type="number" class="form-control" id="daily_budget" name="daily_budget" 
                                           step="0.01" min="1" required value="{{ old('daily_budget', 50) }}">
                                    <small class="text-muted">الحد الأقصى للإنفاق اليومي</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="budget_type" class="form-label">نوع الميزانية</label>
                                    <select class="form-select" id="budget_type" name="budget_type">
                                        <option value="standard" selected>قياسي</option>
                                        <option value="accelerated">معجل</option>
                                        <option value="limited">محدود</option>
                                    </select>
                                    <small class="text-muted">طريقة إنفاق الميزانية</small>
                                </div>
                            </div>
                        </div>

                        <!-- Auto Renewal Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="card-title">إعدادات التجديد التلقائي</h5>
                                <hr>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="auto_renew" name="auto_renew" value="1">
                                    <label class="form-check-label" for="auto_renew">
                                        تفعيل التجديد التلقائي
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="renewal_amount" class="form-label">مبلغ التجديد (ريال)</label>
                                    <input type="number" class="form-control" id="renewal_amount" name="renewal_amount" 
                                           step="0.01" min="1" value="{{ old('renewal_amount') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="renewal_trigger" class="form-label">محفز التجديد</label>
                                    <select class="form-select" id="renewal_trigger" name="renewal_trigger">
                                        <option value="exhausted">عند نفاد الميزانية</option>
                                        <option value="below_threshold">عند الوصول للحد الأدنى</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="alert_threshold" class="form-label">عتبة التنبيه (%)</label>
                                    <input type="number" class="form-control" id="alert_threshold" name="alert_threshold" 
                                           min="1" max="100" step="0.1" value="{{ old('alert_threshold', 80) }}">
                                    <small class="text-muted">نسبة الاستهلاك لإرسال تنبيه</small>
                                </div>
                            </div>
                        </div>

                        <!-- Spending Limits -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="card-title">حدود الإنفاق</h5>
                                <hr>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="spending_limit" class="form-label">حد الإنفاق (ريال)</label>
                                    <input type="number" class="form-control" id="spending_limit" name="spending_limit" 
                                           step="0.01" min="1" value="{{ old('spending_limit') }}">
                                    <small class="text-muted">الحد الأقصى للإنفاق</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="limit_type" class="form-label">نوع الحد</label>
                                    <select class="form-select" id="limit_type" name="limit_type">
                                        <option value="">بدون حد</option>
                                        <option value="daily">يومي</option>
                                        <option value="weekly">أسبوعي</option>
                                        <option value="monthly">شهري</option>
                                        <option value="total">إجمالي</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="delivery_method" class="form-label">طريقة التسليم</label>
                                    <select class="form-select" id="delivery_method" name="delivery_method">
                                        <option value="standard" selected>قياسي</option>
                                        <option value="accelerated">معجل</option>
                                    </select>
                                    <small class="text-muted">سرعة عرض الإعلانات</small>
                                </div>
                            </div>
                        </div>

                        <!-- Campaign Summary -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">ملخص الحملة</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h5 id="duration-days" class="text-primary">30</h5>
                                                    <small class="text-muted">مدة الحملة (يوم)</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h5 id="total-budget-display" class="text-success">1000 ريال</h5>
                                                    <small class="text-muted">الميزانية الإجمالية</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h5 id="daily-budget-display" class="text-info">50 ريال</h5>
                                                    <small class="text-muted">الميزانية اليومية</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h5 id="estimated-impressions" class="text-warning">10,000</h5>
                                                    <small class="text-muted">الظهور المقدر</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('ads.campaigns.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> إلغاء
                                    </a>
                                    <div>
                                        <button type="submit" name="save_draft" value="1" class="btn btn-outline-primary me-2">
                                            <i class="fas fa-save"></i> حفظ كمسودة
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-rocket"></i> إنشاء الحملة
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update campaign summary when values change
    function updateSummary() {
        const startDate = new Date(document.getElementById('start_date').value);
        const endDate = new Date(document.getElementById('end_date').value);
        const totalBudget = parseFloat(document.getElementById('total_budget').value) || 0;
        const dailyBudget = parseFloat(document.getElementById('daily_budget').value) || 0;
        
        // Calculate duration
        if (startDate && endDate) {
            const diffTime = Math.abs(endDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            document.getElementById('duration-days').textContent = diffDays;
        }
        
        // Update budget display
        document.getElementById('total-budget-display').textContent = totalBudget.toLocaleString() + ' ريال';
        document.getElementById('daily-budget-display').textContent = dailyBudget.toLocaleString() + ' ريال';
        
        // Estimate impressions (rough calculation)
        const estimatedImpressions = Math.floor((totalBudget / 0.5) * 1000); // Assuming $0.5 CPM
        document.getElementById('estimated-impressions').textContent = estimatedImpressions.toLocaleString();
    }
    
    // Add event listeners
    document.getElementById('start_date').addEventListener('change', updateSummary);
    document.getElementById('end_date').addEventListener('change', updateSummary);
    document.getElementById('total_budget').addEventListener('input', updateSummary);
    document.getElementById('daily_budget').addEventListener('input', updateSummary);
    
    // Initial calculation
    updateSummary();
    
    // Validate end date is after start date
    document.getElementById('end_date').addEventListener('change', function() {
        const startDate = new Date(document.getElementById('start_date').value);
        const endDate = new Date(this.value);
        
        if (endDate <= startDate) {
            this.setCustomValidity('تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء');
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>
@endsection
