@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">المشاريع</h1>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> مشروع جديد
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('projects.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="planning" {{ request('status') == 'planning' ? 'selected' : '' }}>التخطيط</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>معلق</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الأولوية</label>
                        <select name="priority" class="form-select">
                            <option value="">الكل</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                            <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>حرجة</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="ابحث عن مشروع...">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary me-2">
                            <i class="fas fa-search"></i> بحث
                        </button>
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> مسح
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>المشروع</th>
                            <th>العميل</th>
                            <th>المدير</th>
                            <th>الحالة</th>
                            <th>الأولوية</th>
                            <th>التقدم</th>
                            <th>الميزانية</th>
                            <th>تاريخ الانتهاء</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $project->name }}</strong>
                                        @if($project->location)
                                            <br><small class="text-muted"><i class="fas fa-map-marker-alt"></i> {{ $project->location }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $project->client->name ?? '-' }}</td>
                                <td>{{ $project->manager->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $project->status == 'active' ? 'success' : ($project->status == 'completed' ? 'primary' : ($project->status == 'on_hold' ? 'warning' : 'secondary')) }}">
                                        {{ __('projects.status.' . $project->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $project->priority == 'critical' ? 'danger' : ($project->priority == 'high' ? 'warning' : ($project->priority == 'medium' ? 'info' : 'secondary')) }}">
                                        {{ __('projects.priority.' . $project->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $project->getProgressPercentage() }}%">
                                            {{ $project->getProgressPercentage() }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ number_format($project->budget, 2) }} ريال
                                    <br><small class="text-muted">مُستهلك: {{ number_format($project->getTotalSpent(), 2) }}</small>
                                </td>
                                <td>
                                    {{ $project->end_date->format('Y-m-d') }}
                                    @if($project->isOverdue())
                                        <br><span class="text-danger"><i class="fas fa-exclamation-triangle"></i> متأخر</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-info" title="الجدول الزمني" onclick="window.location.href='{{ route('projects.gantt', $project) }}'">
                                            <i class="fas fa-chart-gantt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">لا توجد مشاريع</p>
                                    <a href="{{ route('projects.create') }}" class="btn btn-primary">إنشاء مشروع جديد</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    عرض {{ $projects->firstItem() }} - {{ $projects->lastItem() }} من {{ $projects->total() }} مشروع
                </div>
                {{ $projects->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
