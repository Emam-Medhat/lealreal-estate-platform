@extends('layouts.app')

@section('title', 'الرموز غير القابلة للاستبدال - NFTs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">الرموز غير القابلة للاستبدال - NFTs</h1>
                <div class="btn-group">
                    <a href="{{ route('blockchain.nfts.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إنشاء NFT
                    </a>
                    <a href="{{ route('blockchain.metaverse.marketplace') }}" class="btn btn-outline-primary">
                        <i class="fas fa-store"></i> السوق
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- NFT Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($totalNfts) }}</h4>
                            <p>إجمالي NFTs</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-image fa-2x"></i>
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
                            <h4 class="mb-0">{{ number_format($listedNfts) }}</h4>
                            <p>المعروضة للبيع</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tag fa-2x"></i>
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
                            <h4 class="mb-0">{{ number_format($totalVolume) }} ETH</h4>
                            <p>حجم التداول</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
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
                            <h4 class="mb-0">{{ number_format($ownersCount) }}</h4>
                            <p>الملاك</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('blockchain.nfts.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="category" class="form-select">
                                    <option value="">كل الفئات</option>
                                    <option value="art">فن</option>
                                    <option value="collectible">تحف</option>
                                    <option value="music">موسيقى</option>
                                    <option value="photography">تصوير</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">كل الحالات</option>
                                    <option value="available">متاح</option>
                                    <option value="sold">تم البيع</option>
                                    <option value="auction">مزاد</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="sort" class="form-select">
                                    <option value="created_desc">الأحدث أولاً</option>
                                    <option value="price_asc">السعر (منخفض)</option>
                                    <option value="price_desc">السمر (مرتفع)</option>
                                    <option value="name_asc">الاسم (أ-ي)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- NFT Grid -->
    <div class="row">
        @foreach($nfts as $nft)
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-img-top position-relative">
                    <img src="{{ $nft->image_url ?? asset('images/placeholder-nft.jpg') }}" 
                         class="card-img-top" alt="{{ $nft->name }}" style="height: 200px; object-fit: cover;">
                    @if($nft->status === 'auction')
                    <span class="badge bg-warning position-absolute top-0 end-0 m-2">مزاد</span>
                    @elseif($nft->status === 'sold')
                    <span class="badge bg-success position-absolute top-0 end-0 m-2">تم البيع</span>
                    @endif
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $nft->name }}</h5>
                    <p class="card-text text-muted small">{{ $nft->description }}</p>
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary">{{ $nft->category }}</span>
                            <small class="text-muted">مجموع: {{ $nft->supply ?? 1 }}</small>
                        </div>
                        @if($nft->price)
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h6 mb-0">{{ $nft->price }} ETH</span>
                            @if($nft->status === 'available')
                            <a href="{{ route('blockchain.nfts.buy', $nft->id) }}" class="btn btn-sm btn-primary">شراء</a>
                            @elseif($nft->status === 'auction')
                            <a href="{{ route('blockchain.nfts.auction', $nft->id) }}" class="btn btn-sm btn-warning">مزاد</a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="row">
        <div class="col-12">
            {{ $nfts->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// NFT card hover effects
document.querySelectorAll('.card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
        this.style.transition = 'transform 0.3s ease';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});
</script>
@endsection
