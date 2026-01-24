@extends('layouts.app')

@section('title', 'العقارات المروجة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">العقارات المروجة</h1>
                <div>
                    <a href="{{ route('ads.promoted-listings.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> ترويج عقار جديد
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ $promotedListings->count() }}</h4>
                                    <p class="card-text">إجمالي العقارات المروجة</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-star fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ $promotedListings->where('status', 'active')->count() }}</h4>
                                    <p class="card-text">عقارات نشطة</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-play-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ number_format($promotedListings->sum('views_count')) }}</h4>
                                    <p class="card-text">إجمالي المشاهدات</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-eye fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ number_format($promotedListings->sum('total_spent'), 2) }} ريال</h4>
                                    <p class="card-text">إجمالي الإنفاق</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-money-bill-wave fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('ads.promoted-listings.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="search">بحث</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="بحث في العقارات المروجة...">
                            </div>
                            <div class="col-md-2">
                                <label for="promotion_type">نوع الترويج</label>
                                <select class="form-select" id="promotion_type" name="promotion_type">
                                    <option value="">جميع الأنواع</option>
                                    <option value="featured" {{ request('promotion_type') == 'featured' ? 'selected' : '' }}>مميز</option>
                                    <option value="premium" {{ request('promotion_type') == 'premium' ? 'selected' : '' }}>مميز</option>
                                    <option value="spotlight" {{ request('promotion_type') == 'spotlight' ? 'selected' : '' }}>مميز</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status">الحالة</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">جميع الحالات</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>موقف</option>
                                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهي</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> بحث
                                    </button>
                                    <a href="{{ route('ads.promoted-listings.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-redo"></i> إعادة تعيين
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
                        <div class="card h-100">
                            <div class="position-relative">
                                @if($promotedListing->property->main_image)
                                    <img src="{{ $promotedListing->property->main_image }}" 
                                         alt="{{ $promotedListing->property->title }}" 
                                         class="card-img-top" style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                         style="height: 200px;">
                                        <i class="fas fa-home fa-3x text-muted"></i>
                                    </div>
                                @endif
                                
                                <!-- Promotion Badge -->
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge bg-{{ $promotedListing->promotion_type == 'featured' ? 'primary' : ($promotedListing->promotion_type == 'premium' ? 'success' : 'warning') }}">
                                        {{ $promotedListing->promotion_type_label }}
                                    </span>
                                </div>
                                
                                <!-- Status Badge -->
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-{{ $promotedListing->status == 'active' ? 'success' : ($promotedListing->status == 'paused' ? 'warning' : 'secondary') }}">
                                        {{ $promotedListing->status_label }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <h6 class="card-title">{{ $promotedListing->property->title }}</h6>
                                <p class="card-text text-muted small">
                                    <i class="fas fa-map-marker-alt"></i> {{ $promotedListing->property->location }}
                                </p>
                                
                                <!-- Promotion Details -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>مدة الترويج:</small>
                                        <small>{{ $promotedListing->duration }} يوم</small>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>الميزانية اليومية:</small>
                                        <small>{{ number_format($promotedListing->daily_budget, 2) }} ريال</small>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>الأيام المتبقية:</small>
                                        <small>{{ $promotedListing->days_remaining }} يوم</small>
                                    </div>
                                </div>
                                
                                <!-- Performance Metrics -->
                                <div class="row text-center mb-3">
                                    <div class="col-4">
                                        <div class="border rounded p-2">
                                            <div class="fw-bold text-primary">{{ number_format($promotedListing->views_count) }}</div>
                                            <small class="text-muted">مشاهدة</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border rounded p-2">
                                            <div class="fw-bold text-success">{{ number_format($promotedListing->clicks_count) }}</div>
                                            <small class="text-muted">نقرة</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border rounded p-2">
                                            <div class="fw-bold text-info">{{ number_format($promotedListing->inquiries_count) }}</div>
                                            <small class="text-muted">استفسار</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Budget Progress -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>استهلاك الميزانية:</small>
                                        <small>{{ number_format($promotedListing->budget_utilization, 1) }}%</small>
                                    </div>
                                    <div class="progress" style="height: 4px;">
                                        @php 
                                    $bgClass = $promotedListing->budget_utilization > 80 ? 'danger' : ($promotedListing->budget_utilization > 50 ? 'warning' : 'success');
                                    $width = $promotedListing->budget_utilization;
                                @endphp
                                        <div class="progress-bar bg-{{ $bgClass }}" 
                                             style="width: {{ $width }}%;"></div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="d-grid gap-2">
                                    <a href="{{ route('ads.promoted-listings.show', $promotedListing->id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> عرض التفاصيل
                                    </a>
                                    <a href="{{ route('properties.show', $promotedListing->property->id) }}" 
                                       class="btn btn-sm btn-outline-secondary" target="_blank">
                                        <i class="fas fa-external-link-alt"></i> عرض العقار
                                    </a>
                                </div>
                            </div>
                            
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        بدأ: {{ $promotedListing->start_date->format('Y-m-d') }}
                                    </small>
                                    <small class="text-muted">
                                        ينتهي: {{ $promotedListing->end_date->format('Y-m-d') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-star fa-3x text-muted mb-3"></i>
                            <p class="text-muted">لا توجد عقارات مروجة حالياً</p>
                            <a href="{{ route('ads.promoted-listings.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> ترويج عقار جديد
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
