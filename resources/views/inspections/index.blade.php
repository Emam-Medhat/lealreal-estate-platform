@extends('layouts.app')

@section('title', 'الفحوصات')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3">الفحوصات</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('inspections.calendar') }}" class="btn btn-info">
                        <i class="fas fa-calendar"></i> التقويم
                    </a>
                    <a href="{{ route('inspections.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt"></i> لوحة التحكم
                    </a>
                    <a href="{{ route('inspections.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> فحص جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('inspections.index') }}">
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
                        <label class="form-label">نوع الفحص</label>
                        <select name="type" class="form-select">
                            <option value="">الكل</option>
                            <option value="routine" {{ request('type') == 'routine' ? 'selected' : '' }}>روتيني</option>
                            <option value="detailed" {{ request('type') == 'detailed' ? 'selected' : '' }}>مفصل</option>
                            <option value="pre_sale" {{ request('type') == 'pre_sale' ? 'selected' : '' }}>قبل البيع</option>
                            <option value="post_repair" {{ request('type') == 'post_repair' ? 'selected' : '' }}>بعد الإصلاح</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">المفتش</label>
                        <select name="inspector_id" class="form-select">
                            <option value="">الكل</option>
                            @foreach($inspectors ?? [] as $inspector)
                                <option value="{{ $inspector->id }}" {{ request('inspector_id') == $inspector->id ? 'selected' : '' }}>
                                    {{ $inspector->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> بحث
                        </button>
                        <a href="{{ route('inspections.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> مسح
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Inspections Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>رقم الفحص</th>
                            <th>العقار</th>
                            <th>المفتش</th>
                            <th>التاريخ</th>
                            <th>النوع</th>
                            <th>الحالة</th>
                            <th>الأولوية</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inspections as $inspection)
                            <tr>
                                <td>#{{ $inspection->id }}</td>
                                <td>
                                    <a href="{{ route('properties.show', $inspection->property) }}">
                                        {{ $inspection->property->title }}
                                    </a>
                                </td>
                                <td>{{ $inspection->inspector->name }}</td>
                                <td>{{ $inspection->scheduled_date->format('Y-m-d H:i') }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $inspection->getTypeLabel() }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $inspection->status == 'completed' ? 'success' : ($inspection->status == 'cancelled' ? 'danger' : 'warning') }}">
                                        {{ $inspection->getStatusLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $inspection->priority == 'urgent' ? 'danger' : ($inspection->priority == 'high' ? 'warning' : 'info') }}">
                                        {{ $inspection->getPriorityLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('inspections.show', $inspection) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($inspection->canBeEdited())
                                            <a href="{{ route('inspections.edit', $inspection) }}" class="btn btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if($inspection->canStart())
                                            <form action="{{ route('inspections.start', $inspection) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($inspection->canComplete())
                                            <a href="{{ route('inspections.complete', $inspection) }}" class="btn btn-outline-info">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        @endif
                                        @if($inspection->canBeCancelled())
                                            <form action="{{ route('inspections.cancel', $inspection) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('هل أنت متأكد من إلغاء هذا الفحص؟')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">لا توجد فحوصات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    عرض {{ $inspections->firstItem() }} - {{ $inspections->lastItem() }} من {{ $inspections->total() }}
                </div>
                {{ $inspections->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
