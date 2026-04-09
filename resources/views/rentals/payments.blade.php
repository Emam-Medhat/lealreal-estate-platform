@extends('layouts.app')

@section('title', 'المدفوعات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">المدفوعات</h1>
                <div class="btn-group">
                    <a href="{{ route('rentals.payments.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> تسجيل دفعة
                    </a>
                    <a href="{{ route('rentals.payments.export') }}" class="btn btn-success">
                        <i class="fas fa-download"></i> تصدير
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">المدفوعات</h5>
                    <h2>{{ number_format(App\Models\RentPayment::where('status', 'paid')->sum('amount'), 2) }}</h2>
                    <small class="text-white-50">ريال</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">المعلقة</h5>
                    <h2>{{ App\Models\RentPayment::where('status', 'pending')->count() }}</h2>
                    <small class="text-white-50">دفعة</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">المتأخرة</h5>
                    <h2>{{ App\Models\RentPayment::where('status', 'overdue')->count() }}</h2>
                    <small class="text-white-50">دفعة</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">هذا الشهر</h5>
                    <h2>{{ number_format(App\Models\RentPayment::whereMonth('payment_date', now()->month)->where('status', 'paid')->sum('amount'), 2) }}</h2>
                    <small class="text-white-50">ريال</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('rentals.payments.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search">بحث</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="{{ request('search') }}" placeholder="رقم الدفعة، العقد، المستأجر">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="status">الحالة</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">الكل</option>
                                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>متأخر</option>
                                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>جزئي</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="lease_id">العقد</label>
                                    <select class="form-control" id="lease_id" name="lease_id">
                                        <option value="">الكل</option>
                                        @foreach(App\Models\Lease::all() as $lease)
                                            <option value="{{ $lease->id }}" {{ request('lease_id') == $lease->id ? 'selected' : '' }}>
                                                {{ $lease->lease_number }} - {{ $lease->tenant->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>تاريخ الدفعة</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="date" class="form-control" name="date_from" 
                                                   value="{{ request('date_from') }}" placeholder="من">
                                        </div>
                                        <div class="col-6">
                                            <input type="date" class="form-control" name="date_to" 
                                                   value="{{ request('date_to') }}" placeholder="إلى">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i> بحث
                                    </button>
                                </div>
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
                <div class="card-body">
                    @if($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>رقم الدفعة</th>
                                        <th>العقد</th>
                                        <th>المستأجر</th>
                                        <th>العقار</th>
                                        <th>تاريخ الاستحقاق</th>
                                        <th>المبلغ</th>
                                        <th>المدفوع</th>
                                        <th>الحالة</th>
                                        <th>طريقة الدفع</th>
                                        <th>إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>
                                                <span class="font-weight-bold">
                                                    #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('rentals.leases.show', $payment->lease) }}">
                                                    {{ $payment->lease->lease_number }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('rentals.tenants.show', $payment->lease->tenant) }}">
                                                    {{ $payment->lease->tenant->name }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('rentals.properties.show', $payment->lease->property) }}">
                                                    {{ $payment->lease->property->title }}
                                                </a>
                                            </td>
                                            <td>{{ $payment->due_date->format('Y-m-d') }}</td>
                                            <td>{{ number_format($payment->amount, 2) }} ريال</td>
                                            <td>
                                                @if($payment->paid_amount > 0)
                                                    {{ number_format($payment->paid_amount, 2) }} ريال
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                {!! $payment->getPaymentStatusBadge() !!}
                                            </td>
                                            <td>
                                                @if($payment->payment_method)
                                                    {{ $payment->payment_method }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('rentals.payments.show', $payment) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="عرض">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($payment->canBePaid())
                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                onclick="markAsPaid({{ $payment->id }})" title="تسجيل الدفع">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                                    @if($payment->is_overdue && !$payment->late_fee_applied)
                                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                onclick="applyLateFee({{ $payment->id }})" title="تطبيق رسوم التأخير">
                                                            <i class="fas fa-clock"></i>
                                                        </button>
                                                    @endif
                                                    @if($payment->status === 'paid')
                                                        <a href="{{ route('rentals.payments.receipt', $payment) }}" 
                                                           class="btn btn-sm btn-outline-info" title="إيصال">
                                                            <i class="fas fa-receipt"></i>
                                                        </a>
                                                    @ingue
                                               yez
                                                </izada
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $payments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد مدفوعات</h5>
                            <p class="text-muted">لم يتم العثور على مدفوعات مطابقة للبحث</p>
                            <a href="{{ route('rentals.payments.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> تسجيل دفعة جديدة
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
function markAsPaid(id) {
    const amount = prompt('المبلغ المدفوع:');
    const method = prompt('طريقة الدفع (cash, bank, transfer, etc.):');
    
    if (amount && method) {
        axios.post(`/rentals/payments/${id}/pay`, {
            amount: parseFloat(amount),
            payment_method: method
        })
            .then(response => {
                location.reload();
            })
            .catch(error => {
                alert('حدث خطأ أثناء تسجيل الدفع');
            });
    }
}

function applyLateFee(id) {
    if (confirm('هل تريد تطبيق رسوم التأخير؟')) {
        axios.post(`/rentals/payments/${id}/late-fee`)
            .then(response => {
                location.reload();
            })
            .catch(error => {
                alert('حدث خطأ أثناء تطبيق رسوم التأخير');
            });
    }
}
</script>
@endpush
