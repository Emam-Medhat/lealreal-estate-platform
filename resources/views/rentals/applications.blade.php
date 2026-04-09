@extends('layouts.app')

@section('title', 'طلبات الإيجار')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">طلبات الإيجار</h1>
                <div class="btn-group">
                    <a href="{{ route('rentals.applications.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> طلب جديد
                    </a>
                    <a href="{{ route('rentals.applications.export') }}" class="btn btn-success">
                        <i class="fas fa-download"></i> تصدير
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">معلقة</h5>
                    <h2>{{ App\Models\RentalApplication::where('status', 'pending')->count() }}</h2>
                    <small class="text-white-50">طلب</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">قيد المراجعة</h5>
                    <h2>{{ App\Models\RentalApplication::where('status', 'reviewing')->count() }}</h2>
                    <small class="text-white-50">طلب</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">مقبولة</h5>
                    <h2>{{ App\Models\RentalApplication::where('status', 'approved')->count() }}</h2>
                    <small class="text-white-50">طلب</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">مرفوضة</h5>
                    <h2>{{ App\Models\RentalApplication::where('status', 'rejected')->count() }}</h2>
                    <small class="text-white-50">طلب</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('rentals.applications.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search">بحث</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="{{ request('search') }}" placeholder="الاسم، البريد، العقار">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="status">الحالة</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">الكل</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                                        <option value="reviewing" {{ request('status') == 'reviewing' ? 'selected' : '' }}>قيد المراجعة</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>مقبولة</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوضة</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="property_id">العقار</label>
                                    <select class="form-control" id="property_id" name="property_id">
                                        <option value="">الكل</option>
                                        @foreach(App\Models\Property::where('is_rental', true)->get() as $property)
                                            <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                                                {{ $property->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="priority">الأولوية</label>
                                    <select class="form-control" id="priority" name="priority">
                                        <option value="">الكل</option>
                                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i> بحث
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Applications List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($applications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>رقم الطلب</th>
                                        <th>المتقدم</th>
                                        <th>العقار</th>
                                        <th>تاريخ التقديم</th>
                                        <th>الإيجار المطلوب</th>
                                        <th>الحالة</th>
                                        <th>الأولوية</th>
                                        <th>النتيجة</th>
                                        <th>إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $application)
                                        <tr>
                                            <td>
                                                <span class="font-weight-bold">
                                                    #{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $application->applicant_name }}</strong><br>
                                                    <small class="text-muted">{{ $application->applicant_email }}</small><br>
                                                    <small class="text-muted">{{ $application->applicant_phone }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('rentals.properties.show', $application->property) }}">
                                                    {{ $application->property->title }}
                                                </a>
                                            </td>
                                            <td>{{ $application->created_at->format('Y-m-d') }}</td>
                                            <td>{{ number_format($application->offered_rent, 2) }} ريال</td>
                                            <td>
                                                <span class="badge badge-{{ $application->status == 'approved' ? 'success' : ($application->status == 'rejected' ? 'danger' : ($application->status == 'reviewing' ? 'info' : 'warning')) }}">
                                                    {{ $application->status == 'pending' ? 'معلق' : ($application->status == 'reviewing' ? 'قيد المراجعة' : ($application->status == 'approved' ? 'مقبولة' : 'مرفوضة')) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $application->priority == 'high' ? 'danger' : ($application->priority == 'medium' ? 'warning' : 'secondary') }}">
                                                    {{ $application->priority == 'high' ? 'عالية' : ($application->priority == 'medium' ? 'متوسطة' : 'منخفضة') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($application->screening_result)
                                                    <span class="badge badge-{{ $application->screening_result == 'passed' ? 'success' : 'danger' }}">
                                                        {{ $application->screening_result == 'passed' ? 'ناجح' : 'راسب' }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('rentals.applications.show', $application) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="عرض">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($application->status == 'pending')
                                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                                onclick="startReview({{ $application->id }})" title="بدء المراجعة">
                                                            <i class="fas fa-search"></i>
                                                        </button>
                                                    @endif
                                                    @if($application->status == 'reviewing')
                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                onclick="approveApplication({{ $application->id }})" title="موافقة">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="rejectApplication({{ $application->id }})" title="رفض">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                    @if($application->status == 'approved' && !$application->lease_id)
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                onclick="createLease({{ $application->id }})" title="إنشاء عقد">
                                                            <i class="fas fa-file-contract"></i>
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
                        <div class="d-flex justify-content-center">
                            {{ $applications->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد طلبات</h5>
                            <p class="text-muted">لم يتم العثور على طلبات مطابقة للبحث</p>
                            <a href="{{ route('rentals.applications.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> إنشاء طلب جديد
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
function startReview(id) {
    if (confirm('هل تريد بدء مراجعة هذا الطلب؟')) {
        axios.post(`/rentals/applications/${id}/review`)
            .then(response => {
                location.reload();
            })
            .catch(error => {
                alert('حدث خطأ أثناء بدء المراجعة');
            });
    }
}

function approveApplication(id) {
    const notes = prompt('ملاحظات الموافقة:');
    if (notes !== null) {
        axios.post(`/rentals/applications/${id}/approve`, { notes: notes })
            .then(response => {
                location.reload();
            })
            .catch(error => {
                alert('حدث خطأ أثناء الموافقة على الطلب');
            });
    }
}

function rejectApplication(id) {
    const reason = prompt('سبب الرفض:');
    if (reason) {
        axios.post(`/rentals/applications/${id}/reject`, { rejection_reason: reason })
            .then(response => {
                location.reload();
            })
            .catch(error => {
                alert('حدث خطأ أثناء رفض الطلب');
            });
    }
}

function createLease(id) {
    if (confirm('هل تريد إنشاء عقد إيجار من هذا الطلب؟')) {
        axios.post(`/rentals/applications/${id}/create-lease`)
            .then(response => {
                if (response.data.redirect) {
                    window.location.href = response.data.redirect;
                }
            })
            .catch(error => {
                alert('حدث خطأ أثناء إنشاء العقد');
            });
    }
}
</script>
@endpush
