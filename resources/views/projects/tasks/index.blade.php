@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">مهام المشروع: {{ $project->name }}</h1>
                <div class="btn-group">
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i> العودة للمشروع
                    </a>
                    <a href="{{ route('projects.tasks.create', $project) }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> مهمة جديدة
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('projects.tasks.index', $project) }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">الحالة</label>
                                <select name="status" class="form-select">
                                    <option value="">الكل</option>
                                    <option value="todo" {{ request('status') == 'todo' ? 'selected' : '' }}>قيد الانتظار</option>
                                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                    <option value="review" {{ request('status') == 'review' ? 'selected' : '' }}>قيد المراجعة</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">الأولوية</label>
                                <select name="priority" class="form-select">
                                    <option value="">الكل</option>
                                    <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>حرجة</option>
                                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">الموظف</label>
                                <select name="assigned_to" class="form-select">
                                    <option value="">الكل</option>
                                    @foreach(\App\Models\User::where('account_status', 'active')->get() as $user)
                                        <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                                            {{ $user->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-filter"></i> فلترة
                                    </button>
                                    <a href="{{ route('projects.tasks.index', $project) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> مسح
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tasks List -->
            <div class="card">
                <div class="card-body">
                    @if($tasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>المهمة</th>
                                        <th>الحالة</th>
                                        <th>الأولوية</th>
                                        <th>الموظف المسند إليه</th>
                                        <th>تاريخ الاستحقاق</th>
                                        <th>التقدم</th>
                                        <th>إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $task->title }}</strong>
                                                    @if($task->description)
                                                        <br><small class="text-muted">{{ Str::limit($task->description, 100) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ getTaskStatusBadgeClass($task->status) }}">
                                                    {{ getTaskStatusLabel($task->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ getPriorityBadgeClass($task->priority) }}">
                                                    {{ getPriorityLabel($task->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($task->assignedTo)
                                                    {{ $task->assignedTo->full_name }}
                                                @else
                                                    <span class="text-muted">غير مسند</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($task->due_date)
                                                    <span class="{{ $task->due_date->isPast() && $task->status != 'completed' ? 'text-danger' : '' }}">
                                                        {{ $task->due_date->format('Y-m-d') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">غير محدد</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ $task->progress }}%"
                                                         aria-valuenow="{{ $task->progress }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                        {{ $task->progress }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('projects.tasks.show', [$project, $task]) }}" class="btn btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="btn btn-outline-secondary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-outline-danger" onclick="deleteTask({{ $task->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $tasks->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد مهام</h5>
                            <p class="text-muted">لم يتم إنشاء أي مهام لهذا المشروع بعد</p>
                            <a href="{{ route('projects.tasks.create', $project) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> إنشاء مهمة جديدة
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function deleteTask(taskId) {
    if (confirm('هل أنت متأكد من حذف هذه المهمة؟')) {
        fetch(`/projects/{{ $project->id }}/tasks/${taskId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء حذف المهمة');
        });
    }
}

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
</script>
@endpush
