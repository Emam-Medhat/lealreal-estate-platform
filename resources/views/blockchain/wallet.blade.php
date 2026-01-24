@extends('layouts.app')

@section('title', 'المحافظ الرقمية')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">المحافظ الرقمية</h1>
                <div class="btn-group">
                    <a href="{{ route('blockchain.wallets.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إنشاء محفظة
                    </a>
                    <a href="{{ route('blockchain.wallets.import') }}" class="btn btn-outline-primary">
                        <i class="fas fa-download"></i> استيراد محفظة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Wallet Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($totalWallets) }}</h4>
                            <p>إجمالي المحافظ</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-wallet fa-2x"></i>
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
                            <h4 class="mb-0">{{ number_format($totalBalance, 4) }} ETH</h4>
                            <p>الرصيد الإجمالي</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-coins fa-2x"></i>
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
                            <h4 class="mb-0">{{ number_format($totalTransactions) }}</h4>
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
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($activeWallets) }}</h4>
                            <p>المحافظ النشطة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wallet List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة المحافظ</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>العنوان</th>
                                    <th>النوع</th>
                                    <th>الرصيد</th>
                                    <th>الحالة</th>
                                    <th>آخر نشاط</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($wallets as $wallet)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <i class="fas fa-wallet text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $wallet->name }}</div>
                                                <small class="text-muted">{{ $wallet->created_at->format('Y-m-d') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="small">{{ substr($wallet->address, 0, 10) }}...{{ substr($wallet->address, -8) }}</code>
                                        <button class="btn btn-sm btn-link p-0" onclick="copyAddress('{{ $wallet->address }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $wallet->type === 'main' ? 'primary' : 'secondary' }}">
                                            {{ $wallet->type === 'main' ? 'رئيسية' : 'فرعية' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ number_format($wallet->balance, 4) }} ETH</div>
                                        <small class="text-muted">${{ number_format($wallet->balance_usd, 2) }}</small>
                                    </td>
                                    <td>
                                        @if($wallet->is_active)
                                        <span class="badge bg-success">نشطة</span>
                                        @else
                                        <span class="badge bg-secondary">غير نشطة</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $wallet->last_activity ? $wallet->last_activity->diffForHumans() : 'لم يتم استخدامها' }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('blockchain.wallets.show', $wallet->id) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('blockchain.wallets.send', $wallet->id) }}" class="btn btn-outline-success">
                                                <i class="fas fa-paper-plane"></i>
                                            </a>
                                            <a href="{{ route('blockchain.wallets.receive', $wallet->id) }}" class="btn btn-outline-info">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="{{ route('blockchain.wallets.export', $wallet->id) }}" class="btn btn-outline-warning">
                                                <i class="fas fa-file-export"></i>
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

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-qrcode fa-3x text-primary mb-3"></i>
                                    <h6>استلام</h6>
                                    <p class="text-muted small">استلم الأموال إلى محفظتك</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-paper-plane fa-3x text-success mb-3"></i>
                                    <h6>إرسال</h6>
                                    <p class="text-muted small">أرسل الأموال إلى محفظة أخرى</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-history fa-3x text-info mb-3"></i>
                                    <h6>السجل</h6>
                                    <p class="text-muted small">عرض سجل المعاملات</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-shield-alt fa-3x text-warning mb-3"></i>
                                    <h6>الأمان</h6>
                                    <p class="text-muted small">إعدادات الأمان والنسخ الاحتياطي</p>
                                </div>
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
function copyAddress(address) {
    navigator.clipboard.writeText(address).then(function() {
        // Show success message
        const toast = document.createElement('div');
        toast.className = 'alert alert-success position-fixed top-0 end-0 m-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = 'تم نسخ العنوان بنجاح!';
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    });
}

// Auto-refresh wallet balances
setInterval(() => {
    fetch('{{ route("blockchain.wallets.refresh") }}')
        .then(response => response.json())
        .then(data => {
            // Update wallet balances
            data.wallets.forEach(wallet => {
                const balanceElement = document.querySelector(`#wallet-balance-${wallet.id}`);
                if (balanceElement) {
                    balanceElement.textContent = `${wallet.balance} ETH`;
                }
            });
        });
}, 30000); // Refresh every 30 seconds
</script>
@endsection
