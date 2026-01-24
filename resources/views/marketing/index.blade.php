@extends('layouts.app')

@section('title')
    التسويق العقاري المتقدم
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">التسويق العقاري المتقدم</h1>
            <p class="text-muted mb-0">إدارة حملات التسويق العقاري الشاملة</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('marketing.property-marketing.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                حملة تسويق جديدة
            </a>
            <button class="btn btn-outline-secondary" onclick="exportData('csv')">
                <i class="fas fa-download me-1"></i>
                تصدير
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">إجمالي الحملات</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_campaigns']) }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">الحملات النشطة</h6>
                            <h3 class="mb-0">{{ number_format($stats['active_campaigns']) }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-play-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">الحملات المجدولة</h6>
                            <h3 class="mb-0">{{ number_format($stats['scheduled_campaigns']) }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-calendar-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">الحملات المكتملة</h6>
                            <h3 class="mb-0">{{ number_format($stats['completed_campaigns']) }}</h3>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">إجراءات سريعة</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('marketing.property-marketing.create') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-bullhorn me-2"></i>
                                إنشاء حملة تسويق
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('marketing.listing-promotion.create') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-star me-2"></i>
                                ترويج إعلان
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('marketing.email-campaign.create') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-envelope me-2"></i>
                                حملة بريد
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('marketing.social-media-post.create') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-share-alt me-2"></i>
                                منشور اجتماعي
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Marketing Channels Overview -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قنوات التسويق</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="border rounded p-3">
                                <i class="fas fa-bullhorn fa-2x text-primary mb-2"></i>
                                <h6>التسويق العقاري</h6>
                                <small class="text-muted">{{ number_format($stats['property_marketing_count'] ?? 0) }}</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="border rounded p-3">
                                <i class="fas fa-star fa-2x text-success mb-2"></i>
                                <h6>ترويج الإعلانات</h6>
                                <small class="text-muted">{{ number_format($stats['listing_promotion_count'] ?? 0) }}</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="border rounded p-3">
                                <i class="fas fa-envelope fa-2x text-info mb-2"></i>
                                <h6>البريد الإلكتروني</h6>
                                <small class="text-muted">{{ number_format($stats['email_campaign_count'] ?? 0) }}</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="border rounded p-3">
                                <i class="fas fa-share-alt fa-2x text-warning mb-2"></i>
                                <h6>وسائل التواصل</h6>
                                <small class="text-muted">{{ number_format($stats['social_media_count'] ?? 0) }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">الأداء الرقمي</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="border rounded p-3">
                                <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>
                                <h6>الكتيبات</h6>
                                <small class="text-muted">{{ number_format($stats['brochure_count'] ?? 0) }}</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="border rounded p-3">
                                <i class="fas fa-video fa-2x text-purple mb-2"></i>
                                <h6>الفيديوهات</h6>
                                <small class="text-muted">{{ number_format($stats['video_count'] ?? 0) }}</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="border rounded p-3">
                                <i class="fas fa-plane fa-2x text-primary mb-2"></i>
                                <h6>الدرون</h6>
                                <small class="text-muted">{{ number_format($stats['drone_count'] ?? 0) }}</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="border rounded p-3">
                                <i class="fas fa-users fa-2x text-success mb-2"></i>
                                <h6>المؤثرين</h6>
                                <small class="text-muted">{{ number_format($stats['influencer_count'] ?? 0) }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaigns Table -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">الحملات الحالية</h5>
                <div class="d-flex gap-2">
                    <select class="form-select" id="statusFilter">
                        <option value="">جميع الحالات</option>
                        <option value="active">نشطة</option>
                        <option value="scheduled">مجدولة</option>
                        <option value="completed">مكتملة</option>
                        <option value="paused">موقفة</option>
                    </select>
                    <select class="form-select" id="typeFilter">
                        <option value="">جميع الأنواعد</option>
                        <option value="property_marketing">تسويق عقاري</option>
                        <option value="listing_promotion">ترويج إعلان</option>
                        <option value="email_campaign">حملة بريد</option>
                        <option value="social_media">منشور اجتماعي</option>
                    </select>
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" placeholder="بحث..." id="searchInput">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(request()->has('campaigns'))
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>العنوان</th>
                                <th>العقار</th>
                                <th>النوع</th>
th>الحالة</th>
                                <th>الميزانية</th>
                                <th>البدء</th>
                                <th>الإنهاء</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($campaigns as $campaign)
                                <tr>
                                    <td>
                                        <strong>{{ $campaign->title }}</strong>
                                        @if($campaign->property)
                                            <br><small class="text-muted">{{ $campaign->property->title }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($campaign->status)
                                            @case('active')
                                                <span class="badge bg-success">نشطة</span>
                                            @case('scheduled')
                                                <span class="badge bg-info">مجدولة</span>
                                            @case('completed')
                                                <span class="badge bg-secondary">مكتملة</span>
                                            @case('paused')
                                                <span class="badge bg-warning">موقفة</span>
                                            @default
                                                <span class="badge bg-secondary">{{ $campaign->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @switch($campaign->campaign_type ?? 'property_marketing')
                                            @case('property_marketing')
                                                <span class="badge bg-primary">تسويق عقاري</span>
                                            @case('listing_promotion')
                                                <span class="badge bg-success">ترويج إعلان</span>
                                            @case('email_campaign')
                                                <span class="badge bg-info">حملة بريد</span>
                                            @case('social_media')
                                                <span class="badge bg-warning">منشور اجتماعي</span>
                                            @default
                                                <span class="badge bg-secondary">{{ $campaign->campaign_type }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($campaign->budget)
                                            {{ number_format($campaign->budget, 2) }} {{ $campaign->currency ?? 'SAR' }}
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>{{ $campaign->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $campaign->updated_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('marketing.' . ($campaign->campaign_type ?? 'property-marketing') . '.show', $campaign) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('marketing.' . ($campaign->campaign_type ?? 'property-marketing') . '.edit', $campaign) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteCampaign({{ $campaign->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="text-muted">
                        عرض {{ $campaigns->firstItem() }} - {{ $campaigns->lastItem() }} من إجمالي {{ $campaigns->total() }}
                    </span>
                    {{ $campaigns->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد حملات حالياً</h5>
                    <p class="text-muted">ابدأ بإنشاء حملة تسويق جديدة للبدء.</p>
                    <a href="{{ route('marketing.property-marketing.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        إنشاء حملة جديدة
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">تصدير البيانات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>اختر تنسيق التصدير:</p>
                <div class="list-group">
                    <button class="list-group-item list-group-item-action" onclick="exportData('csv')">
                        <i class="fas fa-file-csv me-2"></i>
                        CSV
                    </button>
                    <button class="list-group-item list-group-item-action" onclick="exportData('excel')">
                        <i class="fas fa-file-excel me-2"></i>
                        Excel
                    </button>
                    <button class="list-group-item list-group-item-action" onclick="exportData('pdf')">
                        <i class="fas fa-file-pdf me-2"></i>
                        PDF
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلق</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-body .row > div > div {
        transition: transform 0.2s;
    }
    .card-body .row > div > div:hover {
        transform: translateY(-2px);
    }
</style>
@endpush

@push('scripts')
<script>
    function exportData(format) {
        const url = `{{ route('marketing.export') }}?format=${format}`;
        window.open(url, '_blank');
    }

    function deleteCampaign(id) {
        if (confirm('هل أنت متأكد من حذف هذه الحملة؟')) {
            fetch(`/marketing/campaigns/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('حدث خطأء: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأأء أثناء حذف الحملة');
            });
        }
    }

    // Filter functionality
    document.getElementById('statusFilter').addEventListener('change', function() {
        const url = new URL(window.location);
        url.searchParams.set('status', this.value);
        window.location.href = url.toString();
    });

    document.getElementById('typeFilter').addEventListener('change', function() {
        const url = new URL(window.location);
        url.searchParams.set('type', this.value);
        window.location.href = url.toString();
    });

    // Search functionality
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const url = new URL(window.location);
            url.searchParams.set('search', this.value);
            window.location.href = url.toString();
        }, 500);
    });
</script>
@endpush
