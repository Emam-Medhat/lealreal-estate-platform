@extends('layouts.app')

@section('title', 'تفاصيل العميل المحتمل')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Lead Details -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $lead->first_name }} {{ $lead->last_name }}</h5>
                    <div class="btn-group">
                        <a href="{{ route('leads.edit', $lead) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <button class="btn btn-primary" onclick="convertLead()">
                            <i class="fas fa-exchange-alt"></i> تحويل
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>البريد الإلكتروني:</strong> {{ $lead->email }}</p>
                            <p><strong>الهاتف:</strong> {{ $lead->phone ?? '-' }}</p>
                            <p><strong>الشركة:</strong> {{ $lead->company ?? '-' }}</p>
                            <p><strong>المنصب:</strong> {{ $lead->position ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>المصدر:</strong> {{ $lead->source?->name ?? '-' }}</p>
                            <p><strong>الحالة:</strong> 
                                <span class="badge" style="background-color: {{ $lead->status?->color ?? '#6c757d' }}">
                                    {{ $lead->status?->name ?? 'غير محدد' }}
                                </span>
                            </p>
                            <p><strong>المسؤول:</strong> {{ $lead->assignedTo?->name ?? '-' }}</p>
                            <p><strong>القيمة المتوقعة:</strong> {{ $lead->estimated_value ? number_format($lead->estimated_value, 2) : '-' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <p><strong>العنوان:</strong> {{ $lead->address ?? '-' }}</p>
                            <p><strong>ملاحظات:</strong> {{ $lead->notes ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activities Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">سجل الأنشطة</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($activities as $activity)
                            <div class="timeline-item">
                                <div class="timeline-marker">
                                    <i class="fas fa-{{ $activity->type == 'call' ? 'phone' : ($activity->type == 'email' ? 'envelope' : 'calendar') }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>{{ $activity->title }}</h6>
                                    <p>{{ $activity->description }}</p>
                                    <small class="text-muted">{{ $activity->activity_date->format('Y-m-d H:i') }} - {{ $activity->user->name }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-info" onclick="addActivity('call')">
                            <i class="fas fa-phone"></i> تسجيل مكالمة
                        </button>
                        <button class="btn btn-info" onclick="addActivity('email')">
                            <i class="fas fa-envelope"></i> إرسال بريد
                        </button>
                        <button class="btn btn-info" onclick="addActivity('meeting')">
                            <i class="fas fa-calendar"></i> جدولة اجتماع
                        </button>
                        <button class="btn btn-secondary" onclick="addNote()">
                            <i class="fas fa-sticky-note"></i> إضافة ملاحظة
                        </button>
                        <button class="btn btn-success" onclick="scheduleFollowUp()">
                            <i class="fas fa-clock"></i> متابعة
                        </button>
                    </div>
                </div>
            </div>

            <!-- Lead Score -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">تقييم العميل</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h2>{{ $lead->lead_score ?? '0' }}/100</h2>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $lead->lead_score ?? 0 }}%"></div>
                        </div>
                        <button class="btn btn-primary btn-sm mt-2" onclick="scoreLead()">
                            <i class="fas fa-calculator"></i> إعادة التقييم
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tags -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">الوسوم</h5>
                </div>
                <div class="card-body">
                    @if($lead->tags)
                        @foreach(json_decode($lead->tags) as $tag)
                            <span class="badge bg-secondary">{{ $tag }}</span>
                        @endforeach
                    @else
                        <p class="text-muted">لا توجد وسوم</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Convert Lead Modal -->
<div class="modal fade" id="convertLeadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحويل العميل</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('leads.convert', $lead) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">نوع التحويل</label>
                        <select name="converted_to_type" class="form-select" required>
                            <option value="">اختر...</option>
                            <option value="client">عميل</option>
                            <option value="opportunity">فرصة</option>
                            <option value="property">عقار</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">القيمة</label>
                        <input type="number" name="conversion_value" class="form-control" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تحويل</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function convertLead() {
    new bootstrap.Modal(document.getElementById('convertLeadModal')).show();
}

function addActivity(type) {
    // Implementation for adding activity
    console.log('Add activity:', type);
}

alculation');
    console.log('Score lead');
}

alculation');
    console.log('Score lead');
}

function addNote() {
    // Implementation for adding note
    console.log('Add note');
}

lst');
    consoleinus();
}

function bailUp() {
 (type);
 (type);
}
</script>
@endpush
