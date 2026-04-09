@extends('layouts.app')

@section('title', 'عقود الإيجار')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">عقود الإيجار</h1>
                <div class="btn-group">
                    <a href="{{ route('rentals.leases.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إنشاء عقد
                    </a>
                    <a href="{{ route('rentals.leases.export') }}" class="btn btn-success">
                        <i class="fas fa-download"></i> تصدير
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('rentals.leases.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search">بحث</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="{{ request('search') }}" placeholder="رقم العقد، المستأجر، العقار">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="status">الحالة</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">الكل</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهي</option>
                                        <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>ملغي</option>
                                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>معلق</option>
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tenant_id">المستأجر</label>
                                    <select class="form-control" id="tenant_id" name="tenant_id">
                                        <option value="">الكل</option>
                                        @foreach(App\Models\Tenant::all() as $tenant)
                                            <option value="{{ $tenant->id }}" {{ request('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                                {{ $tenant->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Leases List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($leases->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>رقم العقد</th>
                                        <th>المستأجر</th>
                                        <th>العقار</th>
                                        <th>تاريخ البدء</th>
                                        <th>تاريخ الانتهاء</th>
                                        <th>الإيجار</th>
                                        <th>الحالة</th>
                                        <th>المتبقي</th>
                                        <th>إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leases as $lease)
                                        <tr>
                                            <td>
                                                <a href="{{ route('rentals.leases.show', $lease) }}" class="font-weight-bold">
                                                    {{ $lease->lease_number }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('rentals.tenants.show', $lease->tenant) }}">
                                                    {{ $lease->tenant->name }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('rentals.properties.show', $lease->property) }}">
                                                    {{ $lease->property->title }}
                                                </a>
                                            </td>
                                            <td>{{ $lease->start_date->format('Y-m-d') }}</td>
                                            <td>{{ $lease->end_date->format('Y-m-d') }}</td>
                                            <td>{{ number_format($lease->rent_amount, 2) }} ريال</td>
                                            <td>
                                                <span class="badge badge-{{ $lease->status == 'active' ? 'success' : ($lease->status == 'expired' ? 'danger' : 'warning') }}">
                                                    {{ $lease->status == 'active' ? 'نشط' : ($lease->status == 'expired' ? 'منتهي' : ($lease->status == 'terminated' ? 'ملغي' : 'معلق')) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($lease->status == 'active')
                                                    <span class="{{ $lease->days_remaining <= 7 ? 'text-danger' : ($lease->days_remaining <= 30 ? 'text-warning' : 'text-success') }}">
                                                        {{ $lease->days_remaining }} يوم
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('rentals.leases.show', $lease) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="عرض">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('rentals.leases.edit', $lease) }}" 
                                                       class="btn btn-sm btn-outline-info" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('rentals.leases.download', $lease) }}" 
                                                       class="btn btn-sm btn-outline-success" title="تحميل">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    @if($lease->status == 'active')
                                                        @if($lease->renewal_option && $lease->days_remaining <= 60)
                                                            <a href="{{ route('rentals.renewals.create', $lease) }}" 
                                                               class="btn btn-sm btn-outline-warning" title="تجديد">
                                                                <i class="fas fa-redo"></i>
                                                            </a>
                                                        @endif
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="terminateLease({{ $lease->id }})" title="إنهاء">
                                                            <i class="fas fa-times"></i>
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
                            {{ $leases->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد عقود</h5>
                            <p class="text-muted">لم يتم العثور على عقود مطابقة للبحث</p>
                            <a href="{{ route('rentals.leases.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> إنشاء عقد جديد
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
function terminateLease(id) {
    const reason = prompt('سبب إنهاء العقد:');
    if (reason) {
        axios.post(`/rentals/leases/${id}/terminate`, { 
            termination_reason: reason,
            termination_date: new Date().toISOString().split('T')[0]
        })
            .then(response => {
                location.reload();
            })
            .catch(error => {
                alert('حدث خطأ أثناء إنهاء العقد');
            });
    }
}
</script>
@endpush
