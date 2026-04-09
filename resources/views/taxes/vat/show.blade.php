@extends('layouts.app')

@section('title', 'تفاصيل سجل ضريبة القيمة المضافة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">تفاصيل سجل ضريبة القيمة المضافة #{{ $vatRecord->id }}</h1>
                <a href="{{ route('taxes.vat.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right me-2"></i>العودة للسجلات
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Information -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">معلومات السجل</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">رقم السجل</label>
                                <p class="form-control-plaintext fw-bold">#{{ $vatRecord->id }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">الفترة</label>
                                <p class="form-control-plaintext">{{ $vatRecord->period }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">نسبة ضريبة القيمة المضافة</label>
                                <p class="form-control-plaintext">{{ $vatRecord->vat_rate }}%</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">الحالة</label>
                                <p>
                                    @switch($vatRecord->status)
                                        @case('pending')
                                            <span class="badge bg-warning">معلق</span>
                                            @break
                                        @case('payable')
                                            <span class="badge bg-danger">قابل للدفع</span>
                                            @break
                                        @case('refundable')
                                            <span class="badge bg-success">قابل للاسترداد</span>
                                            @break
                                        @case('submitted')
                                            <span class="badge bg-info">مقدم</span>
                                            @break
                                        @case('paid')
                                            <span class="badge bg-success">مدفوع</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $vatRecord->status }}</span>
                                    @endswitch
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">تاريخ الإنشاء</label>
                                <p class="form-control-plaintext">{{ $vatRecord->created_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">المستخدم</label>
                                <p class="form-control-plaintext">{{ $vatRecord->user->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    @if($vatRecord->submitted_at)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">تاريخ التقديم</label>
                                    <p class="form-control-plaintext">{{ $vatRecord->submitted_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($vatRecord->paid_at)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">تاريخ الدفع</label>
                                    <p class="form-control-plaintext">{{ $vatRecord->paid_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">طريقة الدفع</label>
                                    <p class="form-control-plaintext">{{ $vatRecord->payment_method ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($vatRecord->reference_number)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">الرقم المرجعي</label>
                                    <p class="form-control-plaintext">{{ $vatRecord->reference_number }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">ملخص الحسابات</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">المبيعات الخاضعة للضريبة:</span>
                            <span class="fw-bold">{{ number_format($vatRecord->taxable_sales, 2) }} ريال</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">ضريبة القيمة المضافة المحصلة:</span>
                            <span class="fw-bold text-success">{{ number_format($vatRecord->vat_collected, 2) }} ريال</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">المشتريات الخاضعة للضريبة:</span>
                            <span class="fw-bold">{{ number_format($vatRecord->taxable_purchases, 2) }} ريال</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">ضريبة القيمة المضافة المدفوعة:</span>
                            <span class="fw-bold text-danger">{{ number_format($vatRecord->vat_paid, 2) }} ريال</span>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-bold">ضريبة القيمة المضافة المستحقة:</span>
                            <span class="fw-bold {{ $vatRecord->vat_payable > 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format(abs($vatRecord->vat_payable), 2) }} ريال
                            </span>
                        </div>
                        @if($vatRecord->vat_payable > 0)
                            <small class="text-danger">(مستحق الدفع)</small>
                        @elseif($vatRecord->vat_payable < 0)
                            <small class="text-success">(مسترد)</small>
                        @else
                            <small class="text-info">(متوازن)</small>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">الإجراءات</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($vatRecord->status === 'pending' || $vatRecord->status === 'payable' || $vatRecord->status === 'refundable')
                            <form action="{{ route('taxes.vat.submit', $vatRecord) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('هل أنت متأكد من تقديم هذا الإقرار؟')">
                                    <i class="fas fa-check me-2"></i>تقديم الإقرار
                                </button>
                            </form>
                        @endif

                        @if($vatRecord->status === 'submitted' && $vatRecord->vat_payable > 0)
                            <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                <i class="fas fa-money-bill me-2"></i>تسديد المبلغ
                            </button>
                        @endif

                        <a href="{{ route('taxes.vat.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-list me-2"></i>جميع السجلات
                        </a>

                        <a href="{{ route('taxes.vat.create') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus me-2"></i>إضافة سجل جديد
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
@if($vatRecord->status === 'submitted' && $vatRecord->vat_payable > 0)
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">سداد ضريبة القيمة المضافة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('taxes.vat.pay', $vatRecord) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">رقم السجل</label>
                            <input type="text" class="form-control" value="#{{ $vatRecord->id }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">المبلغ المستحق</label>
                            <input type="text" class="form-control" value="{{ number_format($vatRecord->vat_payable, 2) }} ريال" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">طريقة الدفع</label>
                            <select name="payment_method" id="payment_method" class="form-select" required>
                                <option value="">اختر طريقة الدفع</option>
                                <option value="bank_transfer">تحويل بنكي</option>
                                <option value="credit_card">بطاقة ائتمان</option>
                                <option value="cash">نقدي</option>
                                <option value="check">شيك</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="reference_number" class="form-label">الرقم المرجعي (اختياري)</label>
                            <input type="text" name="reference_number" id="reference_number" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">تأكيد الدفع</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection
