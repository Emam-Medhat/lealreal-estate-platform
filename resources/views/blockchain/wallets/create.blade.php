@extends('admin.layouts.admin')

@section('title', 'إنشاء محفظة جديدة')
@section('page-title', 'إنشاء محفظة جديدة')

@push('styles')
<style>
.create-wallet-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}

.form-card {
    background: var(--bg-glass);
    backdrop-filter: blur(25px);
    border: 1px solid rgba(255, 255, 255, 0.4);
    border-radius: var(--radius-lg);
    padding: 2rem;
    box-shadow: var(--shadow-soft);
}

.form-header {
    text-align: center;
    margin-bottom: 2rem;
}

.form-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.form-subtitle {
    color: var(--text-secondary);
    font-size: 0.95rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid rgba(102, 126, 234, 0.2);
    border-radius: var(--radius-md);
    background: rgba(255, 255, 255, 0.9);
    color: var(--text-primary);
    font-size: 0.95rem;
    transition: var(--transition-smooth);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}

.btn-primary {
    background: var(--primary-gradient);
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: var(--transition-smooth);
    box-shadow: var(--shadow-blue);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-large);
}

.btn-secondary {
    background: transparent;
    color: var(--text-secondary);
    border: 2px solid rgba(102, 126, 234, 0.3);
    padding: 0.75rem 2rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: var(--transition-smooth);
    margin-right: 1rem;
}

.btn-secondary:hover {
    background: rgba(102, 126, 234, 0.1);
    border-color: var(--primary-blue);
    color: var(--primary-blue);
}

.form-actions {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(102, 126, 234, 0.1);
}

.alert {
    padding: 1rem;
    border-radius: var(--radius-md);
    margin-bottom: 1.5rem;
    border-left: 4px solid;
}

.alert-info {
    background: rgba(79, 172, 254, 0.1);
    border-color: var(--accent-cyan);
    color: var(--text-primary);
}

.alert-warning {
    background: rgba(255, 193, 7, 0.1);
    border-color: var(--accent-yellow);
    color: var(--text-primary);
}

.wallet-type-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-top: 0.5rem;
}

.wallet-type-card {
    padding: 1rem;
    border: 2px solid rgba(102, 126, 234, 0.2);
    border-radius: var(--radius-md);
    text-align: center;
    cursor: pointer;
    transition: var(--transition-smooth);
    background: rgba(255, 255, 255, 0.9);
}

.wallet-type-card:hover {
    border-color: var(--primary-blue);
    background: rgba(102, 126, 234, 0.05);
}

.wallet-type-card.selected {
    border-color: var(--primary-blue);
    background: var(--primary-gradient);
    color: white;
}

.wallet-type-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.wallet-type-name {
    font-weight: 600;
    font-size: 0.9rem;
}

.network-options {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.network-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    border: 2px solid rgba(102, 126, 234, 0.2);
    background: rgba(255, 255, 255, 0.9);
    cursor: pointer;
    transition: var(--transition-smooth);
    font-size: 0.85rem;
    font-weight: 500;
}

.network-badge:hover {
    border-color: var(--primary-blue);
    background: rgba(102, 126, 234, 0.1);
}

.network-badge.selected {
    background: var(--primary-gradient);
    border-color: var(--primary-blue);
    color: white;
}

.form-help {
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-top: 0.25rem;
}

.required {
    color: var(--accent-coral);
}
</style>
@endpush

