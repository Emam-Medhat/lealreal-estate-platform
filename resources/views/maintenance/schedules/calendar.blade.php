@extends('admin.layouts.admin')

@section('title', 'جدول الصيانة')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">جدول الصيانة</h1>
        <div>
            <a href="{{ route('maintenance.schedule.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> جدولة جديدة
            </a>
            <a href="{{ route('maintenance.schedule.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm ml-2">
                <i class="fas fa-list fa-sm text-white-50"></i> القائمة
            </a>
        </div>
    </div>

    <!-- Calendar Controls -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row align-items-center justify-content-between">
                <div class="col-md-4">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="prevMonth">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="currentMonth">
                            اليوم
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="nextMonth">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <h4 class="m-0 font-weight-bold text-primary" id="currentMonthYear">{{ $currentMonth->format('F Y') }}</h4>
                </div>
                <div class="col-md-4 text-left">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="monthView">
                            شهر
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="weekView">
                            أسبوع
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="dayView">
                            يوم
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar -->
    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div id="calendar" class="p-3"></div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                جداول اليوم</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todaySchedules->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                جداول قادمة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $upcomingSchedules->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                جداول مكتملة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedSchedules->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                جداول متأخرة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overdueSchedules->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Details Modal -->
    <div class="modal fade" id="scheduleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-primary">تفاصيل الجدولة</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="scheduleDetails">
                    <!-- Details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary" id="editSchedule">
                        <i class="fas fa-edit"></i> تعديل
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/ar.js"></script>

    <script>
        function getEventColor(priority) {
            switch(priority) {
                case 'high': return '#e74a3b';
                case 'medium': return '#f6c23e';
                case 'low': return '#1cc88a';
                default: return '#4e73df';
            }
        }

        function showScheduleDetails(id) {
            // Show loading state
            $('#scheduleDetails').html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>');
            $('#scheduleModal').modal('show');

            // Fetch details
            fetch(`{{ url('maintenance/schedule') }}/${id}/details`)
                .then(response => response.json())
                .then(data => {
                    $('#scheduleDetails').html(data.html);
                })
                .catch(error => {
                    console.error('Error:', error);
                    $('#scheduleDetails').html('<div class="alert alert-danger">حدث خطأ أثناء تحميل التفاصيل</div>');
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const currentMonthYear = document.getElementById('currentMonthYear');
        let currentDate = new Date('{{ $currentMonth->format('Y-m-d') }}');

        // Initialize calendar
        const calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'ar',
            initialView: 'dayGridMonth',
            initialDate: currentDate,
            headerToolbar: false,
            height: 'auto',
            events: {!! json_encode($schedules->map(function($schedule) {
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->title,
                    'start' => $schedule->scheduled_date->format('Y-m-d\TH:i:s'),
                    'backgroundColor' => match($schedule->priority) {
                        'high' => '#e74a3b',
                        'medium' => '#f6c23e',
                        'low' => '#1cc88a',
                        default => '#4e73df'
                    },
                    'extendedProps' => [
                        'property' => $schedule->property->title ?? 'N/A',
                        'team' => $schedule->maintenanceTeam->name ?? 'غير محدد',
                        'status' => $schedule->status,
                        'priority' => $schedule->priority
                    ]
                ];
            })) !!},
            eventClick: function(info) {
                showScheduleDetails(info.event.id);
            },
            dateClick: function(info) {
                // You can add functionality to create new schedule on date click
                console.log('Clicked on: ' + info.dateStr);
            }
        });

        calendar.render();

        // Navigation controls
        document.getElementById('prevMonth').addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            updateCalendar();
        });

        document.getElementById('nextMonth').addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            updateCalendar();
        });

        document.getElementById('currentMonth').addEventListener('click', function() {
            currentDate = new Date();
            updateCalendar();
        });

        // View controls
        document.getElementById('monthView').addEventListener('click', function() {
            calendar.changeView('dayGridMonth');
        });

        document.getElementById('weekView').addEventListener('click', function() {
            calendar.changeView('timeGridWeek');
        });

        document.getElementById('dayView').addEventListener('click', function() {
            calendar.changeView('timeGridDay');
        });

        function updateCalendar() {
            calendar.gotoDate(currentDate);
            currentMonthYear.textContent = currentDate.toLocaleDateString('ar-SA', { month: 'long', year: 'numeric' });
        }

        function getEventColor(priority) {
            const colors = {
                'low': '#28a745',
                'medium': '#ffc107',
                'high': '#fd7e14',
                'emergency': '#dc3545'
            };
            return colors[priority] || '#6c757d';
        }

        function showScheduleDetails(scheduleId) {
            fetch(`/maintenance/schedule/${scheduleId}/details`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('scheduleDetails').innerHTML = data.html;
                    document.getElementById('editSchedule').onclick = function() {
                        window.location.href = `/maintenance/schedule/${scheduleId}/edit`;
                    };
                    
                    $('#scheduleModal').modal('show');
                })
                .catch(error => console.error('Error:', error));
        }
    });
</script>
@endpush
