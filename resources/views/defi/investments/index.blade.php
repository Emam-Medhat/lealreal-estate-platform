@extends('layouts.app')

@section('title', 'الاستثمارات اللامركزية')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        الاستثمارات اللامركزية
                    </h1>
                    <p class="text-muted mb-0">إدارة الاستثمارات العقارية اللامركزية</p>
                </div>
                <div>
                    <a href="{{ route('defi.investments.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        استثمار جديد
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
                    <form method="GET" action="{{ route('defi.investments.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">الحالة</label>
                                <select name="status" class="form-select">
                                    <option value="">الكل</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                    <option value="withdrawn" {{ request('status') == 'withdrawn' ? 'selected' : '' }}>مسحوب</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">نوع الاستثمار</label>
                                <select name="investment_type" class="form-select">
                                    <option value="">الكل</option>
                                    <option value="direct" {{ request('investment_type') == 'direct' ? 'selected' : '' }}>مباشر</option>
                                    <option value="token" {{ request('investment_type') == 'token' ? 'selected' : '' }}>توكن</option>
                                    <option value="pool" {{ request('investment_type') == 'pool' ? 'selected' : '' }}>مجمع سيولة</option>
                                    <option value="fractional" {{ request('investment_type') == 'fractional' ? 'selected' : '' }}>جزئي</option>
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
                                <a href="{{ route('defi.investments.index') }}" class="btn btn-outline-secondary">
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
                            <h6 class="card-title">إجمالي الاستثمارات</h6>
                            <h4 class="mb-0">{{ number_format($stats['total_investments'] ?? 0) }}</h4>
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
                            <h6 class="card-title">الاستثمارات النشطة</h6>
                            <h4 class="mb-0">{{ number_format($stats['active_investments'] ?? 0) }}</h4>
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
                            <h4 class="mb-0">${{ number_format($stats['total_invested'] ?? 0, 2) }}</h4>
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
                            <h6 class="card-title">متوسط ROI</h6>
                            <h4 class="mb-0">{{ number_format($stats['average_roi'] ?? 0, 2) }}%</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-percentage fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Investments Table -->
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
                                    <th>النوع</th>
                                    <th>المبلغ</th>
                                    <th>القيمة الحالية</th>
                                    <th>ROI</th>
                                    <th>المخاطرة</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($investments->count() > 0)
                                    @foreach($investments as $investment)
                                        <tr>
                                            <td>#{{ $investment->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        {{ substr($investment->user->name ?? 'NA', 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $investment->user->name ?? 'N/A' }}</div>
                                                        <small class="text-muted">{{ $investment->user->email ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $investment->investment_type_text }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-semibold">${{ number_format($investment->amount, 2) }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-semibold ${{ investment->current_value >= $investment->amount ? 'text-success' : 'text-danger' }}">
                                                    ${{ number_format($investment->current_value, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $investment->roi >= 0 ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $investment->roi >= 0 ? '+' : '' }}{{ number_format($investment->roi, 2) }}%
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $investment->risk_level_color }}">
                                                    {{ $investment->risk_level_text }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $investment->status_color }}">
                                                    {{ $investment->status_text }}
                                                </span>
                                            </td>
                                            <td>{{ $investment->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('defi.investments.show', $investment->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($investment->status === 'active')
                                                        @if($investment->can_be_withdrawn)
                                                            <a href="{{ route('defi.investments.withdraw', $investment->id) }}" class="btn btn-sm btn-outline-warning">
                                                                <i class="fas fa-money-bill-wave"></i>
                                                            </a>
                                                        @endif
                                                        @if($investment->supports_auto_reinvest)
                                                            <a href="{{ route('defi.investments.reinvest', $investment->id) }}" class="btn btn-sm btn-outline-success">
                                                                <i class="fas fa-sync"></i>
                                                            </a>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">
                                            <i class="fas fa-chart-line fa-3x mb-3"></i>
                                            <div>لا توجد استثمارات حالياً</div>
                                            <a href="{{ route('defi.investments.create') }}" class="btn btn-primary mt-2">
                                                <i class="fas fa-plus me-1"></i>
                                                إنشاء استثمار جديد
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($investments->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                عرض {{ $investments->firstItem() }} إلى {{ $investments->lastItem() }} من {{ $investments->total() }} عنصر
                            </div>
                            {{ $investments->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Charts -->
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">توزيع الاستثمارات حسب النوع</h5>
                </div>
                <div class="card-body">
                    <canvas id="investmentTypeChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">توزيع الاستثمارات حسب المخاطرة</h5>
                </div>
                <div class="card-body">
                    <canvas id="riskDistributionChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Investment Type Chart
const investmentTypeCtx = document.getElementById('investmentTypeChart').getContext('2d');
new Chart(investmentTypeCtx, {
    type: 'pie',
    data: {
        labels: @json($stats['type_distribution'] ? array_keys($stats['type_distribution']) : []),
        datasets: [{
            data: @json($stats['type_distribution'] ? array_values($stats['type_distribution']) : []),
            backgroundColor: [
                '#007bff',
                '#28a745',
                '#ffc107',
                '#dc3545'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Risk Distribution Chart
const riskDistributionCtx = document.getElementById('riskDistributionChart').getContext('2d');
new Chart(riskDistributionCtx, {
    type: 'doughnut',
    data: {
        labels: @json($stats['risk_distribution'] ? array_keys($stats['risk_distribution']) : []),
        datasets: [{
            data: @json($stats['risk_distribution'] ? array_values($stats['risk_distribution']) : []),
            backgroundColor: [
                '#28a745',
                '#ffc107',
                '#dc3545',
                '#6f42c1'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endsection
