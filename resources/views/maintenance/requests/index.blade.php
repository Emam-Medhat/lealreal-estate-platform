@extends('layouts.app')

@section('title', 'طلبات الصيانة')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3">طلبات الصيانة</h1>
                <a href="{{ route('maintenance.requests.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> طلب جديد
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('maintenance.requests.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="status">الحالة</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">الكل</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في انتظار</option>
                            <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>مكلف</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="priority">الأولوية</label>
                        <select name="priority" id="priority" class="form-control">
                            <option value="">الكل</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفض</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسط</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالي</option>
                            <option value="emergency" {{ request('priority') == 'emergency' ? 'selected' : '' }}>طوارئ</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="property_id">العقار</label>
                        <select name="property_id" id="property_id" class="form-control">
                            <option value="">الكل</option>
                            @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->title }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="search">البحث</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               value="{{ request('search') }}" placeholder="رقم الطلب أو العنوان">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> بحث
                        </button>
                        <a href="{{ route('maintenance.requests.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> إعادة تعيين
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="card">
        <div class="card-body">
            @if($requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>العنوان</th>
                                <th>العقار</th>
                                <th>الحالة</th>
                                <th>الأولوية</th>
                                <th>التاريخ</th>
                                <th>التكلفة التقديرية</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                            <tr>
                                <td>{{ $request->request_number }}</td>
                                <td>{{ $request->title }}</td>
                                <td>{{ $request->property->title ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-{{ $request->status_color }}">
                                        {{ $request->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $request->priority_color }}">
                                        {{ $request->priority_label }}
                                    </span>
                                </td>
                                <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                <td>{{ $request->estimated_cost ? number_format($request->estimated_cost, 2) : 'N/A' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('maintenance.requests.show', $request) }}" 
                                           class="btn btn-sm btn-info" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($request->canBeAssigned())
                                            <a href="{{ route('maintenance.requests.assign', $request) }}" 
                                               class="btn btn-sm btn-primary" title="تكليف">
                                                <i class="fas fa-user-plus"></i>
                                            </a>
                                        @endif
                                        @if($request->canBeStarted())
                                            <a href="{{ route('maintenance.requests.start', $request) }}" 
                                               class="btn btn-sm btn-success" title="بدء">
                                                <i class="fas fa-play"></i>
                                            </a>
                                        @endif
                                        @if($request->canBeCompleted())
                                            <a href="{{ route('maintenance.requests.complete', $request) }}" 
                                               class="btn btn-sm btn-success" title="إكمال">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        @endif
                                        @if($request->canBeCancelled())
                                            <a href="{{ route('maintenance.requests.cancel', $request) }}" 
                                               class="btn btn-sm btn-danger" title="إلغاء">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('maintenance.requests.edit', $request) }}" 
                                           class="btn btn-sm btn-warning" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $requests->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                    <h5>لا توجد طلبات صيانة</h5>
                    <p class="text-muted">لم يتم العثور على طلبات صيانة تطابق معايير البحث</p>
                    <a href="{{ route('maintenance.requests.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إنشاء طلب جديد
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
