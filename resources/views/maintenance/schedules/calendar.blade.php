@extends('layouts.app')

@section('title', 'جدول الصيانة')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3">جدول الصيانة</h1>
                <div>
                    <a href="{{ route('maintenance.schedules.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> جدولة جديدة
                    </a>
                    <a href="{{ route('maintenance.schedules.index') }}" class="btn btn-secondary">
                        <i class="fas fa-list"></i> القائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Controls -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary" id="prevMonth">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="currentMonth">
                            اليوم
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="nextMonth">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <h4 id="currentMonthYear">{{ $currentMonth->format('F Y') }}</h4>
                </div>
                <div class="col-md-3">
                    <div class="btn-group float-left">
                        <button type="button" class="btn btn-outline-secondary" id="monthView">
                            شهر
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="weekView">
                            أسبوع
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="dayView">
                            يوم
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar -->
    <div class="card">
        <div class="card-body p-0">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Schedule Details Modal -->
    <div class="modal fade" id="scheduleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تفاصيل الجدولة</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="scheduleDetails">
                    <!-- Details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary" id="editSchedule">تعديل</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>{{ $todaySchedules->count() }}</h5>
                    <p>جداول اليوم</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5>{{ $upcomingSchedules->count() }}</h5>
                    <p>جداول قادمة</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>{{ $completedSchedules->count() }}</h5>
                    <p>جداول مكتملة</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5>{{ $overdueSchedules->count() }}</h5>
                    <p>جداول متأخرة</p>
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
            events: @json($schedules->map(function($schedule) {
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->title,
                    'start' => $schedule->scheduled_date->format('Y-m-d H:i:s'),
                    'backgroundColor' => getEventColor($schedule->priority),
                    'extendedProps' => [
                        'property' => $schedule->property->title ?? 'N/A',
                        'team' => $schedule->maintenanceTeam->name ?? 'غير محدد',
                        'status' => $schedule->status_label,
                        'priority' => $schedule->priority_label
                    ]
                ];
            })),
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
            fetch(`/maintenance/schedules/${scheduleId}/details`)
                .then(response => response.json())
                .then(data => {
                    const detailsHtml = `
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>العنوان:</strong> ${data.title}</p>
                                <p><strong>العقار:</strong> ${data.property}</p>
                                <p><strong>الفريق:</strong> ${data.team}</p>
                                <p><strong>الحالة:</strong> <span class="badge badge-${data.status_color}">${data.status}</span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>الأولوية:</strong> <span class="badge badge-${data.priority_color}">${data.priority}</span></p>
                                <p><strong>التاريخ:</strong> ${data.scheduled_date}</p>
                                <p><strong>المدة:</strong> ${data.estimated_duration} دقيقة</p>
                                <p><strong>التكلفة:</strong> ${data.estimated_cost || 'N/A'}</p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <p><strong>الوصف:</strong></p>
                                <p>${data.description || 'لا يوجد وصف'}</p>
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('scheduleDetails').innerHTML = detailsHtml;
                    document.getElementById('editSchedule').onclick = function() {
                        window.location.href = `/maintenance/schedules/${scheduleId}/edit`;
                    };
                    
                    $('#scheduleModal').modal('show');
                })
                .catch(error => console.error('Error:', error));
        }
    });
</script>
@endpush
