@extends('layouts.app')

@section('title', 'لوحة تحكم البلوكشين')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">لوحة تحكم البلوكشين</h1>
                <div class="btn-group">
                    <a href="{{ route('blockchain.network') }}" class="btn btn-outline-primary">
                        <i class="fas fa-network-wired"></i> الشبكة
                    </a>
                    <a href="{{ route('blockchain.analytics') }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-line"></i> التحليلات
                    </a>
                    <a href="{{ route('blockchain.settings') }}" class="btn btn-outline-primary">
                        <i class="fas fa-cog"></i> الإعدادات
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($stats['total_blocks']) }}</h4>
                            <p>إجمالي الكتل</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-cube fa-2x"></i>
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
                            <h4 class="mb-0">{{ number_format($stats['total_transactions']) }}</h4>
                            <p>إجمالي المعاملات</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exchange-alt fa-2x"></i>
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
                            <h4 class="mb-0">{{ number_format($stats['total_contracts']) }}</h4>
                            <p>العقود الذكية</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-contract fa-2x"></i>
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
                            <h4 class="mb-0">{{ number_format($stats['total_wallets']) }}</h4>
                            <p>المحافظ الرقمية</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-wallet fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Network Status -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">حالة الشبكة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6>آخر كتلة</h6>
                                <p class="text-muted">{{ $stats['latest_block']->height ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6>معدل الهاش</h6>
                                <p class="text-muted">{{ $stats['network_hashrate'] }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6>الصعوبة</h6>
                                <p class="text-muted">{{ number_format($stats['difficulty']) }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6>سعر الغاز</h6>
                                <p class="text-muted">{{ $stats['gas_price'] }} Gwei</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Blocks and Transactions -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">الكتل الأخيرة</h5>
                    <a href="{{ route('blockchain.blocks') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>الارتفاع</th>
                                    <th>الهاش</th>
                                    <th>الوقت</th>
                                    <th>المعاملات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBlocks as $block)
                                <tr>
                                    <td>{{ $block->height }}</td>
                                    <td><code>{{ substr($block->hash, 0, 10) }}...</code></td>
                                    <td>{{ $block->created_at->diffForHumans() }}</td>
                                    <td>{{ $block->transaction_count ?? 0 }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">المعاملات الأخيرة</h5>
                    <a href="{{ route('blockchain.transactions') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>الهاش</th>
                                    <th>من</th>
                                    <th>إلى</th>
                                    <th>القيمة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td><code>{{ substr($transaction->hash, 0, 10) }}...</code></td>
                                    <td><code>{{ substr($transaction->from_address, 0, 8) }}...</code></td>
                                    <td><code>{{ substr($transaction->to_address, 0, 8) }}...</code></td>
                                    <td>{{ $transaction->amount ?? 0 }} ETH</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('blockchain.smartcontracts.index') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-file-contract"></i> العقود الذكية
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('blockchain.nfts.index') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-image"></i> NFTs
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('blockchain.wallets.index') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-wallet"></i> المحافظ
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('blockchain.defi.index') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-chart-line"></i> DeFi
                            </a>
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
// Auto-refresh dashboard data
setInterval(() => {
    location.reload();
}, 30000); // Refresh every 30 seconds
</script>
@endsection
