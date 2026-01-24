@extends('layouts.app')

@section('title', 'توريق العقارات')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-coins me-2"></i>
                        توريق العقارات
                    </h1>
                    <p class="text-muted mb-0">إدارة رموز العقارات و NFT</p>
                </div>
                <div>
                    <a href="{{ route('defi.tokens.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        إنشاء رمز جديد
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
                    <form method="GET" action="{{ route('defi.tokens.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">الحالة</label>
                                <select name="status" class="form-select">
                                    <option value="">الكل</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>معلق</option>
                                    <option value="burned" {{ request('status') == 'burned' ? 'selected' : '' }}>محروق</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">البلوك تشين</label>
                                <select name="blockchain" class="form-select">
                                    <option value="">الكل</option>
                                    <option value="ethereum" {{ request('blockchain') == 'ethereum' ? 'selected' : '' }}>إيثيريوم</option>
                                    <option value="polygon" {{ request('blockchain') == 'polygon' ? 'selected' : '' }}>بوليغون</option>
                                    <option value="binance_smart_chain" {{ request('blockchain') == 'binance_smart_chain' ? 'selected' : '' }}>BSC</option>
                                    <option value="solana" {{ request('blockchain') == 'solana' ? 'selected' : '' }}>سولانا</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">نوع الرمز</label>
                                <select name="token_type" class="form-select">
                                    <option value="">الكل</option>
                                    <option value="ERC721" {{ request('token_type') == 'ERC721' ? 'selected' : '' }}>ERC721 (NFT)</option>
                                    <option value="ERC20" {{ request('token_type') == 'ERC20' ? 'selected' : '' }}>ERC20</option>
                                    <option value="ERC1155" {{ request('token_type') == 'ERC1155' ? 'selected' : '' }}>ERC1155</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">المدى</label>
                                <div class="input-group">
                                    <input type="number" name="min_price" class="form-control" placeholder="الحد الأدنى" value="{{ request('min_price') }}">
                                    <input type="number" name="max_price" class="form-control" placeholder="الحد الأقصى" value="{{ request('max_price') }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i>
                                    تطبيق الفلاتر
                                </button>
                                <a href="{{ route('defi.tokens.index') }}" class="btn btn-outline-secondary">
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
                            <h6 class="card-title">إجمالي الرموز</h6>
                            <h4 class="mb-0">{{ number_format($stats['total_tokens'] ?? 0) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-coins fa-2x"></i>
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
                            <h6 class="card-title">الرموز النشطة</h6>
                            <h4 class="mb-0">{{ number_format($stats['active_tokens'] ?? 0) }}</h4>
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
                            <h6 class="card-title">القيمة السوقية</h6>
                            <h4 class="mb-0">${{ number_format($stats['market_cap'] ?? 0, 2) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
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
                            <h6 class="card-title">حجم التداول</h6>
                            <h4 class="mb-0">${{ number_format($stats['volume_24h'] ?? 0, 2) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exchange-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tokens Grid -->
    <div class="row">
        @if($tokens->count() > 0)
            @foreach($tokens as $token)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">{{ $token->name }}</h5>
                                    <small class="text-muted">{{ $token->symbol }}</small>
                                </div>
                                <span class="badge bg-{{ $token->status_color }}">
                                    {{ $token->status_text }}
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">السعر</span>
                                    <span class="fw-semibold">${{ number_format($token->price_per_token, 4) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">الإجمالي</span>
                                    <span>{{ number_format($token->total_supply) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">الموزع</span>
                                    <span>{{ number_format($token->distributed_supply) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">القيمة السوقية</span>
                                    <span>${{ number_format($token->market_cap, 2) }}</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">البلوك تشين</span>
                                    <span class="badge bg-secondary">{{ $token->blockchain_text }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">النوع</span>
                                    <span class="badge bg-info">{{ $token->token_type }}</span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <small class="text-muted">تغيير 24h</small>
                                    <div class="fw-semibold {{ $token->price_change_24h >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $token->price_change_24h >= 0 ? '+' : '' }}{{ number_format($token->price_change_24h, 2) }}%
                                    </div>
                                </div>
                                <div>
                                    <small class="text-muted">حجم 24h</small>
                                    <div class="fw-semibold">${{ number_format($token->volume_24h, 2) }}</div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('defi.tokens.show', $token->id) }}" class="btn btn-sm btn-primary flex-fill">
                                    <i class="fas fa-eye me-1"></i>
                                    عرض
                                </a>
                                @if($token->status === 'active')
                                    <a href="{{ route('defi.tokens.mint', $token->id) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                    <a href="{{ route('defi.tokens.transfer', $token->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-exchange-alt"></i>
                                    </a>
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
                        <i class="fas fa-coins fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد رموز حالياً</h5>
                        <p class="text-muted">ابدأ بإنشاء رمز عقار جديد</p>
                        <a href="{{ route('defi.tokens.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            إنشاء رمز جديد
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($tokens->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                عرض {{ $tokens->firstItem() }} إلى {{ $tokens->lastItem() }} من {{ $tokens->total() }} عنصر
            </div>
            {{ $tokens->links() }}
        </div>
    @endif
</div>
@endsection
