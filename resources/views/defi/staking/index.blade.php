@extends('layouts.app')

@section('title', 'تخزين العقارات')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-lock me-2"></i>
                        تخزين العقارات
                    </h1>
                    <p class="text-muted mb-0">إدارة تخزين رموز العقارات وكسب العوائد</p>
                </div>
                <div>
                    <a href="{{ route('defi.staking.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        تخزين جديد
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
                    <form method="GET" action="{{ route('defi.staking.index') }}">
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
                                <label class="form-label">التراكم التلقائي</label>
                                <select name="auto_compound" class="form-select">
                                    <option value="">الكل</option>
                                    <option value="1" {{ request('auto_compound') == '1' ? 'selected' : '' }}>مفعل</option>
                                    <option value="0" {{ request('auto_compound') == '0' ? 'selected' : '' }}>معطل</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">نطاق APR</label>
                                <div class="input-group">
                                    <input type="number" name="min_apr" class="form-control" placeholder="الحد الأدنى" value="{{ request('min_apr') }}" step="0.1">
                                    <input type="number" name="max_apr" class="form-control" placeholder="الحد الأقصى" value="{{ request('max_apr') }}" step="0.1">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">فترة التخزين</label>
                                <select name="period" class="form-select">
                                    <option value="">الكل</option>
                                    <option value="short" {{ request('period') == 'short' ? 'selected' : '' }}>قصيرة (< 30 يوم)</option>
                                    <option value="medium" {{ request('period') == 'medium' ? 'selected' : '' }}>متوسطة (30-90 يوم)</option>
                                    <option value="long" {{ request('period') == 'long' ? 'selected' : '' }}>طويلة (> 90 يوم)</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i>
                                    تطبيق الفلاتر
                                </button>
                                <a href="{{ route('defi.staking.index') }}" class="btn btn-outline-secondary">
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
                            <h6 class="card-title">إجمالي التخزين</h6>
                            <h4 class="mb-0">{{ number_format($stats['total_staking'] ?? 0) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-lock fa-2x"></i>
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
                            <h6 class="card-title">التخزين النشط</h6>
                            <h4 class="mb-0">{{ number_format($stats['active_staking'] ?? 0) }}</h4>
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

    <!-- Staking Positions -->
    <div class="row">
        @if($stakingPositions->count() > 0)
            @foreach($stakingPositions as $staking)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">{{ $staking->token->name ?? 'N/A' }}</h5>
                                    <small class="text-muted">{{ $staking->token->symbol ?? 'N/A' }}</small>
                                </div>
                                <span class="badge bg-{{ $staking->status_color }}">
                                    {{ $staking->status_text }}
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">المبلغ</span>
                                    <span class="fw-semibold">{{ number_format($staking->amount, 4) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">APR</span>
                                    <span class="badge bg-info">{{ number_format($staking->apr, 2) }}%</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">المدة</span>
                                    <span>{{ $staking->staking_period }} يوم</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">القيمة الحالية</span>
                                    <span class="fw-semibold">${{ number_format($staking->current_value, 2) }}</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">العائد اليومي</span>
                                    <span class="text-success">${{ number_format($staking->daily_earnings, 4) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">العائد الشهري</span>
                                    <span class="text-success">${{ number_format($staking->monthly_earnings, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">إجمالي المكتسب</span>
                                    <span class="text-success">${{ number_format($staking->total_earned, 2) }}</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">التراكم التلقائي</span>
                                    <span class="badge {{ $staking->auto_compound ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $staking->auto_compound ? 'مفعل' : 'معطل' }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">التقدم</span>
                                    <div class="progress" style="width: 100px; height: 20px;">
                                        <div class="progress-bar bg-primary" role="progressbar" 
                                             style="width: {{ $staking->progress_percentage }}%">
                                            {{ number_format($staking->progress_percentage, 1) }}%
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">أيام متبقية</span>
                                    <span>{{ $staking->days_until_unlock }}</span>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('defi.staking.show', $staking->id) }}" class="btn btn-sm btn-primary flex-fill">
                                    <i class="fas fa-eye me-1"></i>
                                    عرض
                                </a>
                                @if($staking->status === 'active')
                                    @if($staking->auto_compound)
                                        <a href="{{ route('defi.staking.compound', $staking->id) }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-sync"></i>
                                        </a>
                                    @endif
                                    @if($staking->can_be_unstaked)
                                        <a href="{{ route('defi.staking.unstake', $staking->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-unlock"></i>
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-lock fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد مواقع تخزين حالياً</h5>
                        <p class="text-muted">ابدأ بتخزين رموز العقارات لكسب العوائد</p>
                        <a href="{{ route('defi.staking.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            تخزين جديد
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($stakingPositions->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                عرض {{ $stakingPositions->firstItem() }} إلى {{ $stakingPositions->lastItem() }} من {{ $stakingPositions->total() }} عنصر
            </div>
            {{ $stakingPositions->links() }}
        </div>
    @endif
</div>
@endsection