@section('content')
<div class="create-wallet-container">
    <div class="form-card">
        <div class="form-header">
            <h1 class="form-title">🔐 إنشاء محفظة جديدة</h1>
            <p class="form-subtitle">قم بإنشاء محفظة رقمية جديدة لإدارة أصولك المشفرة</p>
        </div>

        <div class="alert alert-info">
            <strong>⚠️ تنبيه أمان:</strong> احتفظ بمفتاحك الخاص في مكان آمن. لا تشاركه مع أي شخص أبداً!
        </div>

        <form action="{{ route('blockchain.wallets.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">اسم المحفظة <span class="required">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="مثال: محفظة الاستثمار الرئيسية" required>
                <div class="form-help">اختر اسماً وصفياً لتسهيل التعرف على المحفظة</div>
            </div>

            <div class="form-group">
                <label class="form-label">نوع المحفظة <span class="required">*</span></label>
                <div class="wallet-type-grid">
                    <div class="wallet-type-card" data-type="ethereum">
                        <div class="wallet-type-icon">🔷</div>
                        <div class="wallet-type-name">Ethereum</div>
                    </div>
                    <div class="wallet-type-card" data-type="btc">
                        <div class="wallet-type-icon">🟠</div>
                        <div class="wallet-type-name">Bitcoin</div>
                    </div>
                    <div class="wallet-type-card" data-type="custom">
                        <div class="wallet-type-icon">⚡</div>
                        <div class="wallet-type-name">مخصص</div>
                    </div>
                </div>
                <input type="hidden" name="wallet_type" id="wallet_type" required>
            </div>

            <div class="form-group">
                <label class="form-label">الشبكة <span class="required">*</span></label>
                <div class="network-options">
                    <div class="network-badge" data-network="mainnet">Mainnet</div>
                    <div class="network-badge" data-network="testnet">Testnet</div>
                    <div class="network-badge" data-network="polygon">Polygon</div>
                    <div class="network-badge" data-network="bsc">BSC</div>
                </div>
                <input type="hidden" name="network" id="network" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">العملة <span class="required">*</span></label>
                    <select name="currency" class="form-control form-select" required>
                        <option value="">اختر العملة</option>
                        <option value="ETH">ETH - Ethereum</option>
                        <option value="BTC">BTC - Bitcoin</option>
                        <option value="USDC">USDC - USD Coin</option>
                        <option value="USDT">USDT - Tether</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">الرصيد الابتدائي</label>
                    <input type="number" name="balance" class="form-control" placeholder="0.00" step="0.00000001" min="0">
                    <div class="form-help">الرصيد الابتدائي للمحفظة</div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">عنوان المحفظة <span class="required">*</span></label>
                <input type="text" name="address" class="form-control" placeholder="0x..." required>
                <div class="form-help">عنوان المحفظة العام (40-42 حرفاً)</div>
            </div>

            <div class="form-group">
                <label class="form-label">المفتاح العام</label>
                <input type="text" name="public_key" class="form-control" placeholder="0x...">
                <div class="form-help">المفتاح العام للمحفظة (اختياري)</div>
            </div>

            <div class="form-group">
                <label class="form-label">المفتاح الخاص <span class="required">*</span></label>
                <input type="password" name="private_key" class="form-control" placeholder="••••••••" required>
                <div class="form-help">سيتم تشفير المفتاح الخاص تلقائياً</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">الحالة</label>
                    <select name="is_active" class="form-control form-select">
                        <option value="1">نشطة</option>
                        <option value="0">غير نشطة</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">المحفظة الافتراضية</label>
                    <select name="is_default" class="form-control form-select">
                        <option value="0">لا</option>
                        <option value="1">نعم</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('blockchain.wallets.index') }}" class="btn-secondary">إلغاء</a>
                <button type="submit" class="btn-primary">
                    🚀 إنشاء المحفظة
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wallet type selection
    const walletTypeCards = document.querySelectorAll('.wallet-type-card');
    const walletTypeInput = document.getElementById('wallet_type');
    
    walletTypeCards.forEach(card => {
        card.addEventListener('click', function() {
            walletTypeCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            walletTypeInput.value = this.dataset.type;
        });
    });

    // Network selection
    const networkBadges = document.querySelectorAll('.network-badge');
    const networkInput = document.getElementById('network');
    
    networkBadges.forEach(badge => {
        badge.addEventListener('click', function() {
            networkBadges.forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
            networkInput.value = this.dataset.network;
        });
    });

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        if (!walletTypeInput.value) {
            e.preventDefault();
            alert('الرجاء اختيار نوع المحفظة');
            return;
        }
        
        if (!networkInput.value) {
            e.preventDefault();
            alert('الرجاء اختيار الشبكة');
            return;
        }

        // Confirm private key handling
        const privateKey = document.querySelector('input[name="private_key"]').value;
        if (privateKey) {
            if (!confirm('هل أنت متأكد من حفظ المفتاح الخاص؟ سيتم تشفيره تلقائياً.')) {
                e.preventDefault();
                return;
            }
        }
    });

    // Address validation
    const addressInput = document.querySelector('input[name="address"]');
    addressInput.addEventListener('blur', function() {
        const address = this.value.trim();
        if (address && !address.startsWith('0x')) {
            this.setCustomValidity('يجب أن يبدأ العنوان بـ 0x');
        } else if (address && (address.length < 40 || address.length > 42)) {
            this.setCustomValidity('يجب أن يكون العنوان 40-42 حرفاً');
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>
@endpush
