@extends('layouts.app')

@section('title', 'التقييمات')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3">التقييمات</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('appraisals.calendar') }}" class="btn btn-info">
                        <i class="fas fa-calendar"></i> التقويم
                    </a>
                    <a href="{{ route('appraisals.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt"></i> لوحة التحكم
                    </a>
                    <a href="{{ route('appraisals.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> تقييم جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('appraisals.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>مجدول</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الأولوية</label>
                        <select name="priority" class="form-select">
                            <option value="">الكل</option>
                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>عاجل</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالي</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسط</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفض</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">نوع التقييم</label>
                        <select name="type" class="form-select">
                            <option value="">الكل</option>
                            <option value="market_value" {{ request('type') == 'market_value' ? 'selected' : '' }}>قيمة السوق</option>
                            <option value="insurance" {{ request('type') == 'insurance' ? 'selected' : '' }}>تأمين</option>
                            <option value="tax" {{ request('type') == 'tax' ? 'selected' : '' }}>ضريبة</option>
                            <option value="refinance" {{ request('type') == 'refinance' ? 'selected' : '' }}>إعادة تمويل</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">المقيم</label>
                        <select name="appraiser_id" class="form-select">
                            <option value="">الكل</option>
                            @foreach($appraisers ?? [] as $appraiser)
                                <option value="{{ $appraiser->id }}" {{ request('appraiser_id') == $appraiser->id ? 'selected' : '' }}>
                                    {{ $appraiser->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> بحث
                        </button>
                        <a href="{{ route('appraisals.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> مسح
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Appraisals Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>رقم التقييم</th>
                            <th>العقار</th>
                            <th>المقيم</th>
                            <th>التاريخ</th>
                            <th>النوع</th>
                            <th>الحالة</th>
                            <th>الأولوية</th>
                            <th>القيمة التقديرية</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appraisals as $appraisal)
                            <tr>
                                <td>#{{ $appraisal->id }}</td>
                                <td>
                                    <a href="{{ route('properties.show', $appraisal->property) }}">
                                        {{ $appraisal->property->title }}
                                    </a>
                                </td>
                                <td>{{ $appraisal->appraiser->name }}</td>
                                <td>{{ $appraisal->scheduled_date->format('Y-m-d H:i') }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $appraisal->getTypeLabel() }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $appraisal->status == 'completed' ? 'success' : ($appraisal->status == 'cancelled' ? 'danger' : 'warning') }}">
                                        {{ $appraisal->getStatusLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $appraisal->priority == 'urgent' ? 'danger' : ($appraisal->priority == 'high' ? 'warning' : 'info') }}">
                                        {{ $appraisal->getPriorityLabel() }}
                                    </span>
                                </td>
                                <td>
                                    @if($appraisal->hasReport())
                                        <span class="text-primary fw-bold">
                                            {{ number_format($appraisal->getEstimatedValue(), 2) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('appraisals.show', $appraisal) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($appraisal->canBeEdited())
                                            <a href="{{ route('appraisals.edit', $appraisal) }}" class="btn btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if($appraisal->canStart())
                                            <form action="{{ route('appraisals.start', $appraisal) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($appraisal->canComplete())
                                            <a href="{{ route('appraisals.complete', $appraisal) }}" class="btn btn-outline-info">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        @endif
                                        @if($appraisal->canBeCancelled())
                                            <form action="{{ route('appraisals.cancel', $appraisal) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('هل أنت متأكد من إلغاء هذا التقييم؟')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">لا توجد تقييمات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    عرض {{ $appraisals->firstItem() }} - {{ $appraisals->lastItem() }} من {{ $appraisals->total() }}
                </div>
                {{ $appraisals->links() }}
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-primary">{{ $stats['total'] ?? 0 }}</h4>
                    <small class="text-muted">إجمالي التقييمات</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-success">{{ $stats['completed'] ?? 0 }}</h4>
                    <small class="text-muted">مكتملة</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-warning">{{ $stats['in_progress'] ?? 0 }}</h4>
                    <small class="text-muted">قيد التنفيذ</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-info">{{ number_format($stats['total_value'] ?? 0, 2) }}</h4>
                    <small class="text-muted">إجمالي القيم التقديرية</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
