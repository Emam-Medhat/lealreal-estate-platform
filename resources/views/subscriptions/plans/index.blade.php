@extends('layouts.app')

@section('title', 'إدارة خطط الاشتراكات')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">خطط الاشتراكات</h1>
            <p class="text-muted mb-0">إدارة خطط الاشتراكات المتاحة للمنصة</p>
        </div>
        <div>
            <a href="{{ route('subscriptions.plans.create') }}" class="btn btn-primary">
                <i class="fas fa-plus ms-2"></i>
                إضافة خطة جديدة
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $plans->total() }}</h4>
                            <p class="mb-0">إجمالي الخطط</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-layer-group fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $plans->where('is_active', true)->count() }}</h4>
                            <p class="mb-0">خطط نشطة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $plans->where('is_popular', true)->count() }}</h4>
                            <p class="mb-0">خطط مميزة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-star fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $plans->sum(function($plan) { return $plan->subscriptions->count(); }) }}</h4>
                            <p class="mb-0">إجمالي المشتركين</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('subscriptions.plans.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="بحث عن خطة..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">كل الحالات</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="tier" class="form-select">
                        <option value="">كل المستويات</option>
                        <option value="basic" {{ request('tier') == 'basic' ? 'selected' : '' }}>أساسي</option>
                        <option value="premium" {{ request('tier') == 'premium' ? 'selected' : '' }}>مميز</option>
                        <option value="enterprise" {{ request('tier') == 'enterprise' ? 'selected' : '' }}>مؤسسي</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search ms-2"></i>
                        بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Plans Table -->
    <div class="card">
        <div class="card-body">
            @if($plans->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>الخطة</th>
                                <th>المستوى</th>
                                <th>السعر</th>
                                <th>دورة الفوترة</th>
                                <th>الحد الأقصى للمستخدمين</th>
                                <th>المشتركين</th>
                                <th>الحالة</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plans as $plan)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="ms-3">
                                                <h6 class="mb-0">{{ $plan->name }}</h6>
                                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($plan->description, 50) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($plan->tier)
                                            <span class="badge bg-info">{{ $plan->tier->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ number_format($plan->price, 2) }} {{ $plan->currency ?? 'USD' }}</strong>
                                        @if($plan->setup_fee > 0)
                                            <br><small class="text-muted">رسوم الإعداد: {{ number_format($plan->setup_fee, 2) }} {{ $plan->currency ?? 'USD' }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $plan->billing_cycle ?? 1 }} {{ __('month') }}
                                        @if($plan->trial_days > 0)
                                            <br><small class="text-success">فترة تجريبية: {{ $plan->trial_days }} يوم</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($plan->max_users)
                                            {{ $plan->max_users }}
                                        @else
                                            <span class="text-muted">غير محدود</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $plan->subscriptions->count() }}</span>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" 
                                                   {{ $plan->is_active ? 'checked' : '' }}
                                                   onchange="toggleStatus({{ $plan->id }})"
                                                   id="status-{{ $plan->id }}">
                                            <label class="form-check-label" for="status-{{ $plan->id }}">
                                                {{ $plan->is_active ? 'نشط' : 'غير نشط' }}
                                            </label>
                                        </div>
                                        @if($plan->is_popular)
                                            <span class="badge bg-warning mt-1">
                                                <i class="fas fa-star ms-1"></i>
                                                مميز
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('subscriptions.plans.show', $plan) }}" class="btn btn-outline-info" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('subscriptions.plans.edit', $plan) }}" class="btn btn-outline-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-success" onclick="duplicatePlan({{ $plan->id }})" title="نسخ">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" onclick="deletePlan({{ $plan->id }}, '{{ $plan->name }}')" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">
                            عرض {{ $plans->firstItem() }} إلى {{ $plans->lastItem() }} من {{ $plans->total() }} خطة
                        </small>
                    </div>
                    <div>
                        {{ $plans->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد خطط اشتراك</h5>
                    <p class="text-muted">ابدأ بإضافة أول خطة اشتراك للمنصة</p>
                    <a href="{{ route('subscriptions.plans.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus ms-2"></i>
                        إضافة خطة جديدة
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleStatus(planId) {
    const checkbox = document.getElementById(`status-${planId}`);
    const status = checkbox.checked;
    
    if (confirm('هل أنت متأكد من تغيير حالة هذه الخطة؟')) {
        fetch(`/subscriptions/admin-plans/${planId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم تحديث الحالة بنجاح!');
                location.reload();
            } else {
                checkbox.checked = !status;
                alert('حدث خطأ: ' + (data.message || 'حدث خطأ ما'));
            }
        })
        .catch(error => {
            checkbox.checked = !status;
            alert('حدث خطأ في الاتصال');
        });
    } else {
        checkbox.checked = !status;
    }
}

function duplicatePlan(planId) {
    if (confirm('هل أنت متأكد من نسخ هذه الخطة؟')) {
        fetch(`/subscriptions/admin-plans/${planId}/duplicate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم نسخ الخطة بنجاح!');
                location.reload();
            } else {
                alert('حدث خطأ: ' + (data.message || 'حدث خطأ ما'));
            }
        })
        .catch(error => {
            alert('حدث خطأ في الاتصال');
        });
    }
}

function deletePlan(planId, planName) {
    if (confirm(`هل أنت متأكد من حذف خطة "${planName}"؟ هذا الإجراء لا يمكن التراجع عنه.`)) {
        fetch(`/subscriptions/admin-plans/${planId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم حذف الخطة بنجاح!');
                location.reload();
            } else {
                alert('حدث خطأ: ' + (data.message || 'حدث خطأ ما'));
            }
        })
        .catch(error => {
            alert('حدث خطأ في الاتصال');
        });
    }
}
</script>
@endpush
