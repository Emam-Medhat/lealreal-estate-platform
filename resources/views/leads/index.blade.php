@extends('layouts.app')

@section('title', 'إدارة العملاء المحتملين')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">العملاء المحتملين</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('leads.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> عميل جديد
                        </a>
                        <a href="{{ route('leads.dashboard') }}" class="btn btn-info">
                            <i class="fas fa-chart-line"></i> لوحة التحكم
                        </a>
                        <a href="{{ route('leads.pipeline') }}" class="btn btn-success">
                            <i class="fas fa-stream"></i> مسار المبيعات
                        </a>
                        <a href="{{ route('leads.analytics') }}" class="btn btn-warning">
                            <i class="fas fa-chart-bar"></i> التحليلات
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">جميع الحالات</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="sourceFilter">
                                <option value="">جميع المصادر</option>
                                @foreach($sources as $source)
                                    <option value="{{ $source->id }}">{{ $source->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="assignedFilter">
                                <option value="">غير محدد</option>
                                <option value="me">لي</option>
                                <option value="unassigned">غير معين</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="بحث...">
                        </div>
                    </div>

                    <!-- Leads Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الهاتف</th>
                                    <th>المصدر</th>
                                    <th>الحالة</th>
                                    <th>المسؤول</th>
                                    <th>القيمة المتوقعة</th>
                                    <th>التاريخ</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leads as $lead)
                                    <tr>
                                        <td>
                                            <a href="{{ route('leads.show', $lead) }}">
                                                {{ $lead->first_name }} {{ $lead->last_name }}
                                            </a>
                                        </td>
                                        <td>{{ $lead->email }}</td>
                                        <td>{{ $lead->phone ?? '-' }}</td>
                                        <td>{{ $lead->source?->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge" style="background-color: {{ $lead->status?->color ?? '#6c757d' }}">
                                                {{ $lead->status?->name ?? 'غير محدد' }}
                                            </span>
                                        </td>
                                        <td>{{ $lead->assignedTo?->name ?? '-' }}</td>
                                        <td>{{ $lead->estimated_value ? number_format($lead->estimated_value, 2) : '-' }}</td>
                                        <td>{{ $lead->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('leads.show', $lead) }}" class="btn btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('leads.edit', $lead) }}" class="btn btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد؟')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $leads->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('statusFilter');
    const sourceFilter = document.getElementById('sourceFilter');
    const assignedFilter = document.getElementById('assignedFilter');
    const searchInput = document.getElementById('searchInput');

    function filterLeads() {
        const params = new URLSearchParams();
        if (statusFilter.value) params.set('status', statusFilter.value);
        if (sourceFilter.value) params.set('source', sourceFilter.value);
        if (assignedFilter.value) params.set('assigned', assignedFilter.value);
        if (searchInput.value) params.set('search', searchInput.value);
        
        const url = params.toString() ? `?${params.toString()}` : '';
        window.location.href = url;
    }

    statusFilter.addEventListener('change', filterLeads);
    sourceFilter.addEventListener('change', filterLeads);
    assignedFilter.addEventListener('change', filterLeads);
    
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(filterLeads, 500);
    });
});
</script>
@endpush
