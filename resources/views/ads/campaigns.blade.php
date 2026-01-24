@extends('layouts.app')

@section('title', 'الحملات الإعلانية')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">الحملات الإعلانية</h1>
                <div>
                    <a href="{{ route('ads.campaigns.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إنشاء حملة جديدة
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
                                    <h4 class="card-title">{{ $campaigns->count() }}</h4>
                                    <p class="card-text">إجمالي الحملات</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-bullhorn fa-2x"></i>
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
                                    <h4 class="card-title">{{ $campaigns->where('status', 'active')->count() }}</h4>
                                    <p class="card-text">حملات نشطة</p>
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
                                    <h4 class="card-title">{{ number_format($campaigns->sum('total_spent'), 2) }} ريال</h4>
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
                                    <h4 class="card-title">{{ number_format($campaigns->sum('total_impressions')) }}</h4>
                                    <p class="card-text">إجمالي الظهور</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-eye fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('ads.campaigns.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="search">بحث</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="بحث في الحملات...">
                            </div>
                            <div class="col-md-2">
                                <label for="objective">الهدف</label>
                                < ​​<select .
                                <select">
                                   _.php
/device/3. php
                                <select class="form-select" id="objective" name="objective">
                                    <option value="">جميع الأهداف</option>
                                    <option value="awareness" {{ request('objective') == 'awareness' ? 'selected' : '' }}>زيادة الوعي</option>
                                    <option value="traffic" {{ request('objective') == 'traffic' ? 'selected' : '' }}>زيادة الزيارات</option>
                                    <option value="conversions" {{ request('objective') == 'conversions' ? 'selected' : '' }}>زيادة التحويلات</option>
                                    <option value="engagement" {{ request('objective') == 'engagement' ? 'selected' : '' }}>زيادة التفاعل</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status">الحالة</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">جميع الحالات</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>موقف</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> بحث
                                    </button>
                                    <a href="{{ route('ads.campaigns.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-redo"></i> إعادة تعيين
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Campaigns Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>الحملة</th>
                                    <th>الهدف</th>
                                    <th>الحالة</th>
                                    <th>الإعلانات</th>
                                    <th>الأداء</th>
                                    <th>الميزانية</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campaigns as $campaign)
                                    <tr>
                                        <td>
                                            <div>
                                                <div class="fw-bold">{{ $campaign->name }}</div>
                                                <small class="text-muted">{{ Str::limit($campaign->description, 50) }}</small>
                                                <br><small class="text-muted">
                                                    {{ $campaign->start_date->format('Y-m-d') }} - {{ $campaign->end_date->format('Y-m-d') }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $campaign->objective == 'awareness' ? 'info' : ($campaign->objective == 'traffic' ? 'primary' : ($campaign->objective == 'conversions' ? 'success' : 'warning')) }}">
                                                {{ $campaign->objective_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $campaign->status == 'active' ? 'success' : ($campaign->status == 'paused' ? 'warning' : ($campaign->status == 'draft' ? 'info' : 'secondary')) }}">
                                                {{ $campaign->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <div class="fw-bold">{{ $campaign->ads->count() }}</div>
                                                <small class="text-muted">
                                                    {{ $campaign->ads->where('status', 'active')->count() }} نشط
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div>ظهور: {{ number_format($campaign->total_impressions) }}</div>
                                                <div>نقرات: {{ number_format($campaign->total_clicks) }}</div>
                                                <div>CTR: {{ number_format($campaign->average_ctr, 2) }}%</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div>الإجمالي: {{ number_format($campaign->total_spent, 2) }}</div>
                                                <div>المتبقي: {{ number_format($campaign->budget->remaining_budget ?? 0, 2) }}</div>
                                                <div class="progress mt-1" style="height: 4px;">
                                                    @php $width = $campaign->budget_utilization; @endphp
                                                <div class="progress-bar bg-primary" style="width: {{ $width }}%;"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('ads.campaigns.show', $campaign->id) }}" class="btn btn-sm btn-outline-primary" title="عرض">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('ads.campaigns.edit', $campaign->id) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($campaign->status == 'draft')
                                                    <form action="{{ route('ads.campaigns.launch', $campaign->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="إطلاق">
                                                            <i class="fas fa-rocket"></i>
                                                        </button>
                                                    </form>
                                                @elseif($campaign->status == 'active')
                                                    <form action="{{ route('ads.campaigns.pause', $campaign->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="إيقاف">
                                                            <i class="fas fa-pause"></i>
                                                        </button>
                                                    </form>
                                                @elseif($campaign->status == 'paused')
                                                    <form action="{{ route('ads.campaigns.resume', $campaign->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="استئناف">
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('ads.campaigns.duplicate', $campaign->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-info" title="تكرار">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('ads.campaigns.destroy', $campaign->id) }}" method="POST" class="d-inline" 
                                                      onsubmit="return confirm('هل أنت متأكد من حذف هذه الحملة؟')">
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
                                        <td colspan="7" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">لا توجد حملات حالياً</p>
                                                <a href="{{ route('ads.campaigns.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> إنشاء حملة جديدة
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
                            عرض {{ $campaigns->firstItem() }} - {{ $campaigns->lastItem() }} من {{ $campaigns->total() }} حملة
                        </div>
                        {{ $campaigns->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
