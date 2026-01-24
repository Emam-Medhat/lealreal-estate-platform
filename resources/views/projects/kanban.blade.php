@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">لوحة كانبان - {{ $project->name }}</h1>
            <p class="text-muted mb-0">إدارة المهام بشكل بصري</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right"></i> العودة للمشروع
            </a>
            <a href="{{ route('projects.tasks.create', $project) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> مهمة جديدة
            </a>
        </div>
    </div>

    <!-- Project Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h4>{{ $stats['not_started'] }}</h4>
                    <p class="mb-0">لم تبدأ</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4>{{ $stats['in_progress'] }}</h4>
                    <p class="mb-0">قيد التنفيذ</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4>{{ $stats['on_hold'] }}</h4>
                    <p class="mb-0">معلقة</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4>{{ $stats['completed'] }}</h4>
                    <p class="mb-0">مكتملة</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="card">
        <div class="card-body p-0">
            <div class="kanban-board" style="display: flex; min-height: 600px;">
                <!-- Not Started Column -->
                <div class="kanban-column" data-status="not_started" style="flex: 1; border-right: 1px solid #dee2e6; background: #f8f9fa;">
                    <div class="kanban-header bg-secondary text-white p-3">
                        <h5 class="mb-0">لم تبدأ ({{ $stats['not_started'] }})</h5>
                    </div>
                    <div class="kanban-tasks p-2" data-status="not_started" style="min-height: 500px;">
                        @foreach($tasksByStatus['not_started'] as $task)
                            <div class="kanban-task card mb-2" draggable="true" data-task-id="{{ $task->id }}">
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-2">{{ $task->name }}</h6>
                                    @if($task->description)
                                        <p class="card-text small text-muted">{{ Str::limit($task->description, 50) }}</p>
                                    @endif
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div>
                                            @if($task->assignee)
                                                <small class="text-muted">
                                                    <i class="fas fa-user"></i> {{ $task->assignee->name }}
                                                </small>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="badge bg-{{ $task->priority == 'critical' ? 'danger' : ($task->priority == 'high' ? 'warning' : 'secondary') }} me-1">
                                                {{ __('tasks.priority.' . $task->priority) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="progress mt-2" style="height: 5px;">
                                        <div class="progress-bar" style="width: {{ $task->progress_percentage }}%"></div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> {{ $task->due_date->format('Y-m-d') }}
                                        </small>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="editTask({{ $task->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-info btn-sm" onclick="viewTask({{ $task->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- In Progress Column -->
                <div class="kanban-column" data-status="in_progress" style="flex: 1; border-right: 1px solid #dee2e6; background: #fff3cd;">
                    <div class="kanban-header bg-warning text-white p-3">
                        <h5 class="mb-0">قيد التنفيذ ({{ $stats['in_progress'] }})</h5>
                    </div>
                    <div class="kanban-tasks p-2" data-status="in_progress" style="min-height: 500px;">
                        @foreach($tasksByStatus['in_progress'] as $task)
                            <div class="kanban-task card mb-2" draggable="true" data-task-id="{{ $task->id }}">
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-2">{{ $task->name }}</h6>
                                    @if($task->description)
                                        <p class="card-text small text-muted">{{ Str::limit($task->description, 50) }}</p>
                                    @endif
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div>
                                            @if($task->assignee)
                                                <small class="text-muted">
                                                    <i class="fas fa-user"></i> {{ $task->assignee->name }}
                                                </small>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="badge bg-{{ $task->priority == 'critical' ? 'danger' : ($task->priority == 'high' ? 'warning' : 'secondary') }} me-1">
                                                {{ __('tasks.priority.' . $task->priority) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="progress mt-2" style="height: 5px;">
                                        <div class="progress-bar" style="width: {{ $task->progress_percentage }}%"></div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> {{ $task->due_date->format('Y-m-d') }}
                                        </small>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="editTask({{ $task->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-info btn-sm" onclick="viewTask({{ $task->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- On Hold Column -->
                <div class="kanban-column" data-status="on_hold" style="flex: 1; border-right: 1px solid #dee2e6; background: #cff4fc;">
                    <div class="kanban-header bg-info text-white p-3">
                        <h5 class="mb-0">معلقة ({{ $stats['on_hold'] }})</h5>
                    </div>
                    <div class="kanban-tasks p-2" data-status="on_hold" style="min-height: 500px;">
                        @foreach($tasksByStatus['on_hold'] as $task)
                            <div class="kanban-task card mb-2" draggable="true" data-task-id="{{ $task->id }}">
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-2">{{ $task->name }}</h6>
                                    @if($task->description)
                                        <p class="card-text small text-muted">{{ Str::limit($task->description, 50) }}</p>
                                    @endif
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div>
                                            @if($task->assignee)
                                                <small class="text-muted">
                                                    <i class="fas fa-user"></i> {{ $task->assignee->name }}
                                                </small>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="badge bg-{{ $task->priority == 'critical' ? 'danger' : ($task->priority == 'high' ? 'warning' : 'secondary') }} me-1">
                                                {{ __('tasks.priority.' . $task->priority) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="progress mt-2" style="height: 5px;">
                                        <div class="progress-bar" style="width: {{ $task->progress_percentage }}%"></div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> {{ $task->due_date->format('Y-m-d') }}
                                        </small>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="editTask({{ $task->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-info btn-sm" onclick="viewTask({{ $task->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Completed Column -->
                <div class="kanban-column" data-status="completed" style="flex: 1; background: #d1e7dd;">
                    <div class="kanban-header bg-success text-white p-3">
                        <h5 class="mb-0">مكتملة ({{ $stats['completed'] }})</h5>
                    </div>
                    <div class="kanban-tasks p-2" data-status="completed" style="min-height: 500px;">
                        @foreach($tasksByStatus['completed'] as $task)
                            <div class="kanban-task card mb-2" draggable="true" data-task-id="{{ $task->id }}">
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-2">{{ $task->name }}</h6>
                                    @if($task->description)
                                        <p class="card-text small text-muted">{{ Str::limit($task->description, 50) }}</p>
                                    @endif
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div>
                                            @if($task->assignee)
                                                <small class="text-muted">
                                                    <i class="fas fa-user"></i> {{ $task->assignee->name }}
                                                </small>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="badge bg-success me-1">
                                                <i class="fas fa-check"></i> مكتملة
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="progress mt-2" style="height: 5px;">
                                        <div class="progress-bar bg-success" style="width: 100%"></div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-check"></i> {{ $task->updated_at->format('Y-m-d') }}
                                        </small>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="editTask({{ $task->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-info btn-sm" onclick="viewTask({{ $task->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Task Details Modal -->
<div class="modal fade" id="taskModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل المهمة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="taskDetails">
                <!-- Task details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeKanban();
});

function initializeKanban() {
    // Initialize drag and drop
    const tasks = document.querySelectorAll('.kanban-task');
    const columns = document.querySelectorAll('.kanban-tasks');
    
    tasks.forEach(task => {
        task.addEventListener('dragstart', handleDragStart);
        task.addEventListener('dragend', handleDragEnd);
    });
    
    columns.forEach(column => {
        column.addEventListener('dragover', handleDragOver);
        column.addEventListener('drop', handleDrop);
    });
}

let draggedTask = null;

function handleDragStart(e) {
    draggedTask = this;
    this.style.opacity = '0.5';
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', this.innerHTML);
}

function handleDragEnd(e) {
    this.style.opacity = '';
    
    // Remove all hover effects
    const columns = document.querySelectorAll('.kanban-tasks');
    columns.forEach(column => {
        column.style.background = '';
    });
}

function handleDragOver(e) {
    if (e.preventDefault) {
        e.preventDefault();
    }
    
    e.dataTransfer.dropEffect = 'move';
    this.style.background = '#e9ecef';
    
    return false;
}

function handleDrop(e) {
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    
    if (draggedTask !== this) {
        const newStatus = this.dataset.status;
        const taskId = draggedTask.dataset.taskId;
        
        // Update task status via AJAX
        updateTaskStatus(taskId, newStatus);
        
        // Move task to new column
        this.appendChild(draggedTask);
        
        // Update stats
        updateKanbanStats();
    }
    
    this.style.background = '';
    
    return false;
}

function updateTaskStatus(taskId, newStatus) {
    fetch(`/projects/tasks/${taskId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('تم تحديث حالة المهمة بنجاح', 'success');
        } else {
            showNotification('حدث خطأ أثناء تحديث المهمة', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('حدث خطأ أثناء تحديث المهمة', 'error');
    });
}

function updateKanbanStats() {
    // Update column counts
    const columns = document.querySelectorAll('.kanban-column');
    columns.forEach(column => {
        const status = column.dataset.status;
        const taskCount = column.querySelectorAll('.kanban-task').length;
        const header = column.querySelector('.kanban-header h5');
        const statusText = header.textContent.split('(')[0].trim();
        header.textContent = `${statusText} (${taskCount})`;
    });
}

function viewTask(taskId) {
    fetch(`/projects/tasks/${taskId}/details`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('taskDetails').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>معلومات أساسية</h6>
                        <p><strong>الاسم:</strong> ${data.name}</p>
                        <p><strong>الوصف:</strong> ${data.description || 'لا يوجد وصف'}</p>
                        <p><strong>الحالة:</strong> ${data.status}</p>
                        <p><strong>الأولوية:</strong> ${data.priority}</p>
                        <p><strong>التقدم:</strong> ${data.progress_percentage}%</p>
                    </div>
                    <div class="col-md-6">
                        <h6>المواعيد والمسؤول</h6>
                        <p><strong>تاريخ البدء:</strong> ${data.start_date}</p>
                        <p><strong>تاريخ الانتهاء:</strong> ${data.due_date}</p>
                        <p><strong>المسؤول:</strong> ${data.assignee_name || 'غير محدد'}</p>
                        <p><strong>المرحلة:</strong> ${data.phase_name || 'غير محدد'}</p>
                        <p><strong>الساعات المقدرة:</strong> ${data.estimated_hours || 'غير محدد'}</p>
                    </div>
                </div>
            `;
            new bootstrap.Modal(document.getElementById('taskModal')).show();
        })
        .catch(error => console.error('Error loading task details:', error));
}

function editTask(taskId) {
    window.location.href = `/projects/tasks/${taskId}/edit`;
}

function showNotification(message, type) {
    // Simple notification implementation
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>

<style>
.kanban-task {
    cursor: move;
    transition: transform 0.2s ease;
}

.kanban-task:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.kanban-tasks {
    transition: background-color 0.2s ease;
}

.kanban-header {
    position: sticky;
    top: 0;
    z-index: 10;
}
</style>
@endsection
