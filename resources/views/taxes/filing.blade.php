@extends('layouts.app')

@section('title', 'تقديم الإقرارات الضريبية')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">تقديم الإقرارات الضريبية</h1>
                <div class="btn-group">
                    <a href="{{ route('taxes.filing.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إقرار جديد
                    </a>
                    <a href="{{ route('taxes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i> العودة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('taxes.filing.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="status" class="form-label">الحالة</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">جميع الحالات</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>مقدم</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمد</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="tax_year" class="form-label">السنة الضريبية</label>
                                <input type="number" class="form-control" id="tax_year" name="tax_year" 
                                       value="{{ request('tax_year') }}" min="2020" max="{{ now()->year }}">
                            </div>
                            <div class="col-md-3">
                                <label for="filing_type" class="form-label">نوع الإقرار</label>
                                <select class="form-select" id="filing_type" name="filing_type">
                                    <option value="">جميع الأنواع</option>
                                    <option value="annual">سنوي</option>
                                    <option value="quarterly">ربع سنوي</option>
                                    <option value="amended">معدل</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                                <a href="{{ route('taxes.filing.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> إعادة تعيين
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Filings List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة الإقرارات</h5>
                </div>
                <div class="card-body">
                    @if($filings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>رقم الإقرار</th>
                                        <th>العقار</th>
                                        <th>السنة الضريبية</th>
                                        <th>النوع</th>
                                        <th>الحالة</th>
                                        <th>تاريخ التقديم</th>
                                        <th>المبلغ المعتمد</th>
                                        <th>إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($filings as $filing)
                                    <tr>
                                        <td>#{{ str_pad($filing->id, 6, '0', STR_PAD_LEFT) }}</td>
                                        <td>{{ $filing->propertyTax->property->title ?? 'N/A' }}</td>
                                        <td>{{ $filing->tax_year }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $filing->filing_type === 'annual' ? 'سنوي' : ($filing->filing_type === 'quarterly' ? 'ربع سنوي' : 'معدل') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($filing->status === 'draft')
                                                <span class="badge bg-secondary">مسودة</span>
                                            @elseif($filing->status === 'submitted')
                                                <span class="badge bg-warning">مقدم</span>
                                            @elseif($filing->status === 'approved')
                                                <span class="badge bg-success">معتمد</span>
                                            @elseif($filing->status === 'rejected')
                                                <span class="badge bg-danger">مرفوض</span>
                                            @endif
                                        </td>
                                        <td>{{ $filing->submission_date ? $filing->submission_date->format('Y-m-d') : '-' }}</td>
                                        <td>{{ $filing->approved_amount ? number_format($filing->approved_amount, 2) . ' ريال' : '-' }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('taxes.filing.show', $filing) }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($filing->canBeEdited())
                                                    <a href="{{ route('taxes.filing.edit', $filing) }}" class="btn btn-outline-secondary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                @if($filing->status === 'draft')
                                                    <button type="button" class="btn btn-outline-success" 
                                                            onclick="submitFiling({{ $filing->id }})">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $filings->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد إقرارات</h5>
                            <p class="text-muted">لم يتم العثور على أي إقرارات ضريبية</p>
                            <a href="{{ route('taxes.filing.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> إنشاء إقرار جديد
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
function submitFiling(filingId) {
    if (confirm('هل أنت متأكد من تقديم هذا الإقرار؟ لا يمكن تعديله بعد التقديم.')) {
        $.ajax({
            url: `/taxes/filing/${filingId}/submit`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                alert('حدث خطأ. يرجى المحاولة مرة أخرى.');
            }
        });
    }
}
</script>
@endpush
