@extends('layouts.app')

@section('title', 'المدفوعات الضريبية')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">المدفوعات الضريبية</h1>
                <div class="btn-group">
                    <a href="{{ route('taxes.payments.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> دفعة جديدة
                    </a>
                    <a href="{{ route('taxes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i> العودة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ number_format($totalCollected, 2) }}</h4>
                            <p class="card-text">إجمالي المحصل</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ number_format($pendingPayments, 2) }}</h4>
                            <p class="card-text">المدفوعات المعلقة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ number_format($processingPayments, 2) }}</h4>
                            <p class="card-text">قيد المعالجة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-cog fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $paymentsCount }}</h4>
                            <p class="card-text">إجمالي المعاملات</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-list fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('taxes.payments.index') }}">
                        <div class="row">
                            <div class="col-md-2">
                                <label for="status" class="form-label">الحالة</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">جميع الحالات</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="payment_method" class="form-label">طريقة الدفع</label>
                                <select class="form-select" id="payment_method" name="payment_method">
                                    <option value="">جميع الطرق</option>
                                    <option value="cash">نقدي</option>
                                    <option value="bank_transfer">تحويل بنكي</option>
                                    <option value="credit_card">بطاقة ائتمان</option>
                                    <option value="online">إلكتروني</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">من تاريخ</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" 
                                       value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">إلى تاريخ</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" 
                                       value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                                <button type="button" class="btn btn-success me-2" onclick="exportPayments()">
                                    <i class="fas fa-download"></i> تصدير
                                </button>
                                <a href="{{ route('taxes.payments.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> إعادة تعيين
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة المدفوعات</h5>
                </div>
                <div class="card-body">
                    @if($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>رقم المعاملة</th>
                                        <th>العقار</th>
                                        <th>المبلغ</th>
                                        <th>طريقة الدفع</th>
                                        <th>الحالة</th>
                                        <th>تاريخ الدفع</th>
                                        <th>رقم المرجع</th>
                                        <th>إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                    <tr>
                                        <td>#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</td>
                                        <td>{{ $payment->propertyTax->property->title ?? 'N/A' }}</td>
                                        <td>{{ number_format($payment->amount, 2) }} ريال</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $payment->payment_method === 'cash' ? 'نقدي' :
                                                   ($payment->payment_method === 'bank_transfer' ? 'تحويل بنكي' :
                                                   ($payment->payment_method === 'credit_card' ? 'بطاقة ائتمان' : 'إلكتروني')) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($payment->status === 'pending')
                                                <span class="badge bg-warning">معلق</span>
                                            @elseif($payment->status === 'processing')
                                                <span class="badge bg-info">قيد المعالجة</span>
                                            @elseif($payment->status === 'completed')
                                                <span class="badge bg-success">مكتمل</span>
                                            @elseif($payment->status === 'cancelled')
                                                <span class="badge bg-danger">ملغي</span>
                                            @endif
                                        </td>
                                        <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                                        <td>{{ $payment->reference_number ?? '-' }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('taxes.payments.show', $payment) }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($payment->canBeProcessed())
                                                    <button type="button" class="btn btn-outline-info" 
                                                            onclick="processPayment({{ $payment->id }})">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                @endif
                                                @if($payment->canBeCancelled())
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            onclick="cancelPayment({{ $payment->id }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                                @if($payment->receipt_path)
                                                    <a href="{{ route('taxes.payments.receipt', $payment) }}" class="btn btn-outline-success">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $payments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد مدفوعات</h5>
                            <p class="text-muted">لم يتم العثور على أي مدفوعات ضريبية</p>
                            <a href="{{ route('taxes.payments.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> إنشاء دفعة جديدة
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function processPayment(paymentId) {
    const transactionId = prompt('أدخل رقم المعاملة:');
    if (transactionId) {
        $.ajax({
            url: `/taxes/payments/${paymentId}/process`,
            method: 'POST',
            data: {
                transaction_id: transactionId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                alert('حدث خطأ. يرجى المحاولة مرة أخرى.');
            }
        });
    }
}

function cancelPayment(paymentId) {
    if (confirm('هل أنت متأكد من إلغاء هذه الدفعة؟')) {
        $.ajax({
            url: `/taxes/payments/${paymentId}/cancel`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                alert('حدث خطأ. يرجى المحاولة مرة أخرى.');
            }
        });
    }
}

function exportPayments() {
    const url = new URL(window.location.href);
    url.searchParams.set('export', '1');
    window.open(url.toString(), '_blank');
}
</script>
@endpush
