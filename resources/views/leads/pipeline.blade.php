@extends('layouts.app')

@section('title', 'مسار المبيعات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">مسار المبيعات</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" onclick="addLead()">
                            <i class="fas fa-plus"></i> إضافة عميل
                        </button>
                        <button class="btn btn-info" onclick="refreshPipeline()">
                            <i class="fas fa-sync"></i> تحديث
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Pipeline Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $totalLeads }}</h3>
                                    <p>إجمالي العملاء</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $convertedLeads }}</h3>
                                    <p>العملاء المحولين</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $totalValue }}</h3>
                                    <p>القيمة الإجمالية</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $conversionRate }}%</h3>
                                    <p>معدل التحويل</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pipeline Kanban Board -->
                    <div class="pipeline-board">
                        <div class="row">
                            @foreach($statuses as $status)
                                <div class="col-md-3">
                                    <div class="pipeline-column">
                                        <div class="pipeline-header" style="background-color: {{ $status->color }}">
                                            <h6>{{ $status->name }}</h6>
                                            <span class="badge bg-light text-dark">{{ $status->leads->count() }}</span>
                                        </div>
                                        <div class="pipeline-content" data-status="{{ $status->id }}">
                                            @foreach($status->leads as $lead)
                                                <div class="pipeline-card" draggable="true" data-lead-id="{{ $lead->id }}">
                                                    <div class="card-body">
                                                        <h6 class="card-title">{{ $lead->first_name }} {{ $lead->last_name }}</h6>
                                                        <p class="card-text small">{{ $lead->email }}</p>
                                                        <p class="card-text small">{{ $lead->phone ?? '-' }}</p>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="badge bg-primary">{{ $lead->estimated_value ? number_format($lead->estimated_value, 0) : '-' }}</span>
                                                            <small class="text-muted">{{ $lead->created_at->format('M d') }}</small>
                                                        </div>
                                                        <div class="mt-2">
                                                            <a href="{{ route('leads.show', $lead) }}" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <button class="btn btn-sm btn-outline-success" onclick="convertLead({{ $lead->id }})">
                                                                <i class="fas fa-exchange-alt"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
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
</div>

<!-- Convert Lead Modal -->
<div class="modal fade" id="convertLeadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحويل العميل</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="convertLeadForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="lead_id" id="convertLeadId">
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

@push('styles')
<style>
.pipeline-board {
    min-height: 600px;
}

.pipeline-column {
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 20px;
}

.pipeline-header {
    padding: 15px;
    border-radius: 8px 8px 0 0;
    color: white;
    display: flex;
    justify-content: between;
    align-items: center;
}

.pipeline-content {
    padding: 15px;
    min-height: 400px;
    max-height: 600px;
    overflow-y: auto;
}

.pipeline-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 10px;
    cursor: move;
    transition: all 0.3s ease;
}

.pipeline-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.pipeline-card.dragging {
    opacity: 0.5;
}

.pipeline-content.drag-over {
    background-color: #e9ecef;
    border: 2px dashed #007bff;
}
</style>
@endpush

@push('scripts')
<script>
let draggedElement = null;

document.addEventListener('DOMContentLoaded', function() {
    initializeDragAndDrop();
});

function initializeDragAndDrop() {
    const cards = document.querySelectorAll('.pipeline-card');
    const columns = document.querySelectorAll('.pipeline-content');

    cards.forEach(card => {
        card.addEventListener('dragstart', handleDragStart);
        card.addEventListener('dragend', handleDragEnd);
    });

    columns.forEach(column => {
        column.addEventListener('dragover', handleDragOver);
        column.addEventListener('drop', handleDrop);
        column.addEventListener('dragleave', handleDragLeave);
    });
}

function handleDragStart(e) {
    draggedElement = e.target;
    e.target.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', e.target.innerHTML);
}

function handleDragEnd(e) {
    e.target.classList.remove('dragging');
}

function handleDragOver(e) {
    if (e.preventDefault) {
        e.preventDefault();
    }
    e.dataTransfer.dropEffect = 'move';
    e.currentTarget.classList.add('drag-over');
    return false;
}

function handleDragLeave(e) {
    e.currentTarget.classList.remove('drag-over');
}

function handleDrop(e) {
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    e.preventDefault();

    const column = e.currentTarget;
    column.classList.remove('drag-over');

    if (draggedElement && draggedElement !== e.target) {
        const leadId = draggedElement.dataset.leadId;
        const newStatusId = column.dataset.status;
        
        // Update lead status via AJAX
        updateLeadStatus(leadId, newStatusId);
        
        // Move the card
        column.appendChild(draggedElement);
    }

    return false;
}

function updateLeadStatus(leadId, statusId) {
    fetch(`/leads/${leadId}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status_id: statusId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Lead status updated successfully');
        } else {
            console.error('Error updating lead status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function convertLead(leadId) {
    document.getElementById('convertLeadId').value = leadId;
    document.getElementById('convertLeadForm').action = `/leads/${leadId}/convert`;
    new bootstrap.Modal(document.getElementById('convertLeadModal')).show();
}

function addLead() {
    window.location.href = '{{ route('leads.create') }}';
}

function refreshPipeline() {
    location.reload();
}
</script>
@endpush
