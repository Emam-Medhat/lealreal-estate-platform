@extends('layouts.app')

@section('title', 'إدارة الاستدامة')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">إدارة الاستدامة</h1>
                <div class="btn-group" role="group">
                    <a href="{{ route('sustainability.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة تقييم
                    </a>
                    <a href="{{ route('sustainability.calculator') }}" class="btn btn-info">
                        <i class="fas fa-calculator"></i> حاسبة الاستدامة
                    </a>
                    <a href="{{ route('sustainability.reports.index') }}" class="btn btn-success">
                        <i class="fas fa-file-alt"></i> التقارير
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">الفلاتر</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('sustainability.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="property">العقار</label>
                            <select name="property_id" id="property" class="form-control">
                                <option value="">جميع العقارات</option>
                                @foreach($properties as $property)
                                    <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                                        {{ $property->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="certification_status">حالة الشهادة</label>
                            <select name="certification_status" id="certification_status" class="form-control">
                                <option value="">جميع الحالات</option>
                                <option value="certified" {{ request('certification_status') == 'certified' ? 'selected' : '' }}>معتمد</option>
                                <option value="not_certified" {{ request('certification_status') == 'not_certified' ? 'selected' : '' }}>غير معتمد</option>
                                <option value="in_progress" {{ request('certification_status') == 'in_progress' ? 'selected' : '' }}>قيد المعالجة</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="eco_score_min">أدنى درجة بيئية</label>
                            <input type="number" name="eco_score_min" id="eco_score_min" class="form-control" 
                                   value="{{ request('eco_score_min') }}" min="0" max="100" step="0.1">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="eco_score_max">أعلى درجة بيئية</label>
                            <input type="number" name="eco_score_max" id="eco_score_max" class="form-control" 
                                   value="{{ request('eco_score_max') }}" min="0" max="100" step="0.1">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="sort_by">ترتيب حسب</label>
                            <select name="sort_by" id="sort_by" class="form-control">
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>تاريخ الإنشاء</option>
                                <option value="eco_score" {{ request('sort_by') == 'eco_score' ? 'selected' : '' }}>الدرجة البيئية</option>
                                <option value="energy_efficiency_rating" {{ request('sort_by') == 'energy_efficiency_rating' ? 'selected' : '' }}>كفاءة الطاقة</option>
                                <option value="carbon_footprint" {{ request('sort_by') == 'carbon_footprint' ? 'selected' : '' }}>البصمة الكربونية</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="sort_order">ترتيب</label>
                            <select name="sort_order" id="sort_order" class="form-control">
                                <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>تنازلي</option>
                                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>تصاعدي</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> تطبيق الفلاتر
                                </button>
                                <a href="{{ route('sustainability.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> مسح الفلاتر
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                تم العثور على {{ $sustainabilityRecords->total() }} سجل استدامة
                @if(request()->hasAny(['property_id', 'certification_status', 'eco_score_min', 'eco_score_max']))
                    (مع الفلاتر المطبقة)
                @endif
            </div>
        </div>
    </div>

    <!-- Sustainability Records Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">سجلات الاستدامة</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>العقار</th>
                            <th>الدرجة البيئية</th>
                            <th>كفاءة الطاقة</th>
                            <th>البصمة الكربونية</th>
                            <th>حالة الشهادة</th>
                            <th>آخر تقييم</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($sustainabilityRecords->count() > 0)
                            @foreach($sustainabilityRecords as $record)
                                <tr>
                                    <td>
                                        <a href="{{ route('properties.show', $record->property_id) }}" class="text-primary">
                                            {{ $record->property->title ?? 'عقار غير معروف' }}
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $record->property->address ?? '' }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 mr-2" style="height: 8px;">
                                                <div class="progress-bar {{ $record->eco_score >= 80 ? 'bg-success' : ($record->eco_score >= 60 ? 'bg-warning' : 'bg-danger') }}" 
                                                     style="width: {{ $record->eco_score }}%"></div>
                                            </div>
                                            <span class="badge badge-{{ $record->eco_score >= 80 ? 'success' : ($record->eco_score >= 60 ? 'warning' : 'danger') }}">
                                                {{ number_format($record->eco_score, 1) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 mr-2" style="height: 8px;">
                                                <div class="progress-bar bg-info" style="width: {{ $record->energy_efficiency_rating }}%"></div>
                                            </div>
                                            <span class="badge badge-info">
                                                {{ number_format($record->energy_efficiency_rating, 1) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $record->carbon_footprint <= 30 ? 'success' : ($record->carbon_footprint <= 60 ? 'warning' : 'danger') }}">
                                            {{ number_format($record->carbon_footprint, 1) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $record->certification_status == 'certified' ? 'success' : ($record->certification_status == 'in_progress' ? 'warning' : 'secondary') }}">
                                            {{ $record->certification_status_text }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $record->updated_at->format('Y-m-d') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('sustainability.show', $record) }}" class="btn btn-outline-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('sustainability.edit', $record) }}" class="btn btn-outline-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('sustainability.reports.create', ['property_sustainability_id' => $record->id]) }}" 
                                               class="btn btn-outline-success" title="إنشاء تقرير">
                                                <i class="fas fa-file-alt"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="confirmDelete({{ $record->id }})" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <div>لا توجد سجلات استدامة</div>
                                    <a href="{{ route('sustainability.create') }}" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus"></i> إضافة تقييم استدامة
                                    </a>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($sustainabilityRecords->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        عرض {{ $sustainabilityRecords->firstItem() }} - {{ $sustainabilityRecords->lastItem() }} 
                        من {{ $sustainabilityRecords->total() }} سجل
                    </div>
                    {{ $sustainabilityRecords->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        if (confirm('هل أنت متأكد من حذف سجل الاستدامة هذا؟ لا يمكن التراجع عن هذا الإجراء.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }

    // Auto-submit filters on change
    document.querySelectorAll('#filters select, #filters input').forEach(element => {
        element.addEventListener('change', function() {
            if (this.type === 'select-one' || this.type === 'number') {
                document.getElementById('filters').submit();
            }
        });
    });
</script>
@endpush
