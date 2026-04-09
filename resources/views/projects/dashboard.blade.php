@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">لوحة تحكم المشاريع</h1>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> مشروع جديد
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total_projects'] }}</h4>
                            <p class="mb-0">إجمالي المشاريع</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-project-diagram fa-2x"></i>
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
                            <h4 class="mb-0">{{ $stats['active_projects'] }}</h4>
                            <p class="mb-0">المشاريع النشطة</p>
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
                            <h4 class="mb-0">{{ $stats['completed_projects'] }}</h4>
                            <p class="mb-0">المشاريع المكتملة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                            <h4 class="mb-0">{{ $stats['overdue_projects'] }}</h4>
                            <p class="mb-0">المشاريع المتأخرة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Overview -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">نظرة عامة على الميزانية</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-primary">{{ number_format($stats['total_budget'], 0) }}</h3>
                                <p class="mb-0">إجمالي الميزانية (ريال)</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-success">{{ number_format($stats['total_budget'] * 0.65, 0) }}</h3>
                                <p class="mb-0">الميزانية المستهلكة (ريال)</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-info">{{ number_format($stats['total_budget'] * 0.35, 0) }}</h3>
                                <p class="mb-0">الميزانية المتبقية (ريال)</p>
                            </div>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 65%">
                            65% مستهلك
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Projects & Upcoming Deadlines -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">المشاريع الأخيرة</h5>
                    <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                <div class="card-body">
                    @forelse($recentProjects as $project)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <h6 class="mb-1">{{ $project->name }}</h6>
                                <small class="text-muted">
                                    <i class="fas fa-building"></i> {{ $project->client->name ?? '-' }}
                                    <span class="mx-2">|</span>
                                    <i class="fas fa-calendar"></i> {{ $project->created_at->format('Y-m-d') }}
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-{{ $project->status == 'active' ? 'success' : ($project->status == 'completed' ? 'primary' : 'secondary') }}">
                                    {{ __('projects.status.' . $project->status) }}
                                </span>
                                <br>
                                <small class="text-muted">{{ $project->getProgressPercentage() }}% تقدم</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">لا توجد مشاريع حديثة</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">المواعيد النهائية القادمة</h5>
                    <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                <div class="card-body">
                    @forelse($upcomingDeadlines as $project)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <h6 class="mb-1">{{ $project->name }}</h6>
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> {{ $project->manager->name ?? '-' }}
                                    <span class="mx-2">|</span>
                                    <i class="fas fa-building"></i> {{ $project->client->name ?? '-' }}
                                </small>
                            </div>
                            <div class="text-end">
                                @if($project->end_date->diffInDays(now()) <= 7)
                                    <span class="badge bg-danger">متأخر قريباً</span>
                                @else
                                    <span class="badge bg-warning">قريباً</span>
                                @endif
                                <br>
                                <small class="text-muted">{{ $project->end_date->format('Y-m-d') }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">لا توجد مواعيد نهائية قادمة</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('projects.create') }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-plus"></i> إنشاء مشروع جديد
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('projects.index') }}?status=active" class="btn btn-outline-success w-100 mb-2">
                                <i class="fas fa-play"></i> المشاريع النشطة
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('projects.index') }}?status=completed" class="btn btn-outline-info w-100 mb-2">
                                <i class="fas fa-check"></i> المشاريع المكتملة
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('projects.index') }}?priority=critical" class="btn btn-outline-danger w-100 mb-2">
                                <i class="fas fa-exclamation"></i> المشاريع الحرجة
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
