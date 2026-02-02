@extends('admin.layouts.admin')

@section('title', 'مسار المبيعات')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">مسار المبيعات</h1>
            <p class="text-gray-600 mt-1">إدارة وتتبع مسار تحويل العملاء</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <button onclick="refreshPipeline()" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2 space-x-reverse">
                <i class="fas fa-sync-alt"></i>
                <span>تحديث</span>
            </button>
            <a href="{{ route('leads.create') }}" 
               class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-all duration-200 shadow-lg hover:shadow-xl">
                <i class="fas fa-plus"></i>
                <span>إضافة عميل</span>
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">إجمالي العملاء</p>
                <p class="text-2xl font-bold text-blue-600">{{ $totalLeads }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-users text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">العملاء المحولين</p>
                <p class="text-2xl font-bold text-green-600">{{ $convertedLeads }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">القيمة الإجمالية</p>
                <p class="text-2xl font-bold text-orange-600">{{ number_format($totalValue, 2) }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <i class="fas fa-dollar-sign text-orange-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">معدل التحويل</p>
                <p class="text-2xl font-bold text-purple-600">{{ number_format($conversionRate, 2) }}%</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-chart-line text-purple-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Pipeline -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">مسار المبيعات</h3>
    </div>
    <div class="p-6">
        <div class="overflow-x-auto">
            <div class="grid grid-cols-1 lg:grid-cols-4 xl:grid-cols-6 gap-6 min-w-max">
                @foreach($statuses as $status)
                    <div class="bg-gray-50 rounded-lg p-4 min-h-[400px] flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-semibold text-gray-900">{{ $status->name }}</h4>
                            <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded-full">
                                {{ $status->leads->count() }}
                            </span>
                        </div>
                        
                        <div class="flex-1 space-y-3 overflow-y-auto">
                            @forelse($status->leads as $lead)
                                <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200 hover:shadow-md transition-shadow cursor-pointer"
                                     onclick="viewLead({{ $lead->id }})">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1">
                                            <h5 class="text-sm font-medium text-gray-900 truncate">
                                                {{ $lead->first_name }} {{ $lead->last_name }}
                                            </h5>
                                            <p class="text-xs text-gray-500 truncate">{{ $lead->email }}</p>
                                            @if($lead->company)
                                                <p class="text-xs text-gray-500 truncate">{{ $lead->company }}</p>
                                            @endif
                                        </div>
                                        <div class="flex flex-col items-end space-y-1">
                                            @if($lead->priority)
                                                <span class="text-xs px-2 py-1 rounded-full
                                                    @if($lead->priority == 3) bg-red-100 text-red-700
                                                    @elseif($lead->priority == 2) bg-yellow-100 text-yellow-700
                                                    @else bg-green-100 text-green-700
                                                    @endif">
                                                    @if($lead->priority == 3) عالي
                                                    @elseif($lead->priority == 2) متوسط
                                                    @else منخفض
                                                    @endif
                                                </span>
                                            @endif
                                            @if($lead->estimated_value)
                                                <span class="text-xs text-gray-600">
                                                    {{ number_format($lead->estimated_value, 0) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span>{{ $lead->created_at->format('M d') }}</span>
                                        @if($lead->assignedTo)
                                            <span>{{ $lead->assignedTo->name }}</span>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-2 flex items-center space-x-1 space-x-reverse">
                                        <button onclick="event.stopPropagation(); viewLead({{ $lead->id }})" 
                                                class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-50 transition-colors"
                                                title="عرض">
                                            <i class="fas fa-eye text-xs"></i>
                                        </button>
                                        <button onclick="event.stopPropagation(); editLead({{ $lead->id }})" 
                                                class="text-green-600 hover:text-green-800 p-1 rounded hover:bg-green-50 transition-colors"
                                                title="تعديل">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <button onclick="event.stopPropagation(); convertLead({{ $lead->id }})" 
                                                class="text-purple-600 hover:text-purple-800 p-1 rounded hover:bg-purple-50 transition-colors"
                                                title="تحويل">
                                            <i class="fas fa-arrow-right text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <i class="fas fa-inbox text-gray-300 text-2xl mb-2"></i>
                                    <p class="text-xs text-gray-500">لا توجد عملاء</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Convert Lead Modal -->
<div id="convertLeadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">تحويل العميل</h3>
        </div>
        <form id="convertLeadForm" method="POST" class="p-6">
            @csrf
            <input type="hidden" id="convertLeadId" name="lead_id">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع التحويل</label>
                    <select name="converted_to_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="client">عميل</option>
                        <option value="opportunity">فرصة</option>
                        <option value="property">عقار</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                    <textarea name="conversion_notes" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                              placeholder="أدخل ملاحظات التحويل..."></textarea>
                </div>
            </div>
            
            <div class="flex items-center justify-end space-x-3 space-x-reverse mt-6">
                <button type="button" onclick="closeConvertModal()" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    إلغاء
                </button>
                <button type="submit" 
                        class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    تحويل
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function viewLead(leadId) {
    window.location.href = `/leads/${leadId}`;
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
