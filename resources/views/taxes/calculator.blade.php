@extends('layouts.app')

@section('title', 'حاسبة الضرائب')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">حاسبة الضرائب</h1>
                <a href="{{ route('taxes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Calculator Form -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">حساب الضريبة</h5>
                </div>
                <div class="card-body">
                    <form id="taxCalculatorForm">
                        <div class="mb-3">
                            <label for="property_value" class="form-label">قيمة العقار</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="property_value" name="property_value" step="0.01" required>
                                <span class="input-group-text">ريال</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="property_type" class="form-label">نوع العقار</label>
                            <select class="form-select" id="property_type" name="property_type" required>
                                <option value="">اختر نوع العقار</option>
                                <option value="residential">سكني</option>
                                <option value="commercial">تجاري</option>
                                <option value="industrial">صناعي</option>
                                <option value="agricultural">زراعي</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">الموقع</label>
                            <select class="form-select" id="location" name="location" required>
                                <option value="">اختر الموقع</option>
                                <option value="riyadh">الرياض</option>
                                <option value="jeddah">جدة</option>
                                <option value="dammam">الدمام</option>
                                <option value="mecca">مكة المكرمة</option>
                                <option value="medina">المدينة المنورة</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="ownership_type" class="form-label">نوع الملكية</label>
                            <select class="form-select" id="ownership_type" name="ownership_type" required>
                                <option value="">اختر نوع الملكية</option>
                                <option value="primary_residence">سكن رئيسي</option>
                                <option value="investment">استثماري</option>
                                <option value="rental">إيجاري</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_senior_citizen" name="is_senior_citizen">
                                <label class="form-check-label" for="is_senior_citizen">
                                    مواطن كبير السن (أكبر من 60 سنة)
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_disabled" name="is_disabled">
                                <label class="form-check-label" for="is_disabled">
                                    شخص من ذوي الاحتياجات الخاصة
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-calculator"></i> حساب الضريبة
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Results -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">نتائج الحساب</h5>
                </div>
                <div class="card-body" id="calculationResults">
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-calculator fa-3x mb-3"></i>
                        <p>قم بملء البيانات وحساب الضريبة لعرض النتائج</p>
                    </div>
                </div>
            </div>

            <!-- Payment Schedule -->
            <div class="card mt-3" id="paymentScheduleCard" style="display: none;">
                <div class="card-header">
                    <h5 class="card-title mb-0">جدول السداد</h5>
                </div>
                <div class="card-body" id="paymentSchedule">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Calculators -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">حاسبات إضافية</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <a href="{{ route('taxes.calculator.capital-gains') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-chart-line"></i> حاسبة الأرباح الرأسمالية
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('taxes.calculator.vat') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-percentage"></i> حاسبة ضريبة القيمة المضافة
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('taxes.calculator.property') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-home"></i> حاسبة ضرائب العقارات
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#taxCalculatorForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("taxes.calculator.calculate") }}',
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                displayResults(response);
            },
            error: function(xhr) {
                alert('حدث خطأ في الحساب. يرجى المحاولة مرة أخرى.');
            }
        });
    });
    
    function displayResults(data) {
        var resultsHtml = `
            <div class="row">
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">قيمة العقار</label>
                        <h4>${data.property_value.toLocaleString()} ريال</h4>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">معدل الضريبة</label>
                        <h4>${data.tax_rate}%</h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">الضريبة الأساسية</label>
                        <h4>${data.base_tax.toLocaleString()} ريال</h4>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">إجمالي الإعفاءات</label>
                        <h4>${data.total_exemption.toLocaleString()} ريال</h4>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">المبلغ الخاضع للضريبة</label>
                        <h4>${data.taxable_amount.toLocaleString()} ريال</h4>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label text-primary">الضريبة النهائية</label>
                        <h4 class="text-primary">${data.final_tax.toLocaleString()} ريال</h4>
                    </div>
                </div>
            </div>
        `;
        
        $('#calculationResults').html(resultsHtml);
        
        // Display payment schedule
        displayPaymentSchedule(data.payment_schedule);
    }
    
    function displayPaymentSchedule(schedule) {
        var scheduleHtml = '<div class="list-group">';
        
        schedule.forEach(function(payment) {
            var statusBadge = payment.status === 'pending' ? 'bg-warning' : 'bg-success';
            scheduleHtml += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">القسط ${payment.installment}</h6>
                        <small class="text-muted">تاريخ الاستحقاق: ${payment.due_date}</small>
                    </div>
                    <div class="text-end">
                        <h6 class="mb-1">${payment.amount.toLocaleString()} ريال</h6>
                        <span class="badge ${statusBadge}">${payment.status === 'pending' ? 'معلق' : 'مدفوع'}</span>
                    </div>
                </div>
            `;
        });
        
        scheduleHtml += '</div>';
        $('#paymentSchedule').html(scheduleHtml);
        $('#paymentScheduleCard').show();
    }
});
</script>
@endpush
