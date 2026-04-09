@extends('layouts.app')

@section('title', 'نظام التمويل العقاري اللامركزي')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <h1 class="card-title mb-3">
                        <i class="fas fa-coins me-2"></i>
                        نظام التمويل العقاري اللامركزي
                    </h1>
                    <p class="card-text">
                        استكشف عالم التمويل العقاري اللامركزي مع القروض العقارية، توريق العقارات، التخزين، الملكية الجزئية، الاستثمارات، مجمعات السيولة، العوائد، والـ DAO
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">القروض النشطة</h4>
                            <h2 class="mb-0">{{ number_format($stats['active_loans'] ?? 0) }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-hand-holding-usd fa-2x"></i>
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
                            <h4 class="card-title">الرموز المصدرة</h4>
                            <h2 class="mb-0">{{ number_format($stats['total_tokens'] ?? 0) }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-coins fa-2x"></i>
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
                            <h4 class="card-title">مجمعات السيولة</h4>
                            <h2 class="mb-0">{{ number_format($stats['liquidity_pools'] ?? 0) }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-water fa-2x"></i>
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
                            <h4 class="card-title">الاستثمارات</h4>
                            <h2 class="mb-0">{{ number_format($stats['total_investments'] ?? 0) }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Cards -->
    <div class="row">
        <!-- Property Loans -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-primary text-white rounded-circle p-3 me-3">
                            <i class="fas fa-hand-holding-usd fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">القروض العقارية</h5>
                            <small class="text-muted">تمويل عقاري لامركزي</small>
                        </div>
                    </div>
                    <p class="card-text">
                        احصل على قروض عقارية لامركزية مع تقييم مخاطر، درجة ائتمانية، وعقود ذكية
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('defi.loans.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-1"></i>
                            استعراض القروض
                        </a>
                        <a href="{{ route('defi.loans.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-1"></i>
                            طلب قرض
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Property Tokenization -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-success text-white rounded-circle p-3 me-3">
                            <i class="fas fa-coins fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">توريق العقارات</h5>
                            <small class="text-muted">NFT ورموز العقارات</small>
                        </div>
                    </div>
                    <p class="card-text">
                        حول العقارات إلى رموز رقمية مع NFT، إدارة العرض، وتتبع المعاملات
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('defi.tokens.index') }}" class="btn btn-success">
                            <i class="fas fa-arrow-left me-1"></i>
                            استعراض الرموز
                        </a>
                        <a href="{{ route('defi.tokens.create') }}" class="btn btn-outline-success">
                            <i class="fas fa-plus me-1"></i>
                            إنشاء رمز
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Property Staking -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-info text-white rounded-circle p-3 me-3">
                            <i class="fas fa-lock fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">تخزين العقارات</h5>
                            <small class="text-muted">كسب عوائد APR</small>
                        </div>
                    </div>
                    <p class="card-text">
                        خزن رموز العقارات لكسب عوائد مع APR ديناميكي وتراكم تلقائي
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('defi.staking.index') }}" class="btn btn-info">
                            <i class="fas fa-arrow-left me-1"></i>
                            استعراض التخزين
                        </a>
                        <a href="{{ route('defi.staking.create') }}" class="btn btn-outline-info">
                            <i class="fas fa-plus me-1"></i>
                            تخزين جديد
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fractional Ownership -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-warning text-white rounded-circle p-3 me-3">
                            <i class="fas fa-chart-pie fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">الملكية الجزئية</h5>
                            <small class="text-muted">استثمار جزئي</small>
                        </div>
                    </div>
                    <p class="card-text">
                        استثمر جزئياً في العقارات مع حساب الأرباح/الخسائر وتوزيعات الأرباح
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('defi.fractional.index') }}" class="btn btn-warning">
                            <i class="fas fa-arrow-left me-1"></i>
                            استعراض الملكية
                        </a>
                        <a href="{{ route('defi.fractional.create') }}" class="btn btn-outline-warning">
                            <i class="fas fa-plus me-1"></i>
                            شراء حصة
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Property Investments -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-danger text-white rounded-circle p-3 me-3">
                            <i class="fas fa-chart-line fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">الاستثمارات</h5>
                            <small class="text-muted">محفظة استثمارية متنوعة</small>
                        </div>
                    </div>
                    <p class="card-text">
                        استثمر في العقارات مباشرة، عبر الرموز، مجمعات السيولة، أو الملكية الجزئية
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('defi.investments.index') }}" class="btn btn-danger">
                            <i class="fas fa-arrow-left me-1"></i>
                            استعراض الاستثمارات
                        </a>
                        <a href="{{ route('defi.investments.create') }}" class="btn btn-outline-danger">
                            <i class="fas fa-plus me-1"></i>
                            استثمار جديد
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liquidity Pools -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-secondary text-white rounded-circle p-3 me-3">
                            <i class="fas fa-water fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">مجمعات السيولة</h5>
                            <small class="text-muted">توفير السيولة وكسب الرسوم</small>
                        </div>
                    </div>
                    <p class="card-text">
                        قدم السيولة لمجمعات العقارات مع APR ديناميكي، تراكم تلقائي، وإعادة موازنة
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('defi.pools.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            استعراض المجمعات
                        </a>
                        <a href="{{ route('defi.pools.create') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-plus me-1"></i>
                            إنشاء مجمع
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Property Yields -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-dark text-white rounded-circle p-3 me-3">
                            <i class="fas fa-percentage fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">العوائد</h5>
                            <small class="text-muted">تتبع وتحصيل العوائد</small>
                        </div>
                    </div>
                    <p class="card-text">
                        تتبع عوائد التخزين، مجمعات السيولة، توزيعات الأرباح، والتراكم التلقائي
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('defi.yields.index') }}" class="btn btn-dark">
                            <i class="fas fa-arrow-left me-1"></i>
                            استعراض العوائد
                        </a>
                        <a href="{{ route('defi.yields.create') }}" class="btn btn-outline-dark">
                            <i class="fas fa-plus me-1"></i>
                            عائد جديد
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Property DAO -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-purple text-white rounded-circle p-3 me-3">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">الـ DAO</h5>
                            <small class="text-muted">حوكمة لامركزية</small>
                        </div>
                    </div>
                    <p class="card-text">
                        شارك في حوكمة العقارات اللامركزية مع التصويت، المقترحات، والخزينة
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('defi.dao.index') }}" class="btn btn-purple">
                            <i class="fas fa-arrow-left me-1"></i>
                            استعراض الـ DAO
                        </a>
                        <a href="{{ route('defi.dao.create') }}" class="btn btn-outline-purple">
                            <i class="fas fa-plus me-1"></i>
                            إنشاء DAO
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Crypto Payments -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-teal text-white rounded-circle p-3 me-3">
                            <i class="fas fa-credit-card fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">الدفعات الرقمية</h5>
                            <small class="text-muted">دفع متعدد العملات</small>
                        </div>
                    </div>
                    <p class="card-text">
                        ادفع بالعملات الرقمية عبر بلوك تشينات متعددة مع تتبع المعاملات
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('defi.payments.index') }}" class="btn btn-teal">
                            <i class="fas fa-arrow-left me-1"></i>
                            استعراض الدفعات
                        </a>
                        <a href="{{ route('defi.payments.create') }}" class="btn btn-outline-teal">
                            <i class="fas fa-plus me-1"></i>
                            دفع جديد
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>
                        النشاط الأخير
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>النوع</th>
                                    <th>الوصف</th>
                                    <th>المبلغ</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($recentActivity) && $recentActivity->count() > 0)
                                    @foreach($recentActivity as $activity)
                                        <tr>
                                            <td>
                                                <span class="badge bg-{{ $activity['color'] ?? 'primary' }}">
                                                    {{ $activity['type'] }}
                                                </span>
                                            </td>
                                            <td>{{ $activity['description'] }}</td>
                                            <td>{{ number_format($activity['amount'] ?? 0, 2) }} {{ $activity['currency'] ?? 'USD' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $activity['status_color'] ?? 'success' }}">
                                                    {{ $activity['status'] }}
                                                </span>
                                            </td>
                                            <td>{{ $activity['date'] ?? now()->format('Y-m-d') }}</td>
                                            <td>
                                                <a href="{{ $activity['link'] ?? '#' }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            لا يوجد نشاط حديث
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-box {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-purple {
    background-color: #6f42c1 !important;
}

.btn-purple {
    background-color: #6f42c1;
    border-color: #6f42c1;
    color: white;
}

.btn-outline-purple {
    border-color: #6f42c1;
    color: #6f42c1;
}

.bg-teal {
    background-color: #20c997 !important;
}

.btn-teal {
    background-color: #20c997;
    border-color: #20c997;
    color: white;
}

.btn-outline-teal {
    border-color: #20c997;
    color: #20c997;
}
</style>
@endsection
