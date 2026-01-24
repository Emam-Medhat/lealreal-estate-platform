@extends('layouts.app')

@section('title', 'فعاليات المجتمع')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">فعاليات المجتمع</h1>
            <p class="text-muted mb-0">استكشف وشارك في فعاليات مجتمعك</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#calendarModal">
                <i class="fas fa-calendar me-2"></i>عرض التقويم
            </button>
            <a href="{{ route('community-events.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>إنشاء فعالية جديدة
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('community-events.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" class="form-control" 
                               value="{{ request('search') }}" placeholder="ابحث في الفعاليات...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">المجتمع</label>
                        <select name="community_id" class="form-select">
                            <option value="">جميع المجتمعات</option>
                            @foreach($communities as $community)
                                <option value="{{ $community->id }}" 
                                        {{ request('community_id') == $community->id ? 'selected' : '' }}>
                                    {{ $community->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">نوع الفعالية</label>
                        <select name="event_type" class="form-select">
                            <option value="">جميع الأنواع</option>
                            @foreach($eventTypes as $type)
                                <option value="{{ $type }}" 
                                        {{ request('event_type') == $type ? 'selected' : '' }}>
                                    {{ $eventTypeLabels[$type] ?? $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الفترة الزمنية</label>
                        <select name="date_range" class="form-select">
                            <option value="">جميع الفترات</option>
                            <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>اليوم</option>
                            <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>هذا الأسبوع</option>
                            <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>هذا الشهر</option>
                            <option value="year" {{ request('date_range') == 'year' ? 'selected' : '' }}>هذا العام</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Events Grid -->
    <div class="row">
        @forelse($events->chunk(3) as $chunk)
            @foreach($chunk as $event)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        @if($event->hasImages())
                            <img src="{{ $event->images[0] }}" class="card-img-top" 
                                 style="height: 200px; object-fit: cover;" alt="{{ $event->title }}">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                 style="height: 200px;">
                                <i class="fas fa-calendar-alt fa-3x text-muted"></i>
                            </div>
                        @endif
                        
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">{{ $event->title }}</h5>
                                <span class="badge bg-{{ $event->isHappeningNow() ? 'success' : ($event->isUpcoming() ? 'primary' : 'secondary') }}">
                                    {{ $event->event_status }}
                                </span>
                            </div>
                            
                            <p class="card-text text-muted small mb-2">
                                {{ Str::limit($event->description, 80) }}
                            </p>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-users me-1"></i>
                                    {{ $event->community->name }}
                                </small>
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $event->start_date_label }}
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex flex-wrap gap-1">
                                    @if($event->isFree())
                                        <span class="badge bg-success">
                                            <i class="fas fa-dollar-sign me-1"></i>مجاني
                                        </span>
                                    @endif
                                    @if($event->hasAvailableSpots())
                                        <span class="badge bg-info">
                                            <i class="fas fa-users me-1"></i>{{ $event->availability_label }}
                                        </span>
                                    @endif
                                    @if($event->isFeatured())
                                        <span class="badge bg-warning">
                                            <i class="fas fa-star me-1"></i>مميز
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-star text-warning me-1"></i>
                                    <small>{{ $event->rating }}/5</small>
                                    <span class="text-muted ms-2">({{ $event->review_count }})</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-eye text-muted me-1"></i>
                                    <small>{{ $event->view_count }}</small>
                                </div>
                            </div>
                            
                            <div class="mt-auto">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('community-events.show', $event) }}" 
                                       class="btn btn-outline-primary btn-sm flex-fill">
                                        <i class="fas fa-eye me-1"></i>عرض
                                    </a>
                                    @if($event->isUpcoming() && $event->hasAvailableSpots())
                                        <button class="btn btn-success btn-sm" 
                                                onclick="joinEvent({{ $event->id }})">
                                            <i class="fas fa-user-plus me-1"></i>انضمام
                                        </button>
                                    @endif
                                    @if(auth()->check())
                                        <a href="{{ route('community-events.edit', $event) }}" 
                                           class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-calendar-alt fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">لا توجد فعاليات متاحة</h4>
                    <p class="text-muted">لم يتم العثور على أي فعاليات تطابق معايير البحث الخاصة بك.</p>
                    <a href="{{ route('community-events.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>إنشاء فعالية جديدة
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($events->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $events->links() }}
        </div>
    @endif
</div>

<!-- Calendar Modal -->
<div class="modal fade" id="calendarModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تقويم الفعاليات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="eventCalendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Modal -->
<div class="modal fade" id="statisticsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إحصائيات الفعاليات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="card-title">إجمالي الفعاليات</h6>
                                <h3 class="text-primary">{{ $stats['total_events'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="card-title">الفعاليات القادمة</h6>
                                <h3 class="text-success">{{ $stats['upcoming_events'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="card-title">الفعاليات اليوم</h6>
                                <h3 class="text-info">{{ $stats['today_events'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="card-title">متوسط التقييم</h6>
                                <h3 class="text-warning">{{ number_format($stats['average_rating'], 1) }}/5</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">الفعاليات حسب النوع</h6>
                                @foreach($stats['by_type'] as $type => $count)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>{{ $eventTypeLabels[$type] ?? $type }}</span>
                                        <div class="progress flex-grow-1 mx-3" style="height: 8px;">
                                            <div class="progress-bar bg-primary" 
                                                 style="width: {{ ($count / $stats['total_events']) * 100 }}%"></div>
                                        </div>
                                        <span class="text-muted">{{ $count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Initialize calendar when modal is shown
document.getElementById('calendarModal').addEventListener('shown.bs.modal', function () {
    const calendar = new FullCalendar.Calendar(document.getElementById('eventCalendar'), {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        events: [
            @foreach($events as $event)
                {
                    title: '{{ $event->title }}',
                    start: '{{ $event->start_date->format('Y-m-d\TH:i:s') }}',
                    end: '{{ $event->end_date->format('Y-m-d\TH:i:s') }}',
                    url: '{{ route('community-events.show', $event) }}',
                    backgroundColor: '{{ $event->isFeatured() ? '#ffc107' : '#007bff' }}',
                    borderColor: '{{ $event->isUrgent() ? '#dc3545' : '#007bff' }}',
                    extendedProps: {
                        community: '{{ $event->community->name }}',
                        type: '{{ $event->event_type }}'
                    }
                },
            @endforeach
        ]
    });
});

// Join event function
function joinEvent(eventId) {
    if (confirm('هل أنت متأكد من الانضمام إلى هذه الفعالية؟')) {
        fetch(`/community-events/${eventId}/join`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم الانضمام بنجاح!');
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء الانضمام');
        });
    }
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    }
    
    // Auto-submit on filter change
    const filters = ['community_id', 'event_type', 'date_range'];
    filters.forEach(filterName => {
        const filter = document.querySelector(`select[name="${filterName}"]`);
        if (filter) {
            filter.addEventListener('change', function() {
                this.form.submit();
            });
        }
    });
});
</script>
@endpush
