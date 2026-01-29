@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">تفاصيل المهمة: {{ $task->title }}</h1>
                <div class="btn-group">
                    <a href="{{ route('projects.tasks.index', $project) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i> العودة للمهام
                    </a>
                    <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> تعديل
                    </a>
                </div>
            </div>

            <!-- Task Details -->
            <div class="row">
                <div class="col-md-8">
                    <!-- Main Info -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات المهمة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>العنوان:</strong> {{ $task->title }}</p>
                                    @if($task->description)
                                        <p><strong>الوصف:</strong> {{ $task->description }}</p>
                                    @endif
                                    <p><strong>الأولوية:</strong> 
                                        <span class="badge badge-{{ $task->priority == 'critical' ? 'danger' : ($task->priority == 'high' ? 'warning' : ($task->priority == 'medium' ? 'info' : 'secondary')) }}">
                                            {{ $task->priority == 'critical' ? 'حرجة' : ($task->priority == 'high' ? 'عالية' : ($task->priority == 'medium' ? 'متوسطة' : 'منخفضة')) }}
                                        </span>
                                    </p>
                                    <p><strong>الحالة:</strong> 
                                        <span class="badge badge-{{ $task->status == 'completed' ? 'success' : ($task->status == 'in_progress' ? 'primary' : ($task->status == 'review' ? 'warning' : 'secondary')) }}">
                                            {{ $task->status == 'completed' ? 'مكتملة' : ($task->status == 'in_progress' ? 'قيد التنفيذ' : ($task->status == 'review' ? 'قيد المراجعة' : 'قيد الانتظار')) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>الموظف المسند إليه:</strong>
                                        @if($task->assignee)
                                            {{ $task->assignee->full_name }}
                                        @else
                                            <span class="text-muted">غير مسند</span>
                                        @endif
                                    </p>
                                    <p><strong>تاريخ الاستحقاق:</strong>
                                        @if($task->due_date)
                                            <span class="{{ $task->due_date->isPast() && $task->status != 'completed' ? 'text-danger' : '' }}">
                                                {{ $task->due_date->format('Y-m-d') }}
                                            </span>
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </p>
                                    <p><strong>التقدم:</strong> {{ $task->progress }}%</p>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $task->progress }}%"
                                             aria-valuenow="{{ $task->progress }}" 
                                             aria-valuemin="0" aria-valuemax="100">
                                            {{ $task->progress }}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($task->notes)
                                <div class="mt-3">
                                    <p><strong>ملاحظات:</strong></p>
                                    <p>{{ $task->notes }}</p>
                                </div>
                            @endif

                            @if($task->tags && count($task->tags) > 0)
                                <div class="mt-3">
                                    <p><strong>الوسوم:</strong></p>
                                    <div>
                                        @foreach($task->tags as $tag)
                                            <span class="badge bg-secondary me-1">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Comments -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">التعليقات</h5>
                            <button class="btn btn-sm btn-primary" onclick="showAddCommentModal()">
                                <i class="fas fa-plus"></i> إضافة تعليق
                            </button>
                        </div>
                        <div class="card-body">
                            @if($task->comments->count() > 0)
                                @foreach($task->comments as $comment)
                                    <div class="comment-item mb-3 pb-3 border-bottom">
                                        <div class="d-flex justify-content-between">
                                            <strong>{{ $comment->user->full_name }}</strong>
                                            <small class="text-muted">{{ $comment->created_at->format('Y-m-d H:i') }}</small>
                                        </div>
                                        <p class="mb-0">{{ $comment->comment }}</p>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">لا توجد تعليقات</p>
                            @endif
                        </div>
                    </div>

                    <!-- Attachments -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">المرفقات</h5>
                            <button class="btn btn-sm btn-primary" onclick="showAddAttachmentModal()">
                                <i class="fas fa-plus"></i> إضافة مرفق
                            </button>
                        </div>
                        <div class="card-body">
                            @if($task->attachments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>اسم الملف</th>
                                                <th>الحجم</th>
                                                <th>بواسطة</th>
                                                <th>التاريخ</th>
                                                <th>إجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($task->attachments as $attachment)
                                                <tr>
                                                    <td>{{ $attachment->file_name }}</td>
                                                    <td>{{ number_format($attachment->file_size / 1024, 2) }} KB</td>
                                                    <td>{{ $attachment->uploader->full_name }}</td>
                                                    <td>{{ $attachment->created_at->format('Y-m-d') }}</td>
                                                    <td>
                                                        <a href="{{ asset($attachment->file_path) }}" class="btn btn-sm btn-outline-primary" download>
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">لا توجد مرفقات</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Time Logs -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">سجل الوقت</h5>
                            <button class="btn btn-sm btn-primary" onclick="showAddTimeLogModal()">
                                <i class="fas fa-plus"></i> إضافة وقت
                            </button>
                        </div>
                        <div class="card-body">
                            @if($task->timeLogs->count() > 0)
                                @foreach($task->timeLogs as $timeLog)
                                    <div class="time-log-item mb-2 pb-2 border-bottom">
                                        <div class="d-flex justify-content-between">
                                            <strong>{{ $timeLog->hours }} ساعة</strong>
                                            <small class="text-muted">{{ $timeLog->log_date->format('Y-m-d') }}</small>
                                        </div>
                                        <small class="text-muted">{{ $timeLog->user->full_name }}</small>
                                        @if($timeLog->description)
                                            <p class="mb-0 small">{{ $timeLog->description }}</p>
                                        @endif
                                    </div>
                                @endforeach
                                <div class="mt-2">
                                    <strong>الإجمالي: {{ $task->timeLogs->sum('hours') }} ساعة</strong>
                                </div>
                            @else
                                <p class="text-muted">لا يوجد سجل وقت</p>
                            @endif
                        </div>
                    </div>

                    <!-- Dependencies -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">الاعتماديات</h5>
                        </div>
                        <div class="card-body">
                            @if($task->dependencies->count() > 0)
                                @foreach($task->dependencies as $dependency)
                                    <div class="mb-2">
                                        <a href="{{ route('projects.tasks.show', [$project, $dependency]) }}" class="text-decoration-none">
                                            {{ $dependency->title }}
                                        </a>
                                        <span class="badge badge-{{ $dependency->status == 'completed' ? 'success' : ($dependency->status == 'in_progress' ? 'primary' : ($dependency->status == 'review' ? 'warning' : 'secondary')) }} ms-2">
                                            {{ $dependency->status == 'completed' ? 'مكتملة' : ($dependency->status == 'in_progress' ? 'قيد التنفيذ' : ($dependency->status == 'review' ? 'قيد المراجعة' : 'قيد الانتظار')) }}
                                        </span>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">لا توجد اعتماديات</p>
                            @endif
                        </div>
                    </div>

                    <!-- Checklists -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">قوائم التحقق</h5>
                            <button class="btn btn-sm btn-primary" onclick="showAddChecklistModal()">
                                <i class="fas fa-plus"></i> إضافة قائمة
                            </button>
                        </div>
                        <div class="card-body">
                            @if($task->checklists->count() > 0)
                                @foreach($task->checklists as $checklist)
                                    <div class="checklist-item mb-2">
                                        <div class="d-flex align-items-center">
                                            <input type="checkbox" {{ $checklist->is_completed ? 'checked' : '' }} 
                                                   class="form-check-input me-2" disabled>
                                            <span class="{{ $checklist->is_completed ? 'text-decoration-line-through' : '' }}">
                                                {{ $checklist->title }}
                                            </span>
                                        </div>
                                        @if($checklist->completed_by)
                                            <small class="text-muted">
                                                تم الإنجاز بواسطة {{ $checklist->completedBy->full_name }}
                                                {{ $checklist->completed_at->format('Y-m-d H:i') }}
                                            </small>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">لا توجد قوائم تحقق</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals (placeholder for future implementation) -->
<div id="commentModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة تعليق</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="commentForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">التعليق</label>
                        <textarea class="form-control" name="comment" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Helper functions for badges
function getTaskStatusBadgeClass(status) {
    const classes = {
        'todo': 'secondary',
        'in_progress': 'primary',
        'review': 'warning',
        'completed': 'success',
        'cancelled': 'danger'
    };
    return classes[status] || 'secondary';
}

function getTaskStatusLabel(status) {
    const labels = {
        'todo': 'قيد الانتظار',
        'in_progress': 'قيد التنفيذ',
        'review': 'قيد المراجعة',
        'completed': 'مكتملة',
        'cancelled': 'ملغاة'
    };
    return labels[status] || status;
}

function getPriorityBadgeClass(priority) {
    const classes = {
        'critical': 'danger',
        'high': 'warning',
        'medium': 'info',
        'low': 'secondary'
    };
    return classes[priority] || 'secondary';
}

function getPriorityLabel(priority) {
    const labels = {
        'critical': 'حرجة',
        'high': 'عالية',
        'medium': 'متوسطة',
        'low': 'منخفضة'
    };
    return labels[priority] || priority;
}

// Modal functions (placeholder)
function showAddCommentModal() {
    $('#commentModal').modal('show');
}

function showAddAttachmentModal() {
    alert('سيتم تطبيق إضافة المرفقات قريباً');
}

function showAddTimeLogModal() {
    alert('سيتم تطبيق إضافة سجل الوقت قريباً');
}

function showAddChecklistModal() {
    alert('سيتم تطبيق إضافة قائمة التحقق قريباً');
}
</script>
@endpush
