@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">التقارير المخصصة</h4>
                </div>
                <div class="card-body">
                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs mb-4" id="reportsTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports" type="button" role="tab">
                                <i class="fas fa-file-alt me-2"></i>التقارير
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="templates-tab" data-bs-toggle="tab" data-bs-target="#templates" type="button" role="tab">
                                <i class="fas fa-clipboard-list me-2"></i>القوالب
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="reportsTabContent">
                        <!-- Reports Tab -->
                        <div class="tab-pane fade show active" id="reports" role="tabpanel" aria-labelledby="reports-tab">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5>التقارير الخاصة بي</h5>
                                <a href="{{ route('reports.custom.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>تقرير جديد
                                </a>
                            </div>
                            
                            @if($reports->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>الاسم</th>
                                                <th>الوصف</th>
                                                <th>تاريخ الإنشاء</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($reports as $report)
                                                <tr>
                                                    <td>{{ $report->name }}</td>
                                                    <td>{{ Str::limit($report->description, 50) }}</td>
                                                    <td>{{ $report->created_at->format('Y-m-d') }}</td>
                                                    <td>
                                                        <a href="{{ route('reports.custom.show', $report->id) }}" class="btn btn-sm btn-info" title="عرض">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('reports.custom.edit', $report->id) }}" class="btn btn-sm btn-primary" title="تعديل">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger delete-report" data-id="{{ $report->id }}" title="حذف">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {{ $reports->links() }}
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> لا توجد تقارير متاحة.
                                </div>
                            @endif
                        </div>

                        <!-- Templates Tab -->
                        <div class="tab-pane fade" id="templates" role="tabpanel" aria-labelledby="templates-tab">
                            <h5 class="mb-4">قوالب التقارير</h5>
                            
                            @if($templates->count() > 0)
                                <div class="row">
                                    @foreach($templates as $template)
                                        <div class="col-md-4 mb-4">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ $template->name }}</h5>
                                                    <p class="card-text text-muted">
                                                        {{ Str::limit($template->description, 100) }}
                                                    </p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="badge bg-{{ $template->is_public ? 'success' : 'primary' }}">
                                                            {{ $template->is_public ? 'عام' : 'خاص' }}
                                                        </span>
                                                        <small class="text-muted">
                                                            {{ $template->created_at->diffForHumans() }}
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="card-footer bg-transparent">
                                                    <button class="btn btn-sm btn-outline-primary use-template" data-id="{{ $template->id }}">
                                                        <i class="fas fa-magic me-1"></i> استخدام القالب
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                {{ $templates->links() }}
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> لا توجد قوالب متاحة.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من رغبتك في حذف هذا التقرير؟
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle delete button click
        $('.delete-report').click(function() {
            var id = $(this).data('id');
            var url = '{{ route("reports.custom.destroy", ":id") }}';
            url = url.replace(':id', id);
            $('#deleteForm').attr('action', url);
            $('#deleteModal').modal('show');
        });

        // Handle use template button
        $('.use-template').click(function() {
            var templateId = $(this).data('id');
            // You can implement the logic to use the template
            window.location.href = '{{ route("reports.custom.create") }}?template=' + templateId;
        });
    });
</script>
@endpush
