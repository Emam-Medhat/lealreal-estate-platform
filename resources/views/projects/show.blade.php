@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Project Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $project->name }}</h1>
            <p class="text-muted mb-0">
                <i class="fas fa-map-marker-alt"></i> {{ $project->location ?? 'غير محدد' }}
                <span class="mx-2">|</span>
                <i class="fas fa-building"></i> {{ $project->client->name ?? '-' }}
                <span class="mx-2">|</span>
                <i class="fas fa-user"></i> {{ $project->manager->name ?? '-' }}
            </p>
        </div>
        <div class="btn-group">
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-primary">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <button type="button" class="btn btn-outline-info" onclick="window.location.href='{{ route('projects.gantt', $project) }}'">
                <i class="fas fa-chart-gantt"></i> الجدول الزمني
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='{{ route('projects.kanban', $project) }}'">
                <i class="fas fa-columns"></i> كانبان
            </button>
        </div>
    </div>

    <!-- Project Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">التقدم العام</h5>
                    <h3>{{ $stats['progress_percentage'] }}%</h3>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-white" style="width: {{ $stats['progress_percentage'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">المهام</h5>
                    <h3>{{ $stats['completed_tasks'] }}/{{ $stats['total_tasks'] }}</h3>
                    <small>مكتمل / الإجمالي</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">الميزانية</h5>
                    <h3>{{ number_format($stats['total_spent'], 0) }}</h3>
                    <small>من {{ number_format($project->budget, 0) }} ريال</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">المراحل</h5>
                    <h3>{{ $stats['completed_milestones'] }}/{{ $stats['total_milestones'] }}</h3>
                    <small>مراحل مكتملة / الإجمالي</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Details -->
    <div class="row">
        <div class="col-md-8">
            <!-- Description -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">وصف المشروع</h5>
                </div>
                <div class="card-body">
                    <p>{{ $project->description ?? 'لا يوجد وصف' }}</p>
                    
                    @if($project->features)
                        <h6 class="mt-3">المميزات:</h6>
                        <ul>
                            @foreach($project->features as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Phases -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">مراحل المشروع</h5>
                    <a href="{{ route('projects.phases.create', $project) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> مرحلة جديدة
                    </a>
                </div>
                <div class="card-body">
                    @forelse($project->phases as $phase)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <h6 class="mb-1">{{ $phase->name }}</h6>
                                <small class="text-muted">{{ $phase->start_date->format('Y-m-d') }} - {{ $phase->end_date->format('Y-m-d') }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-{{ $phase->status == 'completed' ? 'success' : ($phase->status == 'in_progress' ? 'primary' : 'secondary') }}">
                                    {{ __('phases.status.' . $phase->status) }}
                                </span>
                                <div class="progress mt-2" style="width: 100px; height: 5px;">
                                    <div class="progress-bar" style="width: {{ $phase->progress_percentage }}%"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">لا توجد مراحل بعد</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Tasks -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">المهام الأخيرة</h5>
                    <a href="{{ route('projects.tasks.index', $project) }}" class="btn btn-sm btn-outline-primary">
                        عرض الكل
                    </a>
                </div>
                <div class="card-body">
                    @forelse($project->tasks()->latest()->take(5)->get() as $task)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <h6 class="mb-1">{{ $task->name }}</h6>
                                <small class="text-muted">
                                    @if($task->assignee)
                                        <i class="fas fa-user"></i> {{ $task->assignee->name }}
                                    @endif
                                    <span class="mx-2">|</span>
                                    <i class="fas fa-calendar"></i> {{ $task->due_date->format('Y-m-d') }}
                                </small>
                            </div>
                            <span class="badge bg-{{ $task->status == 'completed' ? 'success' : ($task->status == 'in_progress' ? 'primary' : 'secondary') }}">
                                {{ __('tasks.status.' . $task->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted text-center">لا توجد مهام بعد</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Project Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">معلومات المشروع</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>نوع المشروع:</strong></td>
                            <td>{{ __('projects.type.' . $project->project_type) }}</td>
                        </tr>
                        <tr>
                            <td><strong>تاريخ البدء:</strong></td>
                            <td>{{ $project->start_date->format('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <td><strong>تاريخ الانتهاء:</strong></td>
                            <td>{{ $project->end_date->format('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <td><strong>الأيام المتبقية:</strong></td>
                            <td>
                                @if($stats['days_remaining'] < 0)
                                    <span class="text-danger">متأخر {{ abs($stats['days_remaining']) }} يوم</span>
                                @else
                                    {{ $stats['days_remaining'] }} يوم
                                @endif
                            </td>
                        </tr>
                        @if($project->total_units)
                            <tr>
                                <td><strong>إجمالي الوحدات:</strong></td>
                                <td>{{ $project->total_units }}</td>
                            </tr>
                        @endif
                        @if($project->total_area)
                            <tr>
                                <td><strong>المساحة الإجمالية:</strong></td>
                                <td>{{ number_format($project->total_area, 2) }} م²</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Team -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">الفريق</h5>
                    <a href="{{ route('projects.teams.index', $project) }}" class="btn btn-sm btn-outline-primary">
                        إدارة
                    </a>
                </div>
                <div class="card-body">
                    @if($project->team && $project->team->members->count() > 0)
                        @foreach($project->team->members->take(5) as $member)
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                    {{ substr($member->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $member->user->name }}</div>
                                    <small class="text-muted">{{ $member->role->name ?? '-' }}</small>
                                </div>
                            </div>
                        @endforeach
                        @if($project->team->members->count() > 5)
                            <small class="text-muted">+{{ $project->team->members->count() - 5 }} أعضاء آخرين</small>
                        @endif
                    @else
                        <p class="text-muted text-center">لا يوجد فريق بعد</p>
                    @endif
                </div>
            </div>

            <!-- Recent Documents -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">الوثائق الأخيرة</h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">
                        عرض الكل
                    </a>
                </div>
                <div class="card-body">
                    @forelse($project->documents()->latest()->take(3)->get() as $document)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <i class="fas fa-file"></i> {{ $document->original_filename }}
                                <br><small class="text-muted">{{ $document->created_at->format('Y-m-d') }}</small>
                            </div>
                            <a href="{{ $document->getDownloadUrl() }}" class="btn btn-sm btn-outline-primary" download>
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    @empty
                        <p class="text-muted text-center">لا توجد وثائق بعد</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
