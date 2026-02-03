@extends('admin.layouts.admin')

@section('title', 'إدارة الوكلاء')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">إدارة الوكلاء</h1>
            <p class="text-muted mb-0">إدارة وكلاء العقارات في المنصة</p>
        </div>
        <div>
            <a href="{{ route('admin.agents.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>إضافة وكيل جديد
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
                            <h4 class="card-title">{{ $agents->total() }}</h4>
                            <p class="card-text">إجمالي الوكلاء</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
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
                            <h4 class="card-title">{{ $agents->where('status', 'active')->count() }}</h4>
                            <p class="card-text">وكلاء نشطون</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-check fa-2x"></i>
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
                            <h4 class="card-title">{{ $agents->where('status', 'pending')->count() }}</h4>
                            <p class="card-text">في انتظار</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $agents->where('status', 'inactive')->count() }}</h4>
                            <p class="card-text">غير نشطين</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-times fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.agents.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="البحث عن وكيل..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">جميع الحالات</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في انتظار</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fas fa-search me-2"></i>بحث
                        </button>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.agents.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-redo me-2"></i>إعادة تعيين
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Agents Table -->
    <div class="card">
        <div class="card-body">
            @if($agents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>البريد الإلكتروني</th>
                                <th>الهاتف</th>
                                <th>الحالة</th>
                                <th>تاريخ الإنشاء</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($agents as $agent)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center text-white me-3">
                                                {{ strtoupper(substr($agent->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $agent->name }}</h6>
                                                <small class="text-muted">ID: {{ $agent->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $agent->email }}</td>
                                    <td>{{ $agent->phone ?? '-' }}</td>
                                    <td>
                                        @if($agent->status == 'active')
                                            <span class="badge bg-success">نشط</span>
                                        @elseif($agent->status == 'inactive')
                                            <span class="badge bg-danger">غير نشط</span>
                                        @else
                                            <span class="badge bg-warning">في انتظار</span>
                                        @endif
                                    </td>
                                    <td>{{ $agent->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.agents.show', $agent) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.agents.edit', $agent) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteAgent({{ $agent->id }})">
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
                        <span class="text-muted">عرض {{ $agents->firstItem() }} إلى {{ $agents->lastItem() }} من {{ $agents->total() }} وكيل</span>
                    </div>
                    <div>
                        {{ $agents->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">لا يوجد وكلاء</h5>
                    <p class="text-muted">لم يتم العثور على أي وكلاء حالياً</p>
                    <a href="{{ route('admin.agents.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة وكيل جديد
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function deleteAgent(id) {
    if(confirm('هل أنت متأكد من حذف هذا الوكيل؟')) {
        // Implement delete functionality
        console.log('Delete agent:', id);
    }
}
</script>
@endsection
