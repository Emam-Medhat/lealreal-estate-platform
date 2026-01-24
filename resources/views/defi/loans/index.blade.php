@extends('layouts.app')

@section('title', 'القروض العقارية اللامركزية')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-hand-holding-usd me-2"></i>
                        القروض العقارية اللامركزية
                    </h1>
                    <p class="text-muted mb-0">إدارة القروض العقارية اللامركزية</p>
                </div>
                <div>
                    <a href="{{ route('defi.loans.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        طلب قرض جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('defi.loans.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">الحالة</label>
                                <select name="status" class="form-select">
                                    <option value="">الكل</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">مستوى المخاطرة</label>
                                <select name="risk_level" class="form-select">
                                    <option value="">الكل</option>
                                    <option value="low" {{ request('risk_level') == 'low' ? 'selected' : '' }}>منخفض</option>
                                    <option value="medium" {{ request('risk_level') == 'medium' ? 'selected' : '' }}>متوسط</option>
                                    <option value="high" {{ request('risk_level') == 'high' ? 'selected' : '' }}>مرتفع</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">نوع الضمان</label>
                                <select name="collateral_type" class="form-select">
                                    <option value="">الكل</option>
                                    <option value="property" {{ request('collateral_type') == 'property' ? 'selected' : '' }}>عقار</option>
                                    <option value="token" {{ request('collateral_type') == 'token' ? 'selected' : '' }}>رمز</option>
                                    <option value="crypto" {{ request('collateral_type') == 'crypto' ? 'selected' : '' }}>عملة رقمية</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">المدى</label>
                                <div class="input-group">
                                    <input type="number" name="min_amount" class="form-control" placeholder="الحد الأدنى" value="{{ request('min_amount') }}">
                                    <input type="number" name="max_amount" class="form-control" placeholder="الحد الأقصى" value="{{ request('max_amount') }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i>
                                    تطبيق الفلاتر
                                </button>
                                <a href="{{ route('defi.loans.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>
                                    مسح الفلاتر
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">إجمالي القروض</h6>
                            <h4 class="mb-0">{{ number_format($stats['total_loans'] ?? 0) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">القروض النشطة</h6>
                            <h4 class="mb-0">{{ number_format($stats['active_loans'] ?? 0) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">إجمالي المبلغ</h6>
                            <h4 class="mb-0">${{ number_format($stats['total_amount'] ?? 0, 2) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">متوسط APR</h6>
                            <h4 class="mb-0">{{ number_format($stats['average_apr'] ?? 0, 2) }}%</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-percentage fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loans Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>المعرّف</th>
                                    <th>المستخدم</th>
                                    <th>المبلغ</th>
                                    <th>APR</th>
                                    <th>المدة</th>
                                    <th>الضمان</th>
                                    <th>مستوى المخاطرة</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($loans->count() > 0)
                                    @foreach($loans as $loan)
                                        <tr>
                                            <td>#{{ $loan->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        {{ substr($loan->user->name ?? 'NA', 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $loan->user->name ?? 'N/A' }}</div>
                                                        <small class="text-muted">{{ $loan->user->email ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-semibold">${{ number_format($loan->amount, 2) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ number_format($loan->apr, 2) }}%</span>
                                            </td>
                                            <td>{{ $loan->loan_term }} شهر</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $loan->collateral_type_text }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $loan->risk_level_color }}">
                                                    {{ $loan->risk_level_text }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $loan->status_color }}">
                                                    {{ $loan->status_text }}
                                                </span>
                                            </td>
                                            <td>{{ $loan->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('defi.loans.show', $loan->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($loan->status === 'pending')
                                                        <a href="{{ route('defi.loans.approve', $loan->id) }}" class="btn btn-sm btn-outline-success">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                        <a href="{{ route('defi.loans.reject', $loan->id) }}" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    @endif
                                                    @if($loan->status === 'active')
                                                        <a href="{{ route('defi.loans.repay', $loan->id) }}" class="btn btn-sm btn-outline-warning">
                                                            <i class="fas fa-dollar-sign"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <div>لا توجد قروض حالياً</div>
                                            <a href="{{ route('defi.loans.create') }}" class="btn btn-primary mt-2">
                                                <i class="fas fa-plus me-1"></i>
                                                إنشاء قرض جديد
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($loans->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                عرض {{ $loans->firstItem() }} إلى {{ $loans->lastItem() }} من {{ $loans->total() }} عنصر
                            </div>
                            {{ $loans->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
