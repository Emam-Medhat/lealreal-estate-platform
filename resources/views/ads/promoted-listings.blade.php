@extends('layouts.app')

@section('title', 'العقارات المروجة')

@push('styles')
<style>
    .ads-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .stats-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .stats-card.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .stats-card.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .stats-card.info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        color: white;
    }
    
    .stats-card.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    
    .filter-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .filter-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .listing-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    
    .listing-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .promotion-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .promotion-badge.featured {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .promotion-badge.premium {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    
    .promotion-badge.spotlight {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .status-badge.active {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .status-badge.paused {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    
    .status-badge.expired {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
    }
    
    .metric-box {
        background: linear-gradient(180deg, #f8f9ff 0%, #e8eaff 100%);
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 0.75rem;
        transition: transform 0.2s ease;
    }
    
    .metric-box:hover {
        transform: scale(1.05);
    }
    
    .progress {
        background-color: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .progress-bar {
        border-radius: 10px;
        transition: width 1s ease;
    }
    
    .progress-bar.success {
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    }
    
    .progress-bar.warning {
        background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
    }
    
    .progress-bar.danger {
        background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
    }
    
    .btn-primary-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }
    
    .btn-secondary-custom {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-secondary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
    }
    
    .btn-outline-primary-custom {
        border: 2px solid #667eea;
        color: #667eea;
        border-radius: 25px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-outline-primary-custom:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }
    
    .btn-outline-secondary-custom {
        border: 2px solid #6c757d;
        color: #6c757d;
        border-radius: 25px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-outline-secondary-custom:hover {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .stats-number {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .stats-label {
        font-size: 0.875rem;
        font-weight: 600;
        opacity: 0.9;
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .empty-state {
        padding: 3rem;
        text-align: center;
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
        border-radius: 15px;
    }
    
    .empty-state i {
        color: #667eea;
        margin-bottom: 1rem;
    }
    
    .property-image {
        height: 200px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .listing-card:hover .property-image {
        transform: scale(1.05);
    }
    
    .icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        opacity: 0.9;
    }
</style>
@endpush

@section('content')
<div class="container-fluid fade-in">
    <!-- Page Header -->
    <div class="ads-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-3">
                    <i class="fas fa-star ml-3"></i>
                    العقارات المروجة
                </h1>
                <p class="mb-0 opacity-90">إدارة وتتبع عقاراتك المروجة وحملات الإعلانات</p>
            </div>
            <div class="col-md-4 text-md-left">
                <a href="{{ route('ads.promoted-listings.create') }}" class="btn btn-primary-custom btn-lg">
                    <i class="fas fa-plus ml-2"></i> ترويج عقار جديد
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-number">{{ $promotedListings->count() }}</div>
                            <div class="stats-label">إجمالي العقارات المروجة</div>
                        </div>
                        <div class="icon-circle">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-number">{{ $promotedListings->where('status', 'active')->count() }}</div>
                            <div class="stats-label">عقارات نشطة</div>
                        </div>
                        <div class="icon-circle">
                            <i class="fas fa-play-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-number">{{ number_format($promotedListings->sum('views_count')) }}</div>
                            <div class="stats-label">إجمالي المشاهدات</div>
                        </div>
                        <div class="icon-circle">
                            <i class="fas fa-eye"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-number">{{ number_format($promotedListings->sum('total_spent'), 2) }}</div>
                            <div class="stats-label">إجمالي الإنفاق (ريال)</div>
                        </div>
                        <div class="icon-circle">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

            <!-- Filters -->
            <div class="card filter-card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('ads.promoted-listings.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="search" class="form-label font-weight-bold">
                                    <i class="fas fa-search ml-2"></i>بحث
                                </label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="بحث في العقارات المروجة...">
                            </div>
                            <div class="col-md-2">
                                <label for="promotion_type" class="form-label font-weight-bold">
                                    <i class="fas fa-tag ml-2"></i>نوع الترويج
                                </label>
                                <select class="form-select" id="promotion_type" name="promotion_type">
                                    <option value="">جميع الأنواع</option>
                                    <option value="featured" {{ request('promotion_type') == 'featured' ? 'selected' : '' }}>مميز</option>
                                    <option value="premium" {{ request('promotion_type') == 'premium' ? 'selected' : '' }}>مميز</option>
                                    <option value="spotlight" {{ request('promotion_type') == 'spotlight' ? 'selected' : '' }}>مميز</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label font-weight-bold">
                                    <i class="fas fa-info-circle ml-2"></i>الحالة
                                </label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">جميع الحالات</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>موقف</option>
                                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهي</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary-custom">
                                        <i class="fas fa-search ml-2"></i> بحث
                                    </button>
                                    <a href="{{ route('ads.promoted-listings.index') }}" class="btn btn-outline-secondary-custom">
                                        <i class="fas fa-redo ml-2"></i> إعادة تعيين
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Promoted Listings Grid -->
            <div class="row">
                @forelse($promotedListings as $promotedListing)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card listing-card h-100">
                            <div class="position-relative overflow-hidden">
                                @if($promotedListing->property->main_image)
                                    <img src="{{ $promotedListing->property->main_image }}" 
                                         alt="{{ $promotedListing->property->title }}" 
                                         class="card-img-top property-image">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                         style="height: 200px;">
                                        <i class="fas fa-home fa-3x text-muted"></i>
                                    </div>
                                @endif
                                
                                <!-- Promotion Badge -->
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="promotion-badge {{ $promotedListing->promotion_type }}">
                                        {{ $promotedListing->promotion_type_label }}
                                    </span>
                                </div>
                                
                                <!-- Status Badge -->
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="status-badge {{ $promotedListing->status }}">
                                        {{ $promotedListing->status_label }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <h6 class="card-title font-weight-bold">{{ $promotedListing->property->title }}</h6>
                                <p class="card-text text-muted small">
                                    <i class="fas fa-map-marker-alt ml-2"></i> {{ $promotedListing->property->location }}
                                </p>
                                
                                <!-- Promotion Details -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="font-weight-bold">مدة الترويج:</small>
                                        <small>{{ $promotedListing->duration }} يوم</small>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="font-weight-bold">الميزانية اليومية:</small>
                                        <small>{{ number_format($promotedListing->daily_budget, 2) }} ريال</small>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="font-weight-bold">الأيام المتبقية:</small>
                                        <small>{{ $promotedListing->days_remaining }} يوم</small>
                                    </div>
                                </div>
                                
                                <!-- Performance Metrics -->
                                <div class="row text-center mb-3">
                                    <div class="col-4">
                                        <div class="metric-box">
                                            <div class="fw-bold text-primary">{{ number_format($promotedListing->views_count) }}</div>
                                            <small class="text-muted">مشاهدة</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-box">
                                            <div class="fw-bold text-success">{{ number_format($promotedListing->clicks_count) }}</div>
                                            <small class="text-muted">نقرة</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-box">
                                            <div class="fw-bold text-info">{{ number_format($promotedListing->inquiries_count) }}</div>
                                            <small class="text-muted">استفسار</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Budget Progress -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="font-weight-bold">استهلاك الميزانية:</small>
                                        <small>{{ number_format($promotedListing->budget_utilization, 1) }}%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        @php 
                                    $bgClass = $promotedListing->budget_utilization > 80 ? 'danger' : ($promotedListing->budget_utilization > 50 ? 'warning' : 'success');
                                    $width = $promotedListing->budget_utilization;
                                @endphp
                                        <div class="progress-bar {{ $bgClass }}" 
                                             style="width: {{ $width }}%;"></div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="d-grid gap-2">
                                    <a href="{{ route('ads.promoted-listings.show', $promotedListing->id) }}" 
                                       class="btn btn-outline-primary-custom">
                                        <i class="fas fa-eye ml-2"></i> عرض التفاصيل
                                    </a>
                                    <a href="{{ route('properties.show', $promotedListing->property->id) }}" 
                                       class="btn btn-outline-secondary-custom" target="_blank">
                                        <i class="fas fa-external-link-alt ml-2"></i> عرض العقار
                                    </a>
                                </div>
                            </div>
                            
                            <div class="card-footer bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt ml-2"></i>بدأ: {{ $promotedListing->start_date->format('Y-m-d') }}
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-check ml-2"></i>ينتهي: {{ $promotedListing->end_date->format('Y-m-d') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="fas fa-star fa-4x mb-3"></i>
                            <h4 class="text-gray-700 mb-3">لا توجد عقارات مروجة حالياً</h4>
                            <p class="text-gray-600 mb-4">ابدأ بترويج عقاراتك للوصول إلى المزيد من العملاء</p>
                            <a href="{{ route('ads.promoted-listings.create') }}" class="btn btn-primary-custom btn-lg">
                                <i class="fas fa-plus ml-2"></i> ترويج عقار جديد
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($promotedListings->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $promotedListings->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Quick Actions Modal -->
<div class="modal fade" id="quickActionsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إجراءات سريعة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary" onclick="pausePromotion()">
                        <i class="fas fa-pause"></i> إيقاف الترويج
                    </button>
                    <button type="button" class="btn btn-outline-success" onclick="resumePromotion()">
                        <i class="fas fa-play"></i> استئناف الترويج
                    </button>
                    <button type="button" class="btn btn-outline-info" onclick="extendPromotion()">
                        <i class="fas fa-clock"></i> تمديد الفترة
                    </button>
                    <button type="button" class="btn btn-outline-warning" onclick="upgradePromotion()">
                        <i class=" */
fas faiture fa-upgrade"></ .fa-upgradetovar faisant
                    'upgrade' </button>
                    <button type="button" class="btn btn-outline-danger" onclick="cancelPromotion()">
                        <i class="fas fa-times"></i> إلغاء الترويج
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showQuickActions(promotedListingId) {
    window.currentPromotedListingId = promotedListingId;
    new bootstrap.Modal(document.getElementById('quickActionsModal')).show();
}

function pausePromotion() {
    if (confirm('هل أنت متأكد من إيقاف هذا الترويج؟')) {
        window.location.href = `/ads/promoted-listings/${window.currentPromotedListingId}/pause`;
    }
}

function resumePromotion() {
    if (confirm('هل أنت متأكد من استئناف هذا الترويج؟')) {
        window.location.href = `/ads/promoted-listings/${window.currentPromotedListingId}/resume`;
    }
}

function extendPromotion() {
    const days = prompt('كم يوم تريد إضافة الفترة؟');
    if (days && !isNaN(days)) {
        window.location.href = `/ads/promoted-listings/${window.currentPromotedListingId}/extend?days=${days}`;
    }
}

function upgradePromotion() {
    if (confirm('هل تريد ترقية هذا الترويج؟')) {
        window.location.href = `/ads/promoted-listings/${window.currentPromotedListingId}/upgrade`;
    }
}

function cancelPromotion() {
    if (confirm('هل أنت متأكد من إلغاء هذا الترويج؟')) {
        window.location.href = `/ads/promoted-listings/${window.currentPromotedListingId}/cancel`;
    }
}
</script>
@endsection
