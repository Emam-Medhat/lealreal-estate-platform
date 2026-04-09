@extends('layouts.app')

@section('title', 'جدولة الفحوصات')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3">جدولة الفحوصات</h1>
                <a href="{{ route('inspections.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> فحص جديد
                </a>
            </div>
        </div>
    </div>

    <!-- Calendar View -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt"></i>
                    {{ now()->format('F Y') }}
                </h5>
                <div class="btn-group">
                    <button class="btn btn-outline-primary btn-sm" onclick="changeMonth(-1)">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <button class="btn btn-outline-primary btn-sm" onclick="changeMonth(1)">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="table-responsive">
                <table class="table table-bordered calendar-table">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">الأحد</th>
                            <th class="text-center">الاثنين</th>
                            <th class="text-center">الثلاثاء</th>
                            <th class="text-center">الأربعاء</th>
                            <th class="text-center">الخميس</th>
                            <th class="text-center">الجمعة</th>
                            <th class="text-center">السبت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($week = 0; $week < 6; $week++)
                            <tr>
                                @for($day = 0; $day < 7; $day++)
                                    @php
                                        $currentDate = now()->startOfMonth()->addWeeks($week)->startOfWeek()->addDays($day);
                                        $dayInspections = $inspections->where('scheduled_date', $currentDate->format('Y-m-d'));
                                    @endphp
                                    <td class="calendar-day" data-date="{{ $currentDate->format('Y-m-d') }}">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <span class="day-number {{ $currentDate->isToday() ? 'today' : '' }}">
                                                {{ $currentDate->day }}
                                            </span>
                                            @if($dayInspections->count() > 0)
                                                <span class="badge bg-primary rounded-pill">{{ $dayInspections->count() }}</span>
                                            @endif
                                        </div>
                                        @if($dayInspections->count() > 0)
                                            <div class="mt-2">
                                                @foreach($dayInspections->take(3) as $inspection)
                                                    <div class="small inspection-item mb-1" style="background: {{ $inspection->priority == 'urgent' ? '#dc3545' : ($inspection->priority == 'high' ? '#ffc107' : '#17a2b8') }}; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px;">
                                                        <a href="{{ route('inspections.show', $inspection) }}" class="text-white text-decoration-none">
                                                            {{ $inspection->property->title }} - {{ $inspection->scheduled_date->format('H:i') }}
                                                        </a>
                                                    </div>
                                                @endforeach
                                                @if($dayInspections->count() > 3)
                                                    <div class="small text-muted">+{{ $dayInspections->count() - 3 }} أخرى</div>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Upcoming Inspections -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-clock"></i>
                الفحوصات القادمة (7 أيام)
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>التاريخ والوقت</th>
                            <th>العقار</th>
                            <th>المفتش</th>
                            <th>النوع</th>
                            <th>الأولوية</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($upcomingInspections as $inspection)
                            <tr>
                                <td>
                                    {{ $inspection->scheduled_date->format('Y-m-d H:i') }}
                                    @if($inspection->scheduled_date->diffInDays(now()) <= 1)
                                        <span class="badge bg-danger ms-1">قريباً</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('properties.show', $inspection->property) }}">
                                        {{ $inspection->property->title }}
                                    </a>
                                </td>
                                <td>{{ $inspection->inspector->name }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $inspection->getTypeLabel() }}</span>
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
                                        <a href="{{ route('inspections.edit', $inspection) }}" class="btn btn-outline-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('inspections.reschedule', $inspection) }}" class="btn btn-outline-info">
                                            <i class="fas fa-calendar"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">لا توجد فحوصات قادمة</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Inspector Availability -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-user-check"></i>
                توفر المفتشين
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($inspectors ?? [] as $inspector)
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">{{ $inspector->name }}</h6>
                                <div class="small text-muted mb-2">
                                    {{ $inspector->getTodayInspections()->count() }} فحص اليوم
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" style="width: {{ min(($inspector->getTodayInspections()->count() / 8) * 100, 100) }}%"></div>
                                </div>
                                <small class="text-muted">السعة: 8 فحوصات يومياً</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
.calendar-table {
    min-height: 500px;
}

.calendar-day {
    height: 100px;
    vertical-align: top;
    padding: 8px;
}

.calendar-day:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.day-number {
    font-weight: bold;
}

.today {
    background-color: #007bff;
    color: white;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.inspection-item {
    line-height: 1.2;
}

.inspection-item:hover {
    opacity: 0.8;
}
</style>

<script>
function changeMonth(direction) {
    // Implementation for month navigation
    console.log('Changing month:', direction);
}

document.addEventListener('DOMContentLoaded', function() {
    // Click handler for calendar days
    document.querySelectorAll('.calendar-day').forEach(function(day) {
        day.addEventListener('click', function() {
            const date = this.dataset.date;
            window.location.href = `{{ route('inspections.create') }}?date=${date}`;
        });
    });
});
</script>
@endsection
