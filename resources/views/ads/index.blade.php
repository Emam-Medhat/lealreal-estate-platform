@extends('layouts.app')

@section('title', 'الإعلانات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">الإعلانات</h1>
                <div>
                    <a href="{{ route('ads.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة إعلان جديد
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
                                    <h4 class="card-title">{{ $ads->sum('impressions_count') }}</h4>
                                    <p class="card-text">إجمالي الظهور</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-eye fa-2x"></i>
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
                                    <h4 class="card-title">{{ $ads->sum('clicks_count') }}</h4>
                                    <p class="card-text">إجمالي النقرات</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-mouse-pointer fa-2x"></i>
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
                                    <h4 class="card-title">{{ number_format($ads->sum('total_spent'), 2) }} ريال</h4>
                                    <p class="card-text">إجمالي الإنفاق</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-money-bill-wave fa-2x"></i>
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
                                    <h4 class="card-title">{{ $ads->where('status', 'active')->count() }}</h4>
                                    <p class="card-text">إعلانات نشطة</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-play-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('ads.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="search">بحث</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="بحث في الإعلانات...">
                            </div>
                            <div class="col-md-2">
                                <label for="type">النوع</label>
                                <select class="form-select" id="type" name="type">
                                    <option value="">جميع الأنواع</option>
                                    <option value="banner" {{ request('type') == 'banner' ? 'selected' : '' }}>بانر</option>
                                    <option value="native" {{ request('type') == 'native' ? 'selected' : '' }}>أصلي</option>
                                    <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>فيديو</option>
                                    <option value="popup" {{ request('type') == 'popup' ? 'selected' : '' }}>نافذة منبثقة</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status">الحالة</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">جميع الحالات</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>موقف</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="approval">الموافقة</label>
                                <select class="form-select" id="approval" name="approval">
                                    <option value="">جميع الموافقات</option>
                                    <option value="pending" {{ request('approval') == 'pending' ? 'selected' : '' }}>في انتظار</option>
                                    <option value="approved" {{ request('approval') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                                    <option value="rejected" {{ request('approval') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> بحث
                                    </button>
                                    <a href="{{ route('ads.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-redo"></i> إعادة تعيين
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Ads Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>الإعلان</th>
                                    <th>النوع</th>
                                    <th>الحملة</th>
                                    <th>الحالة</th>
                                    <th>الموافقة</th>
                                    <th>الأداء</th>
                                    <th>الميزانية</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ads as $ad)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($ad->image_url)
                                                    <img src="{{ $ad->image_url_full }}" alt="{{ $ad->title }}" 
                                                         class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-secondary rounded me-2 d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-ad text-white"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-bold">{{ $ad->title }}</div>
                                                    <small class="text-muted">{{ Str::limit($ad->description, 50) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $ad->type == 'banner' ? 'primary' : ($ad->type == 'video' ? 'danger' : 'info') }}">
                                                {{ $ad->type == 'banner' ? 'بانر' : ($ad->type == 'video' ? 'فيديو' : ($ad->type == 'native' ? 'أصلي' : 'منبثق')) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($ad->campaign)
                                                <a href="{{ route('campaigns.show', $ad->campaign->id) }}" 
                                                   class="text-decoration-none">{{ $ad->campaign->name }}</a>
                                            @else
                                                <span class="text-muted">بدون حملة</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $ad->status == 'active' ? 'success' : ($ad->status == 'paused' ? 'warning' : ($ad->status == 'draft' ? 'info' : 'secondary')) }}">
                                                {{ $ad->status == 'active' ? 'نشط' : ($ad->status == 'paused' ? 'موقف' : ($ad->status == 'draft' ? 'مسودة' : 'غير نشط')) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $ad->approval_status == 'approved' ? 'success' : ($ad->approval_status == 'rejected' ? 'danger' : 'warning') }}">
                                                {{ $ad->approval_status == 'approved' ? 'موافق عليه' : ($ad->approval_status == 'rejected' ? 'مرفوض' : 'في انتظار') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div>ظهور: {{ number_format($ad->impressions_count) }}</div>
                                                <div>نقرات: {{ number_format($ad->clicks_count) }}</div>
                                                <div>CTR: {{ number_format($ad->impressions_count > 0 ? ($ad->clicks_count / $ad->impressions_count) * 100 : 0, 2) }}%</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div>الإجمالي: {{ number_format($ad->total_spent, 2) }}</div>
                                                <div>المتبقي: {{ number_format($ad->campaign->budget->remaining_budget ?? 0, 2) }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('ads.show', $ad->id) }}" class="btn btn-sm btn-outline-primary" title="عرض">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('ads.edit', $ad->id) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($ad->status == 'active')
                                                    <form action="{{ route('ads.pause', $ad->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="إيقاف">
                                                            <i class="fas fa-pause"></i>
                                                        </button>
                                                    </form>
                                                @elseif($ad->status == 'paused')
                                                    <form action="{{ route('ads.resume', $ad->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="استئناف">
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('ads.duplicate', $ad->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-info" title="تكرار">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </form>
                                                @if(auth()->user()->role === 'admin')
                                                    @if($ad->approval_status == 'pending')
                                                        <form action="{{ route('ads.approve', $ad->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-success" title="موافقة">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('ads.reject', $ad->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="رفض">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                                <form action="{{ route('ads.destroy', $ad->id) }}" method="POST" class="d-inline" 
                                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا الإعلان؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-ad fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">لا توجد إعلانات حالياً</p>
                                                <a href="{{ route('ads.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> إضافة إعلان جديد
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            عرض {{ $ads->firstItem() }} - {{ $ads->lastItem() }} من {{ $ads->total() }} إعلان
                        </div>
                        {{ $ads->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
