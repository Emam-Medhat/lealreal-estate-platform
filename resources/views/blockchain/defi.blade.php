@extends('layouts.app')

@section('title', 'التمويل اللامركزي - DeFi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">التمويل اللامركزي - DeFi</h1>
                <div class="btn-group">
                    <a href="{{ route('blockchain.defi.loans') }}" class="btn btn-primary">
                        <i class="fas fa-hand-holding-usd"></i> القروض
                    </a>
                    <a href="{{ route('blockchain.defi.staking') }}" class="btn btn-primary">
                        <i class="fas fa-coins"></i> التخزين
                    </a>
                    <a href="{{ route('blockchain.defi.yield') }}" class="btn btn-primary">
                        <i class="fas fa-chart-line"></i> زراعة العوائد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- DeFi Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($totalLiquidity, 2) }} ETH</h4>
                            <p>إجمالي السيولة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-water fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($totalBorrowed, 2) }} ETH</h4>
                            <p>إجمالي المقترض</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-hand-holding-usd fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($totalStaked, 2) }} ETH</h4>
                            <p>إجمالي المخزون</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-lock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($averageAPY, 2) }}%</h4>
                            <p>متوسط العائد السنوي</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-percentage fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DeFi Products -->
    <div class="row mb-4">
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-hand-holding-usd"></i> القروض اللامركزي
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>إقرض الأموال</h6>
                        <p class="text-muted small">أقرض أصولك وكسب الفائدة</p>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>معدل الفائدة:</span>
                            <span class="fw-bold">5-15%</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>إجمالي القروض:</span>
                            <span class="fw-bold">{{ number_format($totalLent, 2) }} ETH</span>
                        </div>
                    </div>
                    <a href="{{ route('blockchain.defi.lend') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-plus"></i> إقرض الآن
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-coins"></i> التخزين
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>تخزين الأصول</h6>
                        <p class="text-muted small">تخزين أصولك وكسب مكافآت</p>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>معدل المكافأة:</span>
                            <span class="fw-bold">8-25%</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>إجمالي المخزون:</span>
                            <span class="fw-bold">{{ number_format($totalStaked, 2) }} ETH</span>
                        </div>
                    </div>
                    <a href="{{ route('blockchain.defi.stake') }}" class="btn btn-success btn-block">
                        <i class="fas fa-plus"></i> تخزين الآن
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line"></i> زراعة العوائد
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>توفير السيولة</h6>
                        <p class="text-muted small">توفير السيولة للمجمعات وكسب العوائد</p>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>معدل العائد:</span>
                            <span class="fw-bold">20-150%</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>إجمالي العائد:</span>
                            <span class="fw-bold">{{ number_format($totalYield, 2) }} ETH</span>
                        </div>
                    </div>
                    <a href="{{ route('blockchain.defi.yield') }}" class="btn btn-info btn-block">
                        <i class="fas fa-plus"></i> زراعة الآن
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Pools -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">المجمعات النشطة</h5>
                    <a href="{{ route('blockchain.defi.pools') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>المجمع</th>
                                    <th>النوع</th>
                                    <th>السيولة الكلية</th>
                                    <th>معدل العائد السنوي</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activePools as $pool)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <i class="fas fa-water text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $pool->name }}</div>
                                                <small class="text-muted">{{ $pool->token_pair }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $pool->type === 'liquidity' ? 'info' : 'success' }}">
                                            {{ $pool->type === 'liquidity' ? 'سيولة' : 'تخزين' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ number_format($pool->total_liquidity, 2) }} ETH</div>
                                        <small class="text-muted">${{ number_format($pool->total_liquidity_usd, 2) }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">{{ number_format($pool->apy, 2) }}%</span>
                                    </td>
                                    <td>
                                        @if($pool->is_active)
                                        <span class="badge bg-success">نشط</span>
                                        @else
                                        <span class="badge bg-secondary">غير نشط</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('blockchain.defi.pool.show', $pool->id) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('blockchain.defi.pool.deposit', $pool->id) }}" class="btn btn-outline-success">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                            <a href="{{ route('blockchain.defi.pool.withdraw', $pool->id) }}" class="btn btn-outline-warning">
                                                <i class="fas fa-minus"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Portfolio Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">نظرة عامة على المحفظة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4>{{ number_format($portfolioValue, 2) }} ETH</h4>
                                <p class="text-muted">قيمة المحفظة</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-success">{{ number_format($totalEarnings, 2) }} ETH</h4>
                                <p class="text-muted">إجمالي الأرباح</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-info">{{ number_format($activePositions) }}</h4>
                                <p class="text-muted">المراكز النشطة</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-warning">{{ number_format($riskScore, 1) }}/10</h4>
                                <p class="text-muted">مستوى المخاطرة</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Auto-refresh DeFi data
setInterval(() => {
    fetch('{{ route("blockchain.defi.refresh") }}')
        .then(response => response.json())
        .then(data => {
            // Update statistics
            document.querySelector('#total-liquidity').textContent = `${data.total_liquidity} ETH`;
            document.querySelector('#total-borrowed').textContent = `${data.total_borrowed} ETH`;
            document.querySelector('#total-staked').textContent = `${data.total_staked} ETH`;
            document.querySelector('#average-apy').textContent = `${data.average_apy}%`;
        });
}, 30000); // Refresh every 30 seconds
</script>
@endsection
