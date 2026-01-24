@extends('layouts.app')

@section('title', 'المستأجرين')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">المستأجرين</h1>
                <div class="btn-group">
                    <a href="{{ route('rentals.tenants.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة مستأجر
                    </a>
                    <a href="{{ route('rentals.tenants.export') }}" class="btn btn-success">
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
                    <form method="GET" action="{{ route('rentals.tenants.index') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="search">بحث</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="{{ request('search') }}" placeholder="الاسم، البريد، الهاتف، الرقم الوطني">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">الحالة</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">الكل</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                        <option value="blacklisted" {{ request('status') == 'blacklisted' ? 'selected' : '' }}>قائمة سوداء</option>
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

    <!-- Tenants List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($tenants->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>الاسم</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>الهاتف</th>
                                        <th>الرقم الوطني</th>
                                        <th>الحالة</th>
                                        <th>العقار الحالي</th>
                                        <th>التحقق</th>
                                        <th>إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tenants as $tenant)
                                        <tr>
                                            <td>
                                                <a href="{{ route('rentals.tenants.show', $tenant) }}" class="font-weight-bold">
                                                    {{ $tenant->name }}
                                                </a>
                                                @if($tenant->blacklisted)
                                                    <span class="badge badge-danger ml-1">قائمة سوداء</span>
                                                @endif
                                            </td>
                                            <td>{{ $tenant->email }}</td>
                                            <td>{{ $tenant->phone }}</td>
                                            <td>{{ $tenant->national_id }}</td>
                                            <td>
                                                <span class="badge badge-{{ $tenant->status == 'active' ? 'success' : 'secondary' }}">
                                                    {{ $tenant->status == 'active' ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($tenant->currentLease)
                                                    <a href="{{ route('rentals.leases.show', $tenant->currentLease) }}">
                                                        {{ $tenant->currentLease->property->title }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">لا يوجد</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($tenant->verified)
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check"></i> موثق
                                                    </span>
                                                @else
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-times"></i> غير موثق
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('rentals.tenants.show', $tenant) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="عرض">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('rentals.tenants.edit', $tenant) }}" 
                                                       class="btn btn-sm btn-outline-info" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if(!$tenant->verified)
                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                onclick="verifyTenant({{ $tenant->id }})" title="توثيق">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                                    @if(!$tenant->blacklisted)
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="blacklistTenant({{ $tenant->id }})" title="قائمة سوداء">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-sm btn-outline-warning" 
                                                            onclick="screenTenant({{ $tenant->id }})" title="فحص">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $tenants->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا يوجد مستأجرين</h5>
                            <p class="text-muted">لم يتم العثور على مستأجرين مطابقين للبحث</p>
                            <a href="{{ route('rentals.tenants.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> إضافة مستأجر جديد
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
function verifyTenant(id) {
    if (confirm('هل أنت متأكد من توثيق هذا المستأجر؟')) {
        axios.post(`/rentals/tenants/${id}/verify`)
            .then(response => {
                location.reload();
            })
            .catch(error => {
                alert('حدث خطأ أثناء التوثيق');
            });
    }
}

function blacklistTenant(id) {
    const reason = prompt('سبب الإضافة إلى القائمة السوداء:');
    if (reason) {
        axios.post(`/rentals/tenants/${id}/blacklist`, { blacklist_reason: reason })
            .then(response => {
                location.reload();
            })
            .catch(error => {
                alert('حدث خطأ أثناء الإضافة إلى القائمة السوداء');
            });
    }
}

function screenTenant(id) {
    if (confirm('هل تريد بدء فحص المستأجر؟')) {
        axios.post(`/rentals/tenants/${id}/screen`)
            .then(response => {
                alert('تم بدء فحص المستأجر بنجاح');
                location.reload();
            })
            .catch(error => {
                alert('حدث خطأ أثناء بدء الفحص');
            });
    }
}
</script>
@endpush
